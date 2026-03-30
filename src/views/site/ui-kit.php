<?php

use yii\bootstrap5\Html;

$this->title = 'ADMIN-UI-KIT';
$this->params['breadcrumbs'] = [
    ['label' => 'ADMIN-UI-KIT'],
];
?>

<section class="sz-page">
    <div class="sz-page__body">
        <div class="sz-ui-kit-grid">
            <section class="sz-panel sz-ui-kit-panel sz-ui-kit-panel--span-2">
                <p class="sz-ui-kit-section-label">Buttons</p>
                <h3 class="sz-ui-kit-section-title">Кнопки</h3>
                <div class="sz-ui-kit-button-group">
                    <p class="sz-ui-kit-subtitle">Bootstrap-классы в новой палитре</p>
                    <div class="sz-ui-kit-actions">
                        <?= Html::a('Primary', '#', ['class' => 'btn btn-primary']) ?>
                        <?= Html::a('Secondary', '#', ['class' => 'btn btn-secondary']) ?>
                        <?= Html::a('Success', '#', ['class' => 'btn btn-success']) ?>
                        <?= Html::a('Danger', '#', ['class' => 'btn btn-danger']) ?>
                        <?= Html::a('Warning', '#', ['class' => 'btn btn-warning']) ?>
                        <?= Html::a('Info', '#', ['class' => 'btn btn-info']) ?>
                        <?= Html::a('Light', '#', ['class' => 'btn btn-light']) ?>
                    </div>
                </div>

                <div class="sz-ui-kit-button-group">
                    <p class="sz-ui-kit-subtitle">Outline buttons</p>
                    <div class="sz-ui-kit-actions">
                        <?= Html::a('Outline Primary', '#', ['class' => 'btn btn-outline-primary']) ?>
                        <?= Html::a('Outline Secondary', '#', ['class' => 'btn btn-outline-secondary']) ?>
                        <?= Html::a('Outline Success', '#', ['class' => 'btn btn-outline-success']) ?>
                        <?= Html::a('Outline Danger', '#', ['class' => 'btn btn-outline-danger']) ?>
                        <?= Html::a('Outline Warning', '#', ['class' => 'btn btn-outline-warning']) ?>
                        <?= Html::a('Outline Info', '#', ['class' => 'btn btn-outline-info']) ?>
                        <?= Html::a('Outline Light', '#', ['class' => 'btn btn-outline-light']) ?>
                    </div>
                </div>
            </section>

            <section class="sz-panel sz-ui-kit-panel">
                <p class="sz-ui-kit-section-label">Badges</p>
                <h3 class="sz-ui-kit-section-title">Bootstrap badges</h3>
                <div class="sz-ui-kit-actions">
                    <span class="badge text-bg-primary">Primary</span>
                    <span class="badge text-bg-secondary">Secondary</span>
                    <span class="badge text-bg-success">Success</span>
                    <span class="badge text-bg-danger">Danger</span>
                    <span class="badge text-bg-warning">Warning</span>
                    <span class="badge text-bg-info">Info</span>
                    <span class="badge text-bg-light">Light</span>
                    <span class="badge text-bg-dark">Dark</span>
                </div>
            </section>

            <section class="sz-panel sz-ui-kit-panel">
                <p class="sz-ui-kit-section-label">Cards</p>
                <h3 class="sz-ui-kit-section-title">Карточка метрики</h3>
                <p class="home-stat-card__label">Свободное место</p>
                <p class="home-stat-card__value">114.2 GB</p>
                <span class="badge text-bg-success">Стабильно</span>
            </section>

            <section class="sz-panel sz-ui-kit-panel">
                <p class="sz-ui-kit-section-label">Action Column</p>
                <h3 class="sz-ui-kit-section-title">Групповые actions</h3>
                <div class="grid-view sz-ui-kit-action-demo">
                    <div class="action-column">
                        <?= Html::a('<span class="material-symbols-rounded">visibility</span>', '#', ['class' => 'sz-row-action', 'aria-label' => 'Просмотр']) ?>
                        <?= Html::a('<span class="material-symbols-rounded">edit</span>', '#', ['class' => 'sz-row-action', 'aria-label' => 'Редактировать']) ?>
                        <?= Html::a('<span class="material-symbols-rounded">delete</span>', '#', ['class' => 'sz-row-action', 'data-method' => 'post', 'aria-label' => 'Удалить']) ?>
                    </div>
                </div>
            </section>

            <section class="sz-panel sz-ui-kit-panel">
                <p class="sz-ui-kit-section-label">GridView</p>
                <h3 class="sz-ui-kit-section-title">Pagination</h3>
                <div class="sz-ui-kit-pagination-demo">
                    <ul class="pagination"><li class="prev disabled"><span>«</span></li>
<li class="active"><a href="/admin/product/index?page=1" data-page="0">1</a></li>
<li><a href="/admin/product/index?page=2" data-page="1">2</a></li>
<li><a href="/admin/product/index?page=3" data-page="2">3</a></li>
<li><a href="/admin/product/index?page=4" data-page="3">4</a></li>
<li class="next"><a href="/admin/product/index?page=2" data-page="1">»</a></li></ul>
                </div>
            </section>

            <section class="sz-panel sz-ui-kit-panel sz-ui-kit-panel--span-2">
                <p class="sz-ui-kit-section-label">Surface</p>
                <h3 class="sz-ui-kit-section-title">Типовая панель</h3>
                <div class="sz-ui-kit-list">
                    <div class="sz-ui-kit-list__row">
                        <span class="sz-ui-kit-list__key">Topbar</span>
                        <span class="sz-ui-kit-list__value">Breadcrumbs + title + utility cluster</span>
                    </div>
                    <div class="sz-ui-kit-list__row">
                        <span class="sz-ui-kit-list__key">Sidebar</span>
                        <span class="sz-ui-kit-list__value">Dark shell with active state and secondary menu</span>
                    </div>
                    <div class="sz-ui-kit-list__row">
                        <span class="sz-ui-kit-list__key">Tables</span>
                        <span class="sz-ui-kit-list__value">Rounded surfaces, compact filters, grouped actions</span>
                    </div>
                    <div class="sz-ui-kit-list__row">
                        <span class="sz-ui-kit-list__key">Feedback</span>
                        <span class="sz-ui-kit-list__value">Toast notifications instead of default alerts</span>
                    </div>
                </div>
            </section>
        </div>
    </div>
</section>
