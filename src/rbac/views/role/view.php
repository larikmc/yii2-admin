<?php

use larikmc\admin\widgets\AdminPage;
use larikmc\admin\rbac\helpers\UserModelHelper;
use yii\bootstrap5\Html;
use yii\widgets\DetailView;

$this->title = 'Роль: ' . $role->name;
$this->params['breadcrumbs'] = [
    ['label' => 'Администрирование'],
    ['label' => 'RBAC', 'url' => ['/rbac/default/index']],
    ['label' => 'Роли', 'url' => ['/rbac/role/index']],
    ['label' => $this->title],
];

ob_start();
echo DetailView::widget([
    'model' => $role,
    'attributes' => [
        ['label' => 'Имя', 'value' => $role->name],
        ['label' => 'Описание', 'value' => $role->description ?: 'Не указано'],
        ['label' => 'Правило', 'value' => $role->ruleName ?: 'Не используется'],
    ],
]);
?>
<div class="sz-related-block">
    <h2 class="sz-related-block__title">Включённые действия</h2>
    <?php if ($children === []): ?>
        <p class="text-muted mb-0">У роли пока нет привязанных действий.</p>
    <?php else: ?>
        <div class="sz-related-chips">
            <?php foreach ($children as $child): ?>
                <span class="badge text-bg-light"><?= Html::encode($child) ?></span>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<div class="sz-related-block">
    <h2 class="sz-related-block__title">Пользователи с этой ролью</h2>
    <?php if ($users === []): ?>
        <p class="text-muted mb-0">Роль пока никому не назначена.</p>
    <?php else: ?>
        <div class="sz-related-list">
            <?php foreach ($users as $user): ?>
                <div class="sz-related-list__item">
                    <div class="sz-related-list__main">
                        <strong><?= Html::encode(UserModelHelper::label($user, $module)) ?></strong>
                        <span><?= UserModelHelper::statusBadge($user, $module) ?></span>
                    </div>
                    <?= Html::a('Открыть', ['/rbac/user/view', 'id' => $user->{$module->getConfig()->userIdField}], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();

echo AdminPage::widget([
    'title' => $this->title,
    'tabs' => $this->params['rbacTabs'] ?? [],
    'showHeader' => false,
    'actions' => [
        Html::a('Редактировать', ['update', 'name' => $role->name], ['class' => 'btn btn-primary']),
    ],
    'content' => $content,
]);
