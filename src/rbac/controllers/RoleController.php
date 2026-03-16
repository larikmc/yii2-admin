<?php

namespace larikmc\admin\rbac\controllers;

use larikmc\admin\rbac\forms\RoleForm;
use larikmc\admin\rbac\search\RoleSearch;
use larikmc\admin\rbac\services\RbacService;
use Throwable;
use Yii;
use yii\web\NotFoundHttpException;

class RoleController extends BaseController
{
    public function actionIndex()
    {
        $searchModel = new RoleSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', compact('searchModel', 'dataProvider'));
    }

    public function actionCreate()
    {
        $form = new RoleForm();
        $service = new RbacService();

        if ($form->load($this->request->post()) && $form->validate()) {
            try {
                $service->createRole($form);
                $this->success('Роль создана.');
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
        $role = Yii::$app->authManager->getRole($name);
        if ($role === null) {
            throw new NotFoundHttpException('Роль не найдена.');
        }

        $service = new RbacService();

        return $this->render('view', [
            'role' => $role,
            'module' => $this->module,
            'users' => $service->getUsersAssignedToRole($role->name, $this->module),
            'children' => $service->getChildrenNames($role->name, $role->type),
        ]);
    }

    public function actionUpdate($name)
    {
        $service = new RbacService();
        $role = Yii::$app->authManager->getRole($name);
        if ($role === null) {
            throw new NotFoundHttpException('Роль не найдена.');
        }

        $form = new RoleForm([
            'name' => $role->name,
            'description' => $role->description,
            'ruleName' => $role->ruleName,
            'children' => $service->getChildrenNames($name, $role->type),
        ]);

        if ($form->load($this->request->post()) && $form->validate()) {
            try {
                $service->updateRole($name, $form);
                $this->success('Роль обновлена.');
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
            (new RbacService())->deleteRole($name);
            $this->success('Роль удалена.');
        } catch (Throwable $e) {
            Yii::error($e);
            $this->error($e->getMessage());
        }

        return $this->redirect(['index']);
    }
}
