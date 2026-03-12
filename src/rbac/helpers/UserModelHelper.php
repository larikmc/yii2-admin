<?php

namespace larikmc\admin\rbac\helpers;

use larikmc\admin\rbac\Module;
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
}
