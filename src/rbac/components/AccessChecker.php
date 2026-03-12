<?php

namespace larikmc\admin\rbac\components;

use larikmc\admin\rbac\Module;
use Yii;
use yii\web\ForbiddenHttpException;

class AccessChecker
{
    public function check(Module $module): void
    {
        $user = Yii::$app->user;
        if ($user->isGuest) {
            $user->loginRequired();
            Yii::$app->end();
        }

        $roles = $module->getConfig()->accessRoles;
        if ($roles === []) {
            return;
        }

        foreach ($roles as $role) {
            if ($user->can($role)) {
                return;
            }
        }

        throw new ForbiddenHttpException('У вас нет доступа к модулю RBAC.');
    }
}
