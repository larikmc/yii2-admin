<?php

namespace larikmc\admin;

use yii\base\Application;
use yii\base\BootstrapInterface;

class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app): void
    {
        if ($app instanceof \yii\console\Application) {
            if (!isset($app->controllerMap['admin-2fa'])) {
                $app->controllerMap['admin-2fa'] = [
                    'class' => \larikmc\admin\commands\TwoFactorController::class,
                ];
            }
        } elseif ($app instanceof \yii\web\Application) {
            if ($app->has('urlManager')) {
                $app->getUrlManager()->addRules([
                    'auth/<action:[\w\-]+>' => 'admin/auth/auth/<action>',
                    'admin/auth/<action:[\w\-]+>' => 'admin/auth/auth/<action>',
                    'admin/login' => 'admin/auth/auth/login',
                    'admin/logout' => 'admin/auth/auth/logout',
                ], false);
            }

            if ($app->has('errorHandler')) {
                $errorHandler = $app->getErrorHandler();
                if ($errorHandler->errorAction === 'site/error' || $errorHandler->errorAction === null) {
                    $errorHandler->errorAction = 'admin/site/error';
                }
            }
        }
    }
}
