<?php

namespace larikmc\admin\rbac\controllers;

use larikmc\admin\rbac\services\AdminInviteService;
use Yii;
use yii\helpers\Url;

class InviteController extends BaseController
{
    private const INVITE_SESSION_KEY = 'adminInviteData';

    public function actionIndex()
    {
        $service = new AdminInviteService();
        $inviteData = Yii::$app->session->get(self::INVITE_SESSION_KEY);
        if ($inviteData !== null) {
            Yii::$app->session->remove(self::INVITE_SESSION_KEY);
        }

        if ($this->request->isPost) {
            $inviteData = $service->createInvite();
            $inviteData['url'] = Url::to(['/auth/invite', 'token' => $inviteData['token']], true);
            Yii::$app->session->set(self::INVITE_SESSION_KEY, $inviteData);
            $this->success('Одноразовая ссылка для администратора создана.');
            return $this->redirect(['index']);
        }

        return $this->render('index', [
            'inviteData' => $inviteData,
        ]);
    }
}
