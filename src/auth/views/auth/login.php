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

<div class="registration auth-card">
    <div class="auth-brand">
        <div class="auth-brand__logo">A</div>
        <div class="auth-brand__copy">
            <strong>ADMIN PANEL</strong>
            <small>Панель управления</small>
        </div>
    </div>

    <?php if ($model->isFirstAdminScenario()): ?>
        <h1 class="auth-card__title">Первый администратор</h1>
        <p class="auth-card__subtitle">В базе данных нет пользователей. Введите email и пароль для создания аккаунта.</p>
    <?php else: ?>
        <h1 class="auth-card__title"><?= Html::encode($this->title) ?></h1>
        <p class="auth-card__subtitle">Введите данные для входа в панель управления</p>
    <?php endif; ?>

    <?php foreach (Yii::$app->session->getAllFlashes() as $type => $msg): ?>
        <?php if (strpos((string)$type, 'auth.') === 0): ?>
            <?php continue; ?>
        <?php endif; ?>
        <?php $alertClass = $type === 'error' ? 'alert-danger' : 'alert-warning'; ?>
        <div class="alert <?= Html::encode($alertClass) ?>">
            <span class="material-symbols-rounded"><?= $type === 'error' ? 'error' : 'warning' ?></span>
            <div><?= Html::encode($msg) ?></div>
        </div>
    <?php endforeach; ?>

    <?php if ($blocked): ?>
        <div class="alert alert-warning text-center" id="lock-timer">
            <span class="material-symbols-rounded" style="font-size:32px;color:#e24e6c;">lock_clock</span>
            <div>Попытки входа временно ограничены. Повторите попытку через:</div>
            <span id="timer"><?= gmdate('i:s', (int)$remaining) ?></span>
        </div>
    <?php endif; ?>

    <?php if (!$blocked): ?>

        <div class="registration__form-wrap">
        <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

        <?= $form->field($model, 'email')
                ->textInput([
                        'class' => 'form-control',
                        'autofocus' => true,
                        'autocomplete' => 'username',
                        'placeholder' => 'admin@example.com',
                ]) ?>

        <?= $form->field($model, 'password', [
            'template' => "{label}\n<div class=\"auth-input-group auth-password-wrap\">{input}<button type=\"button\" class=\"auth-password-toggle\" data-password-toggle tabindex=\"-1\" aria-label=\"Показать пароль\" title=\"Показать пароль\"><span class=\"material-symbols-rounded\">visibility</span></button></div>\n{error}",
        ])->passwordInput([
            'class' => 'form-control',
            'autocomplete' => 'current-password',
            'placeholder' => '••••••••',
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
                    'template' => '<div class="auth-captcha-row"><div class="auth-captcha-image" title="Нажмите, чтобы обновить код">{image}</div><div class="auth-captcha-input">{input}</div></div>',
                    'options' => [
                            'class' => 'form-control',
                            'placeholder' => 'Код с картинки',
                            'autocomplete' => 'off',
                    ],
                    'imageOptions' => [
                            'alt' => 'CAPTCHA',
                            'title' => 'Нажмите, чтобы обновить код',
                    ],
            ]) ?>
        <?php endif; ?>

        <div class="form-group mt-3">
            <?= Html::submitButton($model->isFirstAdminScenario() ? 'Создать администратора и войти' : 'Войти в панель', [
                    'class' => 'btn auth-submit w-100',
                    'name'  => 'login-button',
            ]) ?>
        </div>

        <?php if (!$model->isFirstAdminScenario()): ?>
        <div class="auth-links text-center mt-3">
            <?= Html::a('Забыли пароль?', ['/request-password-reset'], ['class' => 'auth-link']) ?>
        </div>
        <?php endif; ?>

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
        timerEl.style.color = '#e24e6c';
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
