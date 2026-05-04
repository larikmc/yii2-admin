<?php

namespace larikmc\admin\rbac\controllers;

use larikmc\admin\rbac\helpers\SystemRbacHelper;
use larikmc\admin\rbac\services\AdminInviteService;
use Yii;
use yii\helpers\Url;
use yii\web\ForbiddenHttpException;

class InviteController extends BaseController
{
    private const INVITE_SESSION_KEY = 'adminInviteData';

    public function actionIndex()
    {
        $identity = Yii::$app->user->identity;
        if ($identity === null || (string) $identity->getId() !== SystemRbacHelper::ROOT_USER_ID) {
            throw new ForbiddenHttpException('Генерация ссылок приглашения доступна только главному администратору.');
        }

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
