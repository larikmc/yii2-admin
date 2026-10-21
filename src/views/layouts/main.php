<?php

/** @var \yii\web\View $this */
/** @var string $content */

use larikmc\admin\assets\AppAsset;
use larikmc\admin\rbac\helpers\SystemRbacHelper;
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

$rawCurrentRoute = (string) (Yii::$app->controller->route ?? '');
$requestedRoute = (string) (Yii::$app->requestedRoute ?? '');
$adminModuleId = Yii::$app->controller->module ? (string) Yii::$app->controller->module->uniqueId : 'admin';

$normalizeRoute = static function (string $route) use ($adminModuleId): string {
    $r = trim($route, '/');
    if ($r === '') {
        return '';
    }
    if ($adminModuleId !== '' && (str_starts_with($r, $adminModuleId . '/') || $r === $adminModuleId)) {
        $r = (string) substr($r, strlen($adminModuleId));
        $r = trim($r, '/');
    } elseif (str_starts_with($r, 'admin/')) {
        $r = trim((string) substr($r, 6), '/');
    }
    if (str_starts_with($r, 'auth/auth/')) {
        $r = 'auth/' . substr($r, 10);
    }
    return $r;
};

$currentNorm = $normalizeRoute($rawCurrentRoute);
$requestedNorm = $normalizeRoute($requestedRoute);

