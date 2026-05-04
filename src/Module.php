<?php

namespace larikmc\admin;

use larikmc\admin\assets\AppAsset;
use Yii;
use yii\web\View;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'larikmc\admin\controllers';
    public $defaultRoute = 'site/index';
    public $menu = [];
    public $secondaryMenu = [];
    public string $userClass = '';
    public string $userModel = '';
    public string $userIdField = 'id';
    public string $usernameField = 'username';
    public string $emailField = 'email';
    public string $statusField = 'status';
    public array $rbacAccessRoles = [];
    public int $maxIpAttempts = 10;
    public int $maxEmailAttempts = 5;
    public int $captchaAfterIpAttempts = 5;
    public int $captchaAfterEmailAttempts = 3;
    public int $ipLockDuration = 900;
    public int $emailLockDuration = 900;
    public int $ipAttemptsTtl = 900;
    public int $emailAttemptsTtl = 900;
    public int $maxDelaySeconds = 10;
    public int $rememberMeDuration = 2592000;
    public string $securityLogFile = '@runtime/logs/app.log';
    public ?int $maxUserAttempts = null;
    public ?int $captchaAfterAttempts = null;
    public ?int $lockDuration = null;
    public ?int $userAttemptsTtl = null;
    public ?string $lazyloadPlaceholderUrl = null;

    public function init()
    {
        parent::init();
        Yii::setAlias('@larikmc/admin', __DIR__);
        Yii::setAlias('@larikmc/admin/auth', __DIR__ . '/auth');
        Yii::setAlias('@larikmc/admin/rbac', __DIR__ . '/rbac');

        if (Yii::$app->has('urlManager')) {
            Yii::$app->getUrlManager()->addRules([
                'auth/invite' => 'admin/auth/auth/invite',
                'auth/request-password-reset' => 'admin/auth/auth/request-password-reset',
                'auth/reset-password' => 'admin/auth/auth/reset-password',
            ], false);
        }

        $defaults = require __DIR__ . '/config/menu.php';

        if (empty($this->menu)) {
            $this->menu = $defaults['primary'] ?? [];
        }

        if (empty($this->secondaryMenu)) {
            $this->secondaryMenu = $defaults['secondary'] ?? [];
        }

        $userClass = $this->userClass !== '' ? $this->userClass : $this->userModel;
        $userModel = $this->userModel !== '' ? $this->userModel : $userClass;

        $this->setModules([
            'auth' => [
                'class' => \larikmc\admin\auth\Module::class,
                'userClass' => $userClass,
                'maxIpAttempts' => $this->maxIpAttempts,
                'maxEmailAttempts' => $this->maxEmailAttempts,
                'captchaAfterIpAttempts' => $this->captchaAfterIpAttempts,
                'captchaAfterEmailAttempts' => $this->captchaAfterEmailAttempts,
                'ipLockDuration' => $this->ipLockDuration,
                'emailLockDuration' => $this->emailLockDuration,
                'ipAttemptsTtl' => $this->ipAttemptsTtl,
                'emailAttemptsTtl' => $this->emailAttemptsTtl,
                'maxDelaySeconds' => $this->maxDelaySeconds,
                'rememberMeDuration' => $this->rememberMeDuration,
                'securityLogFile' => $this->securityLogFile,
                'maxUserAttempts' => $this->maxUserAttempts,
                'captchaAfterAttempts' => $this->captchaAfterAttempts,
                'lockDuration' => $this->lockDuration,
                'userAttemptsTtl' => $this->userAttemptsTtl,
            ],
            'rbac' => [
                'class' => \larikmc\admin\rbac\Module::class,
                'userModel' => $userModel,
                'userIdField' => $this->userIdField,
                'usernameField' => $this->usernameField,
                'emailField' => $this->emailField,
                'statusField' => $this->statusField,
                'accessRoles' => $this->rbacAccessRoles,
            ],
        ]);
    }

    public function getLazyloadPlaceholderUrl(View $view): string
    {
        if ($this->lazyloadPlaceholderUrl !== null && $this->lazyloadPlaceholderUrl !== '') {
            return Yii::getAlias($this->lazyloadPlaceholderUrl);
        }

        return AppAsset::register($view)->baseUrl . '/img/load.svg';
    }
}
