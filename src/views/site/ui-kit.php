<?php

use yii\bootstrap5\Html;
use yii\web\View;

$this->title = 'ADMIN-UI-KIT';
$this->params['breadcrumbs'] = [
    ['label' => 'ADMIN-UI-KIT'],
];
$this->params['pageSubtitle'] = 'Дизайн-система панели управления · Спецификация компонентов · Светлая и тёмная темы';

$module = Yii::$app->getModule('admin');
$placeholder = $module ? $module->getLazyloadPlaceholderUrl($this) : '/img/load.svg';
$asset = \larikmc\admin\assets\AppAsset::register($this);
$demoPlaceholder = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 106 106'%3E%3Crect width='106' height='106' fill='%23f1f5fa'/%3E%3Cpath d='M35 70l14-16 11 12 11-14 15 18H35z' fill='%23cad8eb'/%3E%3Ccircle cx='45' cy='42' r='6' fill='%23cad8eb'/%3E%3C/svg%3E";
$demoLocalImg = $asset->baseUrl . '/img/template.jpg';
$demoOriginal = $demoLocalImg;
?>

<div id="uiKitPage" class="ui-kit-wrapper">

  <!-- =========================================================================
       HERO: ШАПКА UI-KIT С ИНТЕРАКТИВНЫМ ПЕРЕКЛЮЧАТЕЛЕМ ТЕМ (LIGHT / DARK)
       ========================================================================= -->
  <div class="sz-panel ui-kit-hero">
    <div class="ui-kit-hero__header">
      <div class="ui-kit-hero__titles">
        <div class="ui-kit-badges">
          <span class="badge text-bg-primary">Design System v2.0</span>
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
        <a href="#sec-tokens" class="ui-kit-nav-pill">Токены & Цвета</a>
        <a href="#sec-typography" class="ui-kit-nav-pill">Типографика</a>
        <a href="#sec-buttons" class="ui-kit-nav-pill">Кнопки</a>
        <a href="#sec-toggles" class="ui-kit-nav-pill">Переключатели</a>
        <a href="#sec-badges" class="ui-kit-nav-pill">Бейджи</a>
        <a href="#sec-tabs" class="ui-kit-nav-pill">Вкладки</a>
        <a href="#sec-forms" class="ui-kit-nav-pill">Формы</a>
        <a href="#sec-table" class="ui-kit-nav-pill">Таблица (GridView)</a>
        <a href="#sec-media" class="ui-kit-nav-pill">Медиа & Popup</a>
        <a href="#sec-jobs" class="ui-kit-nav-pill">Очереди</a>
        <a href="#sec-feedback" class="ui-kit-nav-pill">Алерты & Модалки</a>
      </div>
    </div>
  </div>

  <!-- =========================================================================
       СЕКЦИЯ 1: ЦВЕТОВАЯ ПАЛИТРА И ДИЗАЙН-ТОКЕНЫ
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

      <!-- Accent -->
      <div class="ui-kit-swatch-card" data-copy="var(--sz-accent)">
        <div class="ui-kit-swatch-color" style="background:#c97342;"></div>
        <div class="ui-kit-swatch-info">
          <strong>Warm Accent</strong>
          <code>--sz-accent</code>
          <small>#c97342 · Вторичный тёплый акцент, лейблы</small>
        </div>
      </div>

      <!-- Success -->
      <div class="ui-kit-swatch-card" data-copy="var(--sz-success)">
        <div class="ui-kit-swatch-color" style="background:#079b70;"></div>
        <div class="ui-kit-swatch-info">
          <strong>Success / Active</strong>
          <code>--sz-success</code>
          <small>#079b70 · Статус «Вкл», сохранение, успешные логи</small>
        </div>
      </div>

      <!-- Danger -->
      <div class="ui-kit-swatch-card" data-copy="var(--sz-danger)">
        <div class="ui-kit-swatch-color" style="background:#e24e6c;"></div>
        <div class="ui-kit-swatch-info">
          <strong>Danger / Error</strong>
          <code>--sz-danger</code>
          <small>#e24e6c · Удаление, статус «Откл», блокировки</small>
        </div>
      </div>

      <!-- Warning -->
      <div class="ui-kit-swatch-card" data-copy="var(--sz-warning)">
        <div class="ui-kit-swatch-color" style="background:#eea63c;"></div>
        <div class="ui-kit-swatch-info">
          <strong>Warning</strong>
          <code>--sz-warning</code>
          <small>#eea63c · Ожидание, модерация, системные лимиты</small>
        </div>
      </div>

      <!-- Info -->
      <div class="ui-kit-swatch-card" data-copy="var(--sz-info)">
        <div class="ui-kit-swatch-color" style="background:#2865be;"></div>
        <div class="ui-kit-swatch-info">
          <strong>Info / Neutral</strong>
          <code>--sz-info</code>
          <small>#2865be · Информирование, справка, статистика</small>
        </div>
      </div>

      <!-- Surface Dynamic -->
      <div class="ui-kit-swatch-card" data-copy="var(--sz-surface)">
        <div class="ui-kit-swatch-color ui-kit-swatch-color--dynamic" style="background:var(--sz-surface);border:1px solid var(--sz-line);"></div>
        <div class="ui-kit-swatch-info">
          <strong>Card Surface</strong>
          <code>--sz-surface</code>
          <small>Динамический фон карточек и типовых панелей</small>
        </div>
      </div>

      <!-- Canvas Background -->
      <div class="ui-kit-swatch-card" data-copy="var(--sz-bg)">
        <div class="ui-kit-swatch-color ui-kit-swatch-color--dynamic" style="background:var(--sz-bg);border:1px solid var(--sz-line);"></div>
        <div class="ui-kit-swatch-info">
          <strong>Canvas Background</strong>
          <code>--sz-bg</code>
          <small>Базовый фон страницы и подложек layout</small>
        </div>
      </div>
    </div>

    <!-- Архитектурные слои (Surface hierarchy) -->
    <div class="ui-kit-surface-layers">
      <div class="ui-kit-surface-layer ui-kit-surface-layer--canvas">
        <span class="ui-kit-layer-tag">Layer 0 · Canvas</span>
        <strong>Фон приложения (--sz-bg)</strong>
        <p>Мягкий холст layout со световыми градиентами в светлой теме и глубоким матовым индиго в тёмной.</p>
      </div>
      <div class="ui-kit-surface-layer ui-kit-surface-layer--panel">
        <span class="ui-kit-layer-tag">Layer 1 · Panel (.sz-panel)</span>
        <strong>Основная панель контента</strong>
        <p>Радиус 22px, деликатная тень, контрастный фон, адаптирующийся под тему.</p>
      </div>
      <div class="ui-kit-surface-layer ui-kit-surface-layer--surface">
        <span class="ui-kit-layer-tag">Layer 2 · Surface (.sz-surface)</span>
        <strong>Внутренняя плашка для группировки</strong>
        <p>Используется для фильтров, параметров форм, виджетов и акцентных блоков.</p>
      </div>
    </div>
  </div>

  <!-- =========================================================================
       СЕКЦИЯ 2: ТИПОГРАФИКА И ИЕРАРХИЯ
       ========================================================================= -->
  <div id="sec-typography" class="sz-panel ui-kit-section">
    <div class="ui-kit-section__head">
      <div>
        <span class="sz-eyebrow">Typography</span>
        <h2>Типографика и текстовая иерархия</h2>
        <p class="text-muted">
          Шрифт Inter с оптимизированным оптическим выравниванием, читабельностью на экранах любой плотности и строгим контрастом в обеих темах.
        </p>
      </div>
    </div>

    <div class="row align-items-center g-4">
      <div class="col-lg-7">
        <div class="ui-kit-type-sample">
          <div class="ui-kit-type-row">
            <span class="ui-kit-type-meta">H1 · 26px / 750</span>
            <h1 style="margin:0;font-size:26px;font-weight:750;">Заголовок первого уровня H1</h1>
          </div>
          <div class="ui-kit-type-row">
            <span class="ui-kit-type-meta">H2 · 20px / 750</span>
            <h2 style="margin:0;font-size:20px;font-weight:750;">Заголовок секции H2</h2>
          </div>
          <div class="ui-kit-type-row">
            <span class="ui-kit-type-meta">H3 · 16px / 700</span>
            <h3 style="margin:0;font-size:16px;font-weight:700;">Заголовок подраздела H3</h3>
          </div>
          <div class="ui-kit-type-row">
            <span class="ui-kit-type-meta">H4 · 14px / 650</span>
            <h4 style="margin:0;font-size:14px;font-weight:650;">Микро-заголовок карточки H4</h4>
          </div>
          <div class="ui-kit-type-row">
            <span class="ui-kit-type-meta">Lead text</span>
            <p class="lead" style="margin:0;font-size:15px;">Вводный абзац для ключевых описаний и пояснительных секций страниц.</p>
          </div>
          <div class="ui-kit-type-row">
            <span class="ui-kit-type-meta">Body / Muted</span>
            <p style="margin:0;">Стандартный текст интерфейса с естественным интерлиньяжем. <span class="text-muted">Вспомогательный приглушённый текст (.text-muted) для метаданных и подписей.</span></p>
          </div>
        </div>
      </div>

      <div class="col-lg-5">
        <div class="sz-surface">
          <span class="sz-eyebrow">Text Elements</span>
          <h4 style="margin-top:4px;">Элементы разметки</h4>
          <p style="font-size:13px;margin-bottom:12px;">Примеры инлайн-тегов и акцентов:</p>
          <ul style="list-style:none;padding:0;margin:0;display:grid;gap:8px;font-size:13px;">
            <li>Надзаголовок: <span class="sz-eyebrow">Section Eyebrow</span></li>
            <li>Инлайн-код: <code>$model->save(false)</code></li>
            <li>Клавиатура: <kbd>Ctrl</kbd> + <kbd>S</kbd> для сохранения</li>
            <li>Выделение: <mark>Ключевое совпадение</mark> в поиске</li>
            <li>Хлебные крошки: <span class="sz-breadcrumbs__current">Главная / Каталог / Запись</span></li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <!-- =========================================================================
       СЕКЦИЯ 3: КНОПКИ И СОСТОЯНИЯ СВЕЧЕНИЯ (.btn .btn-*)
       ========================================================================= -->
  <div id="sec-buttons" class="sz-panel ui-kit-section">
    <div class="ui-kit-section__head">
      <div>
        <span class="sz-eyebrow">Interactive Controls</span>
        <h2>Кнопки и состояния свечения (.btn .btn-*)</h2>
        <p class="text-muted">
          Primary светится фирменным синим ореолом всегда; Success, Danger и Warning загораются при наведении; контурные кнопки дают лёгкий интерактивный отклик.
        </p>
      </div>
    </div>

    <!-- Сплошные кнопки -->
    <div class="ui-kit-component-row">
      <div class="ui-kit-component-label">Сплошные кнопки (Solid)</div>
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
          <span class="material-symbols-rounded">block</span> Disabled
        </button>
      </div>
    </div>

    <!-- Контурные кнопки -->
    <div class="ui-kit-component-row">
      <div class="ui-kit-component-label">Контурные кнопки (Outline)</div>
      <div class="ui-kit-btn-group">
        <button class="btn btn-outline-primary" type="button" data-toast="Outline Primary выбран">
          <span class="material-symbols-rounded">edit</span> Outline Primary
        </button>
        <button class="btn btn-outline-secondary" type="button" data-toast="Outline Secondary выбран">
          <span class="material-symbols-rounded">refresh</span> Outline Secondary
        </button>
        <button class="btn btn-outline-danger" type="button" data-modal="delete">
          <span class="material-symbols-rounded">delete_outline</span> Outline Danger
        </button>
        <button class="btn btn-outline-primary" type="button" disabled>
          Outline Disabled
        </button>
      </div>
    </div>

    <!-- Размеры и микро-действия -->
    <div class="ui-kit-component-row">
      <div class="ui-kit-component-label">Размеры и действия в таблицах</div>
      <div class="ui-kit-btn-group align-items-center">
        <button class="btn btn-primary btn-lg" type="button" data-toast="Большая кнопка (btn-lg)">
          <span class="material-symbols-rounded">rocket_launch</span> Large Button (btn-lg)
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

        <!-- Action column group -->
        <div class="btn-group-action" title="Группа действий в строке таблицы">
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
        <span class="sz-eyebrow">Status Switches</span>
        <h2>Сегментированные переключатели статуса [ Вкл | Откл ]</h2>
        <p class="text-muted">
          Фиксированное расположение кнопок: «Вкл» всегда слева, «Откл» всегда справа (не меняются местами). Индикатор статуса снабжён живой пульсацией активности.
        </p>
      </div>
      <span class="badge badge-soft-success">Гарантированный порядок</span>
    </div>

    <!-- Демо-сетка интерактивных переключателей как в рекламном модуле -->
    <div class="ui-kit-toggles-grid">
      <!-- Переключатель 1 -->
      <div class="ui-kit-toggle-item" data-toggle-box="yandex">
        <div class="ui-kit-toggle-label">
          <span class="ui-kit-status-dot is-active"></span>
          <strong>Яндекс РТБ</strong>
        </div>
        <div class="ui-kit-segmented-control" role="group">
          <button type="button" class="ui-kit-seg-btn ui-kit-seg-btn--on is-active" data-seg-val="1">Вкл</button>
          <button type="button" class="ui-kit-seg-btn ui-kit-seg-btn--off" data-seg-val="0">Откл</button>
        </div>
      </div>

      <!-- Переключатель 2 -->
      <div class="ui-kit-toggle-item" data-toggle-box="caramel">
        <div class="ui-kit-toggle-label">
          <span class="ui-kit-status-dot is-active"></span>
          <strong>Digital Caramel</strong>
        </div>
        <div class="ui-kit-segmented-control" role="group">
          <button type="button" class="ui-kit-seg-btn ui-kit-seg-btn--on is-active" data-seg-val="1">Вкл</button>
          <button type="button" class="ui-kit-seg-btn ui-kit-seg-btn--off" data-seg-val="0">Откл</button>
        </div>
      </div>

      <!-- Переключатель 3 -->
      <div class="ui-kit-toggle-item" data-toggle-box="adfinity">
        <div class="ui-kit-toggle-label">
          <span class="ui-kit-status-dot is-active"></span>
          <strong>Adfinity (авто-встраивание)</strong>
        </div>
        <div class="ui-kit-segmented-control" role="group">
          <button type="button" class="ui-kit-seg-btn ui-kit-seg-btn--on is-active" data-seg-val="1">Вкл</button>
          <button type="button" class="ui-kit-seg-btn ui-kit-seg-btn--off" data-seg-val="0">Откл</button>
        </div>
      </div>

      <!-- Переключатель 4 -->
      <div class="ui-kit-toggle-item" data-toggle-box="mps">
        <div class="ui-kit-toggle-label">
          <span class="ui-kit-status-dot is-active"></span>
          <strong>MPS / Mediascope</strong>
        </div>
        <div class="ui-kit-segmented-control" role="group">
          <button type="button" class="ui-kit-seg-btn ui-kit-seg-btn--on is-active" data-seg-val="1">Вкл</button>
          <button type="button" class="ui-kit-seg-btn ui-kit-seg-btn--off" data-seg-val="0">Откл</button>
        </div>
      </div>

      <!-- Переключатель 5 -->
      <div class="ui-kit-toggle-item" data-toggle-box="seo-throttle">
        <div class="ui-kit-toggle-label">
          <span class="ui-kit-status-dot"></span>
          <strong>SEO Throttle</strong>
        </div>
        <div class="ui-kit-segmented-control" role="group">
          <button type="button" class="ui-kit-seg-btn ui-kit-seg-btn--on" data-seg-val="1">Вкл</button>
          <button type="button" class="ui-kit-seg-btn ui-kit-seg-btn--off is-active" data-seg-val="0">Откл</button>
        </div>
      </div>

      <!-- Переключатель 6 -->
      <div class="ui-kit-toggle-item" data-toggle-box="optimizer">
        <div class="ui-kit-toggle-label">
          <span class="ui-kit-status-dot is-active"></span>
          <strong>Оптимизация изображений</strong>
        </div>
        <div class="ui-kit-segmented-control" role="group">
          <button type="button" class="ui-kit-seg-btn ui-kit-seg-btn--on is-active" data-seg-val="1">Вкл</button>
          <button type="button" class="ui-kit-seg-btn ui-kit-seg-btn--off" data-seg-val="0">Откл</button>
        </div>
      </div>
    </div>
  </div>

  <!-- =========================================================================
       СЕКЦИЯ 5: БЕЙДЖИ, СТАТУСЫ И ПУЛЬСИРУЮЩИЕ ИНДИКАТОРЫ
       ========================================================================= -->
  <div id="sec-badges" class="sz-panel ui-kit-section">
    <div class="ui-kit-section__head">
      <div>
        <span class="sz-eyebrow">Status Indicators</span>
        <h2>Бейджи и индикаторы состояния (.badge)</h2>
        <p class="text-muted">
          Сплошные и пастельные бейджи с высоким коэффициентом контрастности для фильтров, таблиц и списков записей.
        </p>
      </div>
    </div>

    <div class="row g-4">
      <div class="col-md-6">
        <div class="sz-surface">
          <h4 style="font-size:14px;margin-bottom:12px;">Стандартные бейджи (Bootstrap 5)</h4>
          <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
            <span class="badge text-bg-success"><span class="material-symbols-rounded" style="font-size:13px;">check_circle</span> Опубликовано</span>
            <span class="badge text-bg-warning"><span class="material-symbols-rounded" style="font-size:13px;">pending</span> На проверке</span>
            <span class="badge text-bg-secondary"><span class="material-symbols-rounded" style="font-size:13px;">edit_note</span> Черновик</span>
            <span class="badge text-bg-danger"><span class="material-symbols-rounded" style="font-size:13px;">error</span> Ошибка / Jail</span>
            <span class="badge text-bg-primary"><span class="material-symbols-rounded" style="font-size:13px;">star</span> Главная</span>
            <span class="badge text-bg-info"><span class="material-symbols-rounded" style="font-size:13px;">info</span> Справка</span>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="sz-surface">
          <h4 style="font-size:14px;margin-bottom:12px;">Пастельные бейджи (Soft Badges)</h4>
          <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
            <span class="badge badge-soft-success">● Активен</span>
            <span class="badge badge-soft-warning">● В обработке</span>
            <span class="badge badge-soft-danger">● Отключен</span>
            <span class="badge badge-soft-primary">● Администратор</span>
            <span class="badge badge-soft-info">● 142 запроса</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- =========================================================================
       СЕКЦИЯ 6: СЕГМЕНТИРОВАННЫЕ ВКЛАДКИ РАЗДЕЛОВ (AdminTabs)
       ========================================================================= -->
  <div id="sec-tabs" class="sz-panel ui-kit-section">
    <div class="ui-kit-section__head">
      <div>
        <span class="sz-eyebrow">Navigation Tabs</span>
        <h2>Вкладки разделов (.nav-pills-solid)</h2>
        <p class="text-muted">
          Сегментированные переключатели для многовкладочных страниц (как в Посещаемости сайтов, Антиботе и Рекламе).
        </p>
      </div>
    </div>

    <ul class="nav-pills-solid" id="demoTabs">
      <li><button class="nav-link active" data-tab="0" type="button"><span class="material-symbols-rounded">dashboard</span> Обзор системы</button></li>
      <li><button class="nav-link" data-tab="1" type="button"><span class="material-symbols-rounded">history</span> Журнал аудита</button></li>
      <li><button class="nav-link" data-tab="2" type="button"><span class="material-symbols-rounded">settings</span> Конфигурация</button></li>
    </ul>

    <div id="demoTabContent" class="sz-surface demo-tab-pane">
      <div id="tabPane0" class="tab-pane-content is-active">
        <h4 style="margin:0 0 6px;">Обзор компонентов системы</h4>
        <p class="text-muted" style="margin:0;">Оперативная статистика, активность сессий и статус модулей панели управления в реальном времени.</p>
      </div>
      <div id="tabPane1" class="tab-pane-content" hidden>
        <h4 style="margin:0 0 6px;">Журнал аудита и безопасности</h4>
        <p class="text-muted" style="margin:0;">События входа администраторов, фиксация изменений прав доступа RBAC и блокировок Антибота.</p>
      </div>
      <div id="tabPane2" class="tab-pane-content" hidden>
        <h4 style="margin:0 0 6px;">Параметры и системные триггеры</h4>
        <p class="text-muted" style="margin:0;">Глобальные настройки кеширования, интеграции с поисковыми роботами и управление токенами.</p>
      </div>
    </div>
  </div>

  <!-- =========================================================================
       СЕКЦИЯ 7: ФОРМЫ РЕДАКТИРОВАНИЯ (ActiveForm)
       ========================================================================= -->
  <div id="sec-forms" class="sz-panel ui-kit-section">
    <div class="ui-kit-section__head">
      <div>
        <span class="sz-eyebrow">Form Controls</span>
        <h2>Элементы форм и валидация (ActiveForm)</h2>
        <p class="text-muted">
          Унифицированные поля ввода с высотой 42px, выразительным фокусом, встроенными индикаторами валидации и плавной тенью.
        </p>
      </div>
    </div>

    <form id="demoForm" class="row g-3">
      <div class="col-md-6">
        <label class="form-label" for="inpTitle">Заголовок материала <span class="text-danger">*</span></label>
        <input class="form-control is-valid" id="inpTitle" value="Весенние раскраски для малышей" required placeholder="Введите заголовок">
        <div class="valid-feedback" style="display:block;font-size:11.5px;">✓ Заголовок проверен и доступен</div>
      </div>

      <div class="col-md-3">
        <label class="form-label" for="inpStatus">Статус публикации</label>
        <select class="form-select" id="inpStatus">
          <option selected>Опубликовано</option>
          <option>На проверке</option>
          <option>Черновик</option>
        </select>
      </div>

      <div class="col-md-3">
        <label class="form-label" for="inpDate">Дата публикации</label>
        <div class="input-group date">
          <span class="input-group-text"><span class="material-symbols-rounded" style="font-size:16px;">calendar_today</span></span>
          <input class="form-control" id="inpDate" type="date" value="2026-11-24">
        </div>
      </div>

      <div class="col-12">
        <label class="form-label" for="inpText">Краткое описание (мета-тег description)</label>
        <textarea class="form-control" id="inpText" rows="3" placeholder="Введите краткое описание...">Коллекция детских раскрасок формата А4 для печати и онлайн-рисования.</textarea>
        <div class="d-flex justify-content-between mt-1">
          <span class="text-muted" style="font-size:11.5px;">Рекомендуемая длина: 140–160 символов</span>
          <span class="text-muted" style="font-size:11.5px;" id="charCount">74 / 160</span>
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-check form-switch" style="padding-top:6px;">
          <input class="form-check-input" type="checkbox" id="swMainPage" checked>
          <label class="form-check-label" for="swMainPage" style="font-size:13px;font-weight:600;">Выводить в рекомендованные на главной странице</label>
        </div>
      </div>

      <div class="col-md-6 text-end">
        <button class="btn btn-secondary me-2" type="reset">Сбросить</button>
        <button class="btn btn-primary" type="submit"><span class="material-symbols-rounded">save</span> Сохранить запись</button>
      </div>
    </form>
  </div>

  <!-- =========================================================================
       СЕКЦИЯ 8: ТАБЛИЦА ЗАПИСЕЙ (Yii2 GridView)
       ========================================================================= -->
  <div id="sec-table" class="sz-panel ui-kit-section">
    <div class="ui-kit-section__head">
      <div>
        <span class="sz-eyebrow">Data Grid</span>
        <h2>Интерактивная таблица записей (GridView)</h2>
        <p class="text-muted">
          Разметка <code>.table</code> с подсветкой при наведении, миниатюрами, фильтрами поиска, пагинацией и группой быстрых действий.
        </p>
      </div>
      <button class="btn btn-primary" type="button" data-modal="form">
        <span class="material-symbols-rounded">add</span> Добавить запись
      </button>
    </div>

    <!-- Тулбар фильтрации таблицы -->
    <div class="ui-kit-table-toolbar">
      <div class="ui-kit-table-search">
        <span class="material-symbols-rounded ui-kit-search-icon">search</span>
        <input class="form-control" id="tableSearch" type="search" placeholder="Поиск по названию, автору или #ID...">
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

    <!-- Контейнер таблицы -->
    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead>
          <tr>
            <th style="width:75px">ID</th>
            <th style="width:70px">Превью</th>
            <th>Название материала</th>
            <th>Автор</th>
            <th>Статус</th>
            <th>Дата</th>
            <th style="width:125px;text-align:center;">Действия</th>
          </tr>
        </thead>
        <tbody id="demoTableBody"></tbody>
      </table>
    </div>

    <!-- Пагинация таблицы -->
    <div class="ui-kit-table-footer">
      <span class="sz-table-summary" id="tableSummary">Показано 1–5 из 8 записей</span>
      <ul class="pagination" id="tablePagination"></ul>
    </div>
  </div>

  <!-- =========================================================================
       СЕКЦИЯ 9: МЕДИА, POPUP + LAZYLOAD
       ========================================================================= -->
  <div id="sec-media" class="sz-panel ui-kit-section">
    <div class="ui-kit-section__head">
      <div>
        <span class="sz-eyebrow">Media & Viewer</span>
        <h2>Изображения: Popup-просмотрщик и Lazyload</h2>
        <p class="text-muted">
          Миниатюра подгружается через <code>data-src</code>, а клик открывает оригинальное изображение высокого разрешения в полноэкранном модальном окне.
        </p>
      </div>
    </div>

    <!-- Живой пример с кликабельной миниатюрой -->
    <div class="sz-thumb-preview-wrap">
      <a class="sz-thumb sz-thumb--lg" href="<?= Html::encode($demoOriginal) ?>" data-pjax="0" data-image-viewer="true" data-image-full="<?= Html::encode($demoOriginal) ?>" data-image-title="Демо popup: оригинальное изображение">
        <img class="sz-thumb__img" src="<?= $placeholder ?>" data-src="<?= Html::encode($demoOriginal) ?>" alt="Миниатюра" loading="lazy" decoding="async">
      </a>
      <div>
        <strong style="display:block;color:var(--sz-text);font-size:14.5px;margin-bottom:3px;">
          <a href="<?= Html::encode($demoOriginal) ?>" data-pjax="0" data-image-viewer="true" data-image-full="<?= Html::encode($demoOriginal) ?>" data-image-title="Демо popup: оригинальное изображение" style="color:var(--sz-text);text-decoration:none;cursor:pointer;">
            Живой пример <span class="material-symbols-rounded" style="font-size:16px;vertical-align:middle;color:var(--sz-primary);">open_in_new</span>
          </a>
        </strong>
        <div style="color:var(--sz-muted);font-size:12.5px;">
          Нажмите на миниатюру или на ссылку 
          <a href="<?= Html::encode($demoOriginal) ?>" data-pjax="0" data-image-viewer="true" data-image-full="<?= Html::encode($demoOriginal) ?>" data-image-title="Демо popup: оригинальное изображение" class="text-primary text-decoration-none">«Живой пример»</a>, чтобы открыть полноразмерный popup.
        </div>
      </div>
    </div>

    <!-- Сетка паттернов кода (HTML и Yii/PHP) -->
    <div class="row g-3">
      <div class="col-lg-6">
        <div class="sz-code-block-title">HTML-паттерн</div>
        <pre class="sz-code-block"><code>&lt;a class="sz-thumb sz-thumb--lg" href="/img/full.jpg"
   data-pjax="0" data-image-viewer="true"
   data-image-full="/img/full.jpg"
   data-image-title="Оригинальное изображение"&gt;
    &lt;img class="sz-thumb__img" src="/img/load.svg"
         data-src="/img/thumb.jpg" alt="Миниатюра"&gt;
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
    ]),
    $originalUrl,
    [
        'data-pjax' =&gt; '0',
        'data-image-viewer' =&gt; true,
        'data-image-full' =&gt; $originalUrl,
        'class' =&gt; 'sz-thumb sz-thumb--lg',
    ]
);</code></pre>
      </div>
    </div>
  </div>

  <!-- =========================================================================
       СЕКЦИЯ 10: ОЧЕРЕДИ ЗАДАЧ (.sz-progress-job)
       ========================================================================= -->
  <div id="sec-jobs" class="sz-panel ui-kit-section">
    <div class="ui-kit-section__head">
      <div>
        <span class="sz-eyebrow">Async Jobs</span>
        <h2>Очереди фоновых задач (.sz-progress-job)</h2>
        <p class="text-muted">
          Сквиркл-индикатор с вращающейся иконкой <code>#sync</code>, прогресс-баром и динамическим текстовым отчётом шагов.
        </p>
      </div>
    </div>

    <div class="sz-progress-job" data-job>
      <div class="sz-progress-job__status" data-job-status>
        <span class="sz-progress-job__icon" data-job-icon>
          <span class="material-symbols-rounded">sync</span>
        </span>
        <span class="sz-progress-job__text" data-job-text>Ожидание запуска фоновой очереди...</span>
      </div>
      <div class="sz-progress-job__bar-wrap">
        <div class="sz-progress-job__bar" data-job-bar style="width:0%">0%</div>
      </div>
      <div class="d-flex justify-content-between text-muted" style="font-size:12px;">
        <span data-job-step>Шаг 0 из 5</span>
        <span data-job-meta>0%</span>
      </div>
      <div style="display:flex;gap:10px;margin-top:6px;">
        <button class="btn btn-primary" type="button" data-job-run>
          <span class="material-symbols-rounded">play_arrow</span> Запустить очередь
        </button>
        <button class="btn btn-secondary" type="button" data-job-reset>
          <span class="material-symbols-rounded">restart_alt</span> Сбросить
        </button>
      </div>
    </div>
  </div>

  <!-- =========================================================================
       СЕКЦИЯ 11: АЛЕРТЫ, TOAST-УВЕДОМЛЕНИЯ И МОДАЛЬНЫЕ ОКНА
       ========================================================================= -->
  <div id="sec-feedback" class="sz-panel ui-kit-section">
    <div class="ui-kit-section__head">
      <div>
        <span class="sz-eyebrow">Feedback & Modals</span>
        <h2>Системные алерты, Toasts и диалоговые окна</h2>
        <p class="text-muted">
          Контекстные уведомления и интерактивные модальные диалоги с размытием фона и плавной анимацией.
        </p>
      </div>
    </div>

    <!-- Алерты -->
    <div class="row g-3 mb-4">
      <div class="col-md-6">
        <div class="alert alert-success d-flex align-items-center gap-2 mb-0" role="alert">
          <span class="material-symbols-rounded">check_circle</span>
          <div>Операция успешно выполнена: конфигурация обновлена.</div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="alert alert-danger d-flex align-items-center gap-2 mb-0" role="alert">
          <span class="material-symbols-rounded">error</span>
          <div>Обнаружен конфликт доступа: проверьте права роли RBAC.</div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="alert alert-warning d-flex align-items-center gap-2 mb-0" role="alert">
          <span class="material-symbols-rounded">warning</span>
          <div>Лимит запросов исчерпан: включён режим замедления SEO.</div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="alert alert-info d-flex align-items-center gap-2 mb-0" role="alert">
          <span class="material-symbols-rounded">info</span>
          <div>Справка: кэш фронтенда обновляется автоматически.</div>
        </div>
      </div>
    </div>

    <!-- Кнопки вызова Toasts -->
    <div class="sz-surface">
      <h4 style="font-size:14px;margin-bottom:10px;">Проверка всплывающих уведомлений (Toast)</h4>
      <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <button class="btn btn-success btn-sm" type="button" data-toast="Успех: Данные успешно сохранены в базе данных">
          Toast: Успех
        </button>
        <button class="btn btn-danger btn-sm" type="button" data-toast="Ошибка: Не удалось подключиться к удаленному API">
          Toast: Ошибка
        </button>
        <button class="btn btn-warning btn-sm" type="button" data-toast="Внимание: Требуется подтверждение email">
          Toast: Предупреждение
        </button>
        <button class="btn btn-info btn-sm" type="button" data-toast="Информация: Выполнена фоновая синхронизация индексов">
          Toast: Информация
        </button>
      </div>
    </div>
  </div>

