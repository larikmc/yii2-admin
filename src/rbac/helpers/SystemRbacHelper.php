<?php

namespace larikmc\admin\rbac\helpers;

class SystemRbacHelper
{
    public const ROOT_USER_ID = '1';
    public const ADMIN_ROLE = 'admin';
    public const ADMIN_ROLE_DESCRIPTION = 'Администратор сайта';
    public const ADMIN_PANEL_PERMISSION = 'adminPanel';
    public const ADMIN_PANEL_PERMISSION_DESCRIPTION = 'Доступ в админ-панель';

    public static function isProtectedRole(string $name): bool
    {
        return $name === self::ADMIN_ROLE;
    }

    public static function isProtectedPermission(string $name): bool
    {
        return $name === self::ADMIN_PANEL_PERMISSION;
    }
}
