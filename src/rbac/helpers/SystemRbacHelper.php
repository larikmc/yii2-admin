<?php

namespace larikmc\admin\rbac\helpers;

class SystemRbacHelper
{
    public const DEFAULT_ROOT_USER_ID = '1';
    public const ROOT_USER_ID = '1';
    public const ADMIN_ROLE = 'admin';
    public const ADMIN_ROLE_DESCRIPTION = 'Администратор сайта';
    public const ADMIN_PANEL_PERMISSION = 'adminPanel';
    public const ADMIN_PANEL_PERMISSION_DESCRIPTION = 'Доступ в админ-панель';

    public static function getRootUserId(): ?string
    {
        $adminModule = \Yii::$app->hasModule('admin') ? \Yii::$app->getModule('admin') : null;
        if ($adminModule !== null && property_exists($adminModule, 'rootUserId')) {
            $val = $adminModule->rootUserId;
            return $val !== null && $val !== '' ? (string) $val : null;
        }

        $appVal = \Yii::$app->params['admin.rootUserId'] ?? null;
        if ($appVal !== null && $appVal !== '') {
            return (string) $appVal;
        }

        return null;
    }

    public static function isRootUser(string|int|null $userId): bool
    {
        if ($userId === null || $userId === '') {
            return false;
        }

        $rootId = self::getRootUserId();
        return $rootId !== null && (string) $userId === (string) $rootId;
    }

    public static function isProtectedRole(string $name): bool
    {
        return $name === self::ADMIN_ROLE;
    }

    public static function isProtectedPermission(string $name): bool
    {
        return $name === self::ADMIN_PANEL_PERMISSION;
    }
}
