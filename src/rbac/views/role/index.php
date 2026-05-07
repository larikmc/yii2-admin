<?php

use larikmc\admin\widgets\AdminPage;
use yii\bootstrap5\Html;
use yii\grid\ActionColumn;
use yii\grid\GridView;

$this->title = 'Роли';
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
    'content' => '<div class="sz-actions-bar"><div class="sz-actions-bar__inner">'
        . Html::a('Создать роль', ['create'], ['class' => 'btn btn-success'])
        . '</div></div>'
        . GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'name',
                'label' => 'Имя',
            ],
            [
                'attribute' => 'description',
                'format' => 'ntext',
                'label' => 'Описание',
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{update} {delete}',
                'contentOptions' => ['class' => 'action-column'],
                'buttonOptions' => ['class' => 'sz-row-action'],
                'urlCreator' => static fn($action, $model) => [$action, 'name' => $model['name']],
                'buttons' => [
                    'update' => static fn($url) => Html::a('<span class="material-symbols-rounded">edit</span>', $url, ['class' => 'sz-row-action', 'title' => 'Редактировать', 'aria-label' => 'Редактировать']),
                    'delete' => static fn($url) => Html::a('<span class="material-symbols-rounded">delete</span>', $url, [
                        'class' => 'sz-row-action',
                        'title' => 'Удалить',
                        'aria-label' => 'Удалить',
                        'data-confirm' => 'Удалить роль?',
                        'data-method' => 'post',
                    ]),
                ],
            ],
        ],
    ]),
]);
