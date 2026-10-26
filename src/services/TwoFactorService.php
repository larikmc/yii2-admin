<?php

namespace larikmc\admin\services;

use larikmc\admin\migrations\m000002_create_admin_2fa_tables;
use Yii;
use yii\base\Component;
use yii\base\Exception;

class TwoFactorService extends Component
{
    public const TABLE_2FA = '{{%admin_user_2fa}}';
    public const TABLE_BACKUP = '{{%admin_user_2fa_backup_code}}';

    public string $secretKey = '';
    public string $issuer = '';

    private ?TotpService $_totp = null;

    public function init(): void
    {
        parent::init();
        if ($this->issuer === '') {
            $this->issuer = (string) (Yii::$app->name ?? 'Admin Panel');
        }
        $this->_totp = new TotpService();
    }

    public function getTotp(): TotpService
    {
        if ($this->_totp === null) {
            $this->_totp = new TotpService();
        }
        return $this->_totp;
    }

    public function ensureTablesInstalled(): void
    {
        $db = Yii::$app->db;
        $has2fa = $db->schema->getTableSchema(self::TABLE_2FA, true) !== null;
        $hasBackup = $db->schema->getTableSchema(self::TABLE_BACKUP, true) !== null;

        if (!$has2fa || !$hasBackup) {
            $migration = new m000002_create_admin_2fa_tables();
            ob_start();
            try {
                $migration->safeUp();
            } catch (\Throwable $e) {
                // Table might have already been created concurrently by parallel worker
                Yii::warning('Миграция 2FA: ' . $e->getMessage(), 'security.auth');
            } finally {
                ob_end_clean();
            }
            $db->schema->refresh();
        }
    }

    public function isTwoFactorEnabled(string|int $userId): bool
    {
        $this->ensureTablesInstalled();

        $record = (new \yii\db\Query())
            ->from(self::TABLE_2FA)
            ->where(['user_id' => (string) $userId, 'is_enabled' => 1])
            ->one();

        return !empty($record);
    }

    public function getTwoFactorRecord(string|int $userId): ?array
    {
        $this->ensureTablesInstalled();

        $record = (new \yii\db\Query())
            ->from(self::TABLE_2FA)
            ->where(['user_id' => (string) $userId])
            ->one();

        return is_array($record) ? $record : null;
    }

    public function generateSecretForSetup(string|int $userId): array
    {
        $this->ensureTablesInstalled();

        $secret = $this->getTotp()->generateSecret();
        $encryptedSecret = $this->encryptSecret($secret);

        $existing = $this->getTwoFactorRecord($userId);
        if ($existing !== null) {
            Yii::$app->db->createCommand()->update(
                self::TABLE_2FA,
                [
                    'secret' => $encryptedSecret,
                    'is_enabled' => 0,
                    'created_at' => time(),
                    'enabled_at' => null,
                ],
                ['user_id' => (string) $userId]
            )->execute();
        } else {
            Yii::$app->db->createCommand()->insert(
                self::TABLE_2FA,
                [
                    'user_id' => (string) $userId,
                    'secret' => $encryptedSecret,
                    'is_enabled' => 0,
                    'created_at' => time(),
                    'enabled_at' => null,
                    'last_used_at' => null,
                ]
            )->execute();
        }

        return [
            'secret' => $secret,
        ];
    }

    public function getQrCodeSvg(string $secret, string $accountName): string
    {
        $uri = $this->getOtpAuthUri($secret, $accountName);
        return $this->getTotp()->generateQrCodeSvg($uri, 200);
    }

    public function getOtpAuthUri(string $secret, string $accountName): string
    {
        return $this->getTotp()->getOtpAuthUri($secret, $accountName, $this->issuer);
    }

