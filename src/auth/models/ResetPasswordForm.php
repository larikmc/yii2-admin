<?php

namespace larikmc\admin\auth\models;

use Yii;
use yii\base\InvalidArgumentException;
use yii\base\Model;
use yii\web\IdentityInterface;

class ResetPasswordForm extends Model
{
    public string $userClass = '';
    public $password;

    private ?IdentityInterface $_user = null;

    public function __construct(string $token, string $userClass, $config = [])
    {
        if ($token === '') {
            throw new InvalidArgumentException('Токен сброса пароля не может быть пустым.');
        }

        $this->userClass = $userClass;

        /** @var class-string<IdentityInterface> $class */
        $class = $this->userClass;
        if (!method_exists($class, 'findByPasswordResetToken')) {
            throw new InvalidArgumentException('Модель пользователя не поддерживает поиск по токену сброса пароля.');
        }

        $user = $class::findByPasswordResetToken($token);
        if (!$user instanceof IdentityInterface) {
            throw new InvalidArgumentException('Неверный или просроченный токен сброса пароля.');
        }

        $this->_user = $user;
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            ['password', 'required'],
            ['password', 'string', 'min' => (int) (Yii::$app->params['user.passwordMinLength'] ?? 6)],
        ];
    }

    public function resetPassword(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $user = $this->getUser();
        if ($user === null || !method_exists($user, 'setPassword')) {
            return false;
        }

        $user->setPassword($this->password);
        if (method_exists($user, 'removePasswordResetToken')) {
            $user->removePasswordResetToken();
        }
        if (method_exists($user, 'generateAuthKey')) {
            $user->generateAuthKey();
        }

        return $user->save(false);
    }

    public function attributeLabels(): array
    {
        return [
            'password' => 'Новый пароль',
        ];
    }

    public function getUser(): ?IdentityInterface
    {
        return $this->_user;
    }
}
