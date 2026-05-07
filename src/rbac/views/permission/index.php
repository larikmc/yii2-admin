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
$this->params['topbarActions'] = [
    Html::a('Создать действие', ['create'], ['class' => 'btn btn-success']),
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
                'attribute' => 'name',
                'label' => 'Имя',
                'headerOptions' => ['style' => 'width: 30%;'],
                'contentOptions' => ['style' => 'width: 30%;'],
                'filterInputOptions' => ['class' => 'form-control', 'style' => 'min-width: 140px;'],
            ],
            [
                'attribute' => 'description',
                'format' => 'ntext',
                'label' => 'Описание',
                'headerOptions' => ['style' => 'width: 50%;'],
                'contentOptions' => ['style' => 'width: 50%;'],
                'filterInputOptions' => ['class' => 'form-control', 'style' => 'min-width: 180px;'],
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{update} {delete}',
                'headerOptions' => ['style' => 'width: 84px; min-width: 84px; padding: 8px 6px;'],
                'contentOptions' => ['class' => 'action-column', 'style' => 'width: 84px; min-width: 84px; padding: 8px 6px;'],
                'buttonOptions' => ['class' => 'sz-row-action'],
                'urlCreator' => static fn($action, $model) => [$action, 'name' => $model['name']],
                'buttons' => [
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
