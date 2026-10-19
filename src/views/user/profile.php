<?php

/** @var yii\web\View $this */
/** @var larikmc\admin\models\ProfileForm $model */
/** @var common\models\User $user */
/** @var array|null $pendingEmailChange */
/** @var bool $isTwoFactorEnabled */
/** @var int $backupCodesRemaining */
/** @var array|null $twoFactorSetupData */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\Url;

$this->title = 'Настройки профиля';
$this->params['breadcrumbs'] = [
    ['label' => 'Панель управления', 'url' => ['/admin/site/index']],
    ['label' => $this->title],
];

$avatarLetter = mb_strtoupper(mb_substr((string) ($user->username ?? 'A'), 0, 1));
?>

<div class="profile-page">
    <div style="margin-bottom: 20px;">
        <h1 style="font-size: 22px; font-weight: 750; margin: 0 0 6px 0; color: var(--text, #101e47);">
            <?= Html::encode($this->title) ?>
        </h1>
        <p style="font-size: 13px; margin: 0; color: var(--muted, #647fa9);">
            Управление личными данными, учетной записью и безопасностью
        </p>
    </div>

    <div class="row align-items-stretch">
        <!-- Левая колонка: Карточка пользователя -->
        <div class="col-12 col-xl-4 d-flex flex-column" style="margin-bottom: 16px;">
            <div class="card h-100 d-flex flex-column">
                <div class="card-body d-flex flex-column" style="align-items: center; text-align: center; padding: 24px 20px;">
                    <div style="
                        width: 76px;
                        height: 76px;
                        border-radius: 22px;
                        background: linear-gradient(135deg, #3189f7, #1a56c4);
                        color: #ffffff;
                        display: grid;
                        place-items: center;
                        font-size: 32px;
                        font-weight: 800;
                        box-shadow: 0 8px 24px rgba(49, 137, 247, .35);
                        margin-bottom: 14px;
                    ">
                        <?= Html::encode($avatarLetter) ?>
                    </div>

                    <h2 style="font-size: 18px; font-weight: 760; margin: 0 0 4px 0; color: var(--text, #101e47);">
                        <?= Html::encode($user->username ?? 'Пользователь') ?>
                    </h2>
                    <p style="font-size: 12.5px; margin: 0 0 14px 0; color: var(--muted, #647fa9);">
                        <?= Html::encode($user->email ?? '') ?>
                    </p>

                    <div style="display: flex; gap: 8px; flex-wrap: wrap; justify-content: center; margin-bottom: 20px;">
                        <span class="badge text-bg-primary" style="padding: 6px 12px; font-size: 11px; font-weight: 700;">
                            Администратор
                        </span>
                        <span class="badge text-bg-success" style="padding: 6px 12px; font-size: 11px; font-weight: 700;">
                            Активен
                        </span>
                        <?php if ($isTwoFactorEnabled): ?>
                            <span class="badge text-bg-info" style="padding: 6px 12px; font-size: 11px; font-weight: 700;">
                                2FA активен
                            </span>
                        <?php endif; ?>
                    </div>

                    <div style="
                        width: 100%;
                        border-top: 1px solid var(--stroke, #e7edfb);
                        padding-top: 16px;
                        display: flex;
                        flex-direction: column;
                        gap: 12px;
                        text-align: left;
                        font-size: 12.5px;
                        margin-bottom: 16px;
                    ">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: var(--muted, #647fa9);">ID аккаунта:</span>
                            <strong style="color: var(--text, #101e47);">#<?= (int) ($user->id ?? 0) ?></strong>
                        </div>
                        <?php if (!empty($user->created_at)): ?>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="color: var(--muted, #647fa9);">Дата регистрации:</span>
                                <strong style="color: var(--text, #101e47);">
                                    <?= Yii::$app->formatter->asDatetime($user->created_at, 'php:d.m.Y H:i') ?>
                                </strong>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($user->updated_at)): ?>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="color: var(--muted, #647fa9);">Обновлен:</span>
                                <strong style="color: var(--text, #101e47);">
                                    <?= Yii::$app->formatter->asRelativeTime($user->updated_at) ?>
                                </strong>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="alert alert-info" style="margin-top: auto; margin-bottom: 0; text-align: left; width: 100%;">
                        <div style="display: flex; gap: 10px; align-items: flex-start;">
                            <span class="material-symbols-rounded" style="font-size: 18px; margin-top: 1px;">shield</span>
                            <span style="font-size: 12px; line-height: 1.45;">
                                Учетная запись надежно защищена. При смене почты подтверждение всегда отправляется на ваш текущий адрес.
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Правая колонка: Форма редактирования -->
        <div class="col-12 col-xl-8 d-flex flex-column" style="margin-bottom: 16px;">
            <div class="card h-100 d-flex flex-column">
                <div class="card-header">
                    <div>
                        <h3 class="card-title">Редактирование профиля</h3>
                        <p class="card-subtitle">Изменение логина, контактного адреса и пароля авторизации</p>
                    </div>
                </div>
                <div class="card-body d-flex flex-column flex-grow-1">
                    <!-- Баннер ожидающего подтверждения смены email -->
                    <?php if (!empty($pendingEmailChange)): ?>
                        <div class="alert alert-warning" style="margin-bottom: 24px; border-radius: 12px; padding: 16px 18px;">
                            <div style="display: flex; gap: 12px; align-items: flex-start;">
                                <span class="material-symbols-rounded" style="color: #d97706; font-size: 24px; flex-shrink: 0; margin-top: 2px;">mark_email_unread</span>
                                <div style="flex-grow: 1;">
                                    <div style="font-size: 14px; font-weight: 750; margin-bottom: 4px; color: inherit;">
                                        Ожидает подтверждения смена E-mail
                                    </div>
                                    <p style="margin: 0 0 14px 0; font-size: 12.5px; line-height: 1.45;">
                                        Запрошен перевод на новый адрес: <strong style="color: inherit;"><?= Html::encode($pendingEmailChange['newEmail']) ?></strong>.<br>
                                        Письмо со ссылкой и проверочным кодом отправлено на ваш текущий ящик: <strong><?= Html::encode($user->email) ?></strong>.
                                    </p>

                                    <!-- Форма ввода проверочного кода -->
                                    <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                                        <?= Html::beginForm(['/admin/user/verify-email-code'], 'post', ['style' => 'display:inline-flex; gap:8px; align-items:center;']) ?>
                                            <input type="text" name="code" class="form-control" placeholder="6-значный код" style="width: 145px; height: 35px; font-size: 12.5px; text-align: center; letter-spacing: 2px; font-weight: 700;" maxlength="6" required>
                                            <button type="submit" class="btn btn-warning" style="height: 35px; padding: 0 14px; font-size: 12px;">
                                                Подтвердить код
                                            </button>
                                        <?= Html::endForm() ?>

                                        <?= Html::beginForm(['/admin/user/resend-email-change'], 'post', ['style' => 'display:inline;']) ?>
                                            <button type="submit" class="btn btn-secondary" style="height: 35px; padding: 0 12px; font-size: 12px;">
                                                Отправить повторно
                                            </button>
                                        <?= Html::endForm() ?>

                                        <?= Html::beginForm(['/admin/user/cancel-email-change'], 'post', ['style' => 'display:inline;']) ?>
                                            <button type="submit" class="btn btn-secondary" style="height: 35px; padding: 0 12px; font-size: 12px; color: #ef4444;" onclick="return confirm('Отменить запрос на смену почты?');">
                                                Отменить смену
                                            </button>
                                        <?= Html::endForm() ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php $form = ActiveForm::begin([
                        'id' => 'profile-form',
                        'enableClientValidation' => true,
                        'options' => ['class' => 'd-flex flex-column flex-grow-1', 'style' => 'display: flex; flex-direction: column; flex: 1;'],
                    ]); ?>

                    <!-- Секция: Основные данные -->
                    <div style="margin-bottom: 28px;">
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 14px;">
                            <span class="material-symbols-rounded" style="color: #3189f7; font-size: 20px;">badge</span>
                            <span style="font-size: 14px; font-weight: 750; color: var(--text, #101e47);">
                                Основная информация
                            </span>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6" style="margin-bottom: 14px;">
                                <?= $form->field($model, 'username', [
                                    'labelOptions' => ['class' => 'form-label'],
                                    'inputOptions' => ['class' => 'form-control', 'placeholder' => 'Введите логин'],
                                ]) ?>
                            </div>
                            <div class="col-12 col-md-6" style="margin-bottom: 14px;">
                                <?= $form->field($model, 'email', [
                                    'labelOptions' => ['class' => 'form-label'],
                                    'inputOptions' => ['class' => 'form-control', 'type' => 'email', 'placeholder' => 'example@domain.com'],
                                    'hintOptions' => ['style' => 'font-size: 11px; color: var(--muted, #647fa9); margin-top: 5px; display: block; line-height: 1.35;'],
                                ])->hint('Смена адреса потребует подтверждения по текущей почте (' . Html::encode($user->email) . ')') ?>
                            </div>
                        </div>
                    </div>

                    <!-- Секция: Смена пароля -->
                    <div style="margin-bottom: 24px; padding-top: 18px; border-top: 1px solid var(--stroke, #e7edfb);">
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                            <span class="material-symbols-rounded" style="color: #d13665; font-size: 20px;">lock_reset</span>
                            <span style="font-size: 14px; font-weight: 750; color: var(--text, #101e47);">
                                Смена пароля
                            </span>
                        </div>
                        <p style="font-size: 12px; color: var(--muted, #647fa9); margin: 0 0 16px 0;">
                            Заполняйте поля ниже только если вы хотите сменить пароль. Если смена не требуется, оставьте их пустыми.
                        </p>

                        <div class="row">
                            <div class="col-12" style="margin-bottom: 14px;">
                                <?= $form->field($model, 'currentPassword', [
                                    'labelOptions' => ['class' => 'form-label'],
                                    'inputOptions' => ['class' => 'form-control', 'type' => 'password', 'placeholder' => 'Текущий пароль для подтверждения смены'],
                                ]) ?>
                            </div>
                            <div class="col-12 col-md-6" style="margin-bottom: 14px;">
                                <?= $form->field($model, 'newPassword', [
                                    'labelOptions' => ['class' => 'form-label'],
                                    'inputOptions' => ['class' => 'form-control', 'type' => 'password', 'placeholder' => 'Не менее 6 символов'],
                                ]) ?>
                            </div>
                            <div class="col-12 col-md-6" style="margin-bottom: 14px;">
                                <?= $form->field($model, 'confirmPassword', [
                                    'labelOptions' => ['class' => 'form-label'],
                                    'inputOptions' => ['class' => 'form-control', 'type' => 'password', 'placeholder' => 'Повторите новый пароль'],
                                ]) ?>
                            </div>
                        </div>
                    </div>

                    <!-- Кнопки управления -->
                    <div style="display: flex; gap: 10px; align-items: center; padding-top: 16px; margin-top: auto;">
                        <button type="submit" class="btn btn-primary">
                            <span class="material-symbols-rounded" style="font-size: 18px;">save</span>
                            Сохранить изменения
                        </button>
                        <a href="<?= Url::to(['/admin/site/index']) ?>" class="btn btn-secondary">
                            Отмена
                        </a>
                    </div>

                    
                    <!-- Секция: Двухфакторная аутентификация (2FA) -->
                    <div style="margin-bottom: 24px; padding-top: 18px; border-top: 1px solid var(--stroke, #e7edfb);">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; flex-wrap: wrap; gap: 10px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span class="material-symbols-rounded" style="color: #10b981; font-size: 20px;">shield</span>
                                <span style="font-size: 14px; font-weight: 750; color: var(--text, #101e47);">
                                    Двухфакторная аутентификация (2FA Google Authenticator)
                                </span>
                            </div>
                            <?php if ($isTwoFactorEnabled): ?>
                                <span class="badge text-bg-success" style="padding: 6px 10px; font-size: 11px;">Включена</span>
                            <?php else: ?>
                                <span class="badge text-bg-secondary" style="padding: 6px 10px; font-size: 11px;">Отключена</span>
                            <?php endif; ?>
                        </div>

                        <?php if (empty($isTwoFactorConfigured)): ?>
                            <div class="alert alert-warning" style="border-radius: 12px; padding: 14px 16px; margin-top: 10px;">
                                <div style="display: flex; gap: 10px; align-items: flex-start;">
                                    <span class="material-symbols-rounded" style="color: #d97706; font-size: 22px; flex-shrink: 0;">lock_clock</span>
                                    <div style="font-size: 12.5px; line-height: 1.45;">
                                        <strong>Настройка 2FA временно недоступна:</strong> не задан секретный ключ шифрования приложения.<br>
                                        Администратору сервера необходимо настроить переменную окружения <code>ADMIN_2FA_SECRET_KEY</code> (минимум 32 случайных символа) или указать ключ в конфигурации приложения <code>params['admin.2fa.secretKey']</code>.
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                        <?php $newBackupCodes = Yii::$app->session->getFlash('2fa_new_backup_codes'); ?>
                        <?php if (!empty($newBackupCodes) && is_array($newBackupCodes)): ?>
                            <div class="alert alert-warning" style="border-radius: 12px; padding: 16px;">
                                <div style="font-weight: 750; font-size: 13.5px; margin-bottom: 6px; color: #92400e;">
                                    Сохраните ваши одноразовые резервные коды!
                                </div>
                                <p style="font-size: 12px; margin-bottom: 10px; color: #78350f;">
                                    Каждый код может быть использован один раз для входа в случае отсутствия доступа к Google Authenticator. Они больше не будут показаны.
                                </p>
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 8px; margin-bottom: 10px;">
                                    <?php foreach ($newBackupCodes as $bCode): ?>
                                        <div style="background: #fff; padding: 6px 10px; border-radius: 6px; font-family: monospace; font-size: 13px; font-weight: 700; text-align: center; border: 1px solid #fde68a;">
                                            <?= Html::encode($bCode) ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($isTwoFactorEnabled): ?>
                            <p style="font-size: 12px; color: var(--muted, #647fa9); margin: 0 0 14px 0;">
                                Ваша учетная запись защищена вторым фактором. При каждом входе требуется код из мобильного приложения TOTP.<br>
                                Доступно активных резервных кодов: <strong><?= (int) $backupCodesRemaining ?></strong>.
                            </p>

                            <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 12px;">
                                <button type="button" class="btn btn-secondary" style="font-size: 12px;" data-bs-toggle="collapse" data-bs-target="#regenBackupCollapse">
                                    <span class="material-symbols-rounded" style="font-size: 16px;">refresh</span> Перегенерировать резервные коды
                                </button>
                                <button type="button" class="btn btn-secondary" style="font-size: 12px; color: #ef4444;" data-bs-toggle="collapse" data-bs-target="#disable2faCollapse">
                                    <span class="material-symbols-rounded" style="font-size: 16px;">no_accounts</span> Отключить 2FA
                                </button>
                            </div>

                            <div class="collapse" id="regenBackupCollapse" style="margin-bottom: 14px;">
                                <div style="background: var(--surface, #f8fafd); border: 1px solid var(--stroke, #e7edfb); border-radius: 10px; padding: 14px;">
                                    <?= Html::beginForm(['/admin/user/regenerate-backup-codes'], 'post', ['style' => 'display: flex; gap: 8px; align-items: center; flex-wrap: wrap;']) ?>
                                        <input type="password" name="password" class="form-control" placeholder="Текущий пароль для подтверждения" style="width: 260px; font-size: 12px;" required>
                                        <button type="submit" class="btn btn-warning" style="font-size: 12px;">Сгенерировать новые коды</button>
                                    <?= Html::endForm() ?>
                                </div>
                            </div>

                            <div class="collapse" id="disable2faCollapse" style="margin-bottom: 14px;">
                                <div style="background: var(--surface, #f8fafd); border: 1px solid var(--stroke, #e7edfb); border-radius: 10px; padding: 14px;">
                                    <?= Html::beginForm(['/admin/user/disable-2fa'], 'post', ['style' => 'display: flex; gap: 8px; align-items: center; flex-wrap: wrap;']) ?>
                                        <input type="password" name="password" class="form-control" placeholder="Текущий пароль" style="width: 200px; font-size: 12px;" required>
                                        <input type="text" name="code" class="form-control" placeholder="Код TOTP или резервный" style="width: 190px; font-size: 12px;" required>
                                        <button type="submit" class="btn btn-danger" style="font-size: 12px;" onclick="return confirm('Вы уверены, что хотите отключить 2FA?');">Подтвердить отключение</button>
                                    <?= Html::endForm() ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <p style="font-size: 12px; color: var(--muted, #647fa9); margin: 0 0 14px 0;">
                                Подключите двухфакторную аутентификацию для защиты аккаунта от несанкционированного доступа при компрометации пароля.
                            </p>

                            <?php if (!empty($twoFactorSetupData)): ?>
                                <div style="background: var(--surface, #f8fafd); border: 1px solid var(--stroke, #e7edfb); border-radius: 12px; padding: 18px; margin-bottom: 14px;">
                                    <div class="row align-items-center">
                                        <div class="col-12 col-md-auto text-center" style="margin-bottom: 14px;">
                                            <div style="background: #fff; padding: 8px; border-radius: 10px; border: 1px solid var(--stroke, #e7edfb); display: inline-block;">
                                                <?= $twoFactorSetupData['qrSvg'] ?>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md">
                                            <div style="font-size: 13px; font-weight: 700; color: var(--text, #101e47); margin-bottom: 6px;">
                                                1. Отсканируйте QR-код в приложении
                                            </div>
                                            <p style="font-size: 12px; color: var(--muted, #647fa9); margin-bottom: 10px;">
                                                Используйте Google Authenticator, Яндекс Ключ или 1Password. Если сканирование недоступно, введите секретный ключ вручную:<br>
                                                <code style="background: #fff; padding: 4px 8px; border-radius: 6px; font-size: 12.5px; border: 1px solid var(--stroke, #e7edfb); display: inline-block; margin-top: 4px;"><?= Html::encode($twoFactorSetupData['secret']) ?></code>
                                            </p>
                                            <div style="font-size: 13px; font-weight: 700; color: var(--text, #101e47); margin-bottom: 6px;">
                                                2. Введите 6-значный код для активации
                                            </div>
                                            <?= Html::beginForm(['/admin/user/confirm-2fa'], 'post', ['style' => 'display: flex; gap: 8px; align-items: center; flex-wrap: wrap;']) ?>
                                                <input type="text" name="code" class="form-control" placeholder="123456" maxlength="6" style="width: 140px; font-size: 14px; text-align: center; font-weight: 700; letter-spacing: 2px;" required>
                                                <button type="submit" class="btn btn-success" style="font-size: 12px;">
                                                    <span class="material-symbols-rounded" style="font-size: 16px;">verified</span> Подтвердить и включить 2FA
                                                </button>
                                            <?= Html::endForm() ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