    public function confirmAndEnable(string|int $userId, string $secret, string $code): bool
    {
        $this->ensureTablesInstalled();

        if (!$this->getTotp()->verifyCode($secret, $code)) {
            return false;
        }

        $encryptedSecret = $this->encryptSecret($secret);

        Yii::$app->db->createCommand()->update(
            self::TABLE_2FA,
            [
                'secret' => $encryptedSecret,
                'is_enabled' => 1,
                'enabled_at' => time(),
                'last_used_at' => time(),
            ],
            ['user_id' => (string) $userId]
        )->execute();

        return true;
    }

    public function disable(string|int $userId): void
    {
        $this->ensureTablesInstalled();

        Yii::$app->db->createCommand()->delete(self::TABLE_2FA, ['user_id' => (string) $userId])->execute();
        Yii::$app->db->createCommand()->delete(self::TABLE_BACKUP, ['user_id' => (string) $userId])->execute();
    }

    public function generateBackupCodes(string|int $userId, int $count = 8): array
    {
        $this->ensureTablesInstalled();

        // Clear existing unused backup codes
        Yii::$app->db->createCommand()->delete(self::TABLE_BACKUP, ['user_id' => (string) $userId])->execute();

        $plainCodes = [];
        $now = time();

        for ($i = 0; $i < $count; $i++) {
            $code = sprintf(
                '%04d-%04d',
                random_int(1000, 9999),
                random_int(1000, 9999)
            );
            $plainCodes[] = $code;

            $hash = Yii::$app->security->generatePasswordHash($code);
            Yii::$app->db->createCommand()->insert(self::TABLE_BACKUP, [
                'user_id' => (string) $userId,
                'code_hash' => $hash,
                'used_at' => null,
                'created_at' => $now,
            ])->execute();
        }

        return $plainCodes;
    }

    public function getRemainingBackupCodesCount(string|int $userId): int
    {
        $this->ensureTablesInstalled();

        return (int) (new \yii\db\Query())
            ->from(self::TABLE_BACKUP)
            ->where(['user_id' => (string) $userId, 'used_at' => null])
            ->count();
    }

    public function verifyTotpOrBackupCode(string|int $userId, string $code): bool
    {
        $this->ensureTablesInstalled();
        $code = trim($code);
        if ($code === '') {
            return false;
        }

        $record = $this->getTwoFactorRecord($userId);
        if (!$record || empty($record['is_enabled'])) {
            return false;
        }

        // 1. Check if it is a 6-digit TOTP code
        if (strlen($code) === 6 && ctype_digit($code)) {
            $secret = $this->decryptSecret((string) $record['secret']);
            if ($secret === '' || !$this->getTotp()->verifyCode($secret, $code)) {
                return false;
            }

            // Anti-replay check: prevent using same code twice within its validity window.
            // Using atomic cache->add() prevents race conditions / concurrent submissions.
            $replayKey = '2fa_replay_' . md5($userId . '_' . $code);
            if (Yii::$app->has('cache')) {
                $cache = Yii::$app->cache;
                // add() returns false if the key already exists (atomic write lock)
                if (!$cache->add($replayKey, time(), 90)) {
                    Yii::warning("Попытка повторного использования (replay) кода 2FA для пользователя ID: {$userId}", 'security.auth');
                    return false;
                }
            }

            // Update last_used_at
            Yii::$app->db->createCommand()->update(
                self::TABLE_2FA,
                ['last_used_at' => time()],
                ['user_id' => (string) $userId]
            )->execute();

            return true;
        }

        // 2. Check if it matches a backup code (atomic single-use verification via DB transaction)
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            $backupCodes = (new \yii\db\Query())
                ->from(self::TABLE_BACKUP)
                ->where(['user_id' => (string) $userId, 'used_at' => null])
                ->all($db);

            foreach ($backupCodes as $row) {
                if (Yii::$app->security->validatePassword($code, (string) $row['code_hash'])) {
                    // Atomically mark backup code as used only if it is still unused (concurrency safe)
                    $affected = $db->createCommand()->update(
                        self::TABLE_BACKUP,
                        ['used_at' => time()],
                        ['id' => $row['id'], 'used_at' => null]
                    )->execute();

                    if ($affected === 1) {
                        $db->createCommand()->update(
                            self::TABLE_2FA,
                            ['last_used_at' => time()],
                            ['user_id' => (string) $userId]
                        )->execute();

                        $transaction->commit();
                        return true;
                    }
                }
            }

            $transaction->rollBack();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::error('Ошибка при атомарной проверке резервного кода 2FA: ' . $e->getMessage(), 'security.auth');
            return false;
        }

