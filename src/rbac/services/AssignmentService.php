<?php

namespace larikmc\admin\rbac\services;

use larikmc\admin\rbac\helpers\SystemRbacHelper;
use Yii;

class AssignmentService
{
    public function assignItems(string|int $userId, array $items): void
    {
        $auth = Yii::$app->authManager;
        $auth->revokeAll($userId);

        if ((string) $userId === SystemRbacHelper::ROOT_USER_ID && !in_array(SystemRbacHelper::ADMIN_ROLE, $items, true)) {
            $items[] = SystemRbacHelper::ADMIN_ROLE;
        }

        foreach ($items as $name) {
            $role = $auth->getRole($name);
            if ($role !== null) {
                $auth->assign($role, $userId);
            }
        }
    }

    public function getAssignedItemNames(string|int $userId): array
    {
        $auth = Yii::$app->authManager;
        $roles = [];

        foreach ($auth->getAssignments($userId) as $name => $assignment) {
            if ($auth->getRole($name) !== null) {
                $roles[] = $name;
            }
        }

        return $roles;
    }

    public function getAssignedRolesAsString(string|int $userId): string
    {
        $roles = $this->getAssignedItemNames($userId);

        return $roles === [] ? 'Не назначены' : implode(', ', $roles);
    }

    public function getAssignedRolesCount(string|int $userId): int
    {
        return count($this->getAssignedItemNames($userId));
    }

    public function getAssignableItems(): array
    {
        $auth = Yii::$app->authManager;
        $items = $auth->getRoles();
        $result = [];

        foreach ($items as $item) {
            $suffix = $item->description ? ' - ' . $item->description : ' [роль]';
            $result[$item->name] = $item->name . $suffix;
        }

        asort($result);

        return $result;
    }
}
