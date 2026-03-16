<?php

use larikmc\admin\widgets\AdminPage;
use larikmc\admin\rbac\helpers\UserModelHelper;
use yii\bootstrap5\Html;
use yii\grid\ActionColumn;
use yii\grid\GridView;

$config = $module->getConfig();
$assignmentService = new \larikmc\admin\rbac\services\AssignmentService();
$this->title = 'Назначения';
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
                'filterAttribute' => 'username',
                'filterInputOptions' => [
                    'class' => 'form-control',
                    'placeholder' => 'Поиск по логину',
                ],
            ],
            [
                'attribute' => $config->emailField,
                'label' => 'Email',
                'filterAttribute' => 'email',
                'filterInputOptions' => [
                    'class' => 'form-control',
                    'placeholder' => 'Поиск по email',
                ],
            ],
            [
                'attribute' => $config->statusField,
                'label' => 'Статус',
                'format' => 'raw',
                'value' => static fn($model) => UserModelHelper::statusBadge($model, $module),
            ],
            [
                'label' => 'Роль',
                'value' => static fn($model) => $assignmentService->getAssignedRolesAsString($model->{$config->userIdField}),
                'filterAttribute' => 'role',
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'role',
                    $searchModel->getRoleOptions(),
                    [
                        'class' => 'form-select',
                        'prompt' => 'Все роли',
                    ]
                ),
            ],
            [
                'attribute' => 'roleCount',
                'label' => 'Кол-во ролей',
                'value' => static fn($model) => $model->roleCount ?? $assignmentService->getAssignedRolesCount($model->{$config->userIdField}),
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{update}',
                'contentOptions' => ['class' => 'action-column'],
                'buttonOptions' => ['class' => 'sz-row-action'],
                'urlCreator' => static fn($action, $model) => ['/rbac/user/update', 'id' => $model->{$config->userIdField}],
                'buttons' => [
                    'update' => static fn($url) => Html::a('<span class="material-symbols-rounded">edit</span>', $url, ['class' => 'sz-row-action', 'title' => 'Изменить назначения', 'aria-label' => 'Изменить назначения']),
                ],
            ],
        ],
    ]),
]);
