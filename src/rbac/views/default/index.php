<?php

use larikmc\admin\widgets\AdminPage;
use yii\bootstrap5\Html;

$this->title = 'Обзор RBAC';
$this->params['breadcrumbs'] = [
    ['label' => 'Администрирование'],
    ['label' => 'RBAC'],
];

$cards = [
    [
        'title' => 'Роли',
        'value' => (int) $report['counts']['roles'],
        'icon' => 'admin_panel_settings',
        'class' => 'hero-blue',
    ],
    [
        'title' => 'Действия',
        'value' => (int) $report['counts']['permissions'],
        'icon' => 'key',
        'class' => 'hero-mint',
    ],
    [
        'title' => 'Назначения',
        'value' => (int) $report['counts']['assignments'],
        'icon' => 'assignment_ind',
        'class' => 'hero-peach',
    ],
    [
        'title' => 'Пользователи',
        'value' => (int) $report['counts']['users'],
        'icon' => 'group',
        'class' => 'hero-violet',
    ],
];

$cardsHtml = [];
foreach ($cards as $c) {
    $cardsHtml[] = '<article class="hero-card ' . Html::encode($c['class']) . ' sz-stat-card">'
        . '<span class="overline">' . Html::encode($c['title']) . '</span>'
        . '<strong>' . $c['value'] . '</strong>'
        . '<span class="hero-ico"><span class="material-symbols-rounded">' . Html::encode($c['icon']) . '</span></span>'
        . '</article>';
}

$content = '<div class="sz-stat-grid">' . implode('', $cardsHtml) . '</div>';

echo AdminPage::widget([
    'title' => $this->title,
    'tabs' => $this->params['rbacTabs'] ?? [],
    'showHeader' => false,
    'boxed' => false,
    'content' => $content,
]);
