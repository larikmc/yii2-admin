<?php

use yii\bootstrap5\Html;
use yii\web\View;

$this->title = 'ADMIN-UI-KIT';
$this->params['breadcrumbs'] = [
    ['label' => 'ADMIN-UI-KIT'],
];
$this->params['pageSubtitle'] = 'Дизайн-система панели управления · Спецификация компонентов · Светлая и тёмная темы';

$module = Yii::$app->getModule('admin');
$placeholder = $module ? $module->getLazyloadPlaceholderUrl($this) : "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 106 106'%3E%3Crect width='106' height='106' fill='%23f1f5fa'/%3E%3C/svg%3E";
$asset = \larikmc\admin\assets\AppAsset::register($this);
$demoLocalImg = $asset->baseUrl . '/img/template.jpg';
$demoPlaceholder = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 106 106'%3E%3Crect width='106' height='106' fill='%23f1f5fa'/%3E%3Cpath d='M35 70l14-16 11 12 11-14 15 18H35z' fill='%23cad8eb'/%3E%3Ccircle cx='45' cy='42' r='6' fill='%23cad8eb'/%3E%3C/svg%3E";
?>

<div id="uiKitPage" class="ui-kit-wrapper">

  <!-- =========================================================================
       HERO: ШАПКА UI-KIT С ПЕРЕКЛЮЧАТЕЛЕМ ТЕМ (LIGHT / DARK)
       ========================================================================= -->
  <div class="sz-panel ui-kit-hero">
    <div class="ui-kit-hero__header">
      <div class="ui-kit-hero__titles">
        <div class="ui-kit-badges">
          <span class="badge text-bg-primary">Design System v2.1</span>
          <span class="badge text-bg-secondary">Bootstrap 5.3</span>
          <span class="badge badge-soft-success">2 Темы оформления</span>
        </div>
        <h1 class="ui-kit-hero__h1">Библиотека компонентов (UI-Kit)</h1>
        <p class="ui-kit-hero__lead">
          Авторитетный визуальный стандарт интерфейсов панели управления: адаптивные токены, интерактивные состояния, сетки и готовые паттерны в светлой и тёмной темах.
        </p>
      </div>

      <!-- ИНТЕРАКТИВНЫЙ ПЕРЕКЛЮЧАТЕЛЬ ТЕМЫ (LIGHT / DARK) -->
      <div class="ui-kit-theme-card">
        <div class="ui-kit-theme-card__label">
          <span class="material-symbols-rounded">palette</span>
          <span>Режим интерфейса</span>
        </div>
        <div class="ui-kit-theme-toggle" role="group" aria-label="Переключение темы оформления">
          <button type="button" class="ui-kit-theme-btn" id="btnThemeLight" data-theme-set="light">
            <span class="material-symbols-rounded">light_mode</span>
            <span>Светлая</span>
          </button>
          <button type="button" class="ui-kit-theme-btn" id="btnThemeDark" data-theme-set="dark">
            <span class="material-symbols-rounded">dark_mode</span>
            <span>Тёмная</span>
          </button>
        </div>
        <div class="ui-kit-theme-status" id="themeStatusText">
          Текущая тема: <strong id="currentThemeLabel">Светлая</strong>
        </div>
      </div>
    </div>

    <!-- БЫСТРАЯ НАВИГАЦИЯ ПО РАЗДЕЛАМ UI-KIT -->
    <div class="ui-kit-quicknav">
      <span class="ui-kit-quicknav__label">Разделы:</span>
      <div class="ui-kit-quicknav__links">
        <a href="#sec-media" class="ui-kit-nav-pill">Медиа & Popup</a>
        <a href="#sec-tokens" class="ui-kit-nav-pill">Токены & Цвета</a>
        <a href="#sec-buttons" class="ui-kit-nav-pill">Кнопки</a>
        <a href="#sec-toggles" class="ui-kit-nav-pill">Переключатели</a>
        <a href="#sec-badges" class="ui-kit-nav-pill">Бейджи</a>
        <a href="#sec-tabs" class="ui-kit-nav-pill">Вкладки</a>
        <a href="#sec-forms" class="ui-kit-nav-pill">Формы</a>
        <a href="#sec-table" class="ui-kit-nav-pill">Таблица (GridView)</a>
        <a href="#sec-jobs" class="ui-kit-nav-pill">Очереди</a>
        <a href="#sec-feedback" class="ui-kit-nav-pill">Алерты & Модалки</a>
      </div>
    </div>
  </div>

  <!-- =========================================================================
       СЕКЦИЯ 1: МЕДИА, POPUP + LAZYLOAD (ЖИВОЙ ПРИМЕР)
       ========================================================================= -->
  <div id="sec-media" class="sz-panel ui-kit-section">
    <div class="ui-kit-section__head">
      <div>
        <span class="sz-eyebrow">Images & Viewer</span>
        <h2>Popup + lazyload (Изображения)</h2>
        <p class="text-muted">
          Миниатюра грузится через <code>data-src</code>, а полноэкранный popup открывает оригинал из <code>data-image-full</code>.
        </p>
      </div>
      <span class="badge badge-soft-primary">image-viewer-admin.js</span>
    </div>

    <!-- Живой пример с кликабельной миниатюрой и ссылкой -->
    <div class="sz-thumb-preview-wrap">
      <a class="sz-thumb sz-thumb--lg" href="<?= Html::encode($demoLocalImg) ?>" data-pjax="0" data-image-viewer="true" data-image-full="<?= Html::encode($demoLocalImg) ?>" data-image-title="Демо popup: оригинальное изображение">
        <img class="sz-thumb__img" src="<?= Html::encode($demoLocalImg) ?>" alt="Миниатюра">
      </a>
      <div>
        <strong style="display:block;font-size:15px;margin-bottom:4px;">
          <a href="<?= Html::encode($demoLocalImg) ?>" data-pjax="0" data-image-viewer="true" data-image-full="<?= Html::encode($demoLocalImg) ?>" data-image-title="Демо popup: оригинальное изображение" style="color:var(--sz-text);text-decoration:none;cursor:pointer;">
            Живой пример <span class="material-symbols-rounded" style="font-size:16px;vertical-align:middle;color:var(--sz-primary);">open_in_new</span>
          </a>
        </strong>
        <div style="color:var(--sz-muted);font-size:12.5px;">
          <a href="<?= Html::encode($demoLocalImg) ?>" data-pjax="0" data-image-viewer="true" data-image-full="<?= Html::encode($demoLocalImg) ?>" data-image-title="Демо popup: оригинальное изображение" class="text-primary text-decoration-none">
            Нажмите на миниатюру или сюда, чтобы открыть popup.
          </a>
        </div>
      </div>
    </div>

    <!-- Сетка паттернов кода (HTML и Yii/PHP) -->
    <div class="row g-3" style="margin-top:4px;">
      <div class="col-lg-6">
        <div class="sz-code-block-title">HTML-паттерн</div>
        <pre class="sz-code-block"><code>&lt;a class="sz-thumb sz-thumb--lg" href="/admin/assets/.../img/template.jpg"
   data-pjax="0" data-image-viewer="true"
   data-image-full="/admin/assets/.../img/template.jpg"
   data-image-title="Демо popup: оригинальное изображение"&gt;
    &lt;img class="sz-thumb__img" src="/img/load.svg"
         data-src="/admin/assets/.../img/template.jpg" alt="Миниатюра"&gt;
