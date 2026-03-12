<?php

use larikmc\admin\widgets\AdminPage;
use yii\bootstrap5\Html;
use yii\grid\ActionColumn;
use yii\grid\GridView;

$config = $module->getConfig();
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
        'columns' => [
            [
                'attribute' => $config->userIdField,
                'label' => 'ID',
            ],
            [
                'attribute' => $config->usernameField,
                'label' => 'Логин',
            ],
            [
                'attribute' => $config->emailField,
                'label' => 'Email',
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{update}',
                'urlCreator' => static fn($action, $model) => ['/rbac/user/update', 'id' => $model->{$config->userIdField}],
                'buttons' => [
                    'update' => static fn($url) => Html::a('Открыть', $url, ['class' => 'btn btn-sm btn-primary']),
                ],
            ],
        ],
    ]),
]);