        return false;
    }

    /**
     * Checks if the 2FA secret encryption key is configured properly.
     */
    public function isConfigured(): bool
    {
        try {
            $key = $this->resolveSecretKey();
            return !empty($key);
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function encryptSecret(string $secret): string
    {
        $key = $this->resolveSecretKey();
        $encryptedBinary = Yii::$app->security->encryptByKey($secret, $key);
        return base64_encode($encryptedBinary);
    }

    public function decryptSecret(string $encrypted): string
    {
        $key = $this->resolveSecretKey();
        $binary = base64_decode($encrypted, true);
        if ($binary === false) {
            // Backward compatibility fallback in case raw binary was stored previously
            $binary = $encrypted;
        }
        $decrypted = Yii::$app->security->decryptByKey($binary, $key);
        return is_string($decrypted) ? $decrypted : '';
    }

    /**
     * Resolves the encryption key for 2FA secrets.
     *
     * Precedence:
     * 1. Explicitly configured $this->secretKey
     * 2. Environment variable ADMIN_2FA_SECRET_KEY
     * 3. Yii::$app->params['admin.2fa.secretKey']
     * 4. Persistent key file (@backend/runtime/admin_2fa.key or @runtime/admin_2fa.key)
     * 5. Automatically generated and persisted 64-char random key
     * 6. Deterministic derivation from cookieValidationKey / app ID
     */
    public function resolveSecretKey(): string
    {
        // 1. Explicitly configured
        if ($this->secretKey !== '' && strlen($this->secretKey) >= 32) {
            return $this->secretKey;
        }

        // 2. Environment variable
        if (!empty($_ENV['ADMIN_2FA_SECRET_KEY']) && strlen((string) $_ENV['ADMIN_2FA_SECRET_KEY']) >= 32) {
            return (string) $_ENV['ADMIN_2FA_SECRET_KEY'];
        }
        if (($envVal = getenv('ADMIN_2FA_SECRET_KEY')) !== false && strlen((string) $envVal) >= 32) {
            return (string) $envVal;
        }

        // 3. Application params
        if (!empty(Yii::$app->params['admin.2fa.secretKey']) && strlen((string) Yii::$app->params['admin.2fa.secretKey']) >= 32) {
            return (string) Yii::$app->params['admin.2fa.secretKey'];
        }

        // 4. Stored persistent key file
        $keyFile = $this->resolveKeyFilePath();
        if ($keyFile !== null && is_file($keyFile)) {
            $fileKey = trim((string) @file_get_contents($keyFile));
            if (strlen($fileKey) >= 32) {
                return $fileKey;
            }
        }

        // 5. Automatically generate and persist a strong 64-character random key
        try {
            $generatedKey = bin2hex(random_bytes(32));
            if ($keyFile !== null) {
                $dir = dirname($keyFile);
                if (!is_dir($dir)) {
                    @mkdir($dir, 0775, true);
                }
                if (@file_put_contents($keyFile, $generatedKey) !== false) {
                    @chmod($keyFile, 0600);
                    return $generatedKey;
                }
            }
        } catch (\Throwable $e) {
            Yii::warning('Не удалось сохранить сгенерированный 2FA ключ в файл: ' . $e->getMessage(), 'security.2fa');
        }

        // 6. Cryptographic derivation fallback from cookieValidationKey
        $cookieKey = null;
        if (Yii::$app->has('request') && !empty(Yii::$app->request->cookieValidationKey)) {
            $cookieKey = (string) Yii::$app->request->cookieValidationKey;
        }
        if ($cookieKey !== null && strlen($cookieKey) >= 16) {
            return hash_hmac('sha256', 'yii2-admin-2fa-totp-encryption-key-salt-v1', $cookieKey);
        }

        return hash_hmac('sha256', 'yii2-admin-2fa-totp-encryption-key-salt-v1', (string) (Yii::$app->id ?? 'yii2-admin-secret-key'));
    }

    /**
     * Resolves key file path on disk.
     */
    public function resolveKeyFilePath(): ?string
    {
        $candidates = [
            Yii::getAlias('@backend/runtime/admin_2fa.key', false),
            Yii::getAlias('@runtime/admin_2fa.key', false),
            Yii::getAlias('@app/runtime/admin_2fa.key', false),
            Yii::getAlias('@common/runtime/admin_2fa.key', false),
        ];

        // Check if file already exists
        foreach ($candidates as $path) {
            if (is_string($path) && $path !== '' && is_file($path)) {
                return $path;
            }
        }

        // Return first writable candidate
        foreach ($candidates as $path) {
            if (is_string($path) && $path !== '') {
                $dir = dirname($path);
                if (is_dir($dir) || @mkdir($dir, 0775, true)) {
                    return $path;
                }
            }
        }

        return null;
    }

    /**
     * Manually generate/regenerate a new 2FA encryption key and save it.
     */
    public function generateNewSecretKey(): string
    {
        $newKey = bin2hex(random_bytes(32));
        $keyFile = $this->resolveKeyFilePath();
        if ($keyFile !== null) {
            $dir = dirname($keyFile);
            if (!is_dir($dir)) {
                @mkdir($dir, 0775, true);
            }
            $written = @file_put_contents($keyFile, $newKey);
            if ($written === false) {
                throw new Exception("Не удалось записать файл ключа 2FA по пути: {$keyFile}. Проверьте права на запись.");
            }
            @chmod($keyFile, 0600);
        } else {
            throw new Exception("Не удалось определить путь для сохранения ключа 2FA (@runtime/admin_2fa.key).");
        }

        return $newKey;
    }

    /**
     * Returns detailed information about current 2FA encryption key.
     */
    public function getKeyInfo(): array
    {
        $key = $this->resolveSecretKey();

        if ($this->secretKey !== '') {
            $source = 'module_config';
            $sourceLabel = 'Конфигурация модуля (twoFactorSecretKey)';
        } elseif (!empty($_ENV['ADMIN_2FA_SECRET_KEY']) || (getenv('ADMIN_2FA_SECRET_KEY') !== false && getenv('ADMIN_2FA_SECRET_KEY') !== '')) {
            $source = 'env';
            $sourceLabel = 'Переменная окружения (ADMIN_2FA_SECRET_KEY)';
        } elseif (!empty(Yii::$app->params['admin.2fa.secretKey'])) {
            $source = 'params';
            $sourceLabel = 'Параметр приложения (admin.2fa.secretKey)';
        } else {
            $keyFile = $this->resolveKeyFilePath();
            if ($keyFile !== null && is_file($keyFile)) {
                $source = 'auto_file';
                $sourceLabel = 'Автоматически сгенерирован (' . basename($keyFile) . ')';
            } else {
                $source = 'derived';
                $sourceLabel = 'Автоматически вычислен (cookieValidationKey)';
            }
        }

        return [
            'key' => $key,
            'source' => $source,
            'sourceLabel' => $sourceLabel,
            'keyLength' => strlen($key),
            'keyMasked' => substr($key, 0, 8) . '...' . substr($key, -6),
            'filePath' => $this->resolveKeyFilePath(),
        ];
    }
}