&lt;/a&gt;</code></pre>
      </div>
      <div class="col-lg-6">
        <div class="sz-code-block-title">Yii/PHP-паттерн</div>
        <pre class="sz-code-block"><code>$placeholder = Yii::$app-&gt;getModule('admin')-&gt;getLazyloadPlaceholderUrl($this);

echo Html::a(
    Html::img($placeholder, [
        'data-src' =&gt; $thumbUrl,
        'class' =&gt; 'sz-thumb__img',
        'loading' =&gt; 'lazy',
        'decoding' =&gt; 'async',
        'alt' =&gt; '',
    ]),
    $originalUrl,
    [
        'data-pjax' =&gt; '0',
        'data-image-viewer' =&gt; true,
        'data-image-full' =&gt; $originalUrl,
        'data-image-title' =&gt; 'Оригинал #' . $model-&gt;id,
        'class' =&gt; 'sz-thumb sz-thumb--lg',
    ]
);</code></pre>
      </div>
    </div>
  </div>

  <!-- =========================================================================
       СЕКЦИЯ 2: ЦВЕТОВАЯ ПАЛИТРА И ДИЗАЙН-ТОКЕНЫ
       ========================================================================= -->
  <div id="sec-tokens" class="sz-panel ui-kit-section">
    <div class="ui-kit-section__head">
      <div>
        <span class="sz-eyebrow">Design Tokens</span>
        <h2>Цветовая палитра и переменные темы</h2>
        <p class="text-muted">
          CSS-токены гарантируют автоматическую адаптацию компонентов при переключении между светлой и тёмной темами без дублирования правил.
        </p>
      </div>
      <span class="badge text-bg-secondary">:root / html.dark</span>
    </div>

    <div class="ui-kit-swatches-grid">
      <!-- Primary -->
      <div class="ui-kit-swatch-card" data-copy="var(--sz-primary)">
        <div class="ui-kit-swatch-color" style="background:#3189f7;"></div>
        <div class="ui-kit-swatch-info">
          <strong>Primary Accent</strong>
          <code>--sz-primary</code>
          <small>#3189f7 · Основные действия, кнопки, фокус</small>
        </div>
      </div>

      <!-- Success -->
      <div class="ui-kit-swatch-card" data-copy="var(--sz-success)">
        <div class="ui-kit-swatch-color" style="background:#079b70;"></div>
        <div class="ui-kit-swatch-info">
          <strong>Success Color</strong>
          <code>--sz-success</code>
          <small>#079b70 · Успешные операции, бейджи, кнопки</small>
        </div>
      </div>

      <!-- Danger -->
      <div class="ui-kit-swatch-card" data-copy="var(--sz-danger)">
        <div class="ui-kit-swatch-color" style="background:#dc3545;"></div>
        <div class="ui-kit-swatch-info">
          <strong>Danger / Error</strong>
          <code>--sz-danger</code>
          <small>#dc3545 · Удаление, предупреждения, ошибки</small>
        </div>
      </div>

      <!-- Warning -->
      <div class="ui-kit-swatch-card" data-copy="var(--sz-warning)">
        <div class="ui-kit-swatch-color" style="background:#f59e0b;"></div>
        <div class="ui-kit-swatch-info">
          <strong>Warning Color</strong>
          <code>--sz-warning</code>
          <small>#f59e0b · Ожидание, модерация, уведомления</small>
        </div>
      </div>

      <!-- Info -->
      <div class="ui-kit-swatch-card" data-copy="var(--sz-info)">
        <div class="ui-kit-swatch-color" style="background:#0ea5e9;"></div>
        <div class="ui-kit-swatch-info">
          <strong>Info Accent</strong>
          <code>--sz-info</code>
          <small>#0ea5e9 · Подсказки, справочная информация</small>
        </div>
      </div>

      <!-- Surface Background -->
      <div class="ui-kit-swatch-card" data-copy="var(--surface)">
        <div class="ui-kit-swatch-color" style="background:#ffffff;border:1px solid #d7e5fa;"></div>
        <div class="ui-kit-swatch-info">
          <strong>Surface / Panel</strong>
          <code>--surface</code>
          <small>#ffffff / #162235 (dark)</small>
        </div>
      </div>

      <!-- Stroke / Border -->
      <div class="ui-kit-swatch-card" data-copy="var(--stroke)">
        <div class="ui-kit-swatch-color" style="background:#e7edfb;"></div>
        <div class="ui-kit-swatch-info">
          <strong>Stroke / Border</strong>
          <code>--stroke</code>
          <small>#e7edfb / #2a3c54 (dark)</small>
        </div>
      </div>

      <!-- Text / Foreground -->
      <div class="ui-kit-swatch-card" data-copy="var(--text)">
        <div class="ui-kit-swatch-color" style="background:#13233a;"></div>
        <div class="ui-kit-swatch-info">
          <strong>Text / Primary</strong>
          <code>--text</code>
          <small>#13233a / #eef4ff (dark)</small>
        </div>
      </div>
    </div>
  </div>

  <!-- =========================================================================
       СЕКЦИЯ 3: КНОПКИ И ГРУППА ДЕЙСТВИЙ (ActionColumn)
       ========================================================================= -->
  <div id="sec-buttons" class="sz-panel ui-kit-section">
    <div class="ui-kit-section__head">
      <div>
        <span class="sz-eyebrow">Buttons & States</span>
        <h2>Кнопки, размеры и компактные действия в таблицах</h2>
        <p class="text-muted">
          Сплошные кнопки со свечением, контурные варианты и монолитная группа действий <code>.btn-group-action</code>.
        </p>
      </div>
      <span class="badge text-bg-secondary">.btn / .btn-group-action</span>
    </div>

    <div class="ui-kit-group-wrapper">
      <div class="ui-kit-component-label">Сплошные кнопки со свечением (Solid Buttons)</div>
      <div class="ui-kit-btn-group">
        <button class="btn btn-primary" type="button" data-toast="Основное действие выполнено (Primary)">
          <span class="material-symbols-rounded">check</span> Primary Action
        </button>
        <button class="btn btn-secondary" type="button" data-toast="Вторичное действие (Secondary)">
          <span class="material-symbols-rounded">tune</span> Secondary
        </button>
        <button class="btn btn-success" type="button" data-toast="Изменения успешно сохранены (Success)">
          <span class="material-symbols-rounded">save</span> Success Save
        </button>
        <button class="btn btn-danger" type="button" data-modal="delete">
          <span class="material-symbols-rounded">delete</span> Danger Delete
        </button>
        <button class="btn btn-warning" type="button" data-toast="Предупреждение системы (Warning)">
          <span class="material-symbols-rounded">warning</span> Warning
        </button>
        <button class="btn btn-info" type="button" data-toast="Справка открыта (Info)">
          <span class="material-symbols-rounded">info</span> Info Action
        </button>
        <button class="btn btn-primary" type="button" disabled>
          Disabled State
        </button>
      </div>

      <div class="ui-kit-component-label" style="margin-top:20px;">Контурные кнопки (Outline)</div>
      <div class="ui-kit-btn-group">
        <button class="btn btn-outline-primary" type="button" data-toast="Outline Primary выбран">
          <span class="material-symbols-rounded">edit</span> Outline Primary
        </button>
        <button class="btn btn-outline-secondary" type="button" data-toast="Outline Secondary выбран">
          <span class="material-symbols-rounded">refresh</span> Outline Secondary
        </button>
        <button class="btn btn-outline-success" type="button" data-toast="Outline Success">
          <span class="material-symbols-rounded">check_circle</span> Outline Success
        </button>
        <button class="btn btn-outline-danger" type="button" data-modal="delete">
          <span class="material-symbols-rounded">delete_outline</span> Outline Danger
        </button>
      </div>

      <div class="ui-kit-component-label" style="margin-top:20px;">Размеры и компактная группа действий в таблицах</div>
      <div class="ui-kit-btn-group align-items-center">
        <button class="btn btn-primary btn-lg" type="button" data-toast="Большая кнопка (btn-lg)">
          <span class="material-symbols-rounded">rocket_launch</span> Large (btn-lg)
        </button>
        <button class="btn btn-primary" type="button" data-toast="Стандартная кнопка (default)">
          Default Button
        </button>
        <button class="btn btn-primary btn-sm" type="button" data-toast="Компактная кнопка (btn-sm)">
          <span class="material-symbols-rounded">add</span> Compact (btn-sm)
        </button>
        <button class="btn btn-secondary btn-xs" type="button" data-toast="Микро-кнопка (btn-xs)">
          Micro (btn-xs)
        </button>

        <!-- Компактная монолитная группа действий (exact match screenshot) -->
        <div class="btn-group-action" title="Компактная группа действий в строке таблицы">
          <button type="button" title="Просмотр" data-toast="Просмотр записи"><span class="material-symbols-rounded">visibility</span></button>
          <button type="button" title="Редактировать" data-toast="Редактирование записи"><span class="material-symbols-rounded">edit</span></button>
          <button type="button" class="del" title="Удалить" data-modal="delete"><span class="material-symbols-rounded">delete</span></button>
        </div>
      </div>
    </div>
  </div>

  <!-- =========================================================================
       СЕКЦИЯ 4: СЕГМЕНТИРОВАННЫЕ ПЕРЕКЛЮЧАТЕЛИ (FIXED-POSITION TOGGLES)
       ========================================================================= -->
  <div id="sec-toggles" class="sz-panel ui-kit-section">
    <div class="ui-kit-section__head">
      <div>
        <span class="sz-eyebrow">Interactive Controls</span>
        <h2>Сегментированные переключатели (Фиксированные позиции)</h2>
        <p class="text-muted">
          Кнопки <code>Вкл</code> (зелёный) и <code>Откл</code> (красный) всегда остаются на своих местах слева и справа.
        </p>
      </div>
      <span class="badge text-bg-secondary">Fixed Layout</span>
    </div>

    <div class="ui-kit-toggles-list">
      <div class="ui-kit-toggle-card" data-toggle-title="Яндекс РТБ">
        <div class="ui-kit-toggle-info">
          <span class="ui-kit-toggle-dot is-active"></span>
          <strong>Яндекс РТБ</strong>
          <small class="text-muted">Загрузка контекстных объявлений</small>
        </div>
        <div class="ui-kit-segmented-toggle">
          <button type="button" class="ui-kit-seg-btn ui-kit-seg-btn--on is-active">Вкл</button>
          <button type="button" class="ui-kit-seg-btn ui-kit-seg-btn--off">Откл</button>
        </div>
      </div>

      <div class="ui-kit-toggle-card" data-toggle-title="Digital Caramel">
        <div class="ui-kit-toggle-info">
          <span class="ui-kit-toggle-dot is-active"></span>
          <strong>Digital Caramel</strong>
          <small class="text-muted">Монетизация медийного трафика</small>
        </div>
        <div class="ui-kit-segmented-toggle">
          <button type="button" class="ui-kit-seg-btn ui-kit-seg-btn--on is-active">Вкл</button>
          <button type="button" class="ui-kit-seg-btn ui-kit-seg-btn--off">Откл</button>
        </div>
      </div>

      <div class="ui-kit-toggle-card" data-toggle-title="Adfinity (авто-встраивание)">
        <div class="ui-kit-toggle-info">
          <span class="ui-kit-toggle-dot is-active"></span>
          <strong>Adfinity (авто-встраивание)</strong>
          <small class="text-muted">Автоматические баннерные форматы</small>
        </div>
        <div class="ui-kit-segmented-toggle">
          <button type="button" class="ui-kit-seg-btn ui-kit-seg-btn--on is-active">Вкл</button>
          <button type="button" class="ui-kit-seg-btn ui-kit-seg-btn--off">Откл</button>
        </div>
      </div>

      <div class="ui-kit-toggle-card" data-toggle-title="MPS / Mediascope">
        <div class="ui-kit-toggle-info">
          <span class="ui-kit-toggle-dot"></span>
          <strong>MPS / Mediascope</strong>
          <small class="text-muted">Счётчик внешней верификации</small>
        </div>
        <div class="ui-kit-segmented-toggle">
          <button type="button" class="ui-kit-seg-btn ui-kit-seg-btn--on">Вкл</button>
          <button type="button" class="ui-kit-seg-btn ui-kit-seg-btn--off is-active">Откл</button>
        </div>
      </div>
    </div>
  </div>

  <!-- =========================================================================
       СЕКЦИЯ 5: БЕЙДЖИ И СТАТУСЫ (BADGES)
       ========================================================================= -->
  <div id="sec-badges" class="sz-panel ui-kit-section">
    <div class="ui-kit-section__head">
      <div>
        <span class="sz-eyebrow">Status Indicators</span>
        <h2>Бейджи и метки состояний</h2>
        <p class="text-muted">
          Сплошные контрастные бейджи Bootstrap 5 и мягкие фирменные пилюли для статусов записей.
        </p>
      </div>
      <span class="badge text-bg-secondary">.badge / .badge-soft-*</span>
    </div>

    <div class="ui-kit-group-wrapper">
      <div class="ui-kit-component-label">Стандартные сплошные бейджи (Bootstrap 5)</div>
      <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
        <span class="badge text-bg-primary">Primary</span>
        <span class="badge text-bg-secondary">Secondary</span>
        <span class="badge text-bg-success">Опубликовано</span>
        <span class="badge text-bg-danger">Удалено</span>
        <span class="badge text-bg-warning">На проверке</span>
        <span class="badge text-bg-info">Информация</span>
        <span class="badge text-bg-dark">Dark Badge</span>
      </div>

      <div class="ui-kit-component-label" style="margin-top:20px;">Мягкие статусные бейджи (Soft Badges)</div>
      <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
        <span class="badge badge-soft-success"><span class="ui-kit-dot is-active"></span> Активен</span>
        <span class="badge badge-soft-warning"><span class="ui-kit-dot is-pending"></span> Ожидание</span>
        <span class="badge badge-soft-danger"><span class="ui-kit-dot is-danger"></span> Заблокирован</span>
        <span class="badge badge-soft-primary"><span class="ui-kit-dot is-primary"></span> В процессе</span>
        <span class="badge badge-soft-secondary">Черновик</span>
      </div>
    </div>
  </div>

  <!-- =========================================================================
       СЕКЦИЯ 6: СЕГМЕНТИРОВАННЫЕ ВКЛАДКИ (AdminTabs)
       ========================================================================= -->
  <div id="sec-tabs" class="sz-panel ui-kit-section">
    <div class="ui-kit-section__head">
      <div>
        <span class="sz-eyebrow">Navigation</span>
        <h2>Сегментированные вкладки разделов (AdminTabs)</h2>
        <p class="text-muted">
          Монолитный стиль сегментов <code>.nav-pills-solid</code> для фильтрации таблиц, переключения графиков и подразделов.
        </p>
      </div>
      <span class="badge text-bg-secondary">.nav-pills-solid</span>
    </div>

    <ul class="nav-pills-solid" id="demoTabs" role="tablist">
      <li><button class="nav-link active" data-tab="0" type="button"><span class="material-symbols-rounded">dashboard</span> Обзор системы</button></li>
      <li><button class="nav-link" data-tab="1" type="button"><span class="material-symbols-rounded">history</span> История очередей</button></li>
      <li><button class="nav-link" data-tab="2" type="button"><span class="material-symbols-rounded">tune</span> Параметры кеша</button></li>
      <li><button class="nav-link" data-tab="3" type="button"><span class="material-symbols-rounded">security</span> Безопасность</button></li>
    </ul>
    <div id="demoTabContent" class="demo-tab-description">
      <strong>Обзор системы:</strong> Сводные показатели активности контент-платформы, нагрузка на сервер, статус рекламных сетей и антибот-фильтров.
    </div>
  </div>

  <!-- =========================================================================
       СЕКЦИЯ 7: ФОРМЫ И ЭЛЕМЕНТЫ ВВОДА (Form Controls)
       ========================================================================= -->
  <div id="sec-forms" class="sz-panel ui-kit-section">
    <div class="ui-kit-section__head">
      <div>
        <span class="sz-eyebrow">Input Controls</span>
        <h2>Формы, поля ввода и валидация</h2>
        <p class="text-muted">
          Стилизованные инпуты, выпадающие списки Select2/Bootstrap, переключатели и плавающие подсказки.
        </p>
      </div>
      <span class="badge text-bg-secondary">.form-control</span>
    </div>

    <form id="demoForm" class="row g-3">
      <div class="col-md-6">
        <label for="demoInputText" class="form-label">Заголовок страницы или материала</label>
        <input type="text" class="form-control" id="demoInputText" placeholder="Например: Праздничные поздравления" value="Современный дизайн админки">
      </div>
      <div class="col-md-6">
        <label for="demoSelect" class="form-label">Категория публикации</label>
        <select class="form-select" id="demoSelect">
          <option value="1">Поздравления и пожелания</option>
          <option value="2">Раскраски и картинки</option>
          <option value="3">Детские загадки</option>
          <option value="4">Мультфильмы и сказки</option>
        </select>
      </div>
      <div class="col-12">
        <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" role="switch" id="demoSwitch" checked>
          <label class="form-check-label" for="demoSwitch">Опубликовать сразу после сохранения</label>
        </div>
      </div>
      <div class="col-12" style="display:flex;gap:10px;">
        <button type="submit" class="btn btn-primary">
          <span class="material-symbols-rounded">save</span> Сохранить изменения
        </button>
        <button type="reset" class="btn btn-secondary">
          Сбросить
        </button>
      </div>
    </form>
  </div>

  <!-- =========================================================================
       СЕКЦИЯ 8: ИНТЕРАКТИВНАЯ ТАБЛИЦА (Yii2 GridView)
       ========================================================================= -->
  <div id="sec-table" class="sz-panel ui-kit-section">
    <div class="ui-kit-section__head">
      <div>
        <span class="sz-eyebrow">Data Grid</span>
        <h2>Таблица записей (Yii2 GridView)</h2>
        <p class="text-muted">
          Стандартная таблица <code>.table</code> с компактными действиями <code>.btn-group-action</code>, пагинацией и поиском.
        </p>
      </div>
      <button class="btn btn-primary btn-sm" type="button" data-modal="create">
        <span class="material-symbols-rounded">add</span> Добавить запись
      </button>
    </div>

    <!-- Тулбар: поиск и фильтрация -->
    <div class="ui-kit-table-toolbar">
      <div class="ui-kit-table-search">
        <span class="material-symbols-rounded ui-kit-search-icon">search</span>
        <input class="form-control" id="tableSearch" type="search" placeholder="Поиск по названию, автору или ID...">
      </div>
      <div class="ui-kit-table-filter">
        <select class="form-select" id="tableFilter">
          <option value="all">Все статусы</option>
          <option value="published">Опубликовано</option>
          <option value="draft">Черновик</option>
          <option value="pending">На проверке</option>
        </select>
      </div>
    </div>

    <!-- Таблица записей -->
    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead>
          <tr>
            <th style="width:75px">ID</th>
            <th style="width:48px;text-align:center;">Фото</th>
            <th>Название материала</th>
            <th>Автор</th>
            <th>Статус</th>
            <th>Дата создания</th>
            <th class="action-column">ДЕЙСТВИЯ</th>
          </tr>
        </thead>
        <tbody id="demoTableBody">
          <!-- Заполняется динамически через JS -->
        </tbody>
      </table>
    </div>

    <!-- Подвал таблицы: сводка и пагинация -->
    <div class="ui-kit-table-footer">
      <div class="text-muted" id="tableSummary">Показано 1–5 из 8 записей</div>
      <ul class="pagination mb-0" id="tablePagination">
        <!-- Пагинация заполняется динамически через JS -->
      </ul>
    </div>
  </div>

  <!-- =========================================================================
       СЕКЦИЯ 9: СИМУЛЯТОР ОЧЕРЕДЕЙ ЗАДАЧ (sz-progress-job)
       ========================================================================= -->
  <div id="sec-jobs" class="sz-panel ui-kit-section">
    <div class="ui-kit-section__head">
      <div>
        <span class="sz-eyebrow">Async Tasks</span>
        <h2>Фоновые очереди и прогресс-бары (.sz-progress-job)</h2>
        <p class="text-muted">
          Индикатор выполнения пакетных задач: оптимизация изображений, пересчёт поисковых индексов, генерация карт сайта.
        </p>
      </div>
      <span class="badge text-bg-secondary">Live Progress</span>
    </div>

    <div class="sz-progress-job">
      <div class="sz-progress-job__header">
        <div class="sz-progress-job__info">
          <div class="sz-progress-job__icon" data-job-icon>
            <span class="material-symbols-rounded">sync</span>
          </div>
          <div>
            <strong style="display:block;font-size:14px;color:var(--sz-text);" data-job-text>Ожидание запуска фоновой очереди...</strong>
            <small class="text-muted" data-job-step>Шаг 0 из 5</small>
          </div>
        </div>
        <div class="sz-progress-job__meta" data-job-meta>0%</div>
      </div>
      <div class="progress" style="height:10px;border-radius:99px;background:var(--stroke);">
        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width:0%" data-job-bar></div>
      </div>
      <div style="display:flex;gap:10px;margin-top:14px;">
        <button class="btn btn-primary btn-sm" type="button" data-job-run>
          <span class="material-symbols-rounded">play_arrow</span> Запустить обработку
        </button>
        <button class="btn btn-secondary btn-sm" type="button" data-job-reset>
          Сброс
        </button>
      </div>
    </div>
  </div>

  <!-- =========================================================================
       СЕКЦИЯ 10: УВЕДОМЛЕНИЯ, АЛЕРТЫ И МОДАЛЬНЫЕ ОКНА
       ========================================================================= -->
  <div id="sec-feedback" class="sz-panel ui-kit-section">
    <div class="ui-kit-section__head">
      <div>
        <span class="sz-eyebrow">Feedback & Dialogs</span>
        <h2>Уведомления, всплывающие карточки и модальные окна</h2>
        <p class="text-muted">
          Информационные сообщения Bootstrap 5 и карточки уведомлений в правом верхнем углу (с анимацией и таймером).
        </p>
      </div>
      <span class="badge text-bg-secondary">showToast()</span>
    </div>

    <div class="row g-3">
      <div class="col-lg-6">
        <div class="alert alert-success d-flex align-items-center" role="alert">
          <span class="material-symbols-rounded me-2" style="font-size:22px;">check_circle</span>
          <div><strong>Успешно:</strong> Операция синхронизации данных завершена без ошибок.</div>
        </div>
        <div class="alert alert-warning d-flex align-items-center" role="alert">
          <span class="material-symbols-rounded me-2" style="font-size:22px;">warning</span>
          <div><strong>Внимание:</strong> Обнаружены неразобранные дубликаты текстов поздравлений.</div>
        </div>
        <div class="alert alert-danger d-flex align-items-center" role="alert">
          <span class="material-symbols-rounded me-2" style="font-size:22px;">error</span>
          <div><strong>Ошибка:</strong> Не удалось обновить токен доступа к стороннему сервису.</div>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="card p-3" style="background:var(--sz-surface);border:1px solid var(--stroke);border-radius:14px;">
          <h4 style="font-size:15px;font-weight:700;margin-bottom:8px;">Интерактивный вызов всплывающих карточек (Toasts)</h4>
          <p class="text-muted" style="font-size:13px;margin-bottom:14px;">
            Нажмите кнопки ниже, чтобы протестировать карточки в правом верхнем углу экрана:
          </p>
          <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <button type="button" class="btn btn-success btn-sm" data-toast="Кеш успешно очищен (backend и frontend).">
              <span class="material-symbols-rounded">check</span> Очистка кеша (Success)
            </button>
            <button type="button" class="btn btn-warning btn-sm" data-toast="Внимание: превышен лимит обращений к API">
              <span class="material-symbols-rounded">warning</span> Предупреждение (Warning)
            </button>
            <button type="button" class="btn btn-danger btn-sm" data-toast="Ошибка: не удалось связаться с сервером базы данных">
              <span class="material-symbols-rounded">error</span> Ошибка (Danger)
            </button>
            <button type="button" class="btn btn-info btn-sm" data-toast="Информационное уведомление системы">
              <span class="material-symbols-rounded">info</span> Инфо (Info)
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>

