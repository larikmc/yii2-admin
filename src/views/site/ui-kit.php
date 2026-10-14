<?php

use yii\bootstrap5\Html;
use yii\web\View;

$this->title = 'ADMIN-UI-KIT';
$this->params['breadcrumbs'] = [
    ['label' => 'ADMIN-UI-KIT'],
];
$this->params['pageSubtitle'] = 'Библиотека компонентов Bootstrap 5 · Темы';

$module = Yii::$app->getModule('admin');
$placeholder = $module->getLazyloadPlaceholderUrl($this);
$asset = \larikmc\admin\assets\AppAsset::register($this);
$demoPlaceholder = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 106 106'%3E%3Crect width='106' height='106' fill='%23f1f5fa'/%3E%3Cpath d='M35 70l14-16 11 12 11-14 15 18H35z' fill='%23cad8eb'/%3E%3Ccircle cx='45' cy='42' r='6' fill='%23cad8eb'/%3E%3C/svg%3E";
$demoOriginal = 'https://images.unsplash.com/photo-1579783900882-c0d3dad7b119?w=1200&auto=format&fit=crop&q=80';
?>

<div id="uiKitPage">

  <!-- СЕКЦИЯ: IMAGES (POPUP + LAZYLOAD) ТОЧНО КАК НА СКРИНШОТЕ -->
  <div class="sz-panel" style="margin-bottom:16px;">
    <span class="sz-eyebrow">Images</span>
    <h2>Popup + lazyload</h2>
    <p class="text-muted">
      Миниатюра грузится через <code>data-src</code>, а рорир открывает оригинал из <code>data-image-full</code>.
    </p>

    <!-- Живой пример с кликабельной миниатюрой -->
    <div class="sz-thumb-preview-wrap">
      <a class="sz-thumb sz-thumb--lg" href="<?= Html::encode($demoOriginal) ?>" data-pjax="0" data-image-viewer="true" data-image-full="<?= Html::encode($demoOriginal) ?>" data-image-title="Демо popup: оригинальное изображение">
        <img class="sz-thumb__img" src="<?= $demoPlaceholder ?>" data-src="<?= $demoPlaceholder ?>" alt="Миниатюра">
      </a>
      <div>
        <strong style="display:block;color:var(--sz-text);font-size:14.5px;margin-bottom:3px;">Живой пример</strong>
        <div style="color:var(--sz-muted);font-size:12px;">Нажмите на миниатюру, чтобы открыть рорир.</div>
      </div>
    </div>

    <!-- Сетка паттернов кода (HTML и Yii/PHP) -->
    <div class="row" style="margin-bottom:0;">
      <div class="col-6" style="margin-bottom:0;">
        <div class="sz-code-block-title">HTML-паттерн</div>
        <pre class="sz-code-block"><code>&lt;a class="sz-thumb sz-thumb--lg" href="/admin/assets/d97e694f/img/template.jpg" data-pjax="0" data-image-viewer="true" data-image-full="/admin/assets/d97e694f/img/template.jpg" data-image-title="Демо popup: оригинальное изображение"&gt;
    &lt;img class="sz-thumb__img" src="/admin/assets/d97e694f/img/load.svg" data-src="/admin/assets/d97e694f/img/template.jpg" alt="Миниатюра"&gt;
