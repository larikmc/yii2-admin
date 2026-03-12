<?php

use larikmc\admin\widgets\AdminPage;
use yii\bootstrap5\Html;
use yii\grid\ActionColumn;
use yii\grid\GridView;

$this->title = 'Действия';
?>
<?php
echo AdminPage::widget([
    'title' => $this->title,
    'tabs' => $this->params['rbacTabs'] ?? [],
    'actions' => [
        Html::a('Создать действие', ['create'], ['class' => 'btn btn-success']),
    ],
    'content' => GridView::widget([
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
                'urlCreator' => static fn($action, $model) => [$action, 'name' => $model['name']],
            ],
        ],
    ]),
]);
