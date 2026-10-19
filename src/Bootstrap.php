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
        }
    }
}