<!-- ДЕМО МОДАЛЬНОЕ ОКНО ДЛЯ УДАЛЕНИЯ / СОЗДАНИЯ -->
<div class="modal fade" id="demoModal" tabindex="-1" aria-labelledby="demoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:16px;border:1px solid var(--stroke);background:var(--surface);">
      <div class="modal-header" style="border-bottom:1px solid var(--stroke);">
        <h5 class="modal-title" id="demoModalLabel" style="font-size:16px;font-weight:700;">Подтверждение</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="demoModalBody" style="font-size:14px;color:var(--sz-text);">
        Вы действительно хотите выполнить это действие?
      </div>
      <div class="modal-footer" style="border-top:1px solid var(--stroke);">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Отмена</button>
        <button type="button" class="btn btn-danger btn-sm" id="demoModalConfirm">Подтвердить</button>
      </div>
    </div>
  </div>
</div>

<?php
$this->registerCss(<<<'CSS'
/* Обертка страницы UI-Kit */
.ui-kit-wrapper {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

/* Hero карточка с переключателем темы */
.ui-kit-hero {
  padding: 24px !important;
  background: var(--surface) !important;
  border-radius: 16px !important;
  border: 1px solid var(--stroke) !important;
  box-shadow: 0 4px 20px rgba(18, 38, 77, 0.05) !important;
}
.ui-kit-hero__header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 20px;
  flex-wrap: wrap;
}
.ui-kit-hero__titles {
  max-width: 640px;
}
.ui-kit-badges {
  display: flex;
  gap: 8px;
  align-items: center;
  margin-bottom: 12px;
}
.ui-kit-hero__h1 {
  font-size: 26px;
  font-weight: 800;
  color: var(--sz-text);
  margin: 0 0 8px 0;
  letter-spacing: -0.02em;
}
.ui-kit-hero__lead {
  font-size: 14px;
  line-height: 1.55;
  color: var(--sz-muted);
  margin: 0;
}

/* Карточка переключателя темы */
.ui-kit-theme-card {
  background: var(--sz-surface, rgba(240, 244, 252, 0.5));
  border: 1px solid var(--stroke);
  border-radius: 14px;
  padding: 14px 18px;
  display: flex;
  flex-direction: column;
  gap: 10px;
  min-width: 240px;
}
body.dark .ui-kit-theme-card {
  background: rgba(22, 34, 53, 0.6);
}
.ui-kit-theme-card__label {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  font-weight: 700;
  color: var(--sz-text);
}
.ui-kit-theme-card__label .material-symbols-rounded {
  font-size: 18px;
  color: var(--sz-primary);
}
.ui-kit-theme-toggle {
  display: flex;
  gap: 6px;
  background: rgba(0, 0, 0, 0.04);
  padding: 4px;
  border-radius: 10px;
  border: 1px solid var(--stroke);
}
body.dark .ui-kit-theme-toggle {
  background: rgba(0, 0, 0, 0.25);
}
.ui-kit-theme-btn {
  flex: 1;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  border: 0;
  border-radius: 8px;
  padding: 7px 12px;
  font-size: 12.5px;
  font-weight: 650;
  background: transparent;
  color: var(--sz-muted);
  cursor: pointer;
  transition: all 0.15s ease;
}
.ui-kit-theme-btn .material-symbols-rounded {
  font-size: 16px;
}
.ui-kit-theme-btn:hover {
  color: var(--sz-text);
}
.ui-kit-theme-btn.is-active {
  background: #ffffff;
  color: var(--sz-primary);
  box-shadow: 0 2px 8px rgba(25, 45, 90, 0.12);
}
body.dark .ui-kit-theme-btn.is-active {
  background: #253956;
  color: #60a5fa;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
}
.ui-kit-theme-status {
  font-size: 11.5px;
  color: var(--sz-muted);
}
.ui-kit-theme-status strong {
  color: var(--sz-text);
}

/* Быстрая навигация */
.ui-kit-quicknav {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-top: 20px;
  padding-top: 16px;
  border-top: 1px solid var(--stroke);
  flex-wrap: wrap;
}
.ui-kit-quicknav__label {
  font-size: 12px;
  font-weight: 750;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--sz-muted);
}
.ui-kit-quicknav__links {
  display: flex;
  gap: 6px;
  flex-wrap: wrap;
}
.ui-kit-nav-pill {
  display: inline-flex;
  align-items: center;
  padding: 4px 11px;
  border-radius: 20px;
  background: var(--sz-surface, #f0f4fa);
  color: var(--sz-text);
  font-size: 12px;
  font-weight: 600;
  text-decoration: none;
  border: 1px solid var(--stroke);
  transition: all 0.15s ease;
}
.ui-kit-nav-pill:hover {
  background: var(--sz-primary);
  color: #fff;
  border-color: var(--sz-primary);
}

/* Секции UI-Kit */
.ui-kit-section {
  padding: 24px !important;
  border-radius: 16px !important;
  border: 1px solid var(--stroke) !important;
  background: var(--surface) !important;
}
.ui-kit-section__head {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 20px;
  gap: 16px;
}
.ui-kit-section__head h2 {
  font-size: 20px;
  font-weight: 800;
  margin: 2px 0 4px 0;
  color: var(--sz-text);
}
.ui-kit-component-label {
  font-size: 12px;
  font-weight: 750;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  color: var(--sz-muted);
  margin-bottom: 10px;
}

/* Палитра токенов */
.ui-kit-swatches-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap: 12px;
}
.ui-kit-swatch-card {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 14px;
  border-radius: 12px;
  border: 1px solid var(--stroke);
  background: var(--sz-surface, rgba(240, 244, 252, 0.4));
  cursor: pointer;
  transition: transform 0.15s ease, border-color 0.15s ease;
}
.ui-kit-swatch-card:hover {
  transform: translateY(-2px);
  border-color: var(--sz-primary);
}
.ui-kit-swatch-color {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  flex-shrink: 0;
}
.ui-kit-swatch-info {
  display: flex;
  flex-direction: column;
  min-width: 0;
}
.ui-kit-swatch-info strong {
  font-size: 13px;
  color: var(--sz-text);
}
.ui-kit-swatch-info code {
  font-size: 11px;
  color: var(--sz-primary);
}
.ui-kit-swatch-info small {
  font-size: 10.5px;
  color: var(--sz-muted);
}

