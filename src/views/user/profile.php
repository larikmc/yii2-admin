<?php

/** @var yii\web\View $this */
/** @var larikmc\admin\models\ProfileForm $model */
/** @var common\models\User $user */
/** @var array|null $pendingEmailChange */
/** @var bool $isTwoFactorEnabled */
/** @var int $backupCodesRemaining */
/** @var array|null $twoFactorSetupData */
/** @var bool $isTwoFactorConfigured */
/** @var array|null $twoFactorKeyInfo */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\Url;

$this->title = 'Настройки профиля';
$this->params['breadcrumbs'] = [
    ['label' => 'Панель управления', 'url' => ['/admin/site/index']],
    ['label' => $this->title],
];

$avatarLetter = mb_strtoupper(mb_substr((string) ($user->username ?? $user->email ?? 'A'), 0, 1));
?>

<div class="profile-page">
    <div class="mb-3">
        <h1 style="font-size: 20px; font-weight: 750; margin: 0 0 4px 0; color: var(--sz-text, #101e47);">
            <?= Html::encode($this->title) ?>
        </h1>
        <p style="font-size: 12.5px; margin: 0; color: var(--sz-muted, #647fa9);">
            Управление личными данными, учетной записью и двухфакторной защитой
        </p>
    </div>

    <!-- Баннер ожидающего подтверждения смены email -->
    <?php if (!empty($pendingEmailChange)): ?>
        <div class="alert alert-warning mb-3" style="border-radius: 12px; padding: 14px 16px;">
            <div style="display: flex; gap: 12px; align-items: flex-start;">
                <span class="material-symbols-rounded" style="color: #d97706; font-size: 22px; flex-shrink: 0; margin-top: 2px;">mark_email_unread</span>
                <div style="flex-grow: 1;">
                    <div style="font-size: 13.5px; font-weight: 750; margin-bottom: 4px;">
                        Ожидает подтверждения смена E-mail
                    </div>
                    <p style="margin: 0 0 10px 0; font-size: 12px; line-height: 1.45;">
                        Запрошен перевод на новый адрес: <strong><?= Html::encode($pendingEmailChange['newEmail']) ?></strong>.<br>
                        Письмо с проверочным кодом отправлено на ваш текущий адрес: <strong><?= Html::encode($user->email) ?></strong>.
                    </p>
                    <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                        <?= Html::beginForm(['/admin/user/verify-email-code'], 'post', ['style' => 'display:inline-flex; gap:6px; align-items:center;']) ?>
                            <input type="text" name="code" class="form-control form-control-sm" placeholder="6-значный код" style="width: 130px; text-align: center; letter-spacing: 2px; font-weight: 700;" maxlength="6" required>
                            <button type="submit" class="btn btn-warning btn-sm">Подтвердить код</button>
                        <?= Html::endForm() ?>

                        <?= Html::beginForm(['/admin/user/resend-email-change'], 'post', ['style' => 'display:inline;']) ?>
                            <button type="submit" class="btn btn-secondary btn-sm">Отправить повторно</button>
                        <?= Html::endForm() ?>

                        <?= Html::beginForm(['/admin/user/cancel-email-change'], 'post', ['style' => 'display:inline;']) ?>
                            <button type="submit" class="btn btn-secondary btn-sm" style="color: #ef4444;" onclick="return confirm('Отменить запрос на смену почты?');">Отменить смену</button>
                        <?= Html::endForm() ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="row g-3 align-items-start">
        <!-- Левая колонка: Карточка профиля -->
        <div class="col-12 col-lg-4 col-xl-3">
            <div class="card">
                <div class="card-body p-3 text-center">
                    <div style="
                        width: 64px;
                        height: 64px;
                        border-radius: 18px;
                        background: linear-gradient(135deg, #3189f7, #1a56c4);
                        color: #ffffff;
                        display: grid;
                        place-items: center;
                        font-size: 26px;
                        font-weight: 800;
                        box-shadow: 0 6px 18px rgba(49, 137, 247, .3);
                        margin: 4px auto 12px auto;
                    ">
                        <?= Html::encode($avatarLetter) ?>
                    </div>

                    <h2 style="font-size: 16px; font-weight: 750; margin: 0 0 2px 0; color: var(--sz-text, #101e47);">
                        <?= Html::encode($user->username ?? 'Пользователь') ?>
                    </h2>
                    <p style="font-size: 12px; margin: 0 0 10px 0; color: var(--sz-muted, #647fa9);">
                        <?= Html::encode($user->email ?? '') ?>
                    </p>

                    <div style="display: flex; gap: 6px; flex-wrap: wrap; justify-content: center; margin-bottom: 14px;">
                        <span class="badge text-bg-primary" style="padding: 4px 8px; font-size: 10.5px; font-weight: 600;">Администратор</span>
                        <span class="badge text-bg-success" style="padding: 4px 8px; font-size: 10.5px; font-weight: 600;">Активен</span>
                        <?php if ($isTwoFactorEnabled): ?>
                            <span class="badge text-bg-info" style="padding: 4px 8px; font-size: 10.5px; font-weight: 600;">2FA активен</span>
                        <?php endif; ?>
                    </div>

                    <div style="
                        border-top: 1px solid var(--sz-line, #e7edfb);
                        padding-top: 10px;
                        display: flex;
                        flex-direction: column;
                        gap: 8px;
                        text-align: left;
                        font-size: 12px;
                        margin-bottom: 12px;
                    ">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: var(--sz-muted, #647fa9);">ID аккаунта:</span>
                            <strong>#<?= (int) ($user->id ?? 0) ?></strong>
                        </div>
                        <?php if (!empty($user->created_at)): ?>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="color: var(--sz-muted, #647fa9);">Регистрация:</span>
                                <strong><?= Yii::$app->formatter->asDatetime($user->created_at, 'php:d.m.Y H:i') ?></strong>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($user->updated_at)): ?>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="color: var(--sz-muted, #647fa9);">Обновлен:</span>
                                <strong><?= Yii::$app->formatter->asRelativeTime($user->updated_at) ?></strong>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div style="
                        background: var(--sz-bg-soft, #f4f8ff);
                        border: 1px solid var(--sz-line, #e7edfb);
                        border-radius: 10px;
                        padding: 10px;
                        text-align: left;
                        display: flex;
                        gap: 8px;
                        align-items: flex-start;
                    ">
                        <span class="material-symbols-rounded" style="color: #3189f7; font-size: 16px; margin-top: 1px;">shield</span>
                        <div style="font-size: 11px; line-height: 1.4; color: var(--sz-muted, #647fa9);">
                            Для защиты аккаунта смена почты всегда требует подтверждения по текущему адресу.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Правая колонка: Карточки данных и 2FA -->
        <div class="col-12 col-lg-8 col-xl-9 d-flex flex-column gap-3">
            <!-- Карточка 1: Основные данные и пароль -->
            <div class="card">
                <div class="card-header py-2 px-3">
                    <h3 class="card-title" style="font-size: 15px; margin: 0; font-weight: 750;">Учетные данные</h3>
                    <p class="card-subtitle" style="font-size: 11.5px; margin: 2px 0 0 0; color: var(--sz-muted, #647fa9);">
                        Логин, контактный E-mail и параметры входа
                    </p>
                </div>
                <div class="card-body p-3">
                    <?php $form = ActiveForm::begin([
                        'id' => 'profile-form',
                        'enableClientValidation' => true,
                    ]); ?>

                    <!-- Блок: Основная информация -->
                    <div class="row g-2 mb-3">
                        <?php if ($user->hasProperty('username') || $user->canGetProperty('username')): ?>
                            <div class="col-12 col-md-6">
                                <?= $form->field($model, 'username', [
                                    'options' => ['class' => 'm-0'],
                                    'labelOptions' => ['class' => 'form-label mb-1', 'style' => 'font-size: 12.5px; font-weight: 600;'],
                                    'inputOptions' => ['class' => 'form-control', 'placeholder' => 'Логин'],
                                ]) ?>
                            </div>
                            <div class="col-12 col-md-6">
                        <?php else: ?>
                            <div class="col-12">
                        <?php endif; ?>
                            <?= $form->field($model, 'email', [
                                'options' => ['class' => 'm-0'],
                                'labelOptions' => ['class' => 'form-label mb-1', 'style' => 'font-size: 12.5px; font-weight: 600;'],
                                'inputOptions' => ['class' => 'form-control', 'type' => 'email', 'placeholder' => 'example@domain.com'],
                                'hintOptions' => ['class' => 'form-text mt-1', 'style' => 'font-size: 11px; color: var(--sz-muted, #647fa9);'],
                            ])->hint('Смена адреса потребует подтверждения по текущей почте') ?>
                        </div>
                    </div>

                    <!-- Разделитель -->
                    <div style="border-top: 1px solid var(--sz-line, #e7edfb); margin: 12px 0 14px 0;"></div>

                    <!-- Блок: Смена пароля -->
                    <div>
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                            <div style="display: flex; align-items: center; gap: 6px;">
                                <span class="material-symbols-rounded" style="color: #d13665; font-size: 18px;">lock_reset</span>
                                <span style="font-size: 13px; font-weight: 750; color: var(--sz-text, #101e47);">Смена пароля</span>
                            </div>
                            <span style="font-size: 11px; color: var(--sz-muted, #647fa9);">Заполняйте только при смене</span>
                        </div>

                        <div class="row g-2">
                            <div class="col-12 col-md-4">
                                <?= $form->field($model, 'currentPassword', [
                                    'options' => ['class' => 'm-0'],
                                    'labelOptions' => ['class' => 'form-label mb-1', 'style' => 'font-size: 12px; font-weight: 600;'],
                                    'inputOptions' => ['class' => 'form-control', 'type' => 'password', 'placeholder' => 'Текущий пароль'],
                                ]) ?>
                            </div>
                            <div class="col-12 col-md-4">
                                <?= $form->field($model, 'newPassword', [
                                    'options' => ['class' => 'm-0'],
                                    'labelOptions' => ['class' => 'form-label mb-1', 'style' => 'font-size: 12px; font-weight: 600;'],
                                    'inputOptions' => ['class' => 'form-control', 'type' => 'password', 'placeholder' => 'Не менее 6 символов'],
                                ]) ?>
                            </div>
                            <div class="col-12 col-md-4">
                                <?= $form->field($model, 'confirmPassword', [
                                    'options' => ['class' => 'm-0'],
                                    'labelOptions' => ['class' => 'form-label mb-1', 'style' => 'font-size: 12px; font-weight: 600;'],
                                    'inputOptions' => ['class' => 'form-control', 'type' => 'password', 'placeholder' => 'Повтор нового'],
                                ]) ?>
                            </div>
                        </div>
                    </div>

                    <!-- Кнопки управления формой -->
                    <div style="display: flex; gap: 8px; align-items: center; margin-top: 16px; padding-top: 12px; border-top: 1px solid var(--sz-line, #e7edfb);">
                        <button type="submit" class="btn btn-primary" style="font-size: 13px; padding: 7px 18px;">
                            <span class="material-symbols-rounded" style="font-size: 17px; vertical-align: -3px; margin-right: 4px;">save</span>
                            Сохранить изменения
                        </button>
                        <a href="<?= Url::to(['/admin/site/index']) ?>" class="btn btn-secondary" style="font-size: 13px; padding: 7px 14px;">
                            Отмена
                        </a>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>

            <!-- Карточка 2: Двухфакторная аутентификация (2FA) -->
            <div class="card">
                <div class="card-header py-2 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <span class="material-symbols-rounded" style="color: #10b981; font-size: 18px;">shield</span>
                            <h3 class="card-title" style="font-size: 15px; margin: 0; font-weight: 750;">Двухфакторная защита (2FA)</h3>
                        </div>
                        <p class="card-subtitle" style="font-size: 11.5px; margin: 2px 0 0 0; color: var(--sz-muted, #647fa9);">
                            Защита с помощью TOTP-приложений (Google Authenticator, Яндекс Ключ, 1Password)
                        </p>
                    </div>
                    <div>
                        <?php if ($isTwoFactorEnabled): ?>
                            <span class="badge text-bg-success" style="font-size: 11px; padding: 5px 10px;">Включена</span>
                        <?php else: ?>
                            <span class="badge text-bg-secondary" style="font-size: 11px; padding: 5px 10px;">Отключена</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card-body p-3">
                    <?php if (empty($isTwoFactorConfigured)): ?>
                        <div class="alert alert-warning mb-0" style="border-radius: 10px; padding: 12px 14px; font-size: 12px; line-height: 1.45;">
                            <div style="display: flex; gap: 12px; align-items: center; justify-content: space-between; flex-wrap: wrap;">
                                <div style="display: flex; gap: 8px; align-items: center;">
                                    <span class="material-symbols-rounded" style="color: #d97706; font-size: 22px; flex-shrink: 0;">lock_clock</span>
                                    <div>
                                        <strong>Ключ шифрования 2FA не инициализирован.</strong><br>
                                        Нажмите кнопку для автоматической генерации ключа шифрования и мгновенной активации 2FA.
                                    </div>
                                </div>
                                <?= Html::a(
                                    '<span class="material-symbols-rounded" style="font-size: 16px; vertical-align: -2px; margin-right: 4px;">key</span> Сгенерировать ключ',
                                    ['/admin/user/regenerate-two-factor-key'],
                                    [
                                        'class' => 'btn btn-primary btn-sm',
                                        'data-method' => 'post',
                                        'style' => 'font-size: 12px; font-weight: 600; padding: 6px 14px;',
                                    ]
                                ) ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php $newBackupCodes = Yii::$app->session->getFlash('2fa_new_backup_codes'); ?>
                        <?php if (!empty($newBackupCodes) && is_array($newBackupCodes)): ?>
                            <div class="alert alert-warning mb-3" style="border-radius: 10px; padding: 14px;">
                                <div style="font-weight: 750; font-size: 13px; margin-bottom: 4px; color: #92400e;">
                                    Сохраните ваши одноразовые резервные коды!
                                </div>
                                <p style="font-size: 11.5px; margin-bottom: 8px; color: #78350f;">
                                    Каждый код может быть использован один раз для входа при утере доступа к телефону.
                                </p>
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 6px;">
                                    <?php foreach ($newBackupCodes as $bCode): ?>
                                        <div style="background: #fff; padding: 4px 8px; border-radius: 6px; font-family: monospace; font-size: 12px; font-weight: 700; text-align: center; border: 1px solid #fde68a;">
                                            <?= Html::encode($bCode) ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($isTwoFactorEnabled): ?>
                            <!-- Состояние: 2FA включена -->
                            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; background: var(--sz-bg-soft, #f4f8ff); border: 1px solid var(--sz-line, #e7edfb); border-radius: 10px; padding: 12px 14px;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(7, 155, 112, 0.12); color: #079b70; display: grid; place-items: center;">
                                        <span class="material-symbols-rounded" style="font-size: 22px;">verified_user</span>
                                    </div>
                                    <div>
                                        <div style="font-size: 13px; font-weight: 700; color: var(--sz-text, #101e47);">
                                            Двухэтапная авторизация активна
                                        </div>
                                        <div style="font-size: 11.5px; color: var(--sz-muted, #647fa9);">
                                            Резервных кодов осталось: <strong style="color: var(--sz-text, #101e47);"><?= (int) $backupCodesRemaining ?> из 8</strong>
                                        </div>
                                    </div>
                                </div>
                                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#regenBackupCollapse">
                                        <span class="material-symbols-rounded" style="font-size: 15px; vertical-align: -2px;">refresh</span>
                                        Новые резервные коды
                                    </button>
                                    <button type="button" class="btn btn-secondary btn-sm" style="color: #ef4444;" data-bs-toggle="collapse" data-bs-target="#disable2faCollapse">
                                        <span class="material-symbols-rounded" style="font-size: 15px; vertical-align: -2px;">lock_open</span>
                                        Отключить 2FA
                                    </button>
                                </div>
                            </div>

                            <div class="collapse mt-2" id="regenBackupCollapse">
                                <div style="background: var(--sz-surface-strong, #fff); border: 1px solid var(--sz-line, #e7edfb); border-radius: 8px; padding: 12px; color: var(--sz-text, #101e47);">
                                    <div style="font-size: 12px; font-weight: 600; margin-bottom: 8px;">Подтверждение генерации новых резервных кодов:</div>
                                    <?= Html::beginForm(['/admin/user/regenerate-backup-codes'], 'post', ['style' => 'display: flex; gap: 8px; align-items: center; flex-wrap: wrap;']) ?>
                                        <input type="password" name="password" class="form-control form-control-sm" placeholder="Текущий пароль" style="width: 220px;" required>
                                        <button type="submit" class="btn btn-warning btn-sm">Сгенерировать</button>
                                    <?= Html::endForm() ?>
                                </div>
                            </div>

                            <div class="collapse mt-2" id="disable2faCollapse">
                                <div style="background: var(--sz-surface-strong, #fff); border: 1px solid var(--sz-line, #e7edfb); border-radius: 8px; padding: 12px; color: var(--sz-text, #101e47);">
                                    <div style="font-size: 12px; font-weight: 600; margin-bottom: 8px; color: #ef4444;">Подтверждение отключения двухфакторной защиты:</div>
                                    <?= Html::beginForm(['/admin/user/disable-2fa'], 'post', ['style' => 'display: flex; gap: 8px; align-items: center; flex-wrap: wrap;']) ?>
                                        <input type="password" name="password" class="form-control form-control-sm" placeholder="Текущий пароль" style="width: 180px;" required>
                                        <input type="text" name="code" class="form-control form-control-sm" placeholder="Код 2FA или резервный" style="width: 170px;" required>
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Отключить двухфакторную аутентификацию?');">Отключить</button>
                                    <?= Html::endForm() ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- Состояние: 2FA не включена (подключение) -->
                            <?php if (!empty($twoFactorSetupData)): ?>
                                <div style="background: var(--sz-bg-soft, #f4f8ff); border: 1px solid var(--sz-line, #e7edfb); border-radius: 12px; padding: 14px;">
                                    <div class="row g-3 align-items-center">
                                        <!-- QR-код -->
                                        <div class="col-12 col-sm-auto text-center">
                                            <div style="
                                                background: #ffffff;
                                                padding: 8px;
                                                border-radius: 12px;
                                                border: 1px solid var(--sz-line, #e7edfb);
                                                display: inline-flex;
                                                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
                                                max-width: 140px;
                                            ">
                                                <div style="width: 124px; height: 124px; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                                                    <style>
                                                        .twofa-qr-box svg, .twofa-qr-box canvas, .twofa-qr-box img { width: 100% !important; height: 100% !important; display: block; border-radius: 4px; }
                                                    </style>
                                                    <div class="twofa-qr-box" id="twofaQrBox" data-otpauth="<?= Html::encode($twoFactorSetupData['otpAuthUri'] ?? '') ?>" style="width: 124px; height: 124px; display: flex; align-items: center; justify-content: center;">
                                                        <?= $twoFactorSetupData['qrSvg'] ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Шаги подключения -->
                                        <div class="col-12 col-sm">
                                            <div style="margin-bottom: 10px;">
                                                <div style="font-size: 13px; font-weight: 750; color: var(--sz-text, #101e47); margin-bottom: 3px;">
                                                    1. Отсканируйте QR-код в приложении
                                                </div>
                                                <div style="font-size: 11.5px; color: var(--sz-muted, #647fa9); line-height: 1.4;">
                                                    Или введите ключ вручную:&nbsp;
                                                    <code class="user-select-all" style="
                                                        font-family: monospace;
                                                        font-size: 12px;
                                                        font-weight: 700;
                                                        background: var(--sz-surface-strong, #ffffff);
                                                        color: #d13665;
                                                        padding: 2px 7px;
                                                        border-radius: 6px;
                                                        border: 1px solid var(--sz-line, #e7edfb);
                                                        display: inline-block;
                                                    "><?= Html::encode($twoFactorSetupData['secret']) ?></code>
                                                </div>
                                            </div>

                                            <div>
                                                <div style="font-size: 13px; font-weight: 750; color: var(--sz-text, #101e47); margin-bottom: 6px;">
                                                    2. Введите 6-значный код из приложения для активации
                                                </div>
                                                <?= Html::beginForm(['/admin/user/confirm-2fa'], 'post', ['style' => 'display: flex; gap: 8px; align-items: center; flex-wrap: wrap;']) ?>
                                                    <input type="text" name="code" class="form-control" placeholder="123456" maxlength="6" style="width: 130px; font-size: 14px; text-align: center; font-weight: 750; letter-spacing: 2.5px; height: 36px;" required autocomplete="one-time-code">
                                                    <button type="submit" class="btn btn-success" style="font-size: 12.5px; height: 36px; padding: 0 16px; font-weight: 600;">
                                                        <span class="material-symbols-rounded" style="font-size: 16px; vertical-align: -2px; margin-right: 4px;">verified</span>
                                                        Подтвердить и включить
                                                    </button>
                                                <?= Html::endForm() ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- Информационная панель и управление мастер-ключом шифрования 2FA -->
                    <?php if (!empty($twoFactorKeyInfo)): ?>
                        <div style="margin-top: 14px; padding-top: 12px; border-top: 1px solid var(--sz-line, #e7edfb); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                            <div style="display: flex; align-items: center; gap: 8px; font-size: 11.5px; color: var(--sz-muted, #647fa9);">
                                <span class="material-symbols-rounded" style="font-size: 16px; color: #10b981;">key</span>
                                <span>Мастер-ключ шифрования:</span>
                                <span class="badge" style="background: rgba(16, 185, 129, 0.12); color: #059669; font-weight: 600; font-size: 11px;">
                                    <?= Html::encode($twoFactorKeyInfo['sourceLabel'] ?? 'Готов к работе') ?>
                                </span>
                                <span class="d-none d-md-inline" style="font-family: monospace; font-size: 11px; opacity: 0.85;">(<?= Html::encode($twoFactorKeyInfo['keyMasked'] ?? '') ?>)</span>
                            </div>
                            <div style="display: flex; gap: 6px; align-items: center;">
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#twoFactorKeyModal" style="font-size: 11px; padding: 3px 8px;">
                                    <span class="material-symbols-rounded" style="font-size: 14px; vertical-align: -2px;">tune</span>
                                    Управление ключом
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($twoFactorKeyInfo)): ?>
    <!-- Модальное окно управления мастер-ключом 2FA -->
    <div class="modal fade" id="twoFactorKeyModal" tabindex="-1" aria-labelledby="twoFactorKeyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: var(--sz-surface, #ffffff); border: 1px solid var(--sz-line, #e7edfb); border-radius: 14px; box-shadow: 0 20px 44px rgba(0,0,0,0.2);">
                <div class="modal-header py-3 px-4" style="border-bottom: 1px solid var(--sz-line, #e7edfb);">
                    <div class="d-flex align-items-center gap-2">
                        <span class="material-symbols-rounded" style="color: #10b981; font-size: 22px;">key</span>
                        <h5 class="modal-title m-0" id="twoFactorKeyModalLabel" style="font-size: 15px; font-weight: 750; color: var(--sz-text, #101e47);">
                            Управление мастер-ключом 2FA
                        </h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <div class="modal-body p-4" style="font-size: 13px; color: var(--sz-text, #101e47);">
                    <div class="mb-3">
                        <div style="font-size: 11px; color: var(--sz-muted, #647fa9); text-transform: uppercase; font-weight: 700; margin-bottom: 4px;">
                            Источник ключа:
                        </div>
                        <span class="badge" style="background: rgba(16, 185, 129, 0.12); color: #059669; font-size: 12px; font-weight: 600; padding: 5px 10px;">
                            <?= Html::encode($twoFactorKeyInfo['sourceLabel'] ?? 'Готов к работе') ?>
                        </span>
                    </div>

                    <?php if (!empty($twoFactorKeyInfo['filePath'])): ?>
                        <div class="mb-3">
                            <div style="font-size: 11px; color: var(--sz-muted, #647fa9); text-transform: uppercase; font-weight: 700; margin-bottom: 4px;">
                                Путь к файлу ключа:
                            </div>
                            <code style="word-break: break-all; overflow-wrap: anywhere; font-family: monospace; font-size: 12px; background: var(--sz-bg-soft, #f4f8ff); color: var(--sz-text, #101e47); padding: 6px 10px; border-radius: 6px; display: block; border: 1px solid var(--sz-line, #e7edfb); user-select: all;">
                                <?= Html::encode($twoFactorKeyInfo['filePath']) ?>
                            </code>
                        </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <div style="font-size: 11px; color: var(--sz-muted, #647fa9); text-transform: uppercase; font-weight: 700; margin-bottom: 4px;">
                            Отпечаток ключа (SHA-256):
                        </div>
                        <div style="font-family: monospace; font-size: 12.5px; font-weight: 700; color: var(--sz-text, #101e47);">
                            <?= Html::encode($twoFactorKeyInfo['keyMasked'] ?? '') ?>
                            <span style="font-size: 11.5px; color: var(--sz-muted, #647fa9); font-weight: 400;">(длина: <?= (int) ($twoFactorKeyInfo['keyLength'] ?? 0) ?> символов)</span>
                        </div>
                    </div>

                    <div class="alert alert-info py-2 px-3 m-0" style="font-size: 11.5px; border-radius: 8px; line-height: 1.45;">
                        Мастер-ключ используется для шифрования секретов двухфакторной аутентификации в базе данных.
                    </div>
                </div>
                <div class="modal-footer py-2 px-4" style="border-top: 1px solid var(--sz-line, #e7edfb); display: flex; justify-content: space-between;">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" style="font-size: 12px;">Закрыть</button>
                    <?= Html::a(
                        '<span class="material-symbols-rounded" style="font-size: 15px; vertical-align: -2px; margin-right: 4px;">refresh</span> Сгенерировать новый ключ',
                        ['/admin/user/regenerate-two-factor-key'],
                        [
                            'class' => 'btn btn-warning btn-sm',
                            'style' => 'font-size: 12px; font-weight: 600;',
                            'data-method' => 'post',
                            'data-confirm' => 'Внимание! При смене мастер-ключа шифрования ранее настроенная 2FA у существующих пользователей может потребовать повторного подключения. Продолжить?',
                        ]
                    ) ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php
$qrJs = <<<JS
(function() {
    function renderQrIfNeeded() {
        var box = document.getElementById('twofaQrBox');
        if (!box) return;
        var uri = box.getAttribute('data-otpauth');
        if (!uri) return;

        var text = (box.textContent || '').trim();
        var hasValidSvg = box.querySelector('svg path') && !text.includes('QR Code Library required');

        if (!hasValidSvg && typeof QRCode !== 'undefined') {
            box.innerHTML = '';
            new QRCode(box, {
                text: uri,
                width: 124,
                height: 124,
                colorDark: "#000000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.M
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', renderQrIfNeeded);
    } else {
        renderQrIfNeeded();
    }
})();
JS;
$this->registerJs($qrJs, \yii\web\View::POS_READY);
?>

