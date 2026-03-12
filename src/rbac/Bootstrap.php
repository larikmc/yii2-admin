<?php

namespace larikmc\admin\rbac;

use yii\base\Application;
use yii\base\BootstrapInterface;

class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app): void
    {
        if (!$app instanceof Application) {
            return;
        }

        if (!isset($app->modules['rbac'])) {
            return;
        }

        $module = $app->getModule('rbac');
        if ($module instanceof Module && $module->enablePrettyUrlRules) {
            $app->getUrlManager()->addRules(require __DIR__ . '/config/routes.php', false);
        }
    }
}
