<?php

use larikmc\admin\widgets\AdminPage;
use yii\bootstrap5\Html;

$this->title = 'Обзор RBAC';

$content = '<div class="sz-stat-grid">'
    . '<div class="sz-stat-card"><div class="sz-stat-card__label">Роли</div><div class="sz-stat-card__value">' . (int) $report['counts']['roles'] . '</div></div>'
    . '<div class="sz-stat-card"><div class="sz-stat-card__label">Действия</div><div class="sz-stat-card__value">' . (int) $report['counts']['permissions'] . '</div></div>'
    . '<div class="sz-stat-card"><div class="sz-stat-card__label">Назначения</div><div class="sz-stat-card__value">' . (int) $report['counts']['assignments'] . '</div></div>'
    . '<div class="sz-stat-card"><div class="sz-stat-card__label">Пользователи</div><div class="sz-stat-card__value">' . (int) $report['counts']['users'] . '</div></div>'
    . '</div>';

echo AdminPage::widget([
    'title' => $this->title,
    'tabs' => $this->params['rbacTabs'] ?? [],
    'boxed' => false,
    'content' => $content,
]);
