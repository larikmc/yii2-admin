<?php

namespace larikmc\admin\models;

use Yii;
use yii\base\Model;
use yii\web\IdentityInterface;

class ProfileForm extends Model
{
    public ?string $username = null;
    public ?string $email = null;
    public ?string $currentPassword = null;
    public ?string $newPassword = null;
    public ?string $confirmPassword = null;

    /**
     * @var \yii\db\ActiveRecord|IdentityInterface
     */
    private $user;

    public function __construct($user, array $config = [])
    {
        $this->user = $user;
        $this->username = isset($user->username) ? (string) $user->username : (string) ($user->email ?? '');
        $this->email = (string) ($user->email ?? '');
        parent::__construct($config);
    }

    public function rules(): array
    {
        $userClass = get_class($this->user);
        $hasUsername = $this->user->hasProperty('username') || $this->user->canGetProperty('username');

        $rules = [
            [['email'], 'required'],
            ['email', 'trim'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            [
                'email',
                'unique',
                'targetClass' => $userClass,
                'targetAttribute' => 'email',
                'filter' => ['!=', 'id', $this->user->id],
                'message' => 'Этот E-mail уже используется.',
            ],
            [['currentPassword', 'newPassword', 'confirmPassword'], 'string', 'min' => 6],
            ['currentPassword', 'validateCurrentPassword'],
            ['confirmPassword', 'compare', 'compareAttribute' => 'newPassword', 'message' => 'Пароли не совпадают.'],
        ];

        if ($hasUsername) {
            $rules[] = ['username', 'required'];
            $rules[] = ['username', 'trim'];
            $rules[] = ['username', 'string', 'min' => 2, 'max' => 255];
            $rules[] = [
                'username',
                'unique',
                'targetClass' => $userClass,
                'targetAttribute' => 'username',
                'filter' => ['!=', 'id', $this->user->id],
                'message' => 'Это имя пользователя уже занято.',
            ];
        }

        return $rules;
    }

    public function validateCurrentPassword(string $attribute): void
    {
        if (!empty($this->newPassword)) {
            if (empty($this->currentPassword)) {
                $this->addError($attribute, 'Для смены пароля укажите текущий пароль.');
            } elseif (method_exists($this->user, 'validatePassword') && !$this->user->validatePassword($this->currentPassword)) {
                $this->addError($attribute, 'Неверный текущий пароль.');
            }
        }
    }

    public function attributeLabels(): array
    {
        return [
            'username' => 'Имя пользователя (логин)',
            'email' => 'Электронная почта (E-mail)',
            'currentPassword' => 'Текущий пароль',
            'newPassword' => 'Новый пароль',
            'confirmPassword' => 'Подтверждение нового пароля',
        ];
    }

    public bool $emailChangeRequested = false;
    public ?string $requestedEmail = null;

    public function save(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        if ($this->user->hasProperty('username') || $this->user->canSetProperty('username')) {
            $this->user->username = $this->username;
        }

        if (!empty($this->newPassword)) {
            if (method_exists($this->user, 'setPassword')) {
                $this->user->setPassword($this->newPassword);
            }
            if (method_exists($this->user, 'generateAuthKey')) {
                $this->user->generateAuthKey();
            }
        }

        $newEmail = trim((string) $this->email);
        $currentEmail = trim((string) $this->user->email);

        if ($newEmail !== '' && strcasecmp($newEmail, $currentEmail) !== 0) {
            $this->emailChangeRequested = true;
            $this->requestedEmail = $newEmail;
        }

        return (bool) $this->user->save();
    }

    public function getUser()
    {
        return $this->user;
    }
}
