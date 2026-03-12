<?php

namespace larikmc\admin\auth;

use yii\base\InvalidConfigException;
use yii\base\Module as BaseModule;
use yii\web\IdentityInterface;

class Module extends BaseModule
{
    /** @var string */
    public $controllerNamespace = 'larikmc\admin\auth\controllers';

    /**
     * @var string Fully-qualified User class
     * Example: common\models\User
     */
    public string $userClass;

    /* ============================================================
     * 🔐 Security settings (defaults)
     * ============================================================ */

    /** Максимальное количество попыток входа по IP */
    public int $maxIpAttempts = 10;

    /** Максимальное количество попыток входа по email */
    public int $maxEmailAttempts = 5;

    /** После скольких попыток по IP показывать CAPTCHA */
    public int $captchaAfterIpAttempts = 5;

    /** После скольких попыток по email показывать CAPTCHA */
    public int $captchaAfterEmailAttempts = 3;

    /** Время блокировки по IP (сек) */
    public int $ipLockDuration = 900; // 15 минут

    /** Время блокировки по email (сек) */
    public int $emailLockDuration = 900; // 15 минут

    /** TTL счётчика попыток по IP (сек) */
    public int $ipAttemptsTtl = 900; // 15 минут

    /** TTL счётчика попыток по email (сек) */
    public int $emailAttemptsTtl = 900; // 15 минут

    /** Максимальная задержка при брутфорсе (сек) */
    public int $maxDelaySeconds = 10;

    /** Срок remember-me в секундах */
    public int $rememberMeDuration = 2592000; // 30 дней

    /** Путь к лог-файлу приложения для security dashboard */
    public string $securityLogFile = '@runtime/logs/app.log';

    /**
     * Backward-compatible aliases for legacy config.
     * They are copied to the new split settings in init() when provided.
     */
    public ?int $maxUserAttempts = null;
    public ?int $captchaAfterAttempts = null;
    public ?int $lockDuration = null;
    public ?int $userAttemptsTtl = null;

    public function init(): void
    {
        parent::init();

        if ($this->maxUserAttempts !== null) {
            $this->maxIpAttempts = $this->maxUserAttempts;
            $this->maxEmailAttempts = $this->maxUserAttempts;
        }

        if ($this->captchaAfterAttempts !== null) {
            $this->captchaAfterIpAttempts = $this->captchaAfterAttempts;
            $this->captchaAfterEmailAttempts = $this->captchaAfterAttempts;
        }

        if ($this->lockDuration !== null) {
            $this->ipLockDuration = $this->lockDuration;
            $this->emailLockDuration = $this->lockDuration;
        }

        if ($this->userAttemptsTtl !== null) {
            $this->ipAttemptsTtl = $this->userAttemptsTtl;
            $this->emailAttemptsTtl = $this->userAttemptsTtl;
        }

        if ($this->userClass === '') {
            throw new InvalidConfigException('The "userClass" property must be set for the auth module.');
        }

        if (!class_exists($this->userClass)) {
            throw new InvalidConfigException(sprintf(
                'Configured userClass "%s" was not found.',
                $this->userClass
            ));
        }

        if (!is_subclass_of($this->userClass, IdentityInterface::class)) {
            throw new InvalidConfigException(sprintf(
                'Configured userClass "%s" must implement %s.',
                $this->userClass,
                IdentityInterface::class
            ));
        }

        if (!method_exists($this->userClass, 'findByEmail')) {
            throw new InvalidConfigException(sprintf(
                'Configured userClass "%s" must define a static findByEmail() method.',
                $this->userClass
            ));
        }

        if (!\Yii::$app->has('cache')) {
            throw new InvalidConfigException('The application must define a "cache" component for the auth module.');
        }
    }
}
