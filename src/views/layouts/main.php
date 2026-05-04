<?php

/** @var \yii\web\View $this */
/** @var string $content */

use larikmc\admin\assets\AppAsset;
use yii\bootstrap5\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

$asset = AppAsset::register($this);
$module = Yii::$app->getModule('admin');
$primaryMenu = $module->menu ?? [];
$secondaryMenu = $module->secondaryMenu ?? [];
$currentRoute = '/' . Yii::$app->controller->route;
$identity = Yii::$app->user->identity;
$pageTitle = $this->title ?: 'Админ-панель';
$isDashboard = Yii::$app->controller->module->id === 'admin'
    && Yii::$app->controller->id === 'site'
    && Yii::$app->controller->action->id === 'index';
$topbarActions = $this->params['topbarActions'] ?? [];
$breadcrumbs = $this->params['breadcrumbs'] ?? [];
$breadcrumbItems = [];

foreach ($breadcrumbs as $breadcrumb) {
    if (is_array($breadcrumb)) {
        $label = ArrayHelper::getValue($breadcrumb, 'label', '');
        $url = ArrayHelper::getValue($breadcrumb, 'url');
        $breadcrumbItems[] = $url
            ? Html::a(Html::encode((string) $label), $url, ['class' => 'sz-breadcrumbs__link'])
            : Html::tag('span', Html::encode((string) $label), ['class' => 'sz-breadcrumbs__current']);
        continue;
    }

    $breadcrumbItems[] = Html::tag('span', Html::encode((string) $breadcrumb), ['class' => 'sz-breadcrumbs__current']);
}

$breadcrumbTrail = implode('<span class="sz-breadcrumbs__sep">/</span>', $breadcrumbItems);
$userName = $identity->username ?? $identity->email ?? 'Администратор';
$userEmail = $identity->email ?? null;

$isActive = static function (array $item) use ($currentRoute): bool {
    $url = $item['url'] ?? null;
    if (!is_array($url) || !isset($url[0])) {
        return false;
    }

    return rtrim((string) $url[0], '*') === $currentRoute
        || (str_ends_with((string) $url[0], '*') && str_starts_with($currentRoute, rtrim((string) $url[0], '*')));
};

$isVisible = static function (array $item): bool {
    if (!array_key_exists('visible', $item)) {
        return true;
    }

    $visible = $item['visible'];

    if (is_callable($visible)) {
        return (bool) $visible();
    }

    return (bool) $visible;
};

$hasActiveChild = static function (array $item) use ($isActive): bool {
    foreach (($item['items'] ?? []) as $child) {
        if ($isActive($child)) {
            return true;
        }
    }

    return false;
};

