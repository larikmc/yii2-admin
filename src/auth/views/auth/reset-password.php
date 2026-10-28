<?php
/** @var yii\web\View $this */
/** @var \larikmc\admin\auth\models\ResetPasswordForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Новый пароль';
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
    <p class="auth-card__subtitle">Придумайте новый надежный пароль для вашей учетной записи</p>

    <div class="registration__form-wrap">
        <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>

        <?= $form->field($model, 'password', [
            'template' => "{label}\n<div class=\"auth-input-group auth-password-wrap\">{input}<button type=\"button\" class=\"auth-password-toggle\" data-password-toggle tabindex=\"-1\" aria-label=\"Показать пароль\" title=\"Показать пароль\"><span class=\"material-symbols-rounded\">visibility</span></button></div>\n{error}",
        ])->passwordInput([
            'class' => 'form-control',
            'autofocus' => true,
            'autocomplete' => 'new-password',
            'placeholder' => '••••••••',
        ]) ?>

        <div class="form-group mt-3">
            <?= Html::submitButton('Сохранить новый пароль', [
                'class' => 'btn auth-submit w-100',
            ]) ?>
        </div>

        <div class="auth-links text-center mt-3">
            <?= Html::a('← Вернуться ко входу', ['/login'], ['class' => 'auth-link']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
