<?php

namespace larikmc\admin\rbac\components;

use yii\base\BaseObject;

class RbacConfig extends BaseObject
{
    public string $userModel;
    public string $userIdField = 'id';
    public string $usernameField = 'username';
    public string $emailField = 'email';
    public string $statusField = 'status';
    public array $accessRoles = ['admin'];
}
