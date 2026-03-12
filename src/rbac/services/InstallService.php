<?php

namespace larikmc\admin\rbac\services;

use larikmc\admin\rbac\helpers\SystemRbacHelper;
use larikmc\admin\rbac\migrations\m000001_create_rbac_tables;
use Yii;
use yii\base\Exception;

class InstallService
{
    public function isInstalled(): bool
    {
        $auth = Yii::$app->authManager;
        $db = Yii::$app->db;

        return $db->schema->getTableSchema($auth->ruleTable, true) !== null
            && $db->schema->getTableSchema($auth->itemTable, true) !== null
            && $db->schema->getTableSchema($auth->itemChildTable, true) !== null
            && $db->schema->getTableSchema($auth->assignmentTable, true) !== null;
    }

    public function installTables(): void
    {
        $alreadyInstalled = $this->isInstalled();
        if (!$alreadyInstalled) {
            $migration = new m000001_create_rbac_tables();
            ob_start();
            try {
                if ($migration->safeUp() === false) {
                    throw new Exception('Не удалось установить таблицы RBAC.');
                }
            } finally {
                ob_end_clean();
            }
        }

        $this->installSystemData();
    }

    private function installSystemData(): void
    {
        $auth = Yii::$app->authManager;

        $permission = $auth->getPermission(SystemRbacHelper::ADMIN_PANEL_PERMISSION);
        if ($permission === null) {
            $permission = $auth->createPermission(SystemRbacHelper::ADMIN_PANEL_PERMISSION);
            $permission->description = SystemRbacHelper::ADMIN_PANEL_PERMISSION_DESCRIPTION;
            $auth->add($permission);
        }

        $role = $auth->getRole(SystemRbacHelper::ADMIN_ROLE);
        if ($role === null) {
            $role = $auth->createRole(SystemRbacHelper::ADMIN_ROLE);
            $role->description = SystemRbacHelper::ADMIN_ROLE_DESCRIPTION;
            $auth->add($role);
        }

        if (!$auth->hasChild($role, $permission)) {
            $auth->addChild($role, $permission);
        }

        $assignments = $auth->getAssignments(SystemRbacHelper::ROOT_USER_ID);
        if (!isset($assignments[SystemRbacHelper::ADMIN_ROLE])) {
            $auth->assign($role, SystemRbacHelper::ROOT_USER_ID);
        }
    }
}
