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
            return;
        }

        if (!$app instanceof \yii\web\Application) {
            return;
        }

        $adminModuleId = $this->findAdminModuleId($app);
        if ($adminModuleId === null) {
            // The current application (e.g. app-frontend) does not have the admin module registered.
            // Do not inject admin rules or modify errorAction!
            return;
        }

        if ($app->has('urlManager')) {
            $app->getUrlManager()->addRules(Module::getDefaultUrlRules($adminModuleId), false);
        }

        if ($app->has('errorHandler')) {
            $errorHandler = $app->getErrorHandler();
            if ($errorHandler->errorAction === 'site/error' || $errorHandler->errorAction === null) {
                $errorHandler->errorAction = $adminModuleId . '/site/error';
            }
        }
    }

    /**
     * Resolves the module ID for larikmc\admin\Module if registered in the application.
     */
    protected function findAdminModuleId(\yii\web\Application $app): ?string
    {
        foreach ($app->getModules() as $id => $module) {
            if ($module instanceof Module) {
                return (string) $id;
            }
            if (is_array($module) && isset($module['class']) && is_a($module['class'], Module::class, true)) {
                return (string) $id;
            }
            if (is_string($module) && is_a($module, Module::class, true)) {
                return (string) $id;
            }
        }

        return null;
    }
}
