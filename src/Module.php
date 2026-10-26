<?php

namespace larikmc\admin;

use larikmc\admin\assets\AppAsset;
use Yii;
use yii\web\View;

class Module extends \yii\base\Module
{
    private const DEFAULT_GUEST_ALLOWED_PATHS = [
        'login',
        'logout',
        'auth/login',
        'auth/logout',
        'auth/captcha',
        'auth/invite',
        'auth/request-password-reset',
        'auth/reset-password',
        'auth/two-factor',
        'site/error',
        'admin/login',
        'admin/logout',
        'admin/auth/login',
        'admin/auth/logout',
        'admin/auth/captcha',
        'admin/auth/invite',
        'admin/auth/request-password-reset',
        'admin/auth/reset-password',
        'admin/auth/two-factor',
        'admin/site/error',
    ];

    public $controllerNamespace = 'larikmc\admin\controllers';
    public $defaultRoute = 'site/index';
    public $menu = [];
    public $secondaryMenu = [];
    public ?string $rootUserId = null;
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
    public bool $enableTwoFactor = true;
    public bool $enforceTwoFactorForAdmins = false;
    public ?string $twoFactorSecretKey = null;
    public ?int $maxUserAttempts = null;
    public ?int $captchaAfterAttempts = null;
    public ?int $lockDuration = null;
    public ?int $userAttemptsTtl = null;
    public bool $allowFirstAdminRegistration = true;
    public ?string $lazyloadPlaceholderUrl = null;
    public bool $guardUnknownAdminRoutesForGuests = true;
    public array $guestAllowedPaths = [];
    public bool $autoExpandActiveSubmenu = true;

