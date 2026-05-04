<?php
/** @var yii\web\View $this */
/** @var \larikmc\admin\auth\models\LoginForm $model */
/** @var int $remaining */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\captcha\Captcha;
use larikmc\admin\auth\models\LoginForm;

$this->title = 'Вход в систему';

$blocked = !empty($remaining) && $remaining > 0;
?>

<div class="registration">
    <button type="button" class="theme-toggle" data-auth-theme-toggle aria-pressed="false">
        <span class="theme-toggle__knob"></span>
        <span class="theme-toggle__label" data-auth-theme-label>Light</span>
    </button>
    <h1 class="mb-4"><?= Html::encode($this->title) ?></h1>

    <?php foreach (Yii::$app->session->getAllFlashes() as $type => $msg): ?>
        <?php if (strpos((string)$type, 'auth.') === 0): ?>
            <?php continue; ?>
        <?php endif; ?>
        <?php $alertClass = $type === 'error' ? 'alert-danger' : 'alert-warning'; ?>
        <div class="alert <?= Html::encode($alertClass) ?>">
            <?= Html::encode($msg) ?>
        </div>
    <?php endforeach; ?>

    <?php if ($blocked): ?>
        <div class="alert alert-warning text-center"
             id="lock-timer">
            Попытки входа временно ограничены. Повторите попытку через
            <span id="timer"><?= gmdate('i:s', (int)$remaining) ?></span>
        </div>
    <?php endif; ?>

    <?php if (!$blocked): ?>

        <div class="registration__form-wrap">
        <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

        <?= $form->field($model, 'email')
                ->textInput([
                        'class' => 'form-control form-control-lg',
                        'autofocus' => true,
                        'autocomplete' => 'username',
                ]) ?>

        <?= $form->field($model, 'password')
                ->passwordInput([
                        'class' => 'form-control form-control-lg',
                        'autocomplete' => 'current-password',
                ]) ?>

        <?php
        echo Html::textInput('login_check', '', [
                'class' => 'bot-trap',
                'autocomplete' => 'new-password',
                'tabindex' => '-1',
                'aria-hidden' => 'true',
        ]);
        $this->registerCss('.bot-trap{position:absolute;left:-9999px;top:-9999px;height:1px;width:1px;opacity:0;}');
        ?>

        <?php if ($model->scenario === 'withCaptcha'): ?>
            <?= $form->field($model, 'verifyCode')->widget(Captcha::class, [
                    'captchaAction' => [LoginForm::CAPTCHA_ROUTE],
                    'template' => '<div class="auth-captcha-row"><div class="auth-captcha-image">{image}</div><div class="auth-captcha-input">{input}</div></div>',
                    'options' => [
                            'class' => 'form-control form-control-lg',
                            'placeholder' => 'Введите код с картинки',
                            'autocomplete' => 'off',
                    ],
                    'imageOptions' => [
                            'alt' => 'CAPTCHA',
                            'title' => 'Обновить изображение',
                            'style' => 'cursor:pointer;',
                    ],
            ]) ?>
        <?php endif; ?>

        <div class="form-group mt-3">
            <?= Html::submitButton('Войти', [
                    'class' => 'btn auth-submit btn-lg w-100',
                    'name'  => 'login-button',
            ]) ?>
        </div>

        <div class="text-center mt-3">
            <?= Html::a('Забыли пароль?', ['/auth/request-password-reset'], ['class' => 'text-decoration-none']) ?>
        </div>

        <?php ActiveForm::end(); ?>
        </div>

    <?php endif; ?>
</div>

<?php if ($blocked): ?>
    <?php
    $remaining = (int)$remaining;
    $this->registerJs(<<<JS
let seconds = {$remaining};
const timerEl = document.getElementById('timer');

function updateTimer() {
    if (seconds <= 0) {
        location.reload();
        return;
    }

    seconds--;

    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;

    if (seconds < 60) {
        timerEl.style.color = '#d9534f';
        timerEl.style.fontWeight = 'bold';
    }

    timerEl.textContent =
        (mins < 10 ? '0' + mins : mins) + ':' +
        (secs < 10 ? '0' + secs : secs);

    setTimeout(updateTimer, 1000);
}

updateTimer();
JS);
    ?>
<?php endif; ?>
