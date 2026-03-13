# Admin UI README

Этот файл описывает текущий UI-слой админки `larikmc/yii2-admin`, который кастомизирован локально для проекта `skidkum.loc`.

## Где находится UI

- Layout: `src/views/layouts/main.php`
- Базовые стили: `src/web/css/style.css`
- Сайдбар: `src/web/css/dashboard.css`
- Главная dashboard-страница: `src/views/site/index.php`
- Стили dashboard-страницы: `src/web/css/home.css`
- JS для sidebar / dropdown / toast: `src/web/js/dashboard.js`
- Asset bundle: `src/assets/AppAsset.php`

## Как устроен текущий дизайн

- Основа интерфейса: светлый glass/panel стиль на холодной blue/slate палитре.
- Сайдбар: тёмный, глубокий, с мягкой подсветкой и active-state у текущего пункта.
- Верхняя шапка: compact topbar с breadcrumbs и заголовком слева, utility-кнопками и аккаунтом справа.
- Карточки: крупные радиусы, мягкие границы, лёгкие градиенты, без тяжёлых bootstrap-рамок.
- Таблицы: стандартный Yii `GridView`, но actions и часть UI переоформлены вручную.
- Основные кнопки и badges стилизуются через стандартные bootstrap-классы, а не через отдельный набор новых semantic-классов.

## Текущие UI-правила

- Не возвращать стандартные bootstrap alert'ы. Вместо них используются toast-уведомления справа сверху.
- Не дублировать один и тот же смысл в topbar и в контенте страницы.
- Для CRUD-страниц используется схема:
  - topbar: breadcrumbs + title
  - ниже отдельная actions-bar с кнопками
  - ниже основной контент
- Для dashboard используется тот же compact topbar, что и для внутренних разделов.
- Кнопки действий в таблицах должны оставаться grouped, иконки в одной группе, без промежутков между сегментами.
- Аккаунт справа открывает dropdown только с logout-действием.

## Быстрые действия в topbar

Справа в шапке сейчас есть:

- переход на сайт
- очистка кеша
- аккаунт с dropdown

Все три элемента должны выглядеть как единый набор: одинаковая высота, близкие радиусы, одна визуальная система.

Важно:

- эти действия уже встроены в layout topbar
- их не нужно переносить в `secondaryMenu` как дефолтный sidebar-набор
- эталонная схема для sidebar сейчас такая:
  - primary menu: dashboard + `Администрирование`
  - secondary menu: только `ADMIN-UI-KIT`
- если в `secondaryMenu` снова появляются `На сайт`, `Очистить кеш`, `Выйти`, это возврат к старому паттерну, а не повторение текущего UI

## Buttons и badges

В текущем UI кнопки и badges не требуют отдельной кастомной HTML-схемы.

Используется bootstrap-семантика:

- кнопки: `.btn`, `.btn-primary`, `.btn-secondary`, `.btn-success`, `.btn-danger`, `.btn-warning`, `.btn-info`, `.btn-light`
- outline-кнопки: `.btn-outline-*`
- badges: `.badge.text-bg-*`

То есть в обычных view лучше использовать стандартные bootstrap-классы, а внешний вид уже подтягивается из `src/web/css/style.css`.

Это важно, потому что так не нужно переписывать старые view ради нового дизайна.

## Dashboard

Главная страница строится из двух зон:

- верхний hero-инфоблок
- сетка status-карточек

Текущее правило для hero:

- заголовок приветствия находится в topbar, а не внутри hero
- внутри hero остаётся только красиво оформленная системная информация
- статусы в карточках dashboard лучше выводить через bootstrap badges

## ADMIN-UI-KIT

Для проверки текущего UI внутри админки есть страница:

- `/admin/site/ui-kit`

Она служит как живая витрина текущих компонентов:

- bootstrap buttons
- outline buttons
- bootstrap badges
- metric card
- grouped action-column buttons

Если нужно быстро понять, как сейчас "должно выглядеть", сначала полезно открыть именно её.

## Topbar title и showHeader

В текущем UI breadcrumbs и основной `H1` живут в topbar, а не внутри контентной карточки.

Из этого следуют правила:

- у страницы должен быть заполнен `$this->title`
- layout сам покажет `H1` в topbar
- `AdminPage::showHeader = false` не скрывает topbar title
- `showHeader = false` скрывает только внутренний header самого `AdminPage`

То есть для RBAC, security log и CRUD-экранов нормальная схема такая:

- topbar: breadcrumbs + `H1`
- actions bar: отдельные кнопки действий, если они есть
- body: контент в `sz-panel`

Важно:

- в пакете нет рабочего флага `showTopbarTitle`
- если заголовок в topbar исчезает, проблема не в том, что где-то забыли включить `showTopbarTitle`
- обычно нужно проверять:
  - какой layout реально подключён
  - задан ли `$this->title`
  - не переопределена ли vendor-view в проекте
  - не используется ли старая версия пакета

## Табличные action-кнопки

Текущая реализация:

- используются `Material Symbols`
- ссылки имеют класс `sz-row-action`
- в `ActionColumn` кнопки должны быть сгруппированы визуально как единая button-group
- hover без подъёма

Если править таблицы, лучше не полагаться только на `.action-column a`, а оставлять явный класс `sz-row-action`.

### Как повторить такие grouped-кнопки в другом проекте

Если нужно получить вот такой вид:

- слева иконки `view/edit`
- справа danger-сегмент `delete`
- все кнопки стыкованы без промежутков
- у группы скруглены только внешние углы

Важно:

