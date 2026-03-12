<?php

namespace larikmc\admin\rbac;

use larikmc\admin\rbac\components\RbacConfig;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Module as BaseModule;
use yii\rbac\DbManager;

class Module extends BaseModule
{
    public $controllerNamespace = 'larikmc\admin\rbac\controllers';
    public $defaultRoute = 'default/index';
    public $layout = '@larikmc/admin/views/layouts/main';
    public $userModel;
    public $userIdField = 'id';
    public $usernameField = 'username';
    public $emailField = 'email';
    public $statusField = 'status';
    public $accessRoles = ['admin'];
    public $enablePrettyUrlRules = true;

    private ?RbacConfig $config = null;

    public function init(): void
    {
        parent::init();
        Yii::setAlias('@larikmc/admin/rbac', __DIR__);

        if ($this->userModel === null) {
            throw new InvalidConfigException('The "userModel" property must be configured.');
        }

        if (!is_subclass_of($this->userModel, \yii\db\ActiveRecord::class)) {
            throw new InvalidConfigException('The "userModel" must extend yii\db\ActiveRecord.');
        }

        if (!$this->getAuthManager() instanceof DbManager) {
            throw new InvalidConfigException('The "authManager" component must be an instance of yii\rbac\DbManager.');
        }

        $this->config = new RbacConfig([
            'userModel' => $this->userModel,
            'userIdField' => $this->userIdField,
            'usernameField' => $this->usernameField,
            'emailField' => $this->emailField,
            'statusField' => $this->statusField,
            'accessRoles' => $this->accessRoles,
        ]);
    }

    public function getConfig(): RbacConfig
    {
        return $this->config;
    }

    public function getAuthManager(): DbManager
    {
        return \Yii::$app->authManager;
    }
}
