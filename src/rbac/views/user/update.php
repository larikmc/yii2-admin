<?php

use larikmc\admin\widgets\AdminPage;
use larikmc\admin\rbac\helpers\SystemRbacHelper;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Назначения: ' . $userLabel;
$this->params['breadcrumbs'] = [
    ['label' => 'Администрирование'],
    ['label' => 'RBAC', 'url' => ['/rbac/default/index']],
    ['label' => 'Пользователи', 'url' => ['/rbac/user/index']],
    ['label' => $this->title],
];

$assignedCount = count($formModel->items);
ob_start();
?>
<div class="sz-assignment-note">
    <p class="text-muted mb-0">Назначьте выбранному пользователю только роли. Действия выдаются через состав роли.</p>
</div>
<?php if ((string) $user->{$module->getConfig()->userIdField} === '1'): ?>
    <div class="sz-assignment-warning">
        <span class="material-symbols-rounded">shield</span>
        <div>
            <strong>Системный администратор</strong>
            <p class="mb-0">Пользователь с ID=1 является системным администратором. Роль `admin` всегда остается назначенной.</p>
        </div>
    </div>
<?php endif; ?>
<?php $form = ActiveForm::begin(); ?>
<div class="sz-role-picker" data-sz-role-picker>
    <div class="sz-role-picker__toolbar">
        <div class="sz-role-picker__search">
            <span class="material-symbols-rounded">search</span>
            <input type="search" class="form-control" placeholder="Поиск роли по имени" data-sz-role-search>
        </div>
        <div class="sz-role-picker__meta">
            <span class="badge text-bg-light" data-sz-role-count><?= $assignedCount ?></span>
            <span class="sz-role-picker__meta-label">выбрано</span>
        </div>
    </div>

    <?= $form->field($formModel, 'items', ['options' => ['class' => 'mb-0']])->checkboxList(
        $itemOptions,
        [
            'class' => 'sz-role-picker__list',
            'item' => static function ($index, $label, $name, $checked, $value) {
                $isPinnedRole = $value === SystemRbacHelper::ADMIN_ROLE;

                return Html::tag(
                    'label',
                    Html::checkbox($name, $checked, [
                        'value' => $value,
                        'class' => 'sz-role-picker__checkbox',
                        'data-sz-role-option' => true,
                    ])
                    . Html::tag(
                        'span',
                        Html::tag('span', Html::encode($label), [
                            'class' => 'sz-role-picker__label',
                            'data-sz-role-label' => true,
                        ])
                        . ($isPinnedRole
                            ? Html::tag('span', 'System', ['class' => 'badge text-bg-light sz-role-picker__badge'])
                            : ''),
                        ['class' => 'sz-role-picker__content']
                    ),
                    [
                        'class' => 'sz-role-picker__item' . ($isPinnedRole ? ' sz-role-picker__item--pinned' : ''),
                    ]
                );
            },
        ]
    )->label('Роли') ?>
</div>
<div class="form-group">
    <?= Html::submitButton('Сохранить назначения', ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Назад', ['view', 'id' => $user->{$module->getConfig()->userIdField}], ['class' => 'btn btn-outline-secondary']) ?>
</div>
<?php ActiveForm::end(); ?>
<?php
$content = ob_get_clean();

echo AdminPage::widget([
    'title' => $this->title,
    'tabs' => $this->params['rbacTabs'] ?? [],
    'showHeader' => false,
    'content' => $content,
]);
