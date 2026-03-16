<?php

use larikmc\admin\widgets\AdminPage;
use larikmc\admin\rbac\helpers\UserModelHelper;
use yii\bootstrap5\Html;
use yii\widgets\DetailView;

$config = $module->getConfig();
$this->title = 'Пользователь: ' . UserModelHelper::label($user, $module);

ob_start();
echo DetailView::widget([
    'model' => $user,
    'attributes' => [
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
            'attribute' => $config->statusField,
            'label' => 'Статус',
        ],
    ],
]);
?>
<h2 class="mt-4">Назначенные роли</h2>
<?php if ($assignedItems === []): ?>
    <div class="alert alert-secondary">Роли не назначены.</div>
<?php else: ?>
    <ul class="list-group">
        <?php foreach ($assignedItems as $item): ?>
            <li class="list-group-item"><?= Html::encode($item) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
<?php
$content = ob_get_clean();

echo AdminPage::widget([
    'title' => $this->title,
    'tabs' => $this->params['rbacTabs'] ?? [],
    'showHeader' => false,
    'actions' => [
        Html::a('Управлять назначениями', ['update', 'id' => $user->{$config->userIdField}], ['class' => 'btn btn-primary']),
    ],
    'content' => $content,
]);