/* Кнопки */
.ui-kit-btn-group {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

/* Сегментированные переключатели */
.ui-kit-toggles-list {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 12px;
}
.ui-kit-toggle-card {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 16px;
  border-radius: 12px;
  border: 1px solid var(--stroke);
  background: var(--sz-surface, rgba(240, 244, 252, 0.4));
}
.ui-kit-toggle-info {
  display: flex;
  align-items: center;
  gap: 10px;
}
.ui-kit-toggle-info strong {
  font-size: 13.5px;
  color: var(--sz-text);
  display: block;
}
.ui-kit-toggle-info small {
  display: block;
  font-size: 11px;
}
.ui-kit-toggle-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #cbd5e1;
  flex-shrink: 0;
}
.ui-kit-toggle-dot.is-active {
  background: #079b70;
  box-shadow: 0 0 6px rgba(7, 155, 112, 0.5);
}
.ui-kit-segmented-toggle {
  display: inline-flex;
  border: 1px solid var(--stroke);
  border-radius: 8px;
  overflow: hidden;
  background: var(--surface);
}
.ui-kit-seg-btn {
  border: 0;
  padding: 4px 12px;
  font-size: 12px;
  font-weight: 700;
  background: transparent;
  color: var(--sz-muted);
  cursor: pointer;
  transition: all 0.15s ease;
}
.ui-kit-seg-btn--on.is-active {
  background: #079b70;
  color: #fff;
}
.ui-kit-seg-btn--off.is-active {
  background: #dc3545;
  color: #fff;
}

