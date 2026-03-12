<?php

namespace larikmc\admin\auth\controllers;

use Yii;
use yii\web\Controller;
use yii\captcha\CaptchaAction;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use larikmc\admin\auth\models\LoginForm;
use larikmc\admin\auth\Module;
use larikmc\admin\rbac\services\InstallService;

class AuthController extends Controller
{
    private const LOG_CATEGORY = 'security.auth';

    public $layout = '@larikmc/admin/auth/views/layouts/auth';

    protected function getAuthRoute(string $route): array
    {
        return ['/auth/' . ltrim($route, '/')];
    }

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['login', 'captcha'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'security-log', 'clear-security-log'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                    'clear-security-log' => ['post'],
                ],
            ],
        ];
    }

    /**
     * CAPTCHA action
     */
    public function actions(): array
    {
        return [
            'captcha' => [
                'class' => CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Login action with brute-force protection v2:
     * - IP: protects endpoint from spam (CAPTCHA + small delay)
     * - Email: protects targeted account (CAPTCHA + LOCK + admin notify)
     */
    public function actionLogin($email = null)
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['/admin/site/index']);
        }

        /** @var Module $module */
        $module = $this->module;

        $model   = new LoginForm([
            'userClass' => $module->userClass,
            'rememberMeDuration' => $module->rememberMeDuration,
        ]);
        $request = Yii::$app->request;
        $cache   = Yii::$app->cache;
        $response = Yii::$app->response;
        $session = Yii::$app->session;

        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        $now = time();
        $remaining = 0;

        // ---------------------------------------------------------------------
        // Keys: IP
        // ---------------------------------------------------------------------
        $ip = (string)$request->userIP;
        $ipHash = md5($ip);

        $ipAttemptsKey = 'login_attempts_ip_' . $ipHash;
        $ipLockKey     = 'login_lock_ip_' . $ipHash;

        $ipAttempts = (int)($cache->get($ipAttemptsKey) ?? 0);
        $ipLockRaw  = $cache->get($ipLockKey);

        // ---------------------------------------------------------------------
        // Email (best effort): POST -> GET param
        // ---------------------------------------------------------------------
        $flashEmail = (string)$session->getFlash('auth.loginEmail', '');
        $postEmail = (string)($request->post('LoginForm')['email'] ?? '');
        $emailNorm = mb_strtolower(trim($postEmail ?: ($flashEmail ?: (string)$email)), 'UTF-8');
        $hasEmail  = $emailNorm !== '';
        $emailHash = $hasEmail ? md5($emailNorm) : null;

        $emailAttemptsKey = $hasEmail ? ('login_attempts_email_' . $emailHash) : null;
        $emailLockKey     = $hasEmail ? ('login_lock_email_' . $emailHash) : null;

        $emailAttempts = $hasEmail ? (int)($cache->get($emailAttemptsKey) ?? 0) : 0;
        $emailLockRaw  = $hasEmail ? $cache->get($emailLockKey) : false;

        // ---------------------------------------------------------------------
        // 🕵️ Honeytoken (bot trap)
        // IMPORTANT: in view login_check must be a TEXT input hidden by CSS (not hiddenInput)
        // ---------------------------------------------------------------------
        if (!empty($request->post('login_check'))) {
            Yii::warning("Обнаружен бот при авторизации с IP {$ip}", self::LOG_CATEGORY);

            $ipLockTime = $now + (int)$module->ipLockDuration;
            $cache->set($ipLockKey, $ipLockTime, (int)$module->ipLockDuration);

            if ($hasEmail) {
                $emailLockTime = $now + (int)$module->emailLockDuration;
                $cache->set($emailLockKey, $emailLockTime, (int)$module->emailLockDuration);
                Yii::warning(sprintf(
                    'Сработала honeytoken-блокировка для email %s с IP %s',
                    $this->maskEmail($emailNorm),
                    $ip
                ), self::LOG_CATEGORY);
            }

            Yii::warning("Сработала honeytoken-блокировка для IP {$ip}", self::LOG_CATEGORY);

            return $this->redirect($this->getAuthRoute('login'));
        }

        // ---------------------------------------------------------------------
        // 🔒 Lock checks (Email lock has priority; IP lock is secondary)
        // ---------------------------------------------------------------------
        if ($hasEmail && $emailLockRaw !== false && is_numeric($emailLockRaw)) {
            $lockTime = (int)$emailLockRaw;
            if ($lockTime > $now) {
                $remaining = $lockTime - $now;
                return $this->render('login', [
                    'model'     => $model,
                    'remaining' => (int)$remaining,
                ]);
            }
            $cache->delete($emailLockKey);
        }

        if ($ipLockRaw !== false && is_numeric($ipLockRaw)) {
            $lockTime = (int)$ipLockRaw;
            if ($lockTime > $now) {
                $remaining = $lockTime - $now;
                return $this->render('login', [
                    'model'     => $model,
                    'remaining' => (int)$remaining,
                ]);
            }
            $cache->delete($ipLockKey);
        }

        // ---------------------------------------------------------------------
        // 🤖 CAPTCHA decision:
        // - If IP is noisy -> CAPTCHA
        // - If email is being attacked -> CAPTCHA
        // ---------------------------------------------------------------------
        $needCaptcha =
            ($ipAttempts >= (int)$module->captchaAfterIpAttempts) ||
            ($hasEmail && $emailAttempts >= (int)$module->captchaAfterEmailAttempts);

        if ($needCaptcha) {
            $model->scenario = 'withCaptcha';
        }

        // ---------------------------------------------------------------------
        // 🔑 POST
        // ---------------------------------------------------------------------
        if ($model->load($request->post())) {
            // normalize email for model and for email-based keys
            $model->email = mb_strtolower(trim((string)$model->email), 'UTF-8');

            // refresh email keys based on submitted email (source of truth)
            $emailNorm = $model->email;
            $hasEmail  = $emailNorm !== '';
            $emailHash = $hasEmail ? md5($emailNorm) : null;

            $emailAttemptsKey = $hasEmail ? ('login_attempts_email_' . $emailHash) : null;
            $emailLockKey     = $hasEmail ? ('login_lock_email_' . $emailHash) : null;
            $emailAttempts    = $hasEmail ? (int)($cache->get($emailAttemptsKey) ?? 0) : 0;

            // Re-check email lock right before validating login (race-safe)
            if ($hasEmail) {
                $emailLockRaw = $cache->get($emailLockKey);
                if ($emailLockRaw !== false && is_numeric($emailLockRaw)) {
                    $lockTime = (int)$emailLockRaw;
                    if ($lockTime > $now) {
                        $remaining = $lockTime - $now;
                        return $this->render('login', [
                            'model'     => $model,
                            'remaining' => (int)$remaining,
                        ]);
                    }
                    $cache->delete($emailLockKey);
                }
            }

            if ($model->login()) {
                Yii::$app->session->regenerateID(true);

                try {
                    (new InstallService())->installTables();
                } catch (\Throwable $e) {
                    Yii::error('Не удалось автоматически инициализировать RBAC: ' . $e->getMessage(), self::LOG_CATEGORY);
                }

                // Success: clear IP counters/locks
                $cache->delete($ipAttemptsKey);
                $cache->delete($ipLockKey);

                // Success: clear email counters/locks (for that email)
                if ($hasEmail) {
                    $cache->delete($emailAttemptsKey);
                    $cache->delete($emailLockKey);
                }

                return $this->goBack();
            }

            Yii::warning(sprintf(
                'Неудачная попытка входа для %s с IP %s',
                $hasEmail ? $this->maskEmail($emailNorm) : '***',
                $ip
            ), self::LOG_CATEGORY);

            // -------------------------------------------------------------
            // Delay (safe): use usleep (milliseconds), not sleep(seconds)
            // -------------------------------------------------------------
            $effAttempts = $hasEmail ? max($ipAttempts, $emailAttempts) : $ipAttempts;
            $delayMs = min(
                max(0, $effAttempts - 1) * 200,
                max(0, (int)$module->maxDelaySeconds) * 1000
            );
            if ($delayMs > 0) {
                usleep($delayMs * 1000);
            }

            // -------------------------------------------------------------
            // Increment attempts
            // -------------------------------------------------------------
            $ipAttempts++;
            $cache->set($ipAttemptsKey, $ipAttempts, (int)$module->ipAttemptsTtl);

            if ($hasEmail) {
                $emailAttempts++;
                $cache->set($emailAttemptsKey, $emailAttempts, (int)$module->emailAttemptsTtl);
            }

            // -------------------------------------------------------------
            // LOCK policy + notify admin on EMAIL lock
            // -------------------------------------------------------------
            if ($hasEmail) {
                $remainingEmailAttempts = max((int)$module->maxEmailAttempts - $emailAttempts, 0);

                if ($remainingEmailAttempts <= 0) {
                    $lockTime = time() + (int)$module->emailLockDuration;
                    $cache->set($emailLockKey, $lockTime, (int)$module->emailLockDuration);
                    Yii::warning(sprintf(
                        'Сработала блокировка по email для %s с IP %s',
                        $this->maskEmail($emailNorm),
                        $ip
                    ), self::LOG_CATEGORY);

                    // 📧 notify admin once per emailLockDuration per attacked email
                    $notifyKey = 'admin_lock_notify_' . md5($emailNorm);
                    if (!$cache->get($notifyKey)) {
                        $cache->set($notifyKey, 1, (int)$module->emailLockDuration);

                        try {
                            $adminEmail = Yii::$app->params['adminEmail'] ?? null;
                            if ($adminEmail && Yii::$app->has('mailer')) {
                                Yii::$app->mailer->compose()
                                    ->setTo($adminEmail)
                                    ->setFrom([
                                            Yii::$app->params['supportEmail'] ?? 'no-reply@site.com' => Yii::$app->name
                                    ])
                                    ->setSubject('🔐 Блокировка входа по email')
                                    ->setTextBody(
                                        "Зафиксирована блокировка входа по email.\n\n" .
                                        "Email: " . $this->maskEmail($emailNorm) . "\n" .
                                        "IP: {$ip}\n" .
                                        "Время блокировки до: " . date('Y-m-d H:i:s', $lockTime) . "\n" .
                                        "Длительность: " . (int)((int)$module->emailLockDuration / 60) . " мин.\n\n" .
                                        "Вероятная причина: подбор пароля (rotating IP/UA)."
                                    )
                                    ->send();
                            } elseif ($adminEmail) {
                                Yii::warning('Компонент mailer не настроен, уведомление администратору о блокировке не отправлено.', self::LOG_CATEGORY);
                            }
                        } catch (\Throwable $e) {
                            Yii::error('Не удалось отправить уведомление администратору о блокировке: ' . $e->getMessage(), self::LOG_CATEGORY);
                        }
                    }
                }
            }

            $remainingIpAttempts = max((int)$module->maxIpAttempts - $ipAttempts, 0);

            if ($remainingIpAttempts <= 0) {
                $lockTime = time() + (int)$module->ipLockDuration;
                $cache->set($ipLockKey, $lockTime, (int)$module->ipLockDuration);
                Yii::warning("Сработала блокировка по IP {$ip}", self::LOG_CATEGORY);
            }

            $session->setFlash('auth.loginEmail', $model->email);
            $session->setFlash('error', 'Неверный логин или пароль.');
            return $this->redirect($this->getAuthRoute('login'));
        }

        // ---------------------------------------------------------------------
        // GET
        // ---------------------------------------------------------------------
        $model->password = '';

        if (!empty($emailNorm)) {
            $model->email = $emailNorm;
        }

        // Ensure scenario is set for GET too (to render captcha when needed)
        if ($needCaptcha) {
            $model->scenario = 'withCaptcha';
        }

        return $this->render('login', [
            'model'     => $model,
            'remaining' => (int)$remaining,
        ]);
    }

    /**
     * Logout
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->redirect($this->getAuthRoute('login'));
    }

    public function actionSecurityLog()
    {
        if (Yii::$app->user->isGuest || !Yii::$app->user->can('admin')) {
            throw new \yii\web\ForbiddenHttpException('Журнал безопасности доступен только администраторам.');
        }

        $this->layout = '@larikmc/admin/views/layouts/main';

        return $this->render('security-log', [
            'logs' => $this->readSecurityLogs(),
        ]);
    }

    public function actionClearSecurityLog()
    {
        if (Yii::$app->user->isGuest || !Yii::$app->user->can('admin')) {
            throw new \yii\web\ForbiddenHttpException('Журнал безопасности доступен только администраторам.');
        }

        $file = Yii::getAlias($this->module->securityLogFile);
        if (is_file($file)) {
            file_put_contents($file, '');
            Yii::warning('Журнал безопасности был очищен авторизованным пользователем.', self::LOG_CATEGORY);
            Yii::$app->session->setFlash('success', 'Журнал безопасности очищен.');
        } else {
            Yii::$app->session->setFlash('warning', 'Файл логов не найден.');
        }

        return $this->redirect($this->getAuthRoute('security-log'));
    }

    private function maskEmail(string $email): string
    {
        if ($email === '' || strpos($email, '@') === false) {
            return '***';
        }

        [$localPart, $domain] = explode('@', $email, 2);
        $visibleLocal = mb_substr($localPart, 0, 2, 'UTF-8');

        return $visibleLocal . '***@' . $domain;
    }

    private function readSecurityLogs(): array
    {
        $file = Yii::getAlias($this->module->securityLogFile);
        if (!is_file($file)) {
            return [];
        }

        $logs = [];
        $lines = array_reverse(file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));

        foreach ($lines as $line) {
            if (strpos($line, self::LOG_CATEGORY) === false) {
                continue;
            }

            preg_match(
                '/^(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}).*\[(.*?)\]\[.*?\]\[(.*?)\]\[(.*?)\]\s+(.*)$/',
                $line,
                $matches
            );

            $logs[] = [
                'date' => $matches[1] ?? '',
                'ip' => $matches[2] ?? '',
                'level' => $matches[3] ?? '',
                'category' => $matches[4] ?? self::LOG_CATEGORY,
                'message' => $matches[5] ?? $line,
            ];
        }

        return $logs;
    }
}
