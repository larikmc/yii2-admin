<?php

use larikmc\admin\widgets\AdminPage;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Назначения: ' . $userLabel;
$this->params['breadcrumbs'] = [
    ['label' => 'Администрирование'],
    ['label' => 'RBAC', 'url' => ['/rbac/default/index']],
    ['label' => 'Пользователи', 'url' => ['/rbac/user/index']],
    ['label' => $this->title],
];

ob_start();
?>
<p class="text-muted">Назначьте выбранному пользователю только роли. Действия выдаются через состав роли.</p>
<?php if ((string) $user->{$module->getConfig()->userIdField} === '1'): ?>
    <div class="alert alert-warning">Пользователь с ID=1 является системным администратором. Роль `admin` всегда остается назначенной.</div>
<?php endif; ?>
<?php $form = ActiveForm::begin(); ?>
<?= $form->field($formModel, 'items')->checkboxList($itemOptions) ?>
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