/* Таблица & Тулбар */
.ui-kit-table-toolbar {
  display: flex;
  gap: 12px;
  margin-bottom: 14px;
  justify-content: space-between;
  flex-wrap: wrap;
}
.ui-kit-table-search {
  position: relative;
  max-width: 340px;
  flex: 1;
}
.ui-kit-search-icon {
  position: absolute;
  left: 10px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--sz-muted);
  font-size: 18px;
  pointer-events: none;
}
.ui-kit-table-search input {
  padding-left: 34px;
}
.ui-kit-table-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 14px;
  flex-wrap: wrap;
  gap: 10px;
}

/* Точки статуса в бейджах */
.ui-kit-dot {
  display: inline-block;
  width: 6px;
  height: 6px;
  border-radius: 50%;
  margin-right: 5px;
  vertical-align: middle;
}
.ui-kit-dot.is-active { background: #079b70; }
.ui-kit-dot.is-pending { background: #f59e0b; }
.ui-kit-dot.is-danger { background: #dc3545; }
.ui-kit-dot.is-primary { background: #3189f7; }

/* Блок кода */
.sz-code-block {
  background: #141c2b;
  color: #e2e8f0;
  border-radius: 10px;
  padding: 14px;
  font-size: 12px;
  line-height: 1.5;
  overflow-x: auto;
  margin: 0;
}
.sz-code-block-title {
  font-size: 12px;
  font-weight: 750;
  color: var(--sz-muted);
  margin-bottom: 6px;
  text-transform: uppercase;
}

/* Превью изображений */
.sz-thumb-preview-wrap {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 16px;
  background: var(--sz-surface, rgba(240, 244, 252, 0.4));
  border: 1px solid var(--stroke);
  border-radius: 12px;
  margin-bottom: 16px;
}
CSS);

$this->registerJs(<<<'JS'
(function () {
  const $ = id => document.getElementById(id);
  const safe = str => String(str).replace(/[&<>"']/g, s => ({ '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;' }[s]));

  // =========================================================================
  // ПЕРЕКЛЮЧАТЕЛЬ ТЕМЫ (LIGHT / DARK)
  // =========================================================================
  const btnLight = $('btnThemeLight');
  const btnDark = $('btnThemeDark');
  const currentThemeLabel = $('currentThemeLabel');

  function updateThemeUI(isDark) {
    if (btnLight && btnDark) {
      btnLight.classList.toggle('is-active', !isDark);
      btnDark.classList.toggle('is-active', isDark);
    }
    if (currentThemeLabel) {
      currentThemeLabel.textContent = isDark ? 'Тёмная' : 'Светлая';
    }
  }

  function setTheme(mode) {
    const isDark = mode === 'dark';
    document.body.classList.toggle('dark', isDark);
    document.documentElement.classList.toggle('dark', isDark);
    document.cookie = `admin-theme=${mode}; path=/; max-age=31536000`;
    try { localStorage.setItem('admin-theme', mode); } catch (e) {}

    const topbarThemeBtn = document.querySelector('[data-sz-theme-toggle]');
    if (topbarThemeBtn) {
      const icon = topbarThemeBtn.querySelector('.material-symbols-rounded, .icon');
      if (icon) icon.textContent = isDark ? 'light_mode' : 'dark_mode';
      topbarThemeBtn.setAttribute('title', isDark ? 'Светлая тема' : 'Темная тема');
    }
    updateThemeUI(isDark);
  }

  const initialDark = document.body.classList.contains('dark') || document.documentElement.classList.contains('dark');
  updateThemeUI(initialDark);

  if (btnLight) {
    btnLight.addEventListener('click', () => {
      setTheme('light');
      if (typeof window.showToast === 'function') {
        window.showToast('Включена светлая тема оформления', 'info');
      }
    });
  }

  if (btnDark) {
    btnDark.addEventListener('click', () => {
      setTheme('dark');
      if (typeof window.showToast === 'function') {
        window.showToast('Включена тёмная тема оформления', 'info');
      }
    });
  }

  // =========================================================================
  // СЕГМЕНТИРОВАННЫЕ ПЕРЕКЛЮЧАТЕЛИ (ВКЛ / ОТКЛ)
  // =========================================================================
  document.querySelectorAll('.ui-kit-toggle-card').forEach(card => {
    const title = card.dataset.toggleTitle || 'Переключатель';
    const dot = card.querySelector('.ui-kit-toggle-dot');
    const btnOn = card.querySelector('.ui-kit-seg-btn--on');
    const btnOff = card.querySelector('.ui-kit-seg-btn--off');

    const updateState = isOn => {
      if (dot) dot.classList.toggle('is-active', isOn);
      if (btnOn) btnOn.classList.toggle('is-active', isOn);
      if (btnOff) btnOff.classList.toggle('is-active', !isOn);
    };

    if (btnOn) {
      btnOn.addEventListener('click', () => {
        updateState(true);
        if (typeof window.showToast === 'function') {
          window.showToast(`${title}: статус изменён на «Вкл»`, 'success');
        }
      });
    }

    if (btnOff) {
      btnOff.addEventListener('click', () => {
        updateState(false);
        if (typeof window.showToast === 'function') {
          window.showToast(`${title}: статус изменён на «Откл»`, 'warning');
        }
      });
    }
  });

  // =========================================================================
  // КОПИРОВАНИЕ ДИЗАЙН-ТОКЕНОВ
  // =========================================================================
  document.querySelectorAll('.ui-kit-swatch-card').forEach(card => {
    card.addEventListener('click', () => {
      const val = card.dataset.copy;
      if (!val) return;
      if (navigator.clipboard) {
        navigator.clipboard.writeText(val).then(() => {
          if (typeof window.showToast === 'function') {
            window.showToast(`Скопировано: ${val}`, 'info');
          }
        });
      }
    });
  });

  // =========================================================================
  // ВКЛАДКИ (AdminTabs)
  // =========================================================================
  const demoTabs = $('demoTabs');
  const demoTabContent = $('demoTabContent');
  const tabDescriptions = [
    '<strong>Обзор системы:</strong> Сводные показатели активности контент-платформы, нагрузка на сервер, статус рекламных сетей и антибот-фильтров.',
    '<strong>История очередей:</strong> Логирование фоновых воркеров, обработка очередей изображений и синхронизация SEO-тегов.',
    '<strong>Параметры кеша:</strong> Управление многоуровневым файловым и Redis-хранилищем с мгновенным сбросом схемы.',
    '<strong>Безопасность:</strong> Двухфакторная аутентификация TOTP, управление ролями RBAC и журнал входов администраторов.'
  ];

  if (demoTabs && demoTabContent) {
    demoTabs.addEventListener('click', e => {
      const btn = e.target.closest('.nav-link');
      if (!btn) return;
      demoTabs.querySelectorAll('.nav-link').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      const idx = Number(btn.dataset.tab) || 0;
      demoTabContent.innerHTML = tabDescriptions[idx] || '';
      if (typeof window.showToast === 'function') {
        window.showToast(`Переключена вкладка: ${btn.textContent.trim()}`, 'info');
      }
    });
  }

  // =========================================================================
  // ТАБЛИЦА С ДАННЫМИ И КОМПАКТНЫМИ ДЕЙСТВИЯМИ
  // =========================================================================
  const demoRows = [
    [15622, 'Поздравления с днем рождения мужчине', 'Николай', 'published', '10.10.2026'],
    [15621, 'Раскраска космонавт на Луне', 'Редактор', 'published', '10.10.2026'],
    [15620, 'Осенние открытки с листьями', 'Ольга', 'pending', '09.10.2026'],
    [15619, 'Загадки про лесных животных', 'Иван', 'draft', '09.10.2026'],
    [15618, 'Архитектура старого города', 'Николай', 'published', '08.10.2026'],
    [15617, 'Сказочный лес и грибы', 'Ольга', 'published', '07.10.2026'],
    [15616, 'Новогодние поздравления коллегам', 'Редактор', 'pending', '06.10.2026'],
    [15615, 'Цветы и садовые растения', 'Николай', 'draft', '05.10.2026']
  ];

  const statusBadges = {
    published: ['Опубликовано', 'badge-soft-success'],
    draft: ['Черновик', 'badge-soft-secondary'],
    pending: ['На проверке', 'badge-soft-warning']
  };

  let tablePage = 1;
  const perPage = 5;

  function renderTable() {
    const searchInput = $('tableSearch');
    const filterSelect = $('tableFilter');
    const tableBody = $('demoTableBody');
    if (!tableBody) return;

    const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
    const filter = filterSelect ? filterSelect.value : 'all';

    const filtered = demoRows.filter(r => {
      const matchText = (r[1] + ' ' + r[2] + ' ' + r[0]).toLowerCase().includes(query);
      const matchFilter = filter === 'all' || r[3] === filter;
      return matchText && matchFilter;
    });

    const totalPages = Math.max(1, Math.ceil(filtered.length / perPage));
    tablePage = Math.min(tablePage, totalPages);

    const currentRows = filtered.slice((tablePage - 1) * perPage, tablePage * perPage);

    tableBody.innerHTML = currentRows.map(row => {
      const b = statusBadges[row[3]] || ['Черновик', 'badge-soft-secondary'];
      return `
        <tr>
          <td><strong style="color:var(--sz-primary);">#${row[0]}</strong></td>
          <td style="text-align:center;">
            <div class="sz-thumb" style="width:34px;height:34px;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.03);border:1px solid var(--stroke);">
              <span class="material-symbols-rounded" style="color:var(--sz-muted);font-size:18px;">image</span>
            </div>
          </td>
          <td>
            <strong style="color:var(--sz-text);display:block;">${safe(row[1])}</strong>
            <small class="text-muted">/catalog/item-${row[0]}</small>
          </td>
          <td>${safe(row[2])}</td>
          <td><span class="badge ${b[1]}">${b[0]}</span></td>
          <td>${row[4]}</td>
          <td class="action-column">
            <div class="btn-group-action">
              <button type="button" title="Просмотр" data-toast="Просмотр материала #${row[0]}"><span class="material-symbols-rounded">visibility</span></button>
              <button type="button" title="Редактировать" data-toast="Редактирование #${row[0]}"><span class="material-symbols-rounded">edit</span></button>
              <button type="button" class="del" title="Удалить" data-modal="delete"><span class="material-symbols-rounded">delete</span></button>
            </div>
          </td>
        </tr>
      `;
    }).join('');

    const summary = $('tableSummary');
    if (summary) {
      summary.textContent = `Показано ${filtered.length ? (tablePage - 1) * perPage + 1 : 0}–${Math.min(tablePage * perPage, filtered.length)} из ${filtered.length} записей`;
    }

    const pagination = $('tablePagination');
    if (pagination) {
      pagination.innerHTML = Array.from({ length: totalPages }, (_, idx) => `
        <li class="page-item ${idx + 1 === tablePage ? 'active' : ''}">
          <button class="page-link" type="button" data-page="${idx + 1}">${idx + 1}</button>
        </li>
      `).join('');
    }
  }

  const tableSearch = $('tableSearch');
  if (tableSearch) tableSearch.addEventListener('input', () => { tablePage = 1; renderTable(); });
  const tableFilter = $('tableFilter');
  if (tableFilter) tableFilter.addEventListener('change', () => { tablePage = 1; renderTable(); });
  const tablePagination = $('tablePagination');
  if (tablePagination) {
    tablePagination.addEventListener('click', e => {
      const btn = e.target.closest('[data-page]');
      if (btn) {
        tablePage = Number(btn.dataset.page);
        renderTable();
      }
    });
  }
  renderTable();

  // =========================================================================
  // СИМУЛЯТОР ФОНОВЫХ ОЧЕРЕДЕЙ .sz-progress-job
  // =========================================================================
  const jobIcon = document.querySelector('[data-job-icon]');
  const jobText = document.querySelector('[data-job-text]');
  const jobBar = document.querySelector('[data-job-bar]');
  const jobStep = document.querySelector('[data-job-step]');
  const jobMeta = document.querySelector('[data-job-meta]');
  const jobRun = document.querySelector('[data-job-run]');
  const jobReset = document.querySelector('[data-job-reset]');
  let jobTimer = null, jobProgress = 0;

  function updateJob(state) {
    if (!jobBar) return;
    const pct = jobProgress * 20;
    jobBar.style.width = pct + '%';
    jobBar.textContent = pct + '%';
    if (jobMeta) jobMeta.textContent = pct + '%';
    if (jobStep) jobStep.textContent = `Шаг ${jobProgress} из 5`;

    if (state === 'running') {
      if (jobIcon) {
        jobIcon.classList.add('is-spinning');
        jobIcon.innerHTML = '<span class="material-symbols-rounded">sync</span>';
      }
      if (jobText) jobText.textContent = 'Выполняется фоновая обработка очереди...';
      if (jobRun) jobRun.disabled = true;
    } else if (state === 'done') {
      if (jobIcon) {
        jobIcon.classList.remove('is-spinning');
        jobIcon.innerHTML = '<span class="material-symbols-rounded" style="color:#079b70">check</span>';
      }
      if (jobText) jobText.textContent = 'Фоновая задача успешно выполнена (100%).';
      if (jobRun) jobRun.disabled = false;
    } else {
      if (jobIcon) {
        jobIcon.classList.remove('is-spinning');
        jobIcon.innerHTML = '<span class="material-symbols-rounded">sync</span>';
      }
      if (jobText) jobText.textContent = 'Ожидание запуска фоновой очереди...';
      if (jobRun) jobRun.disabled = false;
    }
  }

  if (jobRun) {
    jobRun.addEventListener('click', () => {
      if (jobProgress === 5) jobProgress = 0;
      updateJob('running');
      jobTimer = setInterval(() => {
        jobProgress++;
        if (jobProgress >= 5) {
          clearInterval(jobTimer);
          updateJob('done');
          if (typeof window.showToast === 'function') {
            window.showToast('Очередь задач успешно завершена (100%)', 'success');
          }
        } else {
          updateJob('running');
        }
      }, 600);
    });
  }

  if (jobReset) {
    jobReset.addEventListener('click', () => {
      clearInterval(jobTimer);
      jobProgress = 0;
      updateJob('idle');
    });
  }

  // =========================================================================
  // МОДАЛЬНОЕ ОКНО
  // =========================================================================
  let modalInstance = null;
  document.addEventListener('click', e => {
    const btn = e.target.closest('[data-modal]');
    if (!btn) return;
    const modalEl = $('demoModal');
    if (!modalEl || typeof bootstrap === 'undefined') return;

    const type = btn.dataset.modal;
    const label = $('demoModalLabel');
    const body = $('demoModalBody');
    const confirmBtn = $('demoModalConfirm');

    if (type === 'delete') {
      if (label) label.textContent = 'Подтверждение удаления';
      if (body) body.textContent = 'Вы действительно хотите удалить эту запись из базы данных? Это действие необратимо.';
      if (confirmBtn) {
        confirmBtn.className = 'btn btn-danger btn-sm';
        confirmBtn.textContent = 'Удалить';
      }
    } else {
      if (label) label.textContent = 'Создание новой записи';
      if (body) body.textContent = 'Заполните параметры нового материала в каталоге.';
      if (confirmBtn) {
        confirmBtn.className = 'btn btn-primary btn-sm';
        confirmBtn.textContent = 'Создать';
      }
    }

    if (!modalInstance) {
      modalInstance = new bootstrap.Modal(modalEl);
    }
    modalInstance.show();
  });

  const confirmBtn = $('demoModalConfirm');
  if (confirmBtn) {
    confirmBtn.addEventListener('click', () => {
      if (modalInstance) modalInstance.hide();
      if (typeof window.showToast === 'function') {
        window.showToast('Действие успешно подтверждено', 'success');
      }
    });
  }

  // Демо форма сабмит
  const demoForm = $('demoForm');
  if (demoForm) {
    demoForm.addEventListener('submit', e => {
      e.preventDefault();
      if (typeof window.showToast === 'function') {
        window.showToast('Форма успешно валидирована и сохранена', 'success');
      }
    });
  }
})();
JS, View::POS_READY);
?>
