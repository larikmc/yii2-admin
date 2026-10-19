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
    public bool $allowFirstAdminRegistration = true;

    private $_user = null;
    private ?bool $_isFirstAdminScenario = null;

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
            ['verifyCode', 'captcha', 'on' => 'withCaptcha', 'captchaAction' => 'admin/auth/auth/captcha'],
        ];
    }

    public function isFirstAdminScenario(): bool
    {
        if ($this->_isFirstAdminScenario !== null) {
            return $this->_isFirstAdminScenario;
        }

        if (!$this->allowFirstAdminRegistration) {
            $this->_isFirstAdminScenario = false;
            return false;
        }

        $service = new \larikmc\admin\services\FirstAdminService(['userClass' => $this->userClass]);
        $this->_isFirstAdminScenario = $service->isUserTableEmpty();

        return $this->_isFirstAdminScenario;
    }

    public function validatePassword($attribute): void
    {
        if ($this->hasErrors()) {
            return;
        }

        $user = $this->getUser();
        $password = (string)$this->password;

        if (!$user) {
            // Check if this is the first admin on a clean installation
            if ($this->isFirstAdminScenario()) {
                // Table is completely empty! Validate password requirements for initial admin creation
                $minLen = (int) (Yii::$app->params['user.passwordMinLength'] ?? 6);
                if (strlen($password) < $minLen) {
                    $this->addError($attribute, sprintf('Пароль первого администратора должен быть не менее %d символов.', $minLen));
                }
                return;
            }

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

    public function getUser(): ?IdentityInterface
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

    public function setUser(?IdentityInterface $user): void
    {
        $this->_user = $user;
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
