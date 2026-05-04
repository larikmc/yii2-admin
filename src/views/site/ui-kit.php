<?php

use yii\bootstrap5\Html;
use yii\web\View;

$this->title = 'ADMIN-UI-KIT';
$this->params['breadcrumbs'] = [
    ['label' => 'ADMIN-UI-KIT'],
];

$module = Yii::$app->getModule('admin');
$placeholder = $module->getLazyloadPlaceholderUrl($this);
$asset = \larikmc\admin\assets\AppAsset::register($this);
$demoThumb = $asset->baseUrl . '/img/template.jpg';
$demoOriginal = $asset->baseUrl . '/img/template.jpg';
$popupHtmlExample = <<<HTML
<a class="sz-thumb sz-thumb--lg" href="{$demoOriginal}" data-pjax="0" data-image-viewer data-image-full="{$demoOriginal}" data-image-title="Демо popup">
    <img class="sz-thumb__img" src="{$placeholder}" data-src="{$demoThumb}" loading="lazy" decoding="async" alt="">
</a>
HTML;
$popupPhpExample = <<<'PHP'
$placeholder = Yii::$app->getModule('admin')->getLazyloadPlaceholderUrl($this);

echo Html::a(
    Html::img($placeholder, [
        'data-src' => $thumbUrl,
        'class' => 'sz-thumb__img',
        'loading' => 'lazy',
        'decoding' => 'async',
        'alt' => '',
    ]),
    $originalUrl,
    [
        'data-pjax' => '0',
        'data-image-viewer' => true,
        'data-image-full' => $originalUrl,
        'data-image-title' => 'Оригинал #' . $model->id,
        'class' => 'sz-thumb sz-thumb--lg',
    ]
);
PHP;
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
                <p class="sz-ui-kit-section-label">Progress</p>
                <h3 class="sz-ui-kit-section-title">Прогресс выполнения задачи</h3>
                <p class="sz-ui-kit-subtitle">Готовый шаблон для long-running задач (как sitemap update): статус + бар + шаги.</p>
                <div class="sz-progress-job" data-demo-progress data-total="5">
                    <div class="sz-progress-job__status" data-demo-progress-status>
                        <span class="sz-progress-job__icon">
                            <span class="material-symbols-rounded is-spinning" data-demo-progress-icon>sync</span>
                        </span>
                        <span class="sz-progress-job__text" data-demo-progress-text>Ожидание запуска.</span>
                    </div>
                    <div class="sz-progress-job__track">
                        <div class="sz-progress-job__bar-wrap" aria-hidden="true">
                            <div class="sz-progress-job__bar" data-demo-progress-bar role="progressbar" style="width: 0%">0%</div>
                        </div>
                        <div class="sz-progress-job__meta">
                            <span data-demo-progress-step>Шаг 0 из 5</span>
                            <span data-demo-progress-meta>0%</span>
                        </div>
                    </div>
                    <div class="sz-ui-kit-actions">
                        <?= Html::button('Запустить демо', ['class' => 'btn btn-primary', 'type' => 'button', 'data-demo-progress-run' => true]) ?>
                        <?= Html::button('Сбросить', ['class' => 'btn btn-outline-secondary', 'type' => 'button', 'data-demo-progress-reset' => true]) ?>
                    </div>
                </div>
            </section>

            <section class="sz-panel sz-ui-kit-panel sz-ui-kit-panel--span-3">
                <p class="sz-ui-kit-section-label">Images</p>
                <h3 class="sz-ui-kit-section-title">Popup + lazyload</h3>
                <p class="sz-ui-kit-subtitle">Миниатюра грузится через <code>data-src</code>, а popup открывает оригинал из <code>data-image-full</code>.</p>

                <div class="sz-ui-kit-image-demo">
                    <div class="sz-ui-kit-image-demo__preview">
                        <?= Html::a(
                            Html::img($placeholder, [
                                'data-src' => $demoThumb,
                                'class' => 'sz-thumb__img',
                                'alt' => 'Демо lazyload и popup',
                                'loading' => 'lazy',
                                'decoding' => 'async',
                            ]),
                            $demoOriginal,
                            [
                                'data-pjax' => '0',
                                'data-image-viewer' => true,
                                'data-image-full' => $demoOriginal,
                                'data-image-title' => 'Демо popup: оригинальное изображение',
                                'class' => 'sz-thumb sz-thumb--lg',
                            ]
                        ) ?>
                        <div class="sz-ui-kit-image-demo__meta">
                            <strong>Живой пример</strong>
                            <span>Нажмите на миниатюру, чтобы открыть popup.</span>
                        </div>
                    </div>

                    <div class="sz-ui-kit-code-grid">
                        <div>
                            <p class="sz-ui-kit-subtitle">HTML-паттерн</p>
                            <pre class="sz-ui-kit-code"><code><?= Html::encode($popupHtmlExample) ?></code></pre>
                        </div>
                        <div>
                            <p class="sz-ui-kit-subtitle">Yii/PHP-паттерн</p>
                            <pre class="sz-ui-kit-code"><code><?= Html::encode($popupPhpExample) ?></code></pre>
                        </div>
                    </div>
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

