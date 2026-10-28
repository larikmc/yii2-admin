<?php

namespace larikmc\admin\services;

use larikmc\admin\rbac\helpers\SystemRbacHelper;
use larikmc\admin\rbac\services\InstallService;
use Yii;
use yii\base\Component;
use yii\base\Exception;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class FirstAdminService extends Component
{
    private const LOG_CATEGORY = 'security.auth';

    /**
     * Fully-qualified User model class name.
     */
    public string $userClass = '';

    /**
     * Field names for username and email in User model.
     */
    public string $usernameField = 'username';
    public string $emailField = 'email';
    public string $statusField = 'status';

    /**
     * Optional callable for custom user creation:
     * function(string $email, string $password): ?IdentityInterface
     *
     * @var callable|null
     */
    public $customCreator = null;

    /**
     * Checks if the user storage exists and is completely empty (zero users).
     */
    public function isUserTableEmpty(): bool
    {
        $class = $this->userClass;
        if ($class === '' || !class_exists($class)) {
            return false;
        }

        try {
            if (is_subclass_of($class, ActiveRecord::class)) {
                $tableName = $class::tableName();
                $db = $class::getDb();

                // If table doesn't even exist yet, return false
                if ($db->schema->getTableSchema($tableName, true) === null) {
                    return false;
                }

                $count = (new \yii\db\Query())
                    ->from($tableName)
                    ->count('*', $db);

                return (int) $count === 0;
            }

            // Fallback for non-ActiveRecord classes supporting find()
            if (method_exists($class, 'find')) {
                return (int) $class::find()->count() === 0;
            }
        } catch (\Throwable $e) {
            Yii::error('Ошибка проверки наполненности таблицы пользователей: ' . $e->getMessage(), self::LOG_CATEGORY);
            return false;
        }

        return false;
    }

    /**
     * Atomically creates the first administrator user if and only if the user table is empty.
     * Concurrency-safe: uses DB transaction and row-level / table lock or re-checks within transaction.
     *
     * @param string $email
     * @param string $password
     * @param array &$errors Output array of error messages
     * @return IdentityInterface|null
     */
    public function createFirstAdmin(string $email, string $password, array &$errors = []): ?IdentityInterface
    {
        $email = mb_strtolower(trim($email), 'UTF-8');
        $password = (string) $password;

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Некорректный адрес электронной почты.';
            return null;
        }

        $minLen = (int) (Yii::$app->params['user.passwordMinLength'] ?? 6);
        if (strlen($password) < $minLen) {
            $errors[] = sprintf('Длина пароля должна быть не менее %d символов.', $minLen);
            return null;
        }

        $class = $this->userClass;
        if ($class === '' || !class_exists($class)) {
            $errors[] = 'Класс пользователя не сконфигурирован.';
            return null;
        }

        // Ensure RBAC tables and structure are installed and schema is refreshed
        try {
            $installService = new InstallService();
            $installService->installTables();
            Yii::$app->db->schema->refresh();
        } catch (\Throwable $e) {
            Yii::error('Ошибка предварительной установки RBAC: ' . $e->getMessage(), self::LOG_CATEGORY);
        }

        $db = is_subclass_of($class, ActiveRecord::class) ? $class::getDb() : Yii::$app->db;
        $mutex = Yii::$app->has('mutex') ? Yii::$app->mutex : null;
        $mutexKey = 'first_admin_creation_lock_' . md5($class);
        $acquiredMutex = false;

        if ($mutex) {
            $acquiredMutex = $mutex->acquire($mutexKey, 10);
            if (!$acquiredMutex) {
                $errors[] = 'Другой процесс в данный момент выполняет первоначальную настройку администратора.';
                return null;
            }
        }

        $transaction = $db->beginTransaction();

        try {
            // Concurrency check within transaction
            if (is_subclass_of($class, ActiveRecord::class)) {
                $tableName = $class::tableName();

                // For MySQL/MariaDB we can lock table or read with exclusive lock
                if ($db->driverName === 'mysql') {
                    $rawCount = $db->createCommand("SELECT COUNT(*) FROM {$tableName} FOR UPDATE")->queryScalar();
                    $count = (int) $rawCount;
                } else {
                    $count = (int) (new \yii\db\Query())
                        ->from($tableName)
                        ->count('*', $db);
                }

                if ($count > 0) {
                    $transaction->rollBack();
                    if ($acquiredMutex && $mutex) {
                        $mutex->release($mutexKey);
                    }
                    $errors[] = 'В системе уже зарегистрированы пользователи. Автоматическая первоначальная регистрация отклонена.';
                    return null;
                }
            }

            // 1. Instantiate or create user
            /** @var (ActiveRecord&IdentityInterface)|null $user */
            $user = null;

            if (is_callable($this->customCreator)) {
                $user = call_user_func($this->customCreator, $email, $password);
            } else {
                $user = new $class();

                // 1. Email attribute MUST always receive the full validated email
                if ($user->hasAttribute($this->emailField)) {
                    $user->setAttribute($this->emailField, $email);
                }

                // 2. Separate username attribute: only set if table has a distinct username column
                $defaultUsername = strstr($email, '@', true) ?: $email;
                if ($this->usernameField !== '' && $this->usernameField !== $this->emailField && $user->hasAttribute($this->usernameField)) {
                    $user->setAttribute($this->usernameField, $defaultUsername);
                } elseif ($user->hasAttribute('username') && $this->emailField !== 'username') {
                    $user->setAttribute('username', $defaultUsername);
                }

                if ($user->hasAttribute($this->statusField)) {
                    $activeStatus = defined($class . '::STATUS_ACTIVE')
                        ? constant($class . '::STATUS_ACTIVE')
                        : 10;
                    $user->setAttribute($this->statusField, $activeStatus);
                }

                if (method_exists($user, 'setPassword')) {
                    $user->setPassword($password);
                } elseif ($user->hasAttribute('password_hash')) {
                    $user->password_hash = Yii::$app->security->generatePasswordHash($password);
                }

                if (method_exists($user, 'generateAuthKey')) {
                    $user->generateAuthKey();
                } elseif ($user->hasAttribute('auth_key')) {
                    $user->auth_key = Yii::$app->security->generateRandomString();
                }

                if (method_exists($user, 'generateEmailVerificationToken')) {
                    $user->generateEmailVerificationToken();
                }

                if (!$user->save()) {
                    $errMsgs = [];
                    foreach ($user->getFirstErrors() as $msg) {
                        $errMsgs[] = $msg;
                    }
                    $errors = $errMsgs ?: ['Не удалось сохранить модель первого администратора.'];
                    $transaction->rollBack();
                    return null;
                }
            }

            if (!$user || !$user->getId()) {
                $errors[] = 'Пользователь не был создан.';
                $transaction->rollBack();
                return null;
            }

            $userId = (string) $user->getId();

            // 2. Initialize RBAC tables & permissions
            try {
                $installService = new InstallService();
                $installService->installTables();
            } catch (\Throwable $e) {
                Yii::error('Ошибка инициализации RBAC при создании первого администратора: ' . $e->getMessage(), self::LOG_CATEGORY);
            }

            // 3. Assign admin role to this newly created user ID
            $auth = Yii::$app->authManager;
            if ($auth !== null) {
                $role = $auth->getRole(SystemRbacHelper::ADMIN_ROLE);
                if ($role === null) {
                    $role = $auth->createRole(SystemRbacHelper::ADMIN_ROLE);
                    $role->description = SystemRbacHelper::ADMIN_ROLE_DESCRIPTION;
                    $auth->add($role);
                }

                $permission = $auth->getPermission(SystemRbacHelper::ADMIN_PANEL_PERMISSION);
                if ($permission === null) {
                    $permission = $auth->createPermission(SystemRbacHelper::ADMIN_PANEL_PERMISSION);
                    $permission->description = SystemRbacHelper::ADMIN_PANEL_PERMISSION_DESCRIPTION;
                    $auth->add($permission);
                }

                if (!$auth->hasChild($role, $permission)) {
                    $auth->addChild($role, $permission);
                }

                $assignments = $auth->getAssignments($userId);
                if (!isset($assignments[SystemRbacHelper::ADMIN_ROLE])) {
                    $auth->assign($role, $userId);
                }
            }

            $transaction->commit();

            Yii::warning(
                sprintf('Первоначальный администратор успешно создан при первом входе: ID #%s (%s)', $userId, $email),
                self::LOG_CATEGORY
            );

            return $user;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::error('Исключение при создании первого администратора: ' . $e->getMessage(), self::LOG_CATEGORY);
            $errors[] = 'Произошла непредвиденная ошибка при инициализации администратора: ' . $e->getMessage();
            return null;
        } finally {
            if ($acquiredMutex && $mutex) {
                $mutex->release($mutexKey);
            }
        }
    }
}
