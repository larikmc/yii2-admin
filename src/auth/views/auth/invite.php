<?php
/** @var yii\web\View $this */
/** @var \larikmc\admin\auth\models\InviteSignupForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Регистрация администратора';
?>

<div class="registration">
    <button type="button" class="theme-toggle" data-auth-theme-toggle aria-pressed="false">
        <span class="theme-toggle__knob"></span>
        <span class="theme-toggle__label" data-auth-theme-label>Light</span>
    </button>
    <h1 class="mb-4"><?= Html::encode($this->title) ?></h1>

    <?php foreach (Yii::$app->session->getAllFlashes() as $type => $msg): ?>
        <?php if (strpos((string) $type, 'auth.') === 0): ?>
            <?php continue; ?>
        <?php endif; ?>
        <?php $alertClass = $type === 'error' ? 'alert-danger' : 'alert-warning'; ?>
        <div class="alert <?= Html::encode($alertClass) ?>">
            <?= Html::encode($msg) ?>
        </div>
    <?php endforeach; ?>

    <div class="registration__form-wrap">
        <?php $form = ActiveForm::begin(['id' => 'invite-signup-form']); ?>

        <?php if ($model->needsUsername()): ?>
            <?= $form->field($model, 'username')
                ->textInput([
                    'class' => 'form-control form-control-lg',
                    'autofocus' => true,
                    'autocomplete' => 'username',
                ]) ?>
        <?php endif; ?>

        <?= $form->field($model, 'email')
            ->textInput([
                'class' => 'form-control form-control-lg',
                'autofocus' => !$model->needsUsername(),
                'autocomplete' => 'email',
            ]) ?>

        <?= $form->field($model, 'password')
            ->passwordInput([
                'class' => 'form-control form-control-lg',
                'autocomplete' => 'new-password',
            ]) ?>

        <div class="form-group mt-3">
            <?= Html::submitButton('Создать администратора', [
                'class' => 'btn auth-submit btn-lg w-100',
            ]) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
