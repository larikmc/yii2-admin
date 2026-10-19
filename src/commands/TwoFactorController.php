<?php

namespace larikmc\admin\commands;

use larikmc\admin\Module;
use larikmc\admin\services\TwoFactorService;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

class TwoFactorController extends Controller
{
    private const LOG_CATEGORY = 'security.auth';

    /**
     * Emergency reset of Two-Factor Authentication (2FA) for a given user.
     *
     * Usage:
     *   php yii admin-2fa/reset <id-or-email>
     */
    public function actionReset(string $userIdentifier): int
    {
        $adminModule = Yii::$app->hasModule('admin') ? Yii::$app->getModule('admin') : null;
        $userClass = '';
        if ($adminModule instanceof Module) {
            $userClass = $adminModule->userClass !== '' ? $adminModule->userClass : $adminModule->userModel;
        }
        if ($userClass === '' && Yii::$app->has('user') && !empty(Yii::$app->user->identityClass)) {
            $userClass = Yii::$app->user->identityClass;
        }
        if ($userClass === '' && class_exists('common\models\User')) {
            $userClass = 'common\models\User';
        }
        if ($userClass === '' && class_exists('app\models\User')) {
            $userClass = 'app\models\User';
        }

        if ($userClass === '' || !class_exists($userClass)) {
            $this->stderr("Ошибка: Не сконфигурирован класс пользователя в модуле admin.\n", Console::FG_RED);
            return ExitCode::CONFIG;
        }

        /** @var \yii\web\IdentityInterface|null $user */
        $user = null;
        if (str_contains($userIdentifier, '@') && method_exists($userClass, 'findByEmail')) {
            $user = $userClass::findByEmail($userIdentifier);
        } else {
            $user = $userClass::findIdentity($userIdentifier);
        }

        if (!$user) {
            $this->stderr("Пользователь '{$userIdentifier}' не найден.\n", Console::FG_RED);
            return ExitCode::DATAERR;
        }

        $userId = $user->getId();
        $secretKey = '';
        if ($adminModule instanceof Module && !empty($adminModule->twoFactorSecretKey)) {
            $secretKey = (string) $adminModule->twoFactorSecretKey;
        }

        $twoFactorService = new TwoFactorService(['secretKey' => $secretKey]);

        if (!$twoFactorService->isTwoFactorEnabled($userId)) {
            $this->stdout("Для пользователя ID #{$userId} ({$userIdentifier}) 2FA не включен.\n", Console::FG_YELLOW);
            return ExitCode::OK;
        }

        // Disable 2FA and remove backup codes
        $twoFactorService->disable($userId);

        // Revoke existing identity sessions by regenerating auth_key
        if (method_exists($user, 'generateAuthKey')) {
            $user->generateAuthKey();
            if (method_exists($user, 'save')) {
                $user->save(false);
            }
        }

        // Security Audit Log
        Yii::warning(
            sprintf(
                'Аварийный сброс 2FA выполнен через CLI консоль оператором сервера для пользователя ID #%s (%s)',
                $userId,
                $userIdentifier
            ),
            self::LOG_CATEGORY
        );

        $this->stdout("✓ 2FA успешно сброшен для пользователя ID #{$userId} ({$userIdentifier}).\n", Console::FG_GREEN);
        $this->stdout("✓ Существующие сессии отозваны (auth_key обновлен).\n", Console::FG_GREEN);

        return ExitCode::OK;
    }

    /**
     * Show 2FA status for a given user.
     *
     * Usage:
     *   php yii admin-2fa/status <id-or-email>
     */
    public function actionStatus(string $userIdentifier): int
    {
        $adminModule = Yii::$app->hasModule('admin') ? Yii::$app->getModule('admin') : null;
        $userClass = '';
        if ($adminModule instanceof Module) {
            $userClass = $adminModule->userClass !== '' ? $adminModule->userClass : $adminModule->userModel;
        }
        if ($userClass === '' && Yii::$app->has('user') && !empty(Yii::$app->user->identityClass)) {
            $userClass = Yii::$app->user->identityClass;
        }
        if ($userClass === '' && class_exists('common\models\User')) {
            $userClass = 'common\models\User';
        }
        if ($userClass === '' && class_exists('app\models\User')) {
            $userClass = 'app\models\User';
        }

        if ($userClass === '' || !class_exists($userClass)) {
            $this->stderr("Ошибка: Не сконфигурирован класс пользователя в модуле admin.\n", Console::FG_RED);
            return ExitCode::CONFIG;
        }

        $user = null;
        if (str_contains($userIdentifier, '@') && method_exists($userClass, 'findByEmail')) {
            $user = $userClass::findByEmail($userIdentifier);
        } else {
            $user = $userClass::findIdentity($userIdentifier);
        }

        if (!$user) {
            $this->stderr("Пользователь '{$userIdentifier}' не найден.\n", Console::FG_RED);
            return ExitCode::DATAERR;
        }

        $userId = $user->getId();
        $twoFactorService = new TwoFactorService();
        $isEnabled = $twoFactorService->isTwoFactorEnabled($userId);
        $backupCount = $isEnabled ? $twoFactorService->getRemainingBackupCodesCount($userId) : 0;
        $record = $twoFactorService->getTwoFactorRecord($userId);

        $this->stdout("Пользователь: ID #{$userId}\n", Console::BOLD);
        $this->stdout("Статус 2FA: " . ($isEnabled ? "ВКЛЮЧЕН" : "ОТКЛЮЧЕН") . "\n", $isEnabled ? Console::FG_GREEN : Console::FG_YELLOW);
        if ($isEnabled) {
            $this->stdout("Активных резервных кодов: {$backupCount}\n");
            if (!empty($record['enabled_at'])) {
                $this->stdout("Дата включения: " . date('Y-m-d H:i:s', (int) $record['enabled_at']) . "\n");
            }
            if (!empty($record['last_used_at'])) {
                $this->stdout("Последнее использование: " . date('Y-m-d H:i:s', (int) $record['last_used_at']) . "\n");
            }
        }

        return ExitCode::OK;
    }
}