$isActive = static function (array $item) use ($currentRoute, $rawCurrentRoute, $currentNorm, $requestedNorm, $normalizeRoute): bool {
    if (isset($item['active'])) {
        return (bool) (is_callable($item['active']) ? $item['active']() : $item['active']);
    }

    $url = $item['url'] ?? null;
    if (!is_array($url) || !isset($url[0])) {
        return false;
    }

    $itemRaw = (string) $url[0];
    if ($itemRaw === '' || $itemRaw === '#') {
        return false;
    }

    // Direct match with raw current route
    $itemSlash = '/' . ltrim($itemRaw, '/');
    if ($currentRoute === $itemSlash || '/' . ltrim($rawCurrentRoute, '/') === $itemSlash) {
        return true;
    }

    // Wildcard match (* at end)
    if (str_ends_with($itemRaw, '*')) {
        $prefix = rtrim($itemRaw, '*');
        $normPrefix = trim($normalizeRoute($prefix), '/');
        if ($normPrefix !== '' && (str_starts_with($currentNorm, $normPrefix) || str_starts_with($requestedNorm, $normPrefix))) {
            return true;
        }
        $rawPrefix = '/' . trim($prefix, '/');
        if (strlen($rawPrefix) > 1 && str_starts_with($currentRoute, $rawPrefix)) {
            return true;
        }
    }

    $itemNorm = $normalizeRoute($itemRaw);
    if ($itemNorm === '') {
        return false;
    }

    // Exact match after normalization
    if ($itemNorm === $currentNorm || ($requestedNorm !== '' && $itemNorm === $requestedNorm)) {
        return true;
    }

    // Special matching for built-in RBAC sub-controllers
    // Tabs like roles, permissions, assignments, users under RBAC item
    if (($itemNorm === 'rbac/default/index' || $itemNorm === 'rbac/default' || $itemNorm === 'rbac')
        && str_starts_with($currentNorm, 'rbac/')
        && !str_starts_with($currentNorm, 'rbac/invite')
    ) {
        return true;
    }

    // Special matching for Auth / Security Log
    if ($itemNorm === 'auth/security-log' && ($currentNorm === 'auth/security-log' || $currentNorm === 'auth/clear-security-log')) {
        return true;
    }

    // Controller-level match for CRUD actions (e.g. menu link is catalog/product/index, but user is on catalog/product/update)
    $itemParts = explode('/', $itemNorm);
    $currentParts = explode('/', $currentNorm);
    $itemAction = end($itemParts);
    if ($itemAction === 'index' && count($itemParts) === count($currentParts) && count($itemParts) >= 2) {
        array_pop($itemParts);
        $itemCtrl = implode('/', $itemParts);
        array_pop($currentParts);
        $currentCtrl = implode('/', $currentParts);
        if ($itemCtrl === $currentCtrl) {
            return true;
        }
    }

    return false;
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

$hasActiveChild = static function (array $item) use (&$hasActiveChild, $isActive): bool {
    foreach (($item['items'] ?? []) as $child) {
        if ($isActive($child) || $hasActiveChild($child)) {
            return true;
        }
    }

    return false;
};

$renderMenuItem = static function (array $item, bool $secondary = false) use ($isActive, $hasActiveChild, $isVisible, $module) {
    if (!$isVisible($item)) {
        return;
    }

    $url = $item['url'] ?? null;
    if (is_array($url) && ($url[0] ?? null) === '/admin/site/ui-kit') {
        if (!SystemRbacHelper::canAccessUiKit()) {
            return;
        }
    }

    $icon = $item['icon'] ?? 'radio_button_unchecked';
    $label = $item['label'] ?? 'Без названия';
    $items = array_values(array_filter($item['items'] ?? [], static fn(array $child): bool => $isVisible($child)));
    $isDropdown = !empty($items);
    $isCurrent = $isActive($item);
    $childIsActive = $hasActiveChild($item);
    $autoExpand = (bool) ($module->autoExpandActiveSubmenu ?? true);
    $isOpen = ($item['open'] ?? null) === true || ($autoExpand && $childIsActive);
    $itemClass = 'sz-nav__item nav-group'
        . ($isDropdown ? ' sz-dd' : '')
        . ($isOpen ? ' sz-dd--open' : '')
        . ($isCurrent ? ' sz-nav__item--active' : '')
        . ($childIsActive ? ' sz-dd--has-active-child' : '');

    if ($secondary) {
        $linkClass = 'kit sz-nav__link' . ($isCurrent ? ' is-current active' : '');
        echo '<li class="' . Html::encode($itemClass) . '">';
        echo Html::a(
            '<span class="kit-icon"><span class="material-symbols-rounded">' . Html::encode($icon) . '</span></span>' .
            '<span class="kit-copy"><b>' . Html::encode($label) . '</b><small>Bootstrap 5 · Темы</small></span>' .
            '<span class="material-symbols-rounded icon kit-arrow" style="margin-left:auto;font-size:16px;">chevron_right</span>',
            Url::to($url),
            ['class' => $linkClass, 'id' => 'kitButton']
        );
        echo '</li>';
        return;
    }

    echo '<li class="' . Html::encode($itemClass) . '"' . ($isDropdown ? ' data-sz-dd' : '') . '>';

    if ($isDropdown) {
        echo Html::a(
            '<span class="material-symbols-rounded icon nav-icon">' . Html::encode($icon) . '</span>' .
            '<span class="nav-label sz-nav__label"><span>' . Html::encode($label) . '</span></span>' .
            '<span class="material-symbols-rounded nav-chevron sz-dd__icon">keyboard_arrow_down</span>',
            '#',
            [
                'class' => 'nav-button sz-nav__link'
                    . ($isCurrent ? ' active' : '')
                    . ($isOpen ? ' expanded' : '')
                    . ($childIsActive ? ' has-active-child' : ''),
                'data-sz-dd-toggle' => true,
                'aria-expanded' => $isOpen ? 'true' : 'false',
                'title' => $label,
            ]
        );

        echo '<div class="subnav sz-submenu" data-sz-dd-menu' . ($isOpen ? ' style="height:auto"' : '') . '>';
        foreach ($items as $child) {
            $childActive = $isActive($child);
            echo Html::a(
                Html::encode($child['label'] ?? 'Без названия'),
                Url::to($child['url'] ?? '#'),
                ['class' => 'sz-submenu__link' . ($childActive ? ' selected active' : '')]
            );
        }
        echo '</div>';
        echo '</li>';
        return;
    }

    $url = $item['url'] ?? '#';
    $linkOptions = $item['linkOptions'] ?? [];
    $method = strtolower((string) ($item['method'] ?? 'get'));
    $linkClass = 'nav-button sz-nav__link' . ($isCurrent ? ' active' : '');

    if ($method === 'post') {
        echo Html::beginForm($url, 'post', ['class' => 'sz-logout__form']);
        echo Html::submitButton(
            '<span class="material-symbols-rounded icon nav-icon">' . Html::encode($icon) . '</span>' .
            '<span class="nav-label sz-nav__label"><span>' . Html::encode($label) . '</span></span>',
            array_merge(
                [
                    'class' => $linkClass . ' sz-logout__btn',
                    'encode' => false,
                    'title' => $label,
                ],
                $linkOptions
            )
        );
        echo Html::endForm();
    } else {
        echo Html::a(
            '<span class="material-symbols-rounded icon nav-icon">' . Html::encode($icon) . '</span>' .
            '<span class="nav-label sz-nav__label"><span>' . Html::encode($label) . '</span></span>',
            Url::to($url),
            array_merge(['class' => $linkClass, 'title' => $label], $linkOptions)
        );
    }

    echo '</li>';
};
$isSidebarCompact = (isset($_COOKIE['admin-sidebar-compact']) && $_COOKIE['admin-sidebar-compact'] === '1');
$isDarkTheme = (isset($_COOKIE['admin-theme']) && $_COOKIE['admin-theme'] === 'dark');
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="<?= $isDarkTheme ? 'dark' : '' ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <script>
        (function() {
            try {
                var theme = null;
                var cookieTheme = document.cookie.match(/(?:^|; )admin-theme=([^;]*)/);
                if (cookieTheme) {
                    theme = cookieTheme[1];
                } else {
                    theme = localStorage.getItem('admin-theme');
                }
                if (theme === 'dark') {
                    document.documentElement.classList.add('dark');
                } else if (theme === 'light') {
                    document.documentElement.classList.remove('dark');
                }

                var isCompact = false;
                var m = document.cookie.match(/(?:^|; )admin-sidebar-compact=([^;]*)/);
                if (m) {
                    isCompact = m[1] === '1';
                } else {
                    var s = localStorage.getItem('admin-sidebar-compact');
                    isCompact = (s === '1' || (s === null && window.innerWidth <= 830));
                }
                if (isCompact) {
                    document.documentElement.classList.add('sz-preload-compact');
                }
            } catch(e) {}
        })();
    </script>
    <?php $this->head() ?>
</head>

<body class="sz-admin-body d-flex flex-column h-100 <?= $isDarkTheme ? 'dark ' : '' ?><?= $isSidebarCompact ? 'sz-sidebar-is-collapsed' : 'sz-sidebar-is-open' ?>">
<?php $this->beginBody() ?>

<div class="sz-shell-glow sz-shell-glow--violet"></div>
<div class="sz-shell-glow sz-shell-glow--gold"></div>
<div class="sz-mobile-backdrop"></div>

<div class="app <?= $isSidebarCompact ? 'sidebar-small' : '' ?>" id="app">
    <aside class="sidebar sz-sidebar <?= $isSidebarCompact ? 'compact sz-sidebar--collapsed' : '' ?>" id="sidebar" aria-label="Боковое меню">
        <div class="brand sz-sidebar__header">
            <a href="<?= Yii::$app->homeUrl ?>" class="brand-logo sz-sidebar__brand-logo" aria-label="Логотип" style="text-decoration:none;">
                <span class="brand-letter sz-sidebar__brand-letter">A</span>
            </a>
            <a href="<?= Yii::$app->homeUrl ?>" class="brand-copy sz-sidebar__brand-copy" style="text-decoration:none;">
                <strong>ADMIN PANEL</strong>
            </a>

            <button type="button" class="collapse show sz-sidebar__toggler" id="collapse" aria-label="Свернуть меню" aria-expanded="<?= $isSidebarCompact ? 'false' : 'true' ?>">
                <span class="material-symbols-rounded">chevron_left</span>
            </button>
        </div>

        <nav class="nav sz-sidebar__nav" id="nav" aria-label="Разделы панели">
            <ul class="sz-nav sz-nav--primary">
                <?php foreach ($primaryMenu as $item): ?>
                    <?php $renderMenuItem($item); ?>
                <?php endforeach; ?>
            </ul>
        </nav>

        <div class="sidebar-bottom">
            <ul class="sz-nav sz-nav--secondary">
                <?php foreach ($secondaryMenu as $item): ?>
                    <?php $renderMenuItem($item, true); ?>
                <?php endforeach; ?>
            </ul>
        </div>
    </aside>

    <main class="main sz-content">
        <div class="sz-content__inner">
            <header class="topbar">
                <div class="welcome">
                    <h1 id="topTitle"><?= Html::encode($pageTitle) ?></h1>
                    <?php $pageSubtitle = $this->params['pageSubtitle'] ?? ($isDashboard ? 'Панель управления контент-платформой' : null); ?>
                <?php if ($pageSubtitle): ?>
                    <p id="topSubtitle"><?= Html::encode($pageSubtitle) ?></p>
                <?php endif; ?>
            </div>

            <div class="tools">
                <?= Html::a(
                    '<span class="material-symbols-rounded">language</span>',
                    '/',
                    [
                        'class' => 'toolbtn',
                        'title' => 'Перейти на сайт',
                        'aria-label' => 'Перейти на сайт',
                        'target' => '_blank',
                        'rel' => 'noopener',
                    ]
                ) ?>

                <button class="toolbtn" id="themeButton" type="button" data-sz-theme-toggle title="<?= $isDarkTheme ? 'Светлая тема' : 'Темная тема' ?>" aria-label="<?= $isDarkTheme ? 'Светлая тема' : 'Темная тема' ?>">
                    <span class="material-symbols-rounded" id="themeIcon"><?= $isDarkTheme ? 'light_mode' : 'dark_mode' ?></span>
                </button>

                <button class="toolbtn" id="widthButton" type="button" data-sz-width-toggle title="По всей ширине" aria-label="По всей ширине экрана">
                    <span class="material-symbols-rounded" id="widthIcon">width_full</span>
                </button>

                <div class="toolwrap">
                    <button class="toolbtn" id="bellButton" type="button" aria-label="Уведомления" aria-expanded="false" data-sz-pop-toggle="bellPop">
                        <span class="material-symbols-rounded">notifications</span>
                        <span class="red-dot"></span>
                    </button>
                    <div class="pop" id="bellPop" hidden>
                        <b>Уведомления</b>
                        <p>События безопасности и системные логи Yii2.</p>
                    </div>
                </div>

                <div class="toolwrap">
                    <button class="profile" type="button" id="profileButton" aria-expanded="false" data-sz-pop-toggle="profilePop">
                        <span class="avatar"><?= Html::encode(mb_strtoupper(mb_substr((string) $userName, 0, 1))) ?></span>
                        <span class="profile-copy">
                            <strong><?= Html::encode($userName) ?></strong>
                            <?php if ($userEmail): ?>
                                <small><?= Html::encode($userEmail) ?></small>
                            <?php endif; ?>
                        </span>
                        <span class="material-symbols-rounded" style="margin-left:auto;font-size:16px;">keyboard_arrow_down</span>
                    </button>
                    <div class="pop" id="profilePop" hidden>
                        <?= Html::a(
                            'Настройки профиля',
                            ['/admin/user/profile'],
                            ['class' => 'sz-pop-item']
                        ) ?>
                        <?= Html::beginForm(['/auth/logout'], 'post', ['class' => 'sz-logout-form', 'style' => 'margin:0;padding:0;']) ?>
                        <?= Html::submitButton(
                            'Выйти',
                            ['class' => 'sz-pop-item sz-pop-item--logout']
                        ) ?>
                        <?= Html::endForm() ?>
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
                    <?php
                    if (!isset($typeMap[$type])) {
                        continue;
                    }
                    $toastType = $typeMap[$type];
                    ?>
                    <?php foreach ((array) $message as $m): ?>
                        <?php if (!is_scalar($m) || trim((string) $m) === '') continue; ?>
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
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