&lt;/a&gt;</code></pre>
      </div>
      <div class="col-6" style="margin-bottom:0;">
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

  <!-- СЕКЦИЯ: SURFACE (ТИПОВАЯ ПАНЕЛЬ С ТОКЕНАМИ И ТАБЛИЦЕЙ СТРУКТУРЫ) -->
  <div class="sz-panel" style="margin-bottom:16px;">
    <span class="sz-eyebrow">Surface</span>
    <h2>Типовая панель</h2>
    <p class="text-muted">
      Базовая раскладка и архитектурные слои оформления панели управления:
    </p>

    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <th style="width:200px">Компонент</th>
            <th>Описание и поведение</th>
            <th style="width:140px">Класс / Роль</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><strong>Topbar</strong></td>
            <td>Светлая компактная не прилипающая шапка (~54px) с профилем и действиями</td>
            <td><code>.topbar</code> / <code>.sz-topbar</code></td>
          </tr>
          <tr>
            <td><strong>Sidebar</strong></td>
            <td>Dark shell with active state and secondary menu (фиксированные кнопки 44px)</td>
            <td><code>.sidebar</code> / <code>.sz-sidebar</code></td>
          </tr>
          <tr>
            <td><strong>Panel</strong></td>
            <td>Основной белый лист контента страниц с фирменным скруглением и тенью</td>
            <td><code>.sz-panel</code></td>
          </tr>
          <tr>
            <td><strong>Surface</strong></td>
            <td>Типовая полупрозрачная внутренняя подложка для группировки параметров и фильтров</td>
            <td><code>.sz-surface</code></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <div class="row">
    <!-- Секция: Кнопки со свечением (.btn .btn-*) -->
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <div>
            <h3 class="card-title">Кнопки и состояния свечения (.btn .btn-*)</h3>
            <p class="card-subtitle">Primary светится всегда; Success, Danger и Warning загораются при hover; Disabled отключен.</p>
          </div>
        </div>
        <div class="card-body">
          <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
            <button class="btn btn-primary" type="button" data-toast="Основное действие (Primary)">Primary Action</button>
            <button class="btn btn-secondary" type="button" data-toast="Вторичное действие (Secondary)">Secondary</button>
            <button class="btn btn-success" type="button" data-toast="Действие успешно сохранено">Success</button>
            <button class="btn btn-danger" type="button" data-modal="delete">Delete Record</button>
            <button class="btn btn-warning" type="button" data-toast="Предупреждение системы">Warning</button>
            <button class="btn btn-info" type="button" data-toast="Информационное уведомление">Info</button>
            <button class="btn btn-outline-primary" type="button" data-toast="Outline Primary">Outline</button>
            <button class="btn btn-outline-danger" type="button" data-modal="delete">Outline Danger</button>
            <button class="btn btn-primary" type="button" disabled>Disabled</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Секция: ДВА ВАРИАНТА ТАБОВ -->
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <div>
            <h3 class="card-title">Варианты вкладок (AdminTabs)</h3>
            <p class="card-subtitle">Классический стиль с подчеркиванием и монолитные цельные кнопки-пилюли.</p>
          </div>
        </div>
        <div class="card-body">
          <div class="row" style="margin-bottom:0;">
            <!-- Вариант 1: Классические табы с подчеркиванием -->
            <div class="col-6" style="margin-bottom:0;">
              <span style="display:block;font-size:11px;font-weight:750;color:#658bc1;text-transform:uppercase;margin-bottom:10px;letter-spacing:1px;">Вариант 1 · Подчёркивание (.nav-tabs)</span>
              <ul class="nav-tabs" id="demoTabsLine">
                <li><button class="nav-link active" data-tab-line="0" type="button">Обзор системы</button></li>
                <li><button class="nav-link" data-tab-line="1" type="button">Логирование</button></li>
                <li><button class="nav-link" data-tab-line="2" type="button">Параметры</button></li>
              </ul>
              <div id="demoTabContentLine" class="demo-tab-description">
                Классическое оформление с линией-индикатором активного раздела.
              </div>
            </div>

            <!-- Вариант 2: Цельные кнопки (Segmented Pills) -->
            <div class="col-6" style="margin-bottom:0;">
              <span style="display:block;font-size:11px;font-weight:750;color:#658bc1;text-transform:uppercase;margin-bottom:10px;letter-spacing:1px;">Вариант 2 · Цельные кнопки (.nav-pills-solid)</span>
              <ul class="nav-pills-solid" id="demoTabsPills">
                <li><button class="nav-link active" data-tab-pill="0" type="button">Обзор системы</button></li>
                <li><button class="nav-link" data-tab-pill="1" type="button">Логирование</button></li>
                <li><button class="nav-link" data-tab-pill="2" type="button">Параметры</button></li>
              </ul>
              <div id="demoTabContentPill" class="demo-tab-description">
                Сегментированные цельные кнопки на общей подложке: активный таб выделяется выпуклой плашкой.
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Секция: GridView & ActionColumn -->
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <div>
            <h3 class="card-title">Таблица записей (Yii2 GridView)</h3>
            <p class="card-subtitle">Стандартная разметка .table с пагинацией и кнопками ActionColumn.</p>
          </div>
          <button class="btn btn-primary" type="button" data-modal="form">+ Добавить запись</button>
        </div>
        <div class="card-body">
          <div style="display:flex;gap:12px;margin-bottom:14px;flex-wrap:wrap;justify-content:space-between;">
            <div style="max-width:320px;flex:1;">
              <input class="form-control" id="tableSearch" type="search" placeholder="Поиск по названию или автору...">
            </div>
            <div style="width:170px;">
              <select class="form-select" id="tableFilter">
                <option value="all">Все статусы</option>
                <option value="published">Опубликовано</option>
                <option value="draft">Черновик</option>
                <option value="pending">На проверке</option>
              </select>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead>
                <tr>
                  <th style="width:70px">ID</th>
                  <th>Название материала</th>
                  <th>Автор</th>
                  <th>Статус</th>
                  <th>Дата создания</th>
                  <th style="width:120px;text-align:center;">Действия</th>
                </tr>
              </thead>
              <tbody id="demoTableBody"></tbody>
            </table>
          </div>

          <div style="display:flex;justify-content:space-between;align-items:center;margin-top:14px;flex-wrap:wrap;gap:10px;">
            <span style="font-size:12px;color:var(--muted)" id="tableSummary">Показано записей</span>
            <ul class="pagination" id="tablePagination"></ul>
          </div>
        </div>
      </div>
    </div>

    <!-- Секция: ActiveForm -->
    <div class="col-6">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Форма редактирования (ActiveForm)</h3>
        </div>
        <div class="card-body">
          <form id="demoForm">
            <div style="margin-bottom:12px;">
              <label class="form-label" for="inpTitle">Название материала *</label>
              <input class="form-control" id="inpTitle" required placeholder="Введите заголовок">
            </div>
            <div class="row" style="margin-bottom:12px;">
              <div class="col-6" style="margin-bottom:0;">
                <label class="form-label" for="inpStatus">Статус</label>
                <select class="form-select" id="inpStatus">
                  <option>Опубликовано</option>
                  <option>Черновик</option>
                  <option>На проверке</option>
                </select>
              </div>
              <div class="col-6" style="margin-bottom:0;">
                <label class="form-label" for="inpDate">Дата публикации</label>
                <input class="form-control" id="inpDate" type="date">
              </div>
            </div>
            <div style="margin-bottom:14px;">
              <label class="form-label" for="inpText">Описание</label>
              <textarea class="form-control" id="inpText" placeholder="Краткое описание..."></textarea>
            </div>
            <div style="display:flex;gap:10px;">
              <button class="btn btn-primary" type="submit">Сохранить</button>
              <button class="btn btn-secondary" type="reset">Сбросить</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Секция: Прогресс-бар очередей со сквирклом #sync -->
    <div class="col-6">
      <div class="card">
        <div class="card-header">
          <div>
            <h3 class="card-title">Очереди задач (.sz-progress-job)</h3>
            <p class="card-subtitle">Сквиркл-плашка с иконкой обновления #sync.</p>
          </div>
        </div>
        <div class="card-body">
          <div class="sz-progress-job" data-job>
            <div class="sz-progress-job__status" data-job-status>
              <span class="sz-progress-job__icon" data-job-icon>
                <span class="material-symbols-rounded">sync</span>
              </span>
              <span class="sz-progress-job__text" data-job-text>Ожидание запуска задачи.</span>
            </div>
            <div class="sz-progress-job__bar-wrap">
              <div class="sz-progress-job__bar" data-job-bar style="width:0%">0%</div>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:11.5px;color:var(--muted, #7b93be)">
              <span data-job-step>Шаг 0 из 5</span>
              <span data-job-meta>0%</span>
            </div>
            <div style="display:flex;gap:10px;margin-top:6px;">
              <button class="btn btn-primary" type="button" data-job-run>Запустить очередь</button>
              <button class="btn btn-secondary" type="button" data-job-reset>Сбросить</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Модальное окно создания/удаления (Bootstrap Modal) -->
<div class="modal-backdrop-custom" id="customModal" hidden>
  <div class="modal-dialog-custom">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
      <h3 style="margin:0;font-size:17px;color:var(--ink, #0a1d4c);" id="modalTitle">Заголовок</h3>
      <button type="button" id="modalCloseX" style="border:0;background:transparent;color:var(--muted, #7b93be);font-size:18px;cursor:pointer;">✕</button>
    </div>
    <p style="font-size:12.5px;color:var(--muted, #7b93be);line-height:1.55;margin:0 0 16px;" id="modalText">Текст модального окна.</p>
    <form id="modalForm">
      <div id="modalField" hidden style="margin-bottom:14px;">
        <label class="form-label">Название *</label>
        <input class="form-control" id="modalInput" placeholder="Введите значение">
      </div>
      <div style="display:flex;gap:10px;justify-content:flex-end;">
        <button type="button" class="btn btn-secondary" id="modalCancel">Отмена</button>
        <button type="submit" class="btn btn-primary" id="modalConfirm">Подтвердить</button>
      </div>
    </form>
  </div>
</div>

<div class="toast" id="toast"></div>

<?php
$this->registerJs(<<<'JS'
(function () {
  'use strict';

  const $ = id => document.getElementById(id);
  const safe = s => String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

  // Табы Вариант 1 (Линейные)
  const tabTextsLine = [
    'Классическое оформление с линией-индикатором активного раздела.',
    'Логирование событий: история действий администраторов и системных триггеров.',
    'Параметры и конфигурация: редактирование основных констант приложения.'
  ];
  const demoTabsLine = $('demoTabsLine');
  if (demoTabsLine) {
    demoTabsLine.addEventListener('click', e => {
      const btn = e.target.closest('[data-tab-line]');
      if (!btn) return;
      demoTabsLine.querySelectorAll('.nav-link').forEach(b => b.classList.toggle('active', b === btn));
      const lineContent = $('demoTabContentLine');
      if (lineContent) lineContent.textContent = tabTextsLine[Number(btn.dataset.tabLine)];
    });
  }

  // Табы Вариант 2 (Цельные кнопки-пилюли)
  const tabTextsPill = [
    'Сегментированные цельные кнопки на общей подложке: активный таб выделяется выпуклой плашкой.',
    'История и аудит: отслеживание модификаций контента и прав доступа RBAC.',
    'Расширенные свойства: системные переключатели модулей и кэширования.'
  ];
  const demoTabsPills = $('demoTabsPills');
  if (demoTabsPills) {
    demoTabsPills.addEventListener('click', e => {
      const btn = e.target.closest('[data-tab-pill]');
      if (!btn) return;
      demoTabsPills.querySelectorAll('.nav-link').forEach(b => b.classList.toggle('active', b === btn));
      const pillContent = $('demoTabContentPill');
      if (pillContent) pillContent.textContent = tabTextsPill[Number(btn.dataset.tabPill)];
    });
  }

  // Демо-таблица GridView
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
          <td><strong>#${row[0]}</strong></td>
          <td><strong>${safe(row[1])}</strong><small>/catalog/item-${row[0]}</small></td>
          <td>${safe(row[2])}</td>
          <td><span class="badge ${b[1]}">${b[0]}</span></td>
          <td>${row[4]}</td>
          <td style="text-align:center;">
            <div class="btn-group-action">
              <button type="button" title="Просмотр" data-toast="Просмотр записи #${row[0]}"><span class="material-symbols-rounded" style="font-size:16px;">visibility</span></button>
              <button type="button" title="Редактировать" data-toast="Редактирование #${row[0]}"><span class="material-symbols-rounded" style="font-size:16px;">edit</span></button>
              <button type="button" class="del" title="Удалить" data-modal="delete"><span class="material-symbols-rounded" style="font-size:16px;">delete</span></button>
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

  // Модальное окно (Modal)
  let modalAction = '';
  function openModal(type) {
    modalAction = type;
    const isDelete = type === 'delete';
    const modalTitle = $('modalTitle');
    const modalText = $('modalText');
    const modalField = $('modalField');
    const modalConfirm = $('modalConfirm');
    const customModal = $('customModal');

    if (modalTitle) modalTitle.textContent = isDelete ? 'Подтверждение удаления' : 'Создание новой записи';
    if (modalText) modalText.textContent = isDelete ? 'Вы действительно хотите удалить эту запись из базы данных?' : 'Заполните параметры нового материала:';
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
        window.showToast('Форма успешно сохранена (демо)');
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

  // Симулятор задач .sz-progress-job
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
      if (jobText) jobText.textContent = 'Выполняется фоновая задача...';
      if (jobRun) jobRun.disabled = true;
    } else if (state === 'done') {
      if (jobIcon) {
        jobIcon.classList.remove('is-spinning');
        jobIcon.innerHTML = '<span class="material-symbols-rounded" style="color:#079b70">check</span>';
      }
      if (jobText) jobText.textContent = 'Задача успешно завершена.';
      if (jobRun) jobRun.disabled = false;
    } else {
      if (jobIcon) {
        jobIcon.classList.remove('is-spinning');
        jobIcon.innerHTML = '<span class="material-symbols-rounded">sync</span>';
      }
      if (jobText) jobText.textContent = 'Ожидание запуска задачи.';
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
            window.showToast('Очередь задач завершена (100%)');
          }
        } else {
          updateJob('running');
        }
      }, 800);
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
