<?php

namespace larikmc\admin\auth\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class InviteSignupForm extends Model
{
    public string $userClass = '';
    public $username;
    public $email;
    public $password;

    public function rules(): array
    {
        return [
            [['username', 'email', 'password'], 'required'],
            [['username', 'email'], 'trim'],
            ['email', 'filter', 'filter' => static fn($value) => mb_strtolower((string) $value, 'UTF-8')],
            ['username', 'string', 'min' => 2, 'max' => 255],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['password', 'string', 'min' => $this->resolvePasswordMinLength()],
            ['username', 'validateUsernameUnique'],
            ['email', 'validateEmailUnique'],
        ];
    }

    public function signup(): ?IdentityInterface
    {
        if (!$this->validate()) {
            return null;
        }

        /** @var class-string<ActiveRecord&IdentityInterface> $class */
        $class = $this->userClass;
        if (!is_subclass_of($class, ActiveRecord::class)) {
            $this->addError('email', 'Модель пользователя должна быть ActiveRecord для регистрации по приглашению.');
            return null;
        }

        /** @var ActiveRecord&IdentityInterface $user */
        $user = new $class();
        $user->username = $this->username;
        $user->email = $this->email;

        if (defined($class . '::STATUS_ACTIVE') && $user->hasAttribute('status')) {
            $user->setAttribute('status', constant($class . '::STATUS_ACTIVE'));
        }

        if (method_exists($user, 'setPassword')) {
            $user->setPassword($this->password);
        }

        if (method_exists($user, 'generateAuthKey')) {
            $user->generateAuthKey();
        }

        if (!$user->save()) {
            foreach ($user->getFirstErrors() as $message) {
                $this->addError('email', $message);
            }

            return null;
        }

        return $user;
    }

    public function attributeLabels(): array
    {
        return [
            'username' => 'Логин',
            'email' => 'Email',
            'password' => 'Пароль',
        ];
    }

    public function validateUsernameUnique(string $attribute): void
    {
        if ($this->hasErrors()) {
            return;
        }

        $class = $this->userClass;
        if ($class::find()->andWhere(['username' => $this->$attribute])->exists()) {
            $this->addError($attribute, 'Этот логин уже занят.');
        }
    }

    public function validateEmailUnique(string $attribute): void
    {
        if ($this->hasErrors()) {
            return;
        }

        $class = $this->userClass;
        if ($class::find()->andWhere(['email' => $this->$attribute])->exists()) {
            $this->addError($attribute, 'Этот email уже используется.');
        }
    }

    private function resolvePasswordMinLength(): int
    {
        return (int) (Yii::$app->params['user.passwordMinLength'] ?? 6);
    }
}
