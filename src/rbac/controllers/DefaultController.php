<?php

namespace larikmc\admin\rbac\controllers;

use Yii;

class DefaultController extends BaseController
{
    public function actionIndex()
    {
        $auth = Yii::$app->authManager;
        $db = Yii::$app->db;
        $userClass = $this->module->getConfig()->userModel;

        return $this->render('index', [
            'report' => [
                'counts' => [
                    'roles' => count($auth->getRoles()),
                    'permissions' => count($auth->getPermissions()),
                    'assignments' => (int) $db->createCommand('SELECT COUNT(*) FROM ' . $auth->assignmentTable)->queryScalar(),
                    'users' => (int) $userClass::find()->count(),
                ],
            ],
        ]);
    }
}