- в самом расширении эти стили уже встроены в `src/web/css/style.css`
- в текущем проекте их вручную дописывать не нужно
- блок ниже нужен как техническое объяснение, из чего этот вид собирается
- если в другом проекте кнопки выглядят не так, значит обычно проблема не в отсутствии этого куска README, а в том, что не совпали HTML, классы, иконки или подключение asset'ов

То есть это не инструкция формата "обязательно вставь весь этот CSS руками", а шпаргалка "что именно должно быть уже подключено и почему оно работает".

#### 1. В `ActionColumn` нужен явный класс и своя колонка

Пример:

```php
[
    'class' => ActionColumn::class,
    'template' => '{view}{update}{delete}',
    'contentOptions' => ['class' => 'action-column'],
    'buttonOptions' => ['class' => 'sz-row-action'],
    'buttons' => [
        'view' => static fn($url) => Html::a(
            '<span class="material-symbols-rounded">visibility</span>',
            $url,
            ['class' => 'sz-row-action', 'title' => 'Просмотр', 'aria-label' => 'Просмотр']
        ),
        'update' => static fn($url) => Html::a(
            '<span class="material-symbols-rounded">edit</span>',
            $url,
            ['class' => 'sz-row-action', 'title' => 'Редактировать', 'aria-label' => 'Редактировать']
        ),
        'delete' => static fn($url) => Html::a(
            '<span class="material-symbols-rounded">delete</span>',
            $url,
            [
                'class' => 'sz-row-action',
                'title' => 'Удалить',
                'aria-label' => 'Удалить',
                'data-confirm' => 'Удалить запись?',
                'data-method' => 'post',
            ]
        ),
    ],
]
```

Важно:

- шаблон лучше писать без пробелов: `'{view}{update}{delete}'`
- если сделать `'{view} {update} {delete}'`, между кнопками могут появляться щели
- `contentOptions => ['class' => 'action-column']` нужен обязательно
- `class => 'sz-row-action'` лучше задавать каждой кнопке явно

#### 2. Какие CSS-правила обеспечивают grouped-состояние

В самом расширении эти правила уже есть.

Ниже не обязательная ручная вставка "во все проекты", а минимальный набор правил, который должен присутствовать в подключённых стилях, если вы хотите повторить этот же вид вне расширения:

```css
.grid-view .action-column {
    white-space: nowrap;
    width: 132px;
    min-width: 132px;
    text-align: right;
    font-size: 0;
}

.grid-view a.sz-row-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    vertical-align: top;
    width: 36px;
    height: 36px;
    margin: 0;
    border: 0;
    border-radius: 0;
    color: #fff;
    text-decoration: none;
    background: linear-gradient(180deg, #536b98, #34486e);
    box-shadow: 0 10px 18px rgba(28, 40, 64, 0.2);
}

.grid-view a.sz-row-action:first-child {
    border-top-left-radius: 12px;
    border-bottom-left-radius: 12px;
}

.grid-view a.sz-row-action:last-child {
    border-top-right-radius: 12px;
    border-bottom-right-radius: 12px;
}

.grid-view a.sz-row-action:hover {
    color: #fff;
    background: linear-gradient(180deg, #627cab, #425983);
}

.grid-view a.sz-row-action[data-method="post"],
.grid-view a.sz-row-action[data-confirm] {
    background: linear-gradient(180deg, #536b98, #34486e);
}

.grid-view a.sz-row-action[data-method="post"]:hover,
.grid-view a.sz-row-action[data-confirm]:hover {
    background: linear-gradient(180deg, #b05f68, #8f4550);
}

.grid-view .action-column .material-symbols-rounded {
    font-size: 18px;
    line-height: 1;
}

.grid-view tbody td.action-column {
    display: table-cell;
    white-space: nowrap;
    text-align: right;
    padding-left: 10px;
    padding-right: 10px;
}
```

#### 3. Что чаще всего ломает такой вид

- пробелы в `template` у `ActionColumn`
- отсутствие `font-size: 0` у `.action-column`
- попытка ставить `display: flex` на `td.action-column`
- отсутствие явного класса `sz-row-action`
- попытка стилизовать только `.action-column a`, когда Yii или тема добавляет другие классы

#### 4. Иконки

Сейчас используется `Material Symbols Rounded`.

Если в проекте их нет, нужно подключить шрифт или заменить содержимое кнопок на свои SVG/иконки. Без этого grouped-стиль применится, но сами иконки не появятся как на текущем проекте.

## Что важно не сломать

- `appendTimestamp` и `forceCopy` уже включались в приложении, чтобы assets обновлялись корректно
- layout и стили правятся в локальном пакете `d:\OpenServer6.5.0\home\yii2-admin`, а не только в приложении
- для CRUD-страниц часть view лежит уже в самом проекте `backend/views/...`
- для RBAC часть view лежит внутри пакета `src/rbac/views/...`
- surface-контейнеры должны оставаться на `sz-panel`, а не откатываться к bootstrap-default карточкам и alert-блокам

## Если нужно продолжать развитие UI

Предпочтительные направления:

- унифицировать все `GridView` фильтры и toolbar
- держать одинаковую высоту topbar во всех разделах
- не добавлять лишний текст в hero и header
- сохранять холодную slate/blue палитру без слишком ярких кислотных акцентов
- новые элементы делать в том же стиле: мягкий border, glass-light background, крупный radius

## Перед правками полезно проверить

- `src/views/layouts/main.php`
- `src/web/css/style.css`
- `src/web/css/home.css`
- конкретную view проекта, если страница переопределена в `backend/views/...`

## После правок

- использовать жёсткое обновление страницы: `Ctrl+F5`
- при сомнениях проверять опубликованные assets Yii
- для PHP view-файлов полезно гонять `php -l`
