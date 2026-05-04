<?php

namespace larikmc\admin\rbac\controllers;

use larikmc\admin\rbac\forms\PermissionForm;
use larikmc\admin\rbac\search\PermissionSearch;
use larikmc\admin\rbac\services\RbacService;
use Throwable;
use Yii;
use yii\web\NotFoundHttpException;

class PermissionController extends BaseController
{
    public function actionIndex()
    {
        $searchModel = new PermissionSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', compact('searchModel', 'dataProvider'));
    }

    public function actionCreate()
    {
        $form = new PermissionForm();
        $service = new RbacService();

        if ($form->load($this->request->post()) && $form->validate()) {
            try {
                $service->createPermission($form);
                $this->success('Действие создано.');
                return $this->redirect(['index']);
            } catch (Throwable $e) {
                Yii::error($e);
                $form->addError('name', $e->getMessage());
            }
        }

        return $this->render('create', [
            'model' => $form,
            'childOptions' => $service->getChildOptions($form->type),
        ]);
    }

    public function actionView($name)
    {
        $permission = Yii::$app->authManager->getPermission($name);
        if ($permission === null) {
            throw new NotFoundHttpException('Действие не найдено.');
        }

        $service = new RbacService();

        return $this->render('view', [
            'permission' => $permission,
            'roles' => $service->getRolesContainingPermission($permission->name),
        ]);
    }

    public function actionUpdate($name)
    {
        $service = new RbacService();
        $permission = Yii::$app->authManager->getPermission($name);
        if ($permission === null) {
            throw new NotFoundHttpException('Действие не найдено.');
        }

        $form = new PermissionForm([
            'name' => $permission->name,
            'description' => $permission->description,
            'ruleName' => $permission->ruleName,
            'children' => $service->getChildrenNames($name, $permission->type),
        ]);

        if ($form->load($this->request->post()) && $form->validate()) {
            try {
                $service->updatePermission($name, $form);
                $this->success('Действие обновлено.');
                return $this->redirect(['index']);
            } catch (Throwable $e) {
                Yii::error($e);
                $form->addError('name', $e->getMessage());
            }
        }

        return $this->render('update', [
            'model' => $form,
            'originalName' => $name,
            'childOptions' => $service->getChildOptions($form->type, $name),
        ]);
    }

    public function actionDelete($name)
    {
        try {
            (new RbacService())->deletePermission($name);
            $this->success('Действие удалено.');
        } catch (Throwable $e) {
            Yii::error($e);
            $this->error($e->getMessage());
        }

        return $this->redirect(['index']);
    }
}
