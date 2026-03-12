<?php

namespace larikmc\admin\auth\models;

use Yii;
use yii\base\Model;
use yii\web\IdentityInterface;

class LoginForm extends Model
{
    public const CAPTCHA_ROUTE = '/auth/captcha';
    private const DUMMY_PASSWORD_HASH = '$2y$13$usesomesillystringfore7hnbRJHxXVLeakoG8K30oukPsA.ztMG';

    public $email;
    public $password;
    public $verifyCode;
    public string $userClass;
    public int $rememberMeDuration = 2592000;

    private $_user = null;

    public function rules(): array
    {
        return [
            [['email', 'password'], 'required'],

            ['email', 'trim'],
            ['email', 'filter', 'filter' => static fn($v) => mb_strtolower((string)$v, 'UTF-8')],
            ['email', 'email'],

            // Проверка пароля только в сценариях логина
            ['password', 'validatePassword', 'on' => ['default', 'withCaptcha']],

            // CAPTCHA scenario
            ['verifyCode', 'required', 'on' => 'withCaptcha'],
            ['verifyCode', 'captcha', 'on' => 'withCaptcha', 'captchaAction' => self::CAPTCHA_ROUTE],
        ];
    }

    public function validatePassword($attribute): void
    {
        if ($this->hasErrors()) {
            return;
        }

        $user = $this->getUser();
        $password = (string)$this->password;

        if (!$user) {
            password_verify($password, self::DUMMY_PASSWORD_HASH);
            $this->addError($attribute, 'Неверный логин или пароль.');
            return;
        }

        if (!$user->validatePassword($password)) {
            $this->addError($attribute, 'Неверный логин или пароль.');
        }
    }

    public function login(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        return Yii::$app->user->login(
            $this->getUser(),
            $this->rememberMeDuration
        );
    }

    protected function getUser(): ?IdentityInterface
    {
        if ($this->_user !== null) {
            return $this->_user;
        }

        $email = (string)$this->email;
        if ($email === '') {
            return null;
        }

        /** @var class-string<IdentityInterface> $userClass */
        $userClass = $this->userClass;
        $this->_user = $userClass::findByEmail($email);

        return $this->_user;
    }


    public function attributeLabels(): array
    {
        return [
            'email'      => 'Email',
            'password'   => 'Пароль',
            'verifyCode' => 'Проверочный код',
        ];
    }
}
