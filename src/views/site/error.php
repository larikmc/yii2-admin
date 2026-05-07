<?php

/** @var yii\web\View $this */
/** @var string $name */
/** @var string $message */
/** @var Exception $exception */

use yii\bootstrap5\Html;

$this->title = $name;
$code = $exception instanceof \yii\web\HttpException ? (int) $exception->statusCode : 500;
$safeMessage = trim((string) $message) !== '' ? $message : 'Произошла ошибка при обработке запроса.';
$isForbidden = $code === 403;
$isNotFound = $code === 404;

$hint = 'Попробуйте вернуться на предыдущую страницу или обновить раздел.';
if ($isForbidden) {
    $hint = 'У вас недостаточно прав для этого действия. Обратитесь к главному администратору.';
} elseif ($isNotFound) {
    $hint = 'Страница не найдена. Проверьте ссылку или перейдите в нужный раздел через меню.';
}
?>

<section class="sz-panel sz-error-page">
    <div class="sz-error-page__top">
        <p class="sz-error-page__code">#<?= Html::encode((string) $code) ?></p>
        <h2 class="sz-error-page__title"><?= Html::encode($name) ?></h2>
    </div>

    <div class="alert alert-danger sz-error-page__alert" role="alert">
        <?= nl2br(Html::encode($safeMessage)) ?>
    </div>

    <p class="sz-error-page__hint"><?= Html::encode($hint) ?></p>

    <div class="sz-actions-bar">
        <div class="sz-actions-bar__inner">
            <?= Html::a('На главную', ['/admin/site/index'], ['class' => 'btn btn-primary']) ?>
            <button type="button" class="btn btn-outline-secondary" onclick="window.history.back();">Назад</button>
        </div>
    </div>
</section>
