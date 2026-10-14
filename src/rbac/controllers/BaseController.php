<?php

namespace larikmc\admin\rbac\controllers;

use larikmc\admin\rbac\components\AccessChecker;
use larikmc\admin\rbac\Module;
use larikmc\admin\rbac\services\InstallService;
use Yii;
use yii\web\Controller;

class BaseController extends Controller
{
    protected function getRbacRoute(string $route): array
    {
        return ['/rbac/' . ltrim($route, '/')];
    }

    protected function getRbacTabs(): array
    {
        $currentRoute = ltrim(Yii::$app->controller->route, '/');
        $items = [
            ['label' => 'Обзор', 'url' => $this->getRbacRoute('default/index'), 'pattern' => 'default'],
            ['label' => 'Пользователи', 'url' => $this->getRbacRoute('user/index'), 'pattern' => 'user'],
            ['label' => 'Роли', 'url' => $this->getRbacRoute('role/index'), 'pattern' => 'role'],
            ['label' => 'Действия', 'url' => $this->getRbacRoute('permission/index'), 'pattern' => 'permission'],
            ['label' => 'Назначения', 'url' => $this->getRbacRoute('assignment/index'), 'pattern' => 'assignment'],
            ['label' => 'Инвайты', 'url' => $this->getRbacRoute('invite/index'), 'pattern' => 'invite'],
        ];

        foreach ($items as &$item) {
            $pattern = $item['pattern'];
            $item['active'] = str_starts_with($currentRoute, 'admin/rbac/' . $pattern . '/')
                || str_starts_with($currentRoute, 'rbac/' . $pattern . '/')
                || $currentRoute === 'admin/rbac/' . $pattern
                || $currentRoute === 'rbac/' . $pattern;
            unset($item['pattern']);
        }
        unset($item);

        return $items;
    }

    public function beforeAction($action): bool
    {
        /** @var Module $module */
        $module = $this->module;

        $installer = new InstallService();
        try {
            if (!$installer->isInstalled()) {
                $installer->installTables();
            }
        } catch (\Throwable $e) {
            Yii::error($e);
        }

        (new AccessChecker())->check($module);

        $this->view->params['rbacTabs'] = $this->getRbacTabs();

        return parent::beforeAction($action);
    }

    protected function success(string $message): void
    {
        Yii::$app->session->setFlash('success', $message);
    }

    protected function error(string $message): void
    {
        Yii::$app->session->setFlash('error', $message);
    }
}
