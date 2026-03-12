<?php

namespace larikmc\admin\rbac\services;

use larikmc\admin\rbac\forms\PermissionForm;
use larikmc\admin\rbac\forms\RoleForm;
use larikmc\admin\rbac\helpers\SystemRbacHelper;
use Yii;
use yii\base\InvalidArgumentException;
use yii\rbac\Item;

class RbacService
{
    public function createRole(RoleForm $form): void
    {
        $auth = Yii::$app->authManager;
        if ($auth->getRole($form->name) || $auth->getPermission($form->name)) {
            throw new InvalidArgumentException('Элемент с таким именем уже существует.');
        }

        $role = $auth->createRole($form->name);
        $role->description = $form->description;
        $role->ruleName = $form->ruleName ?: null;
        $auth->add($role);
        $this->syncChildren($role, $form->children);
    }

    public function updateRole(string $name, RoleForm $form): void
    {
        $auth = Yii::$app->authManager;
        $role = $auth->getRole($name);
        if ($role === null) {
            throw new InvalidArgumentException('Роль не найдена.');
        }

        if (SystemRbacHelper::isProtectedRole($name)) {
            $form->name = SystemRbacHelper::ADMIN_ROLE;
        }

        if ($name !== $form->name && ($auth->getRole($form->name) || $auth->getPermission($form->name))) {
            throw new InvalidArgumentException('Элемент с таким именем уже существует.');
        }

        $oldName = $role->name;
        $role->name = $form->name;
        $role->description = $form->description;
        $role->ruleName = $form->ruleName ?: null;
        $auth->update($oldName, $role);
        $children = $form->children;
        if (SystemRbacHelper::isProtectedRole($oldName) && !in_array(SystemRbacHelper::ADMIN_PANEL_PERMISSION, $children, true)) {
            $children[] = SystemRbacHelper::ADMIN_PANEL_PERMISSION;
        }
        $this->syncChildren($role, $children);
    }

    public function deleteRole(string $name): void
    {
        if (SystemRbacHelper::isProtectedRole($name)) {
            throw new InvalidArgumentException('Системную роль admin удалить нельзя.');
        }

        $role = Yii::$app->authManager->getRole($name);
        if ($role === null) {
            throw new InvalidArgumentException('Роль не найдена.');
        }

        Yii::$app->authManager->remove($role);
    }

    public function createPermission(PermissionForm $form): void
    {
        $auth = Yii::$app->authManager;
        if ($auth->getPermission($form->name) || $auth->getRole($form->name)) {
            throw new InvalidArgumentException('Элемент с таким именем уже существует.');
        }

        $permission = $auth->createPermission($form->name);
        $permission->description = $form->description;
        $permission->ruleName = $form->ruleName ?: null;
        $auth->add($permission);
        $this->syncChildren($permission, $form->children);
    }

    public function updatePermission(string $name, PermissionForm $form): void
    {
        $auth = Yii::$app->authManager;
        $permission = $auth->getPermission($name);
        if ($permission === null) {
            throw new InvalidArgumentException('Действие не найдено.');
        }

        if (SystemRbacHelper::isProtectedPermission($name)) {
            $form->name = SystemRbacHelper::ADMIN_PANEL_PERMISSION;
        }

        if ($name !== $form->name && ($auth->getPermission($form->name) || $auth->getRole($form->name))) {
            throw new InvalidArgumentException('Элемент с таким именем уже существует.');
        }

        $oldName = $permission->name;
        $permission->name = $form->name;
        $permission->description = $form->description;
        $permission->ruleName = $form->ruleName ?: null;
        $auth->update($oldName, $permission);
        $this->syncChildren($permission, $form->children);
    }

    public function deletePermission(string $name): void
    {
        if (SystemRbacHelper::isProtectedPermission($name)) {
            throw new InvalidArgumentException('Системное действие adminPanel удалить нельзя.');
        }

        $permission = Yii::$app->authManager->getPermission($name);
        if ($permission === null) {
            throw new InvalidArgumentException('Действие не найдено.');
        }

        Yii::$app->authManager->remove($permission);
    }

    public function getChildOptions(int $type, ?string $excludeName = null): array
    {
        $auth = Yii::$app->authManager;
        $items = $type === Item::TYPE_ROLE
            ? array_merge($auth->getPermissions(), $auth->getRoles())
            : $auth->getPermissions();

        $options = [];
        foreach ($items as $item) {
            if ($excludeName !== null && $item->name === $excludeName) {
                continue;
            }

            $options[$item->name] = sprintf('%s (%s)', $item->name, $item->description ?: ($item->type === Item::TYPE_ROLE ? 'role' : 'permission'));
        }

        asort($options);

        return $options;
    }

    public function getChildrenNames(string $name, int $type): array
    {
        $item = $type === Item::TYPE_ROLE
            ? Yii::$app->authManager->getRole($name)
            : Yii::$app->authManager->getPermission($name);

        if ($item === null) {
            return [];
        }

        return array_keys(Yii::$app->authManager->getChildren($item->name));
    }

    private function syncChildren(Item $item, array $childrenNames): void
    {
        $auth = Yii::$app->authManager;
        $auth->removeChildren($item);

        foreach ($childrenNames as $childName) {
            $child = $auth->getRole($childName) ?? $auth->getPermission($childName);
            if ($child !== null && !$auth->hasChild($item, $child)) {
                $auth->addChild($item, $child);
            }
        }
    }
}
