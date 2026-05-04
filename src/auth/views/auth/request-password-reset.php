<?php
/** @var yii\web\View $this */
/** @var \larikmc\admin\auth\models\PasswordResetRequestForm $model */
/** @var bool $submitted */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Восстановление пароля';
$this->registerCss(<<<CSS
.auth-success-note {
    display: flex;
    gap: 14px;
    align-items: flex-start;
    margin: 0 0 22px;
    padding: 18px 20px;
    border-radius: 16px;
    border: 1px solid rgba(74, 199, 135, 0.28);
    background: linear-gradient(135deg, rgba(229, 249, 239, 0.92) 0%, rgba(210, 244, 228, 0.88) 100%);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.24);
}

.auth-success-note__icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 42px;
    height: 42px;
    border-radius: 14px;
    background: linear-gradient(135deg, #2ea66f 0%, #4ac787 100%);
    color: #fff;
    font-size: 22px;
    font-weight: 700;
    flex: 0 0 42px;
    box-shadow: 0 10px 24px rgba(55, 166, 110, 0.26);
}

.auth-success-note__title {
    margin: 0 0 4px;
    color: #163726;
    font-size: 18px;
    font-weight: 700;
}

.auth-success-note__text {
    margin: 0;
    color: #416150;
    line-height: 1.5;
}

html[data-theme="dark"] .auth-success-note {
    background: linear-gradient(135deg, rgba(45, 105, 76, 0.55) 0%, rgba(34, 134, 91, 0.45) 100%);
}

html[data-theme="dark"] .auth-success-note__title {
    color: #eef3ff;
}

html[data-theme="dark"] .auth-success-note__text {
    color: #c6d7d1;
}
CSS);
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
        <?php if ($type === 'success'): ?>
            <div class="auth-success-note">
                <div class="auth-success-note__icon">✓</div>
                <div>
                    <div class="auth-success-note__title">Проверьте почту</div>
                    <p class="auth-success-note__text"><?= Html::encode((string) $msg) ?></p>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-danger">
                <?= Html::encode((string) $msg) ?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>

    <?php if (!$submitted): ?>
        <div class="registration__form-wrap">
            <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>

            <?= $form->field($model, 'email')->textInput([
                'class' => 'form-control form-control-lg',
                'autofocus' => true,
                'autocomplete' => 'email',
            ]) ?>

            <div class="form-group mt-3">
                <?= Html::submitButton('Отправить ссылку', [
                    'class' => 'btn auth-submit btn-lg w-100',
                ]) ?>
            </div>

            <div class="text-center mt-3">
                <?= Html::a('Вернуться ко входу', ['/auth/login'], ['class' => 'text-decoration-none']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    <?php else: ?>
        <div class="text-center mt-4">
            <?= Html::a('Вернуться ко входу', ['/auth/login'], ['class' => 'text-decoration-none']) ?>
        </div>
    <?php endif; ?>
</div>
