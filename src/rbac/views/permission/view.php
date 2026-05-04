<?php

use larikmc\admin\widgets\AdminPage;
use yii\bootstrap5\Html;
use yii\widgets\DetailView;

$this->title = 'Действие: ' . $permission->name;
$this->params['breadcrumbs'] = [
    ['label' => 'Администрирование'],
    ['label' => 'RBAC', 'url' => ['/rbac/default/index']],
    ['label' => 'Действия', 'url' => ['/rbac/permission/index']],
    ['label' => $this->title],
];

ob_start();
echo DetailView::widget([
    'model' => $permission,
    'attributes' => [
        ['label' => 'Имя', 'value' => $permission->name],
        ['label' => 'Описание', 'value' => $permission->description ?: 'Не указано'],
        ['label' => 'Правило', 'value' => $permission->ruleName ?: 'Не используется'],
    ],
]);
?>
<div class="sz-related-block">
    <h2 class="sz-related-block__title">Роли, в которые входит действие</h2>
    <?php if ($roles === []): ?>
        <p class="text-muted mb-0">Действие пока не включено ни в одну роль.</p>
    <?php else: ?>
        <div class="sz-related-list">
            <?php foreach ($roles as $role): ?>
                <div class="sz-related-list__item">
                    <div class="sz-related-list__main">
                        <strong><?= Html::encode($role->name) ?></strong>
                        <?php if ($role->description): ?>
                            <span><?= Html::encode($role->description) ?></span>
                        <?php endif; ?>
                    </div>
                    <?= Html::a('Открыть', ['/rbac/role/view', 'name' => $role->name], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
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
        Html::a('Редактировать', ['update', 'name' => $permission->name], ['class' => 'btn btn-primary']),
    ],
    'content' => $content,
]);