</div>

<!-- МОДАЛЬНОЕ ОКНО СОЗДАНИЯ / УДАЛЕНИЯ -->
<div class="modal-backdrop-custom" id="customModal" hidden>
  <div class="modal-dialog-custom">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <h3 style="margin:0;font-size:17px;font-weight:750;" id="modalTitle">Заголовок</h3>
      <button type="button" id="modalCloseX" style="border:0;background:transparent;color:var(--sz-muted);font-size:20px;cursor:pointer;line-height:1;">✕</button>
    </div>
    <p style="font-size:13px;color:var(--sz-muted);line-height:1.55;margin:0 0 16px;" id="modalText">Текст модального окна.</p>
    <form id="modalForm">
      <div id="modalField" hidden style="margin-bottom:14px;">
        <label class="form-label">Название записи <span class="text-danger">*</span></label>
        <input class="form-control" id="modalInput" placeholder="Введите название материала...">
      </div>
      <div style="display:flex;gap:10px;justify-content:flex-end;">
        <button type="button" class="btn btn-secondary" id="modalCancel">Отмена</button>
        <button type="submit" class="btn btn-primary" id="modalConfirm">Подтвердить</button>
      </div>
    </form>
  </div>
</div>

<!-- ВСПЛЫВАЮЩИЙ TOAST -->
<div class="toast" id="toast"></div>

