<?php

namespace larikmc\admin\rbac\helpers;

use larikmc\admin\rbac\Module;
use yii\bootstrap5\Html;
use yii\db\ActiveRecord;

class UserModelHelper
{
    public static function instantiate(Module $module): ActiveRecord
    {
        $class = $module->getConfig()->userModel;

        return new $class();
    }

    public static function label(ActiveRecord $user, Module $module): string
    {
        $config = $module->getConfig();
        $parts = [];

        foreach ([$config->usernameField, $config->emailField] as $field) {
            if ($field && isset($user->{$field}) && $user->{$field} !== null && $user->{$field} !== '') {
                $parts[] = (string) $user->{$field};
            }
        }

        return $parts !== [] ? implode(' / ', array_unique($parts)) : '#' . $user->{$config->userIdField};
    }

    public static function statusBadge(ActiveRecord $user, Module $module): string
    {
        $config = $module->getConfig();
        $raw = $user->{$config->statusField} ?? null;
        $value = is_scalar($raw) ? (string) $raw : 'unknown';
        $normalized = mb_strtolower(trim($value));

        $class = match (true) {
            $normalized === '1',
            $normalized === 'active',
            $normalized === 'активен',
            $normalized === 'активный',
            $normalized === 'enabled' => 'success',
            $normalized === '0',
            $normalized === 'inactive',
            $normalized === 'disabled',
            $normalized === 'неактивен' => 'secondary',
            $normalized === '-1',
            $normalized === 'deleted',
            $normalized === 'удален',
            $normalized === 'удалён' => 'warning',
            $normalized === 'blocked',
            $normalized === 'banned',
            $normalized === 'ban',
            $normalized === 'заблокирован' => 'danger',
            default => 'light',
        };

        return Html::tag('span', Html::encode($value), ['class' => 'badge text-bg-' . $class]);
    }
}