$renderMenuItem = static function (array $item, bool $secondary = false) use ($isActive, $hasActiveChild, $isVisible) {
    if (!$isVisible($item)) {
        return;
    }

    $icon = $item['icon'] ?? 'radio_button_unchecked';
    $label = $item['label'] ?? 'Без названия';
    $items = array_values(array_filter($item['items'] ?? [], static fn(array $child): bool => $isVisible($child)));
    $isDropdown = !empty($items);
    $isCurrent = $isActive($item);
    $isOpen = $hasActiveChild($item);
    $itemClass = 'sz-nav__item'
        . ($isDropdown ? ' sz-dd' : '')
        . ($isOpen ? ' sz-dd--open' : '')
        . ($isCurrent ? ' sz-nav__item--active' : '');

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

<body class="sz-admin-body d-flex flex-column h-100">
<?php $this->beginBody() ?>

<div class="sz-shell-glow sz-shell-glow--violet"></div>
<div class="sz-shell-glow sz-shell-glow--gold"></div>
<div class="sz-mobile-backdrop"></div>

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
    <div class="sz-content__inner">
        <header class="sz-topbar sz-topbar--compact">
            <div class="sz-topbar__meta sz-topbar__meta--compact">
                <?php if ($breadcrumbTrail !== ''): ?>
                    <div class="sz-breadcrumbs"><?= $breadcrumbTrail ?></div>
                <?php endif; ?>
                <h1 class="sz-topbar__title sz-topbar__title--compact"><?= Html::encode($pageTitle) ?></h1>
            </div>

            <div class="sz-topbar__side">
                <div class="sz-topbar__userbar">
                <?= Html::a(
                    '<span class="material-symbols-rounded">language</span>',
                    '/',
                    [
                        'class' => 'sz-topbar__quicklink',
                        'title' => 'Перейти на сайт',
                        'aria-label' => 'Перейти на сайт',
                        'target' => '_blank',
                        'rel' => 'noopener',
                    ]
                ) ?>

                <?= Html::beginForm(['/admin/site/clear-cache'], 'post', ['class' => 'sz-topbar__quickform']) ?>
                <?= Html::submitButton(
                    '<span class="material-symbols-rounded">restart_alt</span>',
                    [
                        'class' => 'sz-topbar__quicklink',
                        'title' => 'Очистить кеш',
                        'aria-label' => 'Очистить кеш',
                    ]
                ) ?>
                <?= Html::endForm() ?>

                <div class="sz-account-menu" data-sz-account>
                    <button type="button" class="sz-topbar__user sz-account-menu__toggle" data-sz-account-toggle aria-expanded="false">
                        <div class="sz-topbar__avatar">
                            <?= Html::encode(mb_strtoupper(mb_substr((string) $userName, 0, 1))) ?>
                        </div>
                        <div class="sz-topbar__user-text">
                            <strong><?= Html::encode($userName) ?></strong>
                            <?php if ($userEmail): ?>
                                <span><?= Html::encode($userEmail) ?></span>
                            <?php endif; ?>
                        </div>
                        <span class="material-symbols-rounded sz-account-menu__chevron">keyboard_arrow_down</span>
                    </button>

                    <div class="sz-account-menu__dropdown" data-sz-account-dropdown>
                        <?= Html::beginForm(['/auth/logout'], 'post', ['class' => 'sz-account-menu__form']) ?>
                        <?= Html::submitButton(
                            '<span class="material-symbols-rounded">logout</span><span>Выйти</span>',
                            ['class' => 'sz-account-menu__logout']
                        ) ?>
                        <?= Html::endForm() ?>
                    </div>
                </div>
                </div>
            </div>
        </header>

        <?php if (!$isDashboard && $topbarActions !== []): ?>
            <div class="sz-actions-bar">
                <div class="sz-actions-bar__inner">
                    <?= implode('', $topbarActions) ?>
                </div>
            </div>
        <?php endif; ?>

        <?php
        $flashes = Yii::$app->session->getAllFlashes(true);
        $typeMap = [
            'error' => 'danger',
            'danger' => 'danger',
            'warning' => 'warning',
            'success' => 'success',
            'info' => 'info',
        ];
        ?>
        <?php if (!empty($flashes)): ?>
            <div class="sz-toast-stack" data-sz-toast-stack>
                <?php foreach ($flashes as $type => $message): ?>
                    <?php $toastType = $typeMap[$type] ?? 'info'; ?>
                    <?php foreach ((array) $message as $m): ?>
                        <div class="sz-toast sz-toast--<?= Html::encode($toastType) ?>" role="status" data-sz-toast data-duration="2000">
                            <div class="sz-toast__icon">
                                <span class="material-symbols-rounded">
                                    <?php
                                    echo match ($toastType) {
                                        'success' => 'check_circle',
                                        'warning' => 'warning',
                                        'danger' => 'error',
                                        default => 'info',
                                    };
                                    ?>
                                </span>
                            </div>
                            <div class="sz-toast__body">
                                <strong class="sz-toast__title">
                                    <?= Html::encode(match ($toastType) {
                                        'success' => 'Готово',
                                        'warning' => 'Внимание',
                                        'danger' => 'Ошибка',
                                        default => 'Уведомление',
                                    }) ?>
                                </strong>
                                <div class="sz-toast__message"><?= Html::encode((string) $m) ?></div>
                                <div class="sz-toast__progress"><span class="sz-toast__progress-bar"></span></div>
                            </div>
                            <button type="button" class="sz-toast__close" data-sz-toast-close aria-label="Закрыть">
                                <span class="material-symbols-rounded">close</span>
                            </button>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?= $content ?>
    </div>
</main>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
