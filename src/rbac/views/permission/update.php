<?php

use larikmc\admin\widgets\AdminPage;

$this->title = 'Редактирование действия: ' . $originalName;

echo AdminPage::widget([
    'title' => $this->title,
    'tabs' => $this->params['rbacTabs'] ?? [],
    'content' => $this->render('_form', ['model' => $model, 'childOptions' => $childOptions]),
]);
