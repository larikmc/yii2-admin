<?php

/** @var yii\web\View $this */
/** @var larikmc\admin\models\ProfileForm $model */
/** @var common\models\User $user */
/** @var array|null $pendingEmailChange */

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

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
