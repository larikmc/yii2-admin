<?php
/** @var yii\web\View $this */
/** @var \larikmc\admin\auth\models\TwoFactorForm $model */
/** @var \yii\web\IdentityInterface $user */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Двухфакторная аутентификация';
?>

<div class="registration auth-card">
    <div class="auth-brand">
        <div class="auth-brand__logo" style="background: linear-gradient(135deg, #10b981, #059669);">
            <span class="material-symbols-rounded" style="color: #fff; font-size: 28px;">verified_user</span>
        </div>
        <div class="auth-brand__copy">
            <strong>ADMIN PANEL</strong>
            <small>Проверка безопасности</small>
        </div>
    </div>

    <h1 class="auth-card__title"><?= Html::encode($this->title) ?></h1>
    <p class="auth-card__subtitle">
        Введите 6-значный код из Google Authenticator или один из ваших резервных кодов восстановления
    </p>

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
        <?php $form = ActiveForm::begin(['id' => 'two-factor-form']); ?>

        <?= $form->field($model, 'code', [
            'template' => "{label}\n<div class=\"auth-input-group\">{input}</div>\n{error}",
        ])->textInput([
            'class' => 'form-control',
            'autofocus' => true,
            'autocomplete' => 'one-time-code',
            'inputmode' => 'numeric',
            'placeholder' => '123456 или XXXX-XXXX',
            'style' => 'text-align: center; font-size: 20px; letter-spacing: 3px; font-weight: 700;',
        ])->label('Код подтверждения') ?>

        <div class="form-group mt-3">
            <?= Html::submitButton('Подтвердить вход', [
                'class' => 'btn auth-submit w-100',
                'style' => 'background: linear-gradient(135deg, #10b981, #059669); border-color: #059669;',
            ]) ?>
        </div>

        <div class="auth-links text-center mt-3">
            <?= Html::a('← Отмена и возврат ко входу', ['/auth/login'], ['class' => 'auth-link']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
