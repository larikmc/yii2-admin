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

    $url = $item['url'] ?? null;
    if (is_array($url) && ($url[0] ?? null) === '/admin/site/ui-kit') {
        $identity = Yii::$app->user->identity;
        if ($identity === null || (string) $identity->getId() !== SystemRbacHelper::ROOT_USER_ID) {
            return;
        }
    }

    $icon = $item['icon'] ?? 'radio_button_unchecked';
    $label = $item['label'] ?? 'Без названия';
    $items = array_values(array_filter($item['items'] ?? [], static fn(array $child): bool => $isVisible($child)));
    $isDropdown = !empty($items);
    $isCurrent = $isActive($item);
    $isOpen = $hasActiveChild($item);
    $itemClass = 'sz-nav__item nav-group'
        . ($isDropdown ? ' sz-dd' : '')
        . ($isOpen ? ' sz-dd--open' : '')
        . ($isCurrent ? ' sz-nav__item--active' : '');

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
                'class' => 'nav-button sz-nav__link' . ($isCurrent ? ' active' : '') . ($isOpen ? ' expanded' : ''),
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
                ['class' => 'sz-submenu__link' . ($childActive ? ' selected' : '')]
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
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <script>
        (function() {
            try {
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

<body class="sz-admin-body d-flex flex-column h-100 <?= $isSidebarCompact ? 'sz-sidebar-is-collapsed' : 'sz-sidebar-is-open' ?>">
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

                <button class="toolbtn" id="themeButton" type="button" data-sz-theme-toggle title="Переключить тему" aria-label="Переключить тему">
                    <span class="material-symbols-rounded" id="themeIcon">dark_mode</span>
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
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
