<?php

use larikmc\admin\widgets\AdminPage;
use larikmc\admin\rbac\helpers\UserModelHelper;
use larikmc\admin\rbac\services\AssignmentService;
use yii\bootstrap5\Html;
use yii\grid\ActionColumn;
use yii\grid\GridView;

$config = $module->getConfig();
$assignmentService = new AssignmentService();
$this->title = 'Пользователи';
$this->params['breadcrumbs'] = [
    ['label' => 'Администрирование'],
    ['label' => 'RBAC', 'url' => ['/rbac/default/index']],
    ['label' => $this->title],
];
?>
<?php
echo AdminPage::widget([
    'title' => $this->title,
    'tabs' => $this->params['rbacTabs'] ?? [],
    'showHeader' => false,
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
                'contentOptions' => ['class' => 'action-column'],
                'buttonOptions' => ['class' => 'sz-row-action'],
                'urlCreator' => static fn($action, $model) => [$action, 'id' => $model->{$config->userIdField}],
                'buttons' => [
                    'view' => static fn($url) => Html::a('<span class="material-symbols-rounded">visibility</span>', $url, ['class' => 'sz-row-action', 'title' => 'Просмотр', 'aria-label' => 'Просмотр']),
                    'update' => static fn($url) => Html::a('<span class="material-symbols-rounded">edit</span>', $url, ['class' => 'sz-row-action', 'title' => 'Редактировать', 'aria-label' => 'Редактировать']),
                ],
            ],
        ],
    ]),
]);
