<?php

namespace larikmc\admin\rbac\controllers;

use larikmc\admin\rbac\forms\UserAssignmentForm;
use larikmc\admin\rbac\helpers\UserModelHelper;
use larikmc\admin\rbac\search\UserSearch;
use larikmc\admin\rbac\services\AssignmentService;
use yii\web\NotFoundHttpException;

class UserController extends BaseController
{
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search($this->request->queryParams, $this->module);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'module' => $this->module,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'user' => $this->findUser($id),
            'module' => $this->module,
            'assignedItems' => (new AssignmentService())->getAssignedItemNames($id),
        ]);
    }

    public function actionUpdate($id)
    {
        $user = $this->findUser($id);
        $assignmentService = new AssignmentService();
        $form = new UserAssignmentForm([
            'userId' => $id,
            'items' => $assignmentService->getAssignedItemNames($id),
        ]);

        if ($form->load($this->request->post()) && $form->validate()) {
            $assignmentService->assignItems($id, $form->items);
            $this->success('Назначения обновлены.');

            return $this->redirect(['view', 'id' => $id]);
        }

        return $this->render('update', [
            'user' => $user,
            'module' => $this->module,
            'formModel' => $form,
            'itemOptions' => $assignmentService->getAssignableItems(),
            'userLabel' => UserModelHelper::label($user, $this->module),
        ]);
    }

    private function findUser($id)
    {
        $class = $this->module->getConfig()->userModel;
        $field = $this->module->getConfig()->userIdField;
        $user = $class::findOne([$field => $id]);
        if ($user === null) {
            throw new NotFoundHttpException('Пользователь не найден.');
        }

        return $user;
    }
}
