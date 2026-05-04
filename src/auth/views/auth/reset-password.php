<?php
/** @var yii\web\View $this */
/** @var \larikmc\admin\auth\models\ResetPasswordForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Новый пароль';
?>

<div class="registration">
    <button type="button" class="theme-toggle" data-auth-theme-toggle aria-pressed="false">
        <span class="theme-toggle__knob"></span>
        <span class="theme-toggle__label" data-auth-theme-label>Light</span>
    </button>
    <h1 class="mb-4"><?= Html::encode($this->title) ?></h1>

    <div class="registration__form-wrap">
        <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>

        <?= $form->field($model, 'password')->passwordInput([
            'class' => 'form-control form-control-lg',
            'autofill' => 'new-password',
            'autocomplete' => 'new-password',
        ]) ?>

        <div class="form-group mt-3">
            <?= Html::submitButton('Сохранить пароль', [
                'class' => 'btn auth-submit btn-lg w-100',
            ]) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
