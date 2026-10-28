<?php
/** @var yii\web\View $this */
/** @var \larikmc\admin\auth\models\InviteSignupForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Регистрация администратора';
?>

<div class="registration auth-card">
    <div class="auth-brand">
        <div class="auth-brand__logo">A</div>
        <div class="auth-brand__copy">
            <strong>ADMIN PANEL</strong>
            <small>Панель управления</small>
        </div>
    </div>

    <h1 class="auth-card__title"><?= Html::encode($this->title) ?></h1>
    <p class="auth-card__subtitle">Заполните данные для создания учетной записи по приглашению</p>

    <?php foreach (Yii::$app->session->getAllFlashes() as $type => $msg): ?>
        <?php if (strpos((string) $type, 'auth.') === 0): ?>
            <?php continue; ?>
        <?php endif; ?>
        <?php $alertClass = $type === 'error' ? 'alert-danger' : 'alert-warning'; ?>
        <div class="alert <?= Html::encode($alertClass) ?>">
            <span class="material-symbols-rounded"><?= $type === 'error' ? 'error' : 'warning' ?></span>
            <div><?= Html::encode($msg) ?></div>
        </div>
    <?php endforeach; ?>

    <div class="registration__form-wrap">
        <?php $form = ActiveForm::begin(['id' => 'invite-signup-form']); ?>

        <?php if ($model->needsUsername()): ?>
            <?= $form->field($model, 'username')
                ->textInput([
                    'class' => 'form-control',
                    'autofocus' => true,
                    'autocomplete' => 'username',
                    'placeholder' => 'admin_username',
                ]) ?>
        <?php endif; ?>

        <?= $form->field($model, 'email')
            ->textInput([
                'class' => 'form-control',
                'autofocus' => !$model->needsUsername(),
                'autocomplete' => 'email',
                'placeholder' => 'admin@example.com',
            ]) ?>

        <?= $form->field($model, 'password', [
            'template' => "{label}\n<div class=\"auth-input-group auth-password-wrap\">{input}<button type=\"button\" class=\"auth-password-toggle\" data-password-toggle tabindex=\"-1\" aria-label=\"Показать пароль\" title=\"Показать пароль\"><span class=\"material-symbols-rounded\">visibility</span></button></div>\n{error}",
        ])->passwordInput([
            'class' => 'form-control',
            'autocomplete' => 'new-password',
            'placeholder' => '••••••••',
        ]) ?>

        <div class="form-group mt-3">
            <?= Html::submitButton('Создать администратора', [
                'class' => 'btn auth-submit w-100',
            ]) ?>
        </div>

        <div class="auth-links text-center mt-3">
            <?= Html::a('← Вернуться ко входу', ['/login'], ['class' => 'auth-link']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