    public function init()
    {
        parent::init();
        Yii::setAlias('@larikmc/admin', __DIR__);
        Yii::setAlias('@larikmc/admin/auth', __DIR__ . '/auth');
        Yii::setAlias('@larikmc/admin/rbac', __DIR__ . '/rbac');

        if (Yii::$app->has('urlManager')) {
            $moduleId = $this->id;
            Yii::$app->getUrlManager()->addRules([
                'login' => $moduleId . '/auth/auth/login',
                $moduleId . '/login' => $moduleId . '/auth/auth/login',
                $moduleId . '/logout' => $moduleId . '/auth/auth/logout',
                $moduleId . '/auth/<action:[\w\-]+>' => $moduleId . '/auth/auth/<action>',
                'auth/<action:[\w\-]+>' => $moduleId . '/auth/auth/<action>',
                'ui-kit' => $moduleId . '/site/ui-kit',
                'site/ui-kit' => $moduleId . '/site/ui-kit',
                $moduleId . '/site/ui-kit' => $moduleId . '/site/ui-kit',
                'profile' => $moduleId . '/user/profile',
                'user/profile' => $moduleId . '/user/profile',
                $moduleId . '/user/profile' => $moduleId . '/user/profile',
            ], false);
        }

        if (Yii::$app->has('errorHandler')) {
            $errorHandler = Yii::$app->getErrorHandler();
            if ($errorHandler->errorAction === 'site/error' || $errorHandler->errorAction === null) {
                $errorHandler->errorAction = $this->id . '/site/error';
            }
        }

        if ($this->guardUnknownAdminRoutesForGuests) {
            Yii::$app->on(\yii\base\Application::EVENT_BEFORE_REQUEST, function (): void {
                $this->guardGuestRequest();
                $this->guardTwoFactorRequest();
            });
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
                'enableTwoFactor' => $this->enableTwoFactor,
                'enforceTwoFactorForAdmins' => $this->enforceTwoFactorForAdmins,
                'twoFactorSecretKey' => $this->twoFactorSecretKey,
                'maxUserAttempts' => $this->maxUserAttempts,
                'captchaAfterAttempts' => $this->captchaAfterAttempts,
                'lockDuration' => $this->lockDuration,
                'userAttemptsTtl' => $this->userAttemptsTtl,
                'allowFirstAdminRegistration' => $this->allowFirstAdminRegistration,
                'usernameField' => $this->usernameField,
                'emailField' => $this->emailField,
                'statusField' => $this->statusField,
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

    private function guardGuestRequest(): void
    {
        $app = Yii::$app;
        if (!$app->has('user') || !$app->has('request') || $app->user->isGuest === false) {
            return;
        }

        $pathInfo = trim((string) $app->request->pathInfo, '/');
        $normalizedPath = preg_replace('#^admin/#', '', $pathInfo) ?? $pathInfo;

        if ($pathInfo === '' || $pathInfo === 'admin') {
            // Check if there is a pending 2FA authentication in session
            if ($app->has('session') && $app->session->has('auth_2fa_pending_user_id')) {
                $app->response->redirect(['/admin/auth/two-factor']);
                $app->end();
            }
            $app->user->loginRequired();
            $app->end();
        }

        $allowedPaths = array_values(array_unique(array_merge(
            self::DEFAULT_GUEST_ALLOWED_PATHS,
            $this->guestAllowedPaths
        )));

        if (in_array($pathInfo, $allowedPaths, true) || in_array($normalizedPath, $allowedPaths, true)) {
            return;
        }

        $app->user->loginRequired();
        $app->end();
    }

    private function guardTwoFactorRequest(): void
    {
        $app = Yii::$app;
        if (!$this->enableTwoFactor || !$app->has('user') || !$app->has('request') || $app->user->isGuest) {
            return;
        }

        $pathInfo = trim((string) $app->request->pathInfo, '/');
        $normalizedPath = preg_replace('#^admin/#', '', $pathInfo) ?? $pathInfo;

        // Only guard routes belonging to admin panel
        $isAdminRoute = ($pathInfo === 'admin' || str_starts_with($pathInfo, 'admin/')
            || $normalizedPath === 'login' || $normalizedPath === 'logout'
            || str_starts_with($normalizedPath, 'auth/') || str_starts_with($normalizedPath, 'rbac/')
            || str_starts_with($normalizedPath, 'user/') || str_starts_with($normalizedPath, 'site/'));

        if (!$isAdminRoute) {
            return;
        }

        // Exclude logout from 2FA guard so user can always log out
        if ($normalizedPath === 'logout' || $normalizedPath === 'auth/logout' || $pathInfo === 'admin/auth/logout') {
            return;
        }

        $userId = $app->user->getId();
        if ($userId === null) {
            return;
        }

        $twoFactorService = new \larikmc\admin\services\TwoFactorService([
            'secretKey' => (string) ($this->twoFactorSecretKey ?? ''),
        ]);

        if (!$twoFactorService->isTwoFactorEnabled($userId)) {
            return;
        }

        $session = $app->has('session') ? $app->session : null;
        $passedUserId = $session ? $session->get('auth_2fa_passed_user_id') : null;
        $sessionAuthToken = $session ? $session->get('auth_2fa_token') : null;

        // Verify session validity and token bound to current identity's authKey
        $identity = $app->user->identity;
        $expectedToken = null;
        if ($identity && method_exists($identity, 'getAuthKey')) {
            $expectedToken = hash('sha256', (string) $identity->getAuthKey());
        }

        $isSessionValid = ($passedUserId !== null && (string) $passedUserId === (string) $userId);
        if ($isSessionValid && $expectedToken !== null) {
            // Centralized session revocation: if user's authKey changed (password change, 2FA reset, etc.),
            // any active session holding the old token is immediately invalidated!
            if (empty($sessionAuthToken) || !hash_equals($expectedToken, (string) $sessionAuthToken)) {
                $isSessionValid = false;
                Yii::warning("Сессия 2FA аннулирована для пользователя ID {$userId} из-за смены authKey.", 'security.auth');
            }
        }

        if (!$isSessionValid) {
            // User was authenticated via cookie/rememberMe or authKey changed,
            // must re-verify 2FA in this browser session!
            $rememberDuration = (int) $this->rememberMeDuration;
            $app->user->logout(false); // remove active identity without destroying frontend session data

            if ($session) {
                $session->remove('auth_2fa_passed_user_id');
                $session->remove('auth_2fa_token');
                $session->set('auth_2fa_pending_user_id', (string) $userId);
                $session->set('auth_2fa_pending_remember', $rememberDuration);
                $session->set('auth_2fa_pending_expire', time() + 300);
            }

            $app->response->redirect(['/admin/auth/two-factor']);
            $app->end();
        }
    }
}
