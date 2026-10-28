<?php
/** @var yii\web\View $this */
/** @var \larikmc\admin\auth\models\PasswordResetRequestForm $model */
/** @var bool $submitted */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Восстановление пароля';
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
    <p class="auth-card__subtitle">Укажите email, привязанный к аккаунту администратора</p>

    <?php foreach (Yii::$app->session->getAllFlashes() as $type => $msg): ?>
        <?php if (strpos((string) $type, 'auth.') === 0): ?>
            <?php continue; ?>
        <?php endif; ?>
        <?php if ($type === 'success'): ?>
            <div class="auth-success-note">
                <div class="auth-success-note__icon">
                    <span class="material-symbols-rounded">check_circle</span>
                </div>
                <div>
                    <div class="auth-success-note__title">Проверьте почту</div>
                    <p class="auth-success-note__text"><?= Html::encode((string) $msg) ?></p>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-danger">
                <span class="material-symbols-rounded">error</span>
                <div><?= Html::encode((string) $msg) ?></div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>

    <?php if (!$submitted): ?>
        <div class="registration__form-wrap">
            <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>

            <?= $form->field($model, 'email')->textInput([
                'class' => 'form-control',
                'autofocus' => true,
                'autocomplete' => 'email',
                'placeholder' => 'admin@example.com',
            ]) ?>

            <div class="form-group mt-3">
                <?= Html::submitButton('Отправить ссылку для сброса', [
                    'class' => 'btn auth-submit w-100',
                ]) ?>
            </div>

            <div class="auth-links text-center mt-3">
                <?= Html::a('← Вернуться ко входу', ['/login'], ['class' => 'auth-link']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    <?php else: ?>
        <div class="auth-links text-center mt-4">
            <?= Html::a('← Вернуться ко входу', ['/login'], ['class' => 'auth-link']) ?>
        </div>
    <?php endif; ?>
</div>
