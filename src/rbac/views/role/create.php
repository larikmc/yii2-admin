<?php

use larikmc\admin\widgets\AdminPage;

$this->title = 'Создать роль';

echo AdminPage::widget([
    'title' => $this->title,
    'tabs' => $this->params['rbacTabs'] ?? [],
    'showHeader' => false,
    'content' => $this->render('_form', ['model' => $model, 'childOptions' => $childOptions]),
]);
