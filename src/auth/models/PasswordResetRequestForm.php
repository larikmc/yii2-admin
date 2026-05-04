<?php

namespace larikmc\admin\auth\models;

use Yii;
use yii\base\Model;
use yii\web\IdentityInterface;

class PasswordResetRequestForm extends Model
{
    public string $userClass = '';
    public $email;

    public function rules(): array
    {
        return [
            ['email', 'required'],
            ['email', 'trim'],
            ['email', 'filter', 'filter' => static fn($value) => mb_strtolower((string) $value, 'UTF-8')],
            ['email', 'email'],
        ];
    }

    public function sendEmail(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $user = $this->getUser();
        if ($user === null) {
            return true;
        }

        $userId = $user->getId();
        if ($userId === null || !Yii::$app->authManager->checkAccess($userId, 'adminPanel')) {
            return true;
        }

        if (!method_exists($user, 'generatePasswordResetToken') || !method_exists($user, 'save')) {
            return true;
        }

        $user->generatePasswordResetToken();
        if (!$user->save(false)) {
            return false;
        }

        if (!Yii::$app->has('mailer')) {
            return true;
        }

        $resetLink = Yii::$app->urlManager->createAbsoluteUrl([
            '/auth/reset-password',
            'token' => $user->password_reset_token,
        ]);

        return Yii::$app->mailer->compose()
            ->setTo($this->email)
            ->setFrom([
                Yii::$app->params['supportEmail'] ?? 'no-reply@site.com' => Yii::$app->name,
            ])
            ->setSubject('Восстановление пароля в админке')
            ->setTextBody(
                "Для сброса пароля перейдите по ссылке:\n" . $resetLink
            )
            ->send();
    }

    public function attributeLabels(): array
    {
        return [
            'email' => 'Email',
        ];
    }

    protected function getUser(): ?IdentityInterface
    {
        /** @var class-string<IdentityInterface> $userClass */
        $userClass = $this->userClass;

        return $userClass::findByEmail((string) $this->email);
    }
}
