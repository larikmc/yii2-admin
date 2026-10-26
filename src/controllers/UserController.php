<?php

namespace larikmc\admin\controllers;

use larikmc\admin\models\ProfileForm;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class UserController extends Controller
{
    private const EMAIL_CHANGE_TTL = 3600; // 1 час

    public $layout = '@larikmc/admin/views/layouts/main';

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => [
                            'profile',
                            'confirm-email',
                            'verify-email-code',
                            'cancel-email-change',
                            'resend-email-change',
                            'enable-2fa',
                            'confirm-2fa',
                            'disable-2fa',
                            'regenerate-backup-codes',
                            'regenerate-two-factor-key',
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'profile' => ['get', 'post'],
                    'confirm-email' => ['get'],
                    'verify-email-code' => ['post'],
                    'cancel-email-change' => ['post'],
                    'resend-email-change' => ['post'],
                    'enable-2fa' => ['post'],
                    'confirm-2fa' => ['post'],
                    'disable-2fa' => ['post'],
                    'regenerate-backup-codes' => ['post'],
                    'regenerate-two-factor-key' => ['post', 'get'],
                ],
            ],
        ];
    }

    public function actionProfile()
    {
        $user = Yii::$app->user->identity;
        if (!$user) {
            throw new NotFoundHttpException('Пользователь не авторизован.');
        }

        $model = new ProfileForm($user);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            if ($model->emailChangeRequested && $model->requestedEmail !== null) {
                $this->initiateEmailChange($user, $model->requestedEmail);
                Yii::$app->session->setFlash(
                    'info',
                    "Основные данные сохранены. Для изменения почты на {$model->requestedEmail} отправлено письмо с подтверждением на текущий адрес {$user->email}."
                );
            } else {
                Yii::$app->session->setFlash('success', 'Настройки профиля успешно сохранены.');
            }

            // If user updated their password, authKey was regenerated, so update current session's token
            if (!empty($model->newPassword) && method_exists($user, 'getAuthKey')) {
                Yii::$app->session->set('auth_2fa_token', hash('sha256', (string) $user->getAuthKey()));
            }

            return $this->refresh();
        }

        $pendingEmailChange = $this->getPendingEmailChange((int) $user->id);

        $twoFactorService = $this->getTwoFactorService();
        $isTwoFactorConfigured = $twoFactorService->isConfigured();
        $isTwoFactorEnabled = false;
        $backupCodesRemaining = 0;
        $twoFactorSetupData = null;

        if ($isTwoFactorConfigured) {
            $isTwoFactorEnabled = $twoFactorService->isTwoFactorEnabled($user->getId());
            $backupCodesRemaining = $isTwoFactorEnabled ? $twoFactorService->getRemainingBackupCodesCount($user->getId()) : 0;

            if (!$isTwoFactorEnabled) {
                // Generate or fetch secret for initial setup
                $setupSecret = (string) Yii::$app->session->get('2fa_setup_secret_' . $user->getId(), '');
                if ($setupSecret === '') {
                    $setupData = $twoFactorService->generateSecretForSetup($user->getId());
                    $setupSecret = $setupData['secret'];
                    Yii::$app->session->set('2fa_setup_secret_' . $user->getId(), $setupSecret);
                }
                $accountName = (string) ($user->email ?? $user->username);
                $qrSvg = $twoFactorService->getQrCodeSvg($setupSecret, $accountName);

                $twoFactorSetupData = [
                    'secret' => $setupSecret,
                    'qrSvg' => $qrSvg,
                    'otpAuthUri' => $twoFactorService->getOtpAuthUri($setupSecret, $accountName),
                ];
            }
        }

        return $this->render('@larikmc/admin/views/user/profile', [
            'model' => $model,
            'user' => $user,
            'pendingEmailChange' => $pendingEmailChange,
            'isTwoFactorConfigured' => $isTwoFactorConfigured,
            'twoFactorKeyInfo' => $twoFactorService->getKeyInfo(),
            'isTwoFactorEnabled' => $isTwoFactorEnabled,
            'backupCodesRemaining' => $backupCodesRemaining,
            'twoFactorSetupData' => $twoFactorSetupData,
        ]);
    }

    public function actionConfirmEmail(?string $token = null): Response
    {
        if (empty($token)) {
            throw new BadRequestHttpException('Отсутствует токен подтверждения.');
        }

        $user = Yii::$app->user->identity;
        if (!$user) {
            throw new NotFoundHttpException('Пользователь не авторизован.');
        }

        $cache = Yii::$app->cache;
        $tokenKey = 'email_change_token_' . $token;
        $userId = $cache ? $cache->get($tokenKey) : null;

        if (!$userId || (int) $userId !== (int) $user->id) {
            Yii::$app->session->setFlash('danger', 'Ссылка подтверждения недействительна или срок ее действия истек.');
            return $this->redirect(['profile']);
        }

        $pending = $this->getPendingEmailChange((int) $user->id);
        if (!$pending || empty($pending['newEmail'])) {
            Yii::$app->session->setFlash('danger', 'Запрос на смену почты не найден или уже был обработан.');
            return $this->redirect(['profile']);
        }

        $newEmail = (string) $pending['newEmail'];
        $user->email = $newEmail;
        if ($user->save(false)) {
            $this->clearPendingEmailChange((int) $user->id, $token);
            Yii::$app->session->setFlash('success', "E-mail успешно подтвержден и изменен на {$newEmail}.");
        } else {
            Yii::$app->session->setFlash('danger', 'Не удалось сохранить новый E-mail.');
        }

        return $this->redirect(['profile']);
    }

    public function actionVerifyEmailCode(): Response
    {
        $user = Yii::$app->user->identity;
        if (!$user) {
            throw new NotFoundHttpException('Пользователь не авторизован.');
        }

        $code = trim((string) $this->request->post('code', ''));
        $pending = $this->getPendingEmailChange((int) $user->id);

        if (!$pending || empty($pending['code'])) {
            Yii::$app->session->setFlash('danger', 'Активный запрос на смену почты не найден.');
            return $this->redirect(['profile']);
        }

        if ($code === '' || $code !== (string) $pending['code']) {
            Yii::$app->session->setFlash('danger', 'Неверный проверочный код. Проверьте код из письма.');
            return $this->redirect(['profile']);
        }

        $newEmail = (string) $pending['newEmail'];
        $user->email = $newEmail;
        if ($user->save(false)) {
            $this->clearPendingEmailChange((int) $user->id, $pending['token'] ?? null);
            Yii::$app->session->setFlash('success', "E-mail успешно подтвержден и изменен на {$newEmail}.");
        } else {
            Yii::$app->session->setFlash('danger', 'Не удалось сохранить новый E-mail.');
        }

        return $this->redirect(['profile']);
    }

    public function actionCancelEmailChange(): Response
    {
        $user = Yii::$app->user->identity;
        if ($user) {
            $pending = $this->getPendingEmailChange((int) $user->id);
            $this->clearPendingEmailChange((int) $user->id, $pending['token'] ?? null);
            Yii::$app->session->setFlash('info', 'Запрос на смену почты отменен.');
        }

        return $this->redirect(['profile']);
    }

    public function actionResendEmailChange(): Response
    {
        $user = Yii::$app->user->identity;
        if (!$user) {
            throw new NotFoundHttpException('Пользователь не авторизован.');
        }

        $pending = $this->getPendingEmailChange((int) $user->id);
        if (!$pending) {
            Yii::$app->session->setFlash('danger', 'Активный запрос на смену почты не найден.');
            return $this->redirect(['profile']);
        }

        $this->sendConfirmationEmail($user, (string) $pending['newEmail'], (string) $pending['token'], (string) $pending['code']);
        Yii::$app->session->setFlash('success', "Письмо с подтверждением повторно отправлено на текущий адрес {$user->email}.");

        return $this->redirect(['profile']);
    }

    private function initiateEmailChange($user, string $newEmail): void
    {
        $token = Yii::$app->security->generateRandomString(32);
        $code = sprintf('%06d', mt_rand(100000, 999999));

        $data = [
            'userId' => (int) $user->id,
            'currentEmail' => (string) $user->email,
            'newEmail' => $newEmail,
            'token' => $token,
            'code' => $code,
            'expiresAt' => time() + self::EMAIL_CHANGE_TTL,
        ];

        $cache = Yii::$app->cache;
        if ($cache) {
            $cache->set('email_change_' . $user->id, $data, self::EMAIL_CHANGE_TTL);
            $cache->set('email_change_token_' . $token, (int) $user->id, self::EMAIL_CHANGE_TTL);
        }

        $this->sendConfirmationEmail($user, $newEmail, $token, $code);
    }

    private function sendConfirmationEmail($user, string $newEmail, string $token, string $code): bool
    {
        if (!Yii::$app->has('mailer')) {
            return false;
        }

        $supportEmail = Yii::$app->params['supportEmail'] ?? (Yii::$app->params['senderEmail'] ?? 'no-reply@skidkum.loc');
        $senderName = Yii::$app->params['senderName'] ?? (Yii::$app->name ?? 'Admin Panel');

        try {
            $confirmUrl = Yii::$app->urlManager->createAbsoluteUrl([
                '/admin/user/confirm-email',
                'token' => $token,
            ]);

            return (bool) Yii::$app->mailer->compose()
                ->setTo($user->email)
                ->setFrom([$supportEmail => $senderName])
                ->setSubject('Подтверждение смены адреса электронной почты')
                ->setTextBody(
                    "Здравствуйте, {$user->username}!\n\n" .
                    "Был получен запрос на смену адреса электронной почты в панели управления.\n\n" .
                    "Новый адрес: {$newEmail}\n" .
                    "Проверочный код: {$code}\n\n" .
                    "Или перейдите по следующей ссылке для подтверждения:\n{$confirmUrl}\n\n" .
                    "Код и ссылка действительны в течение 1 часа.\n" .
                    "Если вы не совершали этот запрос, проигнорируйте письмо — текущий адрес останется без изменений."
                )
                ->send();
        } catch (\Throwable $e) {
            Yii::error('Failed to send email confirmation: ' . $e->getMessage(), __METHOD__);
            return false;
        }
    }

    private function getPendingEmailChange(int $userId): ?array
    {
        $cache = Yii::$app->cache;
        if (!$cache) {
            return null;
        }

        $data = $cache->get('email_change_' . $userId);
        return is_array($data) ? $data : null;
    }

    private function clearPendingEmailChange(int $userId, ?string $token = null): void
    {
        $cache = Yii::$app->cache;
        if (!$cache) {
            return;
        }

        $cache->delete('email_change_' . $userId);
        if (!empty($token)) {
            $cache->delete('email_change_token_' . $token);
        }
    }

    public function actionConfirm2fa(): Response
    {
        $user = Yii::$app->user->identity;
        if (!$user) {
            throw new NotFoundHttpException('Пользователь не авторизован.');
        }

        $code = trim((string) $this->request->post('code', ''));
        $secret = (string) Yii::$app->session->get('2fa_setup_secret_' . $user->getId(), '');

        if ($secret === '') {
            Yii::$app->session->setFlash('danger', 'Сессия настройки 2FA устарела. Обновите страницу и попробуйте снова.');
            return $this->redirect(['profile']);
        }

        $twoFactorService = $this->getTwoFactorService();
        if ($twoFactorService->confirmAndEnable($user->getId(), $secret, $code)) {
            Yii::$app->session->remove('2fa_setup_secret_' . $user->getId());
            Yii::$app->session->set('auth_2fa_passed_user_id', (string) $user->getId());
            if (method_exists($user, 'getAuthKey')) {
                Yii::$app->session->set('auth_2fa_token', hash('sha256', (string) $user->getAuthKey()));
            }
            $backupCodes = $twoFactorService->generateBackupCodes($user->getId());
            Yii::$app->session->setFlash('2fa_new_backup_codes', $backupCodes);

            Yii::info(
                sprintf('Двухфакторная аутентификация успешно включена пользователем ID #%s (%s)', $user->getId(), $user->username ?? $user->email),
                'security.auth'
            );

            Yii::$app->session->setFlash('success', 'Двухфакторная аутентификация успешно включена! Обязательно сохраните ваши резервные коды.');
        } else {
            Yii::$app->session->setFlash('danger', 'Неверный 6-значный код подтверждения. Проверьте время на телефоне и введите актуальный код.');
        }

        return $this->redirect(['profile']);
    }

    public function actionDisable2fa(): Response
    {
        $user = Yii::$app->user->identity;
        if (!$user) {
            throw new NotFoundHttpException('Пользователь не авторизован.');
        }

        $password = (string) $this->request->post('password', '');
        $code = trim((string) $this->request->post('code', ''));

        if ($password === '' || (method_exists($user, 'validatePassword') && !$user->validatePassword($password))) {
            Yii::$app->session->setFlash('danger', 'Для отключения 2FA необходимо указать правильный текущий пароль учетной записи.');
            return $this->redirect(['profile']);
        }

        $twoFactorService = $this->getTwoFactorService();
        if (!$twoFactorService->verifyTotpOrBackupCode($user->getId(), $code)) {
            Yii::$app->session->setFlash('danger', 'Неверный код 2FA для подтверждения отключения.');
            return $this->redirect(['profile']);
        }

        $twoFactorService->disable($user->getId());
        if (method_exists($user, 'generateAuthKey')) {
            $user->generateAuthKey();
            if (method_exists($user, 'save')) {
                $user->save(false);
            }
        }

        Yii::$app->session->remove('auth_2fa_passed_user_id');
        Yii::$app->session->remove('auth_2fa_token');

        Yii::warning(
            sprintf('Двухфакторная аутентификация отключена пользователем ID #%s (%s)', $user->getId(), $user->username ?? $user->email),
            'security.auth'
        );

        Yii::$app->session->setFlash('info', 'Двухфакторная аутентификация отключена.');
        return $this->redirect(['profile']);
    }

    public function actionRegenerateBackupCodes(): Response
    {
        $user = Yii::$app->user->identity;
        if (!$user) {
            throw new NotFoundHttpException('Пользователь не авторизован.');
        }

        $password = (string) $this->request->post('password', '');
        if ($password === '' || (method_exists($user, 'validatePassword') && !$user->validatePassword($password))) {
            Yii::$app->session->setFlash('danger', 'Для перегенерации кодов подтвердите ваш текущий пароль.');
            return $this->redirect(['profile']);
        }

        $twoFactorService = $this->getTwoFactorService();
        if (!$twoFactorService->isTwoFactorEnabled($user->getId())) {
            Yii::$app->session->setFlash('danger', '2FA не включен.');
            return $this->redirect(['profile']);
        }

        $backupCodes = $twoFactorService->generateBackupCodes($user->getId());
        Yii::$app->session->setFlash('2fa_new_backup_codes', $backupCodes);

        Yii::warning(
            sprintf('Сгенерированы новые резервные коды 2FA пользователем ID #%s (%s)', $user->getId(), $user->username ?? $user->email),
            'security.auth'
        );

        Yii::$app->session->setFlash('success', 'Сгенерированы новые резервные коды. Предыдущие коды аннулированы.');

        return $this->redirect(['profile']);
    }

    public function actionRegenerateTwoFactorKey(): Response
    {
        $user = Yii::$app->user->identity;
        if (!$user) {
            throw new NotFoundHttpException('Пользователь не авторизован.');
        }

        try {
            $twoFactorService = $this->getTwoFactorService();
            $newKey = $twoFactorService->generateNewSecretKey();

            // Clear session cached setup secret so a fresh secret and QR code are generated with the new key
            Yii::$app->session->remove('2fa_setup_secret_' . $user->getId());

            Yii::warning(
                sprintf('Сгенерирован новый мастер-ключ шифрования 2FA пользователем ID #%s (%s)', $user->getId(), $user->username ?? $user->email),
                'security.auth'
            );

            Yii::$app->session->setFlash('success', 'Новый ключ шифрования 2FA успешно сгенерирован и сохранён: ' . substr($newKey, 0, 8) . '...' . substr($newKey, -6));
        } catch (\Throwable $e) {
            Yii::error('Ошибка генерации ключа 2FA: ' . $e->getMessage(), 'security.auth');
            Yii::$app->session->setFlash('danger', 'Ошибка генерации ключа: ' . $e->getMessage());
        }

        return $this->redirect(['profile']);
    }

    private function getTwoFactorService(): \larikmc\admin\services\TwoFactorService
    {
        $adminModule = Yii::$app->hasModule('admin') ? Yii::$app->getModule('admin') : null;
        $secretKey = '';
        if ($adminModule !== null && property_exists($adminModule, 'twoFactorSecretKey') && !empty($adminModule->twoFactorSecretKey)) {
            $secretKey = (string) $adminModule->twoFactorSecretKey;
        }

        return new \larikmc\admin\services\TwoFactorService([
            'secretKey' => $secretKey,
        ]);
    }
}