<?php
$this->registerJs(<<<JS
(function () {
    const root = document.querySelector('[data-demo-progress]');
    if (!root) {
        return;
    }

    const total = Number(root.getAttribute('data-total')) || 5;
    const status = root.querySelector('[data-demo-progress-status]');
    const icon = root.querySelector('[data-demo-progress-icon]');
    const text = root.querySelector('[data-demo-progress-text]');
    const bar = root.querySelector('[data-demo-progress-bar]');
    const stepLabel = root.querySelector('[data-demo-progress-step]');
    const meta = root.querySelector('[data-demo-progress-meta]');
    const runButton = root.querySelector('[data-demo-progress-run]');
    const resetButton = root.querySelector('[data-demo-progress-reset]');

    let processed = 0;
    let timer = null;
    let inProgress = false;

    function render(state) {
        const percent = Math.max(0, Math.min(100, Math.round((processed / total) * 100)));

        bar.style.width = percent + '%';
        bar.textContent = percent + '%';
        meta.textContent = percent + '%';
        stepLabel.textContent = 'Шаг ' + processed + ' из ' + total;

        status.className = 'sz-progress-job__status';
        icon.classList.remove('is-spinning');

        if (state === 'idle') {
            text.textContent = 'Ожидание запуска.';
            icon.textContent = 'sync';
            icon.classList.add('is-spinning');
        } else if (state === 'running') {
            text.textContent = 'Выполняется задача. Не закрывайте вкладку.';
            icon.textContent = 'sync';
            icon.classList.add('is-spinning');
        } else if (state === 'success') {
            status.classList.add('sz-progress-job__status--success');
            text.textContent = 'Задача выполнена успешно.';
            icon.textContent = 'check_circle';
        } else if (state === 'error') {
            status.classList.add('sz-progress-job__status--error');
            text.textContent = 'Ошибка выполнения задачи.';
            icon.textContent = 'error';
        }
    }

    function stop() {
        if (timer) {
            clearTimeout(timer);
            timer = null;
        }
        inProgress = false;
    }

    function run() {
        if (inProgress) {
            return;
        }

        inProgress = true;
        processed = 0;
        render('running');

        const tick = function () {
            if (!inProgress) {
                return;
            }

            processed += 1;

            if (processed >= total) {
                processed = total;
                render('success');
                stop();
                return;
            }

            render('running');
            timer = setTimeout(tick, 350);
        };

        timer = setTimeout(tick, 350);
    }

    runButton.addEventListener('click', run);
    resetButton.addEventListener('click', function () {
        stop();
        processed = 0;
        render('idle');
    });

    render('idle');
})();
JS, View::POS_READY);
?>
