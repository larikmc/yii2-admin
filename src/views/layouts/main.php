<?php

/** @var \yii\web\View $this */
/** @var string $content */

use larikmc\admin\assets\AppAsset;
use yii\bootstrap5\Html;
use yii\helpers\Url;

$asset = AppAsset::register($this);
$module = Yii::$app->getModule('admin');
$primaryMenu = $module->menu ?? [];
$secondaryMenu = $module->secondaryMenu ?? [];
$currentRoute = '/' . Yii::$app->controller->route;

$isActive = static function (array $item) use ($currentRoute): bool {
    $url = $item['url'] ?? null;
    if (!is_array($url) || !isset($url[0])) {
        return false;
    }

    return rtrim((string) $url[0], '*') === $currentRoute
        || (str_ends_with((string) $url[0], '*') && str_starts_with($currentRoute, rtrim((string) $url[0], '*')));
};

$hasActiveChild = static function (array $item) use ($isActive): bool {
    foreach (($item['items'] ?? []) as $child) {
        if ($isActive($child)) {
            return true;
        }
    }

    return false;
};

$renderMenuItem = static function (array $item, bool $secondary = false) use ($isActive, $hasActiveChild) {
    $icon = $item['icon'] ?? 'radio_button_unchecked';
    $label = $item['label'] ?? 'Без названия';
    $items = $item['items'] ?? [];
    $isDropdown = !empty($items);
    $isOpen = $hasActiveChild($item);
    $itemClass = 'sz-nav__item' . ($isDropdown ? ' sz-dd' : '') . ($isOpen ? ' sz-dd--open' : '');

    echo '<li class="' . Html::encode($itemClass) . '"' . ($isDropdown ? ' data-sz-dd' : '') . '>';

    if ($isDropdown) {
        echo Html::a(
            '<span class="material-symbols-rounded">' . Html::encode($icon) . '</span>' .
            '<span class="sz-nav__label">' . Html::encode($label) . '</span>' .
            '<span class="material-symbols-rounded sz-dd__icon">keyboard_arrow_down</span>',
            '#',
            ['class' => 'sz-nav__link', 'data-sz-dd-toggle' => true]
        );

        echo '<ul class="sz-submenu" data-sz-dd-menu' . ($isOpen ? ' style="height:auto"' : '') . '>';
        echo '<li class="sz-submenu__item"><span class="sz-submenu__title">' . Html::encode($label) . '</span></li>';
        foreach ($items as $child) {
            echo '<li class="sz-submenu__item">';
            echo Html::a(
                Html::encode($child['label'] ?? 'Без названия'),
                Url::to($child['url'] ?? '#'),
                ['class' => 'sz-submenu__link']
            );
            echo '</li>';
        }
        echo '</ul>';
        echo '</li>';
        return;
    }

    $url = $item['url'] ?? '#';
    $linkOptions = $item['linkOptions'] ?? [];
    $method = strtolower((string) ($item['method'] ?? 'get'));

    if ($method === 'post') {
        echo Html::beginForm($url, 'post', ['class' => 'sz-logout__form']);
        echo Html::submitButton(
            '<span class="material-symbols-rounded">' . Html::encode($icon) . '</span>' .
            '<span class="sz-nav__label">' . Html::encode($label) . '</span>',
            array_merge(
                [
                    'class' => 'sz-nav__link sz-logout__btn',
                    'encode' => false,
                    'title' => $label,
                ],
                $linkOptions
            )
        );
        echo Html::endForm();
    } else {
        echo Html::a(
            '<span class="material-symbols-rounded">' . Html::encode($icon) . '</span>' .
            '<span class="sz-nav__label">' . Html::encode($label) . '</span>',
            Url::to($url),
            array_merge(['class' => 'sz-nav__link'], $linkOptions)
        );
    }

    if (!$secondary) {
        echo '<ul class="sz-submenu"><li class="sz-submenu__item"><span class="sz-submenu__title">' . Html::encode($label) . '</span></li></ul>';
    }

    echo '</li>';
};
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>

<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<button type="button" class="sz-sidebar-menu-btn" aria-label="Открыть боковое меню">
    <span class="material-symbols-rounded">menu</span>
</button>

<aside class="sz-sidebar">
    <header class="sz-sidebar__header">
        <a href="<?= Yii::$app->homeUrl ?>" class="sz-sidebar__logo">
            <img src="<?= $asset->baseUrl ?>/img/logo.png" alt="Логотип" />
        </a>

        <button type="button" class="sz-sidebar__toggler" aria-label="Свернуть боковое меню">
            <span class="material-symbols-rounded">chevron_left</span>
        </button>
    </header>

    <nav class="sz-sidebar__nav">
        <ul class="sz-nav sz-nav--primary">
            <?php foreach ($primaryMenu as $item): ?>
                <?php $renderMenuItem($item); ?>
            <?php endforeach; ?>
        </ul>

        <ul class="sz-nav sz-nav--secondary">
            <?php foreach ($secondaryMenu as $item): ?>
                <?php $renderMenuItem($item, true); ?>
            <?php endforeach; ?>
        </ul>
    </nav>
</aside>

<main class="sz-content">
    <?php
    $flashes = Yii::$app->session->getAllFlashes(true);
    $classMap = [
        'error' => 'alert-danger',
        'danger' => 'alert-danger',
        'warning' => 'alert-warning',
        'success' => 'alert-success',
        'info' => 'alert-info',
    ];
    ?>
    <?php if (!empty($flashes)): ?>
        <div class="mb-3">
            <?php foreach ($flashes as $type => $message): ?>
                <?php $css = $classMap[$type] ?? 'alert-info'; ?>
                <?php foreach ((array) $message as $m): ?>
                    <div class="alert <?= Html::encode($css) ?> alert-dismissible fade show" role="alert">
                        <?= Html::encode((string) $m) ?>
                        <button type="button" class="btn-close js-flash-close" data-bs-dismiss="alert" aria-label="Закрыть"></button>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?= $content ?>
</main>

<script>
document.addEventListener('click', function (e) {
    var btn = e.target.closest('.js-flash-close');
    if (!btn) return;
    var alert = btn.closest('.alert');
    if (alert) {
        alert.remove();
    }
});
</script>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
