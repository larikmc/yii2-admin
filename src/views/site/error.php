<?php

/** @var yii\web\View $this */
/** @var string $name */
/** @var string $message */
/** @var Exception|null $exception */

use yii\bootstrap5\Html;

$code = $exception instanceof \yii\web\HttpException ? (int) $exception->statusCode : 500;
if ($code === 0 && preg_match('/#(\d+)/', (string) $name, $m)) {
    $code = (int) $m[1];
}
if ($code === 0) {
    $code = 500;
}

$isForbidden = $code === 403;
$isNotFound = $code === 404;

if ($isNotFound) {
    $displayTitle = 'Страница не найдена';
    $this->title = 'Ошибка 404 — Страница не найдена';
    $icon = 'search_off';
    $hint = 'Запрошенная страница не существует, была перемещена или удалена.';
} elseif ($isForbidden) {
    $displayTitle = 'Доступ ограничен';
    $this->title = 'Ошибка 403 — Доступ ограничен';
    $icon = 'lock';
    $hint = 'У вашей учетной записи недостаточно прав для просмотра этого раздела.';
} else {
    $displayTitle = 'Внутренняя ошибка сервера';
    $this->title = 'Ошибка ' . $code . ' — Внутренняя ошибка';
    $icon = 'error_outline';
    $hint = 'Произошла непредвиденная ошибка на стороне сервера при обработке запроса.';
}

$safeMessage = trim((string) $message) !== '' ? $message : 'Действие не может быть выполнено.';

$this->params['breadcrumbs'] = [
    ['label' => 'Ошибка ' . $code],
];
$this->params['pageSubtitle'] = 'Код ответа: HTTP ' . $code;
?>

<div class="sz-error-wrapper">
    <div class="sz-error-card">
        <div class="sz-error-card__visual">
            <div class="sz-error-card__icon-badge sz-error-card__icon-badge--<?= (int) $code ?>">
                <span class="material-symbols-rounded"><?= Html::encode($icon) ?></span>
            </div>
            <div class="sz-error-card__code"><?= (int) $code ?></div>
        </div>

        <h2 class="sz-error-card__title"><?= Html::encode($displayTitle) ?></h2>

        <div class="sz-error-card__message">
            <?= nl2br(Html::encode($safeMessage)) ?>
        </div>

        <p class="sz-error-card__hint"><?= Html::encode($hint) ?></p>

        <div class="sz-error-card__actions">
            <?= Html::a(
                '<span class="material-symbols-rounded">home</span> На главную',
                ['/admin/site/index'],
                ['class' => 'btn btn-primary sz-btn-icon']
            ) ?>
            <button type="button" class="btn btn-outline-secondary sz-btn-icon" onclick="window.history.back();">
                <span class="material-symbols-rounded">arrow_back</span> Назад
            </button>
        </div>
    </div>
</div>
