<?php

namespace larikmc\admin\rbac\controllers;

use larikmc\admin\rbac\search\UserSearch;

class AssignmentController extends BaseController
{
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search($this->request->queryParams, $this->module, true);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'module' => $this->module,
        ]);
    }
}
