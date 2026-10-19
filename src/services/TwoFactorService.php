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
        $uri = $this->getTotp()->getOtpAuthUri($secret, $accountName, $this->issuer);
        return $this->getTotp()->generateQrCodeSvg($uri, 200);
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
     *
     * @throws \yii\base\InvalidConfigException if no key is configured or key length is less than 32 bytes
     */
    public function resolveSecretKey(): string
    {
        $key = null;

        if ($this->secretKey !== '') {
            $key = $this->secretKey;
        } elseif (!empty($_ENV['ADMIN_2FA_SECRET_KEY'])) {
            $key = (string) $_ENV['ADMIN_2FA_SECRET_KEY'];
        } elseif (($envVal = getenv('ADMIN_2FA_SECRET_KEY')) !== false && $envVal !== '') {
            $key = (string) $envVal;
        } elseif (!empty(Yii::$app->params['admin.2fa.secretKey'])) {
            $key = (string) Yii::$app->params['admin.2fa.secretKey'];
        }

        if (empty($key)) {
            throw new \yii\base\InvalidConfigException(
                'Критическая ошибка безопасности 2FA: Не настроен криптографический ключ приложения для шифрования секретов 2FA. ' .
                'Задайте переменную окружения ADMIN_2FA_SECRET_KEY или параметр Yii::$app->params[\'admin.2fa.secretKey\'] ' .
                '(криптографически стойкая случайная строка длиной не менее 32 символов).'
            );
        }

        if (strlen($key) < 32) {
            throw new \yii\base\InvalidConfigException(
                'Критическая ошибка безопасности 2FA: Длина ключа шифрования 2FA недостаточна (' . strlen($key) . ' байт). ' .
                'Требуется минимум 32 символа.'
            );
        }

        return $key;
    }
}
