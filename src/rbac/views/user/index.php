<?php

use larikmc\admin\widgets\AdminPage;
use larikmc\admin\rbac\helpers\UserModelHelper;
use larikmc\admin\rbac\services\AssignmentService;
use yii\grid\ActionColumn;
use yii\grid\GridView;

$config = $module->getConfig();
$assignmentService = new AssignmentService();
$this->title = 'Пользователи';
?>
<?php
echo AdminPage::widget([
    'title' => $this->title,
    'tabs' => $this->params['rbacTabs'] ?? [],
    'content' => GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => $config->userIdField,
                'label' => 'ID',
            ],
            [
                'attribute' => $config->usernameField,
                'label' => 'Логин',
                'value' => static fn($model) => $model->{$config->usernameField},
            ],
            [
                'attribute' => $config->emailField,
                'label' => 'Email',
                'value' => static fn($model) => $model->{$config->emailField},
            ],
            [
                'attribute' => $config->statusField,
                'label' => 'Статус',
                'value' => static fn($model) => $model->{$config->statusField},
            ],
            [
                'label' => 'Отображение',
                'value' => static fn($model) => UserModelHelper::label($model, $module),
            ],
            [
                'label' => 'Роли',
                'value' => static fn($model) => $assignmentService->getAssignedRolesAsString($model->{$config->userIdField}),
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{view} {update}',
                'urlCreator' => static fn($action, $model) => [$action, 'id' => $model->{$config->userIdField}],
            ],
        ],
    ]),
]);
