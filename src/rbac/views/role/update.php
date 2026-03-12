<?php

use larikmc\admin\widgets\AdminPage;

$this->title = 'Редактирование роли: ' . $originalName;

echo AdminPage::widget([
    'title' => $this->title,
    'tabs' => $this->params['rbacTabs'] ?? [],
    'content' => $this->render('_form', ['model' => $model, 'childOptions' => $childOptions]),
]);
