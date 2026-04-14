<?php

use larikmc\admin\widgets\AdminPage;
use yii\bootstrap5\Html;
use yii\grid\ActionColumn;
use yii\grid\GridView;

$this->title = 'Действия';
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
    'actions' => [
        Html::a('Создать действие', ['create'], ['class' => 'btn btn-success']),
    ],
    'actionsPosition' => 'below_tabs',
    'showHeader' => false,
    'content' => GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'name',
                'label' => 'Имя',
                'format' => 'raw',
                'value' => static fn($model) => '<div class="sz-grid-meta"><strong>' . Html::encode($model['name']) . '</strong>'
                    . ($model['description'] ? '<span>' . Html::encode($model['description']) . '</span>' : '')
                    . '</div>',
                'filterInputOptions' => [
                    'class' => 'form-control',
                    'placeholder' => 'Поиск по имени',
                ],
            ],
            [
                'attribute' => 'description',
                'format' => 'ntext',
                'label' => 'Описание',
                'filterInputOptions' => [
                    'class' => 'form-control',
                    'placeholder' => 'Поиск по описанию',
                ],
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{view}{update}{delete}',
                'contentOptions' => ['class' => 'action-column'],
                'buttonOptions' => ['class' => 'sz-row-action'],
                'urlCreator' => static fn($action, $model) => [$action, 'name' => $model['name']],
                'buttons' => [
                    'view' => static fn($url) => Html::a('<span class="material-symbols-rounded">visibility</span>', $url, ['class' => 'sz-row-action', 'title' => 'Просмотр', 'aria-label' => 'Просмотр']),
                    'update' => static fn($url) => Html::a('<span class="material-symbols-rounded">edit</span>', $url, ['class' => 'sz-row-action', 'title' => 'Редактировать', 'aria-label' => 'Редактировать']),
                    'delete' => static fn($url) => Html::a('<span class="material-symbols-rounded">delete</span>', $url, [
                        'class' => 'sz-row-action',
                        'title' => 'Удалить',
                        'aria-label' => 'Удалить',
                        'data-confirm' => 'Удалить действие?',
                        'data-method' => 'post',
                    ]),
                ],
            ],
        ],
    ]),
]);