<!-- =========================================================================
     СТИЛИ СТРАНИЦЫ UI-KIT (АДАПТИВНЫЕ ТОКЕНЫ, ПЕРЕКЛЮЧАТЕЛЬ ТЕМ, КАРТОЧКИ)
     ========================================================================= -->
<?php
$this->registerCss(<<<'CSS'
/* Обертка страницы UI-Kit */
.ui-kit-wrapper {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

/* Hero-баннер UI-Kit */
.ui-kit-hero {
  border-radius: var(--sz-radius-lg, 24px);
  padding: 28px 32px;
  background: #ffffff !important;
  border: 1px solid var(--stroke, #e7edfb) !important;
  box-shadow: 0 10px 30px rgba(20, 50, 120, 0.05) !important;
  position: relative;
}

body.dark .ui-kit-hero,
html.dark .ui-kit-hero {
  background: #17243c !important;
  border-color: #2c3f5d !important;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25) !important;
}

.ui-kit-hero__header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 24px;
  flex-wrap: wrap;
}

.ui-kit-hero__titles {
  max-width: 680px;
}

.ui-kit-badges {
  display: flex;
  gap: 8px;
  margin-bottom: 12px;
  flex-wrap: wrap;
}

.ui-kit-hero__h1 {
  font-size: 26px;
  font-weight: 800;
  margin: 0 0 8px 0;
  letter-spacing: -0.4px;
  color: var(--sz-text);
}

.ui-kit-hero__lead {
  font-size: 14px;
  color: var(--sz-muted);
  line-height: 1.6;
  margin: 0;
}

/* Интерактивная карточка переключения тем */
.ui-kit-theme-card {
  background: var(--sz-bg-soft, rgba(255, 255, 255, 0.7));
  border: 1px solid var(--sz-line);
  border-radius: 18px;
  padding: 14px 18px;
  min-width: 250px;
  display: flex;
  flex-direction: column;
  gap: 10px;
  box-shadow: 0 4px 16px rgba(25, 45, 90, 0.04);
}

body.dark .ui-kit-theme-card {
  background: #17243c;
  border-color: #354766;
}

.ui-kit-theme-card__label {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 12px;
  font-weight: 750;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: var(--sz-muted);
}

.ui-kit-theme-card__label .material-symbols-rounded {
  font-size: 16px;
  color: var(--sz-primary);
}

.ui-kit-theme-toggle {
  display: inline-flex;
  padding: 4px;
  background: rgba(113, 157, 235, 0.12);
  border-radius: 12px;
  gap: 4px;
  border: 1px solid var(--sz-line);
}

body.dark .ui-kit-theme-toggle {
  background: #111a2c;
  border-color: #273750;
}

.ui-kit-theme-btn {
  border: 0;
  background: transparent;
  padding: 6px 14px;
  border-radius: 9px;
  font-size: 12.5px;
  font-weight: 650;
  color: var(--sz-muted);
  display: inline-flex;
  align-items: center;
  gap: 6px;
  cursor: pointer;
  transition: all 0.18s ease;
  flex: 1;
  justify-content: center;
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
  font-weight: 750;
  box-shadow: 0 2px 8px rgba(30, 64, 175, 0.15);
}

body.dark .ui-kit-theme-btn.is-active {
  background: linear-gradient(125deg, #3189f7, #2465d6);
  color: #ffffff;
  box-shadow: 0 4px 12px rgba(49, 137, 247, 0.35);
}

.ui-kit-theme-status {
  font-size: 11.5px;
  color: var(--sz-muted);
  text-align: center;
}

.ui-kit-theme-status strong {
  color: var(--sz-text);
}

/* Навигационные пиллы по разделам */
.ui-kit-quicknav {
  margin-top: 20px;
  padding-top: 16px;
  border-top: 1px solid var(--sz-line);
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}

.ui-kit-quicknav__label {
  font-size: 11.5px;
  font-weight: 750;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: var(--sz-muted);
}

.ui-kit-quicknav__links {
  display: flex;
  gap: 6px;
  flex-wrap: wrap;
}

.ui-kit-nav-pill {
  padding: 4px 11px;
  border-radius: 999px;
  background: var(--sz-bg-soft, rgba(255, 255, 255, 0.6));
  border: 1px solid var(--sz-line);
  color: var(--sz-muted);
  font-size: 12px;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.15s ease;
}

.ui-kit-nav-pill:hover {
  background: var(--sz-primary-soft, rgba(49, 137, 247, 0.1));
  color: var(--sz-primary);
  border-color: var(--sz-primary);
  transform: translateY(-1px);
}

/* Секции UI-Kit */
/* Секции UI-Kit с гарантированным сплошным фоном в обеих темах */
.ui-kit-section {
  background: #ffffff !important;
  border: 1px solid var(--stroke, #e7edfb) !important;
  border-radius: 20px !important;
  box-shadow: 0 10px 30px rgba(20, 50, 120, 0.05) !important;
  margin-bottom: 0 !important;
  scroll-margin-top: 75px;
}

body.dark .ui-kit-section,
html.dark .ui-kit-section {
  background: #17243c !important;
  border-color: #2c3f5d !important;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25) !important;
}

.ui-kit-section__head {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 18px;
  gap: 16px;
  flex-wrap: wrap;
}

.ui-kit-section__head h2 {
  font-size: 18.5px;
  font-weight: 780;
  margin: 3px 0 6px 0;
  letter-spacing: -0.3px;
  color: var(--sz-text);
}

.ui-kit-section__head p {
  font-size: 13px;
  margin: 0;
  max-width: 720px;
}

/* Палитра токенов */
.ui-kit-swatches-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(215px, 1fr));
  gap: 14px;
  margin-bottom: 20px;
}

.ui-kit-swatch-card {
  background: var(--sz-surface-strong, #ffffff);
  border: 1px solid var(--sz-line);
  border-radius: 14px;
  padding: 12px;
  display: flex;
  gap: 12px;
  align-items: center;
  box-shadow: 0 2px 8px rgba(30, 50, 90, 0.03);
  cursor: pointer;
  transition: transform 0.15s, box-shadow 0.15s, border-color 0.15s;
}

body.dark .ui-kit-swatch-card {
  background: #17243c;
  border-color: #314460;
}

.ui-kit-swatch-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 18px rgba(30, 50, 90, 0.08);
  border-color: var(--sz-primary);
}

.ui-kit-swatch-color {
  width: 44px;
  height: 44px;
  border-radius: 10px;
  flex-shrink: 0;
  box-shadow: inset 0 0 0 1px rgba(0, 0, 0, 0.08);
}

.ui-kit-swatch-info {
  display: flex;
  flex-direction: column;
  gap: 2px;
  overflow: hidden;
}

.ui-kit-swatch-info strong {
  font-size: 13px;
  color: var(--sz-text);
}

.ui-kit-swatch-info code {
  font-size: 11px;
  padding: 1px 4px;
  border-radius: 4px;
  width: fit-content;
}

.ui-kit-swatch-info small {
  font-size: 11px;
  color: var(--sz-muted);
  line-height: 1.3;
}

/* Архитектурные слои */
.ui-kit-surface-layers {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 14px;
}

@media (max-width: 768px) {
  .ui-kit-surface-layers {
    grid-template-columns: 1fr;
  }
}

.ui-kit-surface-layer {
  padding: 16px;
  border-radius: 14px;
  border: 1px solid var(--sz-line);
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.ui-kit-surface-layer--canvas {
  background: var(--sz-bg);
}

.ui-kit-surface-layer--panel {
  background: var(--sz-surface);
}

.ui-kit-surface-layer--surface {
  background: var(--sz-surface-strong, #ffffff);
}

body.dark .ui-kit-surface-layer--surface {
  background: #203352;
}

.ui-kit-layer-tag {
  font-size: 10.5px;
  font-weight: 750;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: var(--sz-primary);
}

.ui-kit-surface-layer strong {
  font-size: 13.5px;
  color: var(--sz-text);
}

.ui-kit-surface-layer p {
  font-size: 12px;
  color: var(--sz-muted);
  margin: 0;
  line-height: 1.45;
}

/* Типографические строки */
.ui-kit-type-sample {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.ui-kit-type-row {
  display: flex;
  flex-direction: column;
  gap: 3px;
  padding-bottom: 12px;
  border-bottom: 1px dashed var(--sz-line);
}

.ui-kit-type-row:last-child {
  border-bottom: 0;
  padding-bottom: 0;
}

.ui-kit-type-meta {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: var(--sz-muted);
}

/* Кнопки */
.ui-kit-component-row {
  margin-bottom: 18px;
}

.ui-kit-component-row:last-child {
  margin-bottom: 0;
}

.ui-kit-component-label {
  font-size: 12px;
  font-weight: 700;
  color: var(--sz-muted);
  margin-bottom: 8px;
  text-transform: uppercase;
  letter-spacing: 0.4px;
}

.ui-kit-btn-group {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  align-items: center;
}

/* Сегментированные переключатели статуса */
.ui-kit-toggles-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));
  gap: 14px;
}

.ui-kit-toggle-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 10px 14px;
  background: var(--sz-surface-strong, #ffffff);
  border: 1px solid var(--sz-line);
  border-radius: 999px;
  box-shadow: 0 2px 8px rgba(25, 45, 90, 0.03);
  transition: border-color 0.15s, box-shadow 0.15s;
}

body.dark .ui-kit-toggle-item {
  background: #17243c;
  border-color: #314460;
}

.ui-kit-toggle-label {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  color: var(--sz-text);
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

/* Пульсирующая точка статуса */
.ui-kit-status-dot {
  width: 9px;
  height: 9px;
  border-radius: 50%;
  background: #94a3b8;
  flex-shrink: 0;
  position: relative;
  transition: background-color 0.2s ease;
}

.ui-kit-status-dot.is-active {
  background: #079b70;
  box-shadow: 0 0 0 3px rgba(7, 155, 112, 0.18);
  animation: uiKitDotPulse 2s infinite ease-in-out;
}

@keyframes uiKitDotPulse {
  0% { box-shadow: 0 0 0 2px rgba(7, 155, 112, 0.25); }
  50% { box-shadow: 0 0 0 5px rgba(7, 155, 112, 0.08); }
  100% { box-shadow: 0 0 0 2px rgba(7, 155, 112, 0.25); }
}

/* Сегментный контрол [ Вкл | Откл ] */
.ui-kit-segmented-control {
  display: inline-flex;
  padding: 2px;
  background: rgba(113, 157, 235, 0.12);
  border-radius: 999px;
  border: 1px solid var(--sz-line);
  flex-shrink: 0;
}

body.dark .ui-kit-segmented-control {
  background: #111a2c;
  border-color: #263852;
}

.ui-kit-seg-btn {
  border: 0;
  background: transparent;
  padding: 3px 12px;
  border-radius: 999px;
  font-size: 11.5px;
  font-weight: 650;
  color: var(--sz-muted);
  cursor: pointer;
  transition: all 0.15s ease;
}

.ui-kit-seg-btn:hover {
  color: var(--sz-text);
}

/* Активное состояние "Вкл" */
.ui-kit-seg-btn--on.is-active {
  background: #079b70;
  color: #ffffff;
  font-weight: 750;
  box-shadow: 0 2px 6px rgba(7, 155, 112, 0.35);
}

/* Активное состояние "Откл" */
.ui-kit-seg-btn--off.is-active {
  background: #e24e6c;
  color: #ffffff;
  font-weight: 750;
  box-shadow: 0 2px 6px rgba(226, 78, 108, 0.35);
}

/* Пастельные бейджи */
.badge-soft-success { background: rgba(7, 155, 112, 0.12) !important; color: #079b70 !important; }
.badge-soft-warning { background: rgba(238, 166, 60, 0.14) !important; color: #c48016 !important; }
.badge-soft-danger  { background: rgba(226, 78, 108, 0.12) !important; color: #e24e6c !important; }
.badge-soft-primary { background: rgba(49, 137, 247, 0.12) !important; color: #3189f7 !important; }
.badge-soft-info    { background: rgba(40, 101, 190, 0.12) !important; color: #2865be !important; }

body.dark .badge-soft-success { background: rgba(7, 155, 112, 0.22) !important; color: #4ae0b1 !important; }
body.dark .badge-soft-warning { background: rgba(238, 166, 60, 0.22) !important; color: #ffc46a !important; }
body.dark .badge-soft-danger  { background: rgba(226, 78, 108, 0.22) !important; color: #ff849d !important; }
body.dark .badge-soft-primary { background: rgba(49, 137, 247, 0.22) !important; color: #87bbff !important; }
body.dark .badge-soft-info    { background: rgba(40, 101, 190, 0.22) !important; color: #8cb8f8 !important; }

/* Вкладки */
.demo-tab-pane {
  margin-top: 10px;
}

.tab-pane-content {
  display: none;
}

.tab-pane-content.is-active {
  display: block;
}

/* Таблица тулбар */
.ui-kit-table-toolbar {
  display: flex;
  gap: 12px;
  margin-bottom: 14px;
  flex-wrap: wrap;
  justify-content: space-between;
}

.ui-kit-table-search {
  max-width: 360px;
  flex: 1;
  position: relative;
}

.ui-kit-search-icon {
  position: absolute;
  left: 11px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--sz-muted);
  font-size: 18px;
  pointer-events: none;
}

.ui-kit-table-search input {
  padding-left: 36px !important;
}

.ui-kit-table-filter {
  width: 180px;
}

.ui-kit-table-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 14px;
  flex-wrap: wrap;
  gap: 10px;
}
CSS);
?>

<!-- =========================================================================
     JAVASCRIPT: ИНТЕРАКТИВНОЕ ПЕРЕКЛЮЧЕНИЕ ТЕМ, ТАБЛИЦА, СЕГМЕНТЫ, ЗАДАЧИ
     ========================================================================= -->
<?php
$this->registerJs(<<<'JS'
(function () {
  'use strict';

  const $ = id => document.getElementById(id);
  const safe = s => String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

  // =========================================================================
  // 1. ИНТЕРАКТИВНОЕ ПЕРЕКЛЮЧЕНИЕ ТЕМЫ (LIGHT / DARK)
  // =========================================================================
  const btnLight = $('btnThemeLight');
  const btnDark = $('btnThemeDark');
  const labelStatus = $('currentThemeLabel');

  function syncThemeUi(isDark) {
    if (btnLight && btnDark) {
      btnLight.classList.toggle('is-active', !isDark);
      btnDark.classList.toggle('is-active', isDark);
    }
    if (labelStatus) {
      labelStatus.textContent = isDark ? 'Тёмная' : 'Светлая';
    }

    // Синхронизируем также кнопку в topbar layout
    const topbarThemeBtn = document.querySelector('[data-sz-theme-toggle]');
    if (topbarThemeBtn) {
      const icon = topbarThemeBtn.querySelector('.material-symbols-rounded, .icon');
      if (icon) icon.textContent = isDark ? 'light_mode' : 'dark_mode';
      topbarThemeBtn.setAttribute('title', isDark ? 'Светлая тема' : 'Темная тема');
      topbarThemeBtn.setAttribute('aria-label', isDark ? 'Светлая тема' : 'Темная тема');
    }
  }

  function setTheme(theme) {
    const isDark = (theme === 'dark');
    document.documentElement.classList.toggle('dark', isDark);
    document.body.classList.toggle('dark', isDark);

    try {
      localStorage.setItem('admin-theme', theme);
      document.cookie = 'admin-theme=' + theme + '; path=/; max-age=31536000; SameSite=Lax';
    } catch (e) {}

    syncThemeUi(isDark);
  }

  // Начальная инициализация состояния темы
  const isCurrentDark = document.documentElement.classList.contains('dark') || document.body.classList.contains('dark');
  syncThemeUi(isCurrentDark);

  if (btnLight) {
    btnLight.addEventListener('click', () => {
      setTheme('light');
      if (typeof window.showToast === 'function') {
        window.showToast('Включена светлая тема оформления');
      }
    });
  }

  if (btnDark) {
    btnDark.addEventListener('click', () => {
      setTheme('dark');
      if (typeof window.showToast === 'function') {
        window.showToast('Включена тёмная тема оформления');
      }
    });
  }

  // Слушаем клик по topbar кнопке, чтобы обновить UI-Kit переключатель
  const topbarThemeBtn = document.querySelector('[data-sz-theme-toggle]');
  if (topbarThemeBtn) {
    topbarThemeBtn.addEventListener('click', () => {
      setTimeout(() => {
        const isDarkNow = document.documentElement.classList.contains('dark') || document.body.classList.contains('dark');
        syncThemeUi(isDarkNow);
      }, 50);
    });
  }

  // =========================================================================
  // 2. СЕГМЕНТИРОВАННЫЕ ПЕРЕКЛЮЧАТЕЛИ СТАТУСА [ ВКЛ | ОТКЛ ]
  // =========================================================================
  document.querySelectorAll('[data-toggle-box]').forEach(box => {
    const title = box.querySelector('.ui-kit-toggle-label strong')?.textContent || 'Параметр';
    const dot = box.querySelector('.ui-kit-status-dot');
    const btnOn = box.querySelector('.ui-kit-seg-btn--on');
    const btnOff = box.querySelector('.ui-kit-seg-btn--off');

    function updateState(isOn) {
      if (btnOn) btnOn.classList.toggle('is-active', isOn);
      if (btnOff) btnOff.classList.toggle('is-active', !isOn);
      if (dot) dot.classList.toggle('is-active', isOn);
    }

    if (btnOn) {
      btnOn.addEventListener('click', () => {
        updateState(true);
        if (typeof window.showToast === 'function') {
          window.showToast(title + ': статус изменён на «Вкл»');
        }
      });
    }

    if (btnOff) {
      btnOff.addEventListener('click', () => {
        updateState(false);
        if (typeof window.showToast === 'function') {
          window.showToast(title + ': статус изменён на «Откл»');
        }
      });
    }
  });

  // =========================================================================
  // 3. КОПИРОВАНИЕ ТОКЕНОВ ПРИ КЛИКЕ НА SWATCH
  // =========================================================================
  document.querySelectorAll('[data-copy]').forEach(card => {
    card.addEventListener('click', () => {
      const val = card.dataset.copy;
      if (navigator.clipboard) {
        navigator.clipboard.writeText(val).then(() => {
          if (typeof window.showToast === 'function') {
            window.showToast('Скопировано: ' + val);
          }
        });
      }
    });
  });

  // =========================================================================
  // 4. ТАБЫ РАЗДЕЛОВ (AdminTabs)
  // =========================================================================
  const demoTabs = $('demoTabs');
  if (demoTabs) {
    demoTabs.addEventListener('click', e => {
      const btn = e.target.closest('[data-tab]');
      if (!btn) return;
      const idx = Number(btn.dataset.tab);
      demoTabs.querySelectorAll('.nav-link').forEach(b => b.classList.toggle('active', b === btn));
      for (let i = 0; i < 3; i++) {
        const pane = $('tabPane' + i);
        if (pane) {
          pane.hidden = (i !== idx);
          pane.classList.toggle('is-active', i === idx);
        }
      }
    });
  }

  // =========================================================================
  // 5. ДЕМО-ТАБЛИЦА GRIDVIEW (ПОИСК, ФИЛЬТР, ПАГИНАЦИЯ)
  // =========================================================================
  const demoRows = [
    [15621, 'Весенние раскраски для детей', 'Редактор', 'published', '10.10.2026'],
    [15620, 'Космические приключения: марсоход', 'Ольга', 'pending', '10.10.2026'],
    [15619, 'Осенний пейзаж и деревья', 'Редактор', 'draft', '09.10.2026'],
    [15618, 'Архитектура старого города', 'Николай', 'published', '08.10.2026'],
    [15617, 'Сказочный лес и грибы', 'Ольга', 'published', '07.10.2026'],
    [15616, 'Новогодние открытки 2027', 'Редактор', 'pending', '06.10.2026'],
    [15615, 'Цветы и садовые растения', 'Николай', 'draft', '05.10.2026'],
    [15614, 'Путешествия по городам мира', 'Ольга', 'published', '04.10.2026']
  ];

  const statusBadges = {
    published: ['Опубликовано', 'text-bg-success'],
    draft: ['Черновик', 'text-bg-secondary'],
    pending: ['На проверке', 'text-bg-warning']
  };

  let tablePage = 1;
  function renderTable() {
    const searchInput = $('tableSearch');
    const filterSelect = $('tableFilter');
    const tableBody = $('demoTableBody');
    if (!tableBody) return;

    const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
    const filter = filterSelect ? filterSelect.value : 'all';
    const filtered = demoRows.filter(r => (r[1] + ' ' + r[2] + ' ' + r[0]).toLowerCase().includes(query) && (filter === 'all' || r[3] === filter));
    const perPage = 5;
    const totalPages = Math.max(1, Math.ceil(filtered.length / perPage));
    tablePage = Math.min(tablePage, totalPages);

    const currentRows = filtered.slice((tablePage - 1) * perPage, tablePage * perPage);
    tableBody.innerHTML = currentRows.map(row => {
      const b = statusBadges[row[3]] || ['Черновик', 'text-bg-secondary'];
      return `
        <tr>
          <td><strong style="color:var(--sz-primary);">#${row[0]}</strong></td>
          <td>
            <div class="sz-thumb" style="width:36px;height:36px;border-radius:8px;">
              <span class="material-symbols-rounded" style="color:var(--sz-muted);font-size:20px;display:grid;place-items:center;width:100%;height:100%;">image</span>
            </div>
          </td>
          <td>
            <strong>${safe(row[1])}</strong>
            <small class="text-muted">/catalog/item-${row[0]}</small>
          </td>
          <td>${safe(row[2])}</td>
          <td><span class="badge ${b[1]}">${b[0]}</span></td>
          <td>${row[4]}</td>
          <td style="text-align:center;">
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
      if (btn) { tablePage = Number(btn.dataset.page); renderTable(); }
    });
  }
  renderTable();

  // Подсчёт символов в textarea
  const inpText = $('inpText');
  const charCount = $('charCount');
  if (inpText && charCount) {
    inpText.addEventListener('input', () => {
      charCount.textContent = inpText.value.length + ' / 160';
    });
  }

  // =========================================================================
  // 6. МОДАЛЬНОЕ ОКНО (BOOTSTRAP MODAL)
  // =========================================================================
  let modalAction = '';
  function openModal(type) {
    modalAction = type;
    const isDelete = (type === 'delete');
    const modalTitle = $('modalTitle');
    const modalText = $('modalText');
    const modalField = $('modalField');
    const modalConfirm = $('modalConfirm');
    const customModal = $('customModal');

    if (modalTitle) modalTitle.textContent = isDelete ? 'Подтверждение удаления' : 'Создание нового материала';
    if (modalText) modalText.textContent = isDelete ? 'Вы действительно хотите удалить эту запись? Действие необратимо.' : 'Заполните параметры нового материала:';
    if (modalField) modalField.hidden = isDelete;
    if (modalConfirm) {
      modalConfirm.textContent = isDelete ? 'Удалить' : 'Сохранить';
      modalConfirm.className = isDelete ? 'btn btn-danger' : 'btn btn-primary';
    }
    if (customModal) customModal.hidden = false;
  }

  function closeModal() {
    const customModal = $('customModal');
    if (customModal) customModal.hidden = true;
    const modalInput = $('modalInput');
    if (modalInput) modalInput.value = '';
  }

  const modalCancel = $('modalCancel');
  if (modalCancel) modalCancel.addEventListener('click', closeModal);
  const modalCloseX = $('modalCloseX');
  if (modalCloseX) modalCloseX.addEventListener('click', closeModal);

  const modalForm = $('modalForm');
  if (modalForm) {
    modalForm.addEventListener('submit', e => {
      e.preventDefault();
      closeModal();
      if (typeof window.showToast === 'function') {
        window.showToast(modalAction === 'delete' ? 'Запись успешно удалена (демо)' : 'Запись успешно сохранена (демо)');
      }
    });
  }

  const demoForm = $('demoForm');
  if (demoForm) {
    demoForm.addEventListener('submit', e => {
      e.preventDefault();
      if (typeof window.showToast === 'function') {
        window.showToast('Форма успешно валидирована и сохранена');
      }
    });
  }

  document.addEventListener('click', e => {
    const m = e.target.closest('[data-modal]');
    if (m) openModal(m.dataset.modal);
  });

  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeModal();
  });

  // =========================================================================
  // 7. СИМУЛЯТОР ОЧЕРЕДЕЙ ЗАДАЧ .sz-progress-job
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
      if (jobText) jobText.textContent = 'Выполняется фоновая обработка задачи...';
      if (jobRun) jobRun.disabled = true;
    } else if (state === 'done') {
      if (jobIcon) {
        jobIcon.classList.remove('is-spinning');
        jobIcon.innerHTML = '<span class="material-symbols-rounded" style="color:#079b70">check</span>';
      }
      if (jobText) jobText.textContent = 'Фоновая задача успешно выполнена.';
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
      if (jobProgress === 5) return;
      jobProgress = 0;
      updateJob('running');
      jobTimer = setInterval(() => {
        jobProgress++;
        if (jobProgress >= 5) {
          clearInterval(jobTimer);
          updateJob('done');
          if (typeof window.showToast === 'function') {
            window.showToast('Очередь задач успешно завершена (100%)');
          }
        } else {
          updateJob('running');
        }
      }, 700);
    });
  }

  if (jobReset) {
    jobReset.addEventListener('click', () => {
      clearInterval(jobTimer);
      jobProgress = 0;
      updateJob('idle');
    });
  }
})();
JS, View::POS_READY);
?>
