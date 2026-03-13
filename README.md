# larikmc/yii2-admin

Готовое расширение для `Yii2 backend`: одна установка, один модуль `admin`, внутри уже есть:

- dashboard и общий layout админки
- авторизация и страница входа
- RBAC: роли, действия и назначения
- security log
- очистка кеша
- системный bootstrap для `admin`, `adminPanel` и пользователя `ID=1`

Пакет рассчитан на сценарий, где админка, auth и RBAC живут вместе как единая backend-платформа.

Для UI-слоя, визуальных правил и кастомных паттернов админки смотрите отдельный файл:

- `UI-README.md`

## Требования

- PHP 8.1+
- Yii2 2.0.x
- `yiisoft/yii2-bootstrap5`
- `authManager` = `yii\rbac\DbManager`

## Что дает пакет

После подключения у вас есть:

- `/admin`
- `/admin/login`
- `/admin/site/ui-kit`
- `/admin/auth/security-log`
- `/admin/rbac`
- `/admin/rbac/user`
- `/admin/rbac/role`
- `/admin/rbac/permission`
- `/admin/rbac/assignment`

Внутри RBAC уже поддержана схема:

- `действия -> роли -> пользователи`

То есть:

1. создаете действия
2. включаете действия в роль
3. назначаете роль пользователю

## Установка

### Composer

```bash
composer require larikmc/yii2-admin
```

Для локального path-пакета:

```json
{
  "repositories": [
    {
      "type": "path",
      "url": "../yii2-admin",
      "options": {
        "symlink": true,
        "relative": true
      }
    }
  ],
  "require": {
    "larikmc/yii2-admin": "@dev"
  }
}
```

## Базовая конфигурация backend

Пример для `backend/config/main.php`:

```php
'defaultRoute' => 'admin/site/index',
'layout' => '@larikmc/admin/views/layouts/main',
'modules' => [
    'admin' => [
        'class' => larikmc\admin\Module::class,
        'userClass' => common\models\User::class,
        'userModel' => common\models\User::class,
        'userIdField' => 'id',
        'usernameField' => 'username',
        'emailField' => 'email',
        'statusField' => 'status',
        'rbacAccessRoles' => [],

        // Настройки авторизации
        'maxUserAttempts' => 5,
        'captchaAfterAttempts' => 3,
        'lockDuration' => 120,
        'userAttemptsTtl' => 120,
        'maxDelaySeconds' => 10,
    ],
],
'components' => [
    'request' => [
        'baseUrl' => '/admin',
    ],
    'user' => [
        'identityClass' => common\models\User::class,
        'enableAutoLogin' => true,
        'loginUrl' => ['/auth/login'],
    ],
    'authManager' => [
        'class' => yii\rbac\DbManager::class,
    ],
    'urlManager' => [
        'enablePrettyUrl' => true,
        'showScriptName' => false,
        'rules' => [
            '' => 'admin/site/index',
            'login' => 'admin/auth/auth/login',
            'auth/login' => 'admin/auth/auth/login',
            'auth/logout' => 'admin/auth/auth/logout',
            'auth/captcha' => 'admin/auth/auth/captcha',
            'auth/security-log' => 'admin/auth/auth/security-log',
            'auth/clear-security-log' => 'admin/auth/auth/clear-security-log',
            'rbac' => 'admin/rbac/default/index',
            'rbac/<controller:[\w-]+>' => 'admin/rbac/<controller>/index',
            'rbac/<controller:[\w-]+>/<action:[\w-]+>' => 'admin/rbac/<controller>/<action>',
        ],
    ],
],
```

### Схема доступа

Рекомендуемая схема разделения доступа:

- `adminPanel` дает право войти в админку
- `admin` дает доступ к рабочим backend-контроллерам и административным разделам

То есть:

1. пользователь без `adminPanel` не должен попадать в `/admin`
2. пользователь с `adminPanel`, но без `admin`, может пройти только уровень входа в админку
3. backend-контроллеры, dashboard, RBAC и security log должны быть закрыты ролью `admin`

### Security Model

Матрица доступа по умолчанию:

- `adminPanel` — право пройти в `/admin`
- `admin` — право работать с backend-контроллерами
- `admin` — право видеть dashboard и внутренние admin-страницы
- `admin` — право работать с RBAC
- `admin` — право просматривать и очищать security log

Коротко:

- `adminPanel` = вход в админку
- `admin` = вся рабочая часть админки

## Меню

Основное меню и нижнее меню задаются через:

- `menu`
- `secondaryMenu`

По умолчанию пакет использует схему, совпадающую с текущим UI:

- `menu`: dashboard + dropdown `Администрирование` с `RBAC` и `Security Log`
- `secondaryMenu`: только `ADMIN-UI-KIT`

Важно:

- ссылки `На сайт`, `Очистить кеш`, `Выйти` не должны дублироваться в `secondaryMenu`
- эти действия уже встроены в topbar layout и находятся справа в шапке
- если добавить их ещё и в `secondaryMenu`, получится старый дублирующийся sidebar-паттерн, который больше не является эталонным для пакета

Пример:

```php
'modules' => [
    'admin' => [
        'class' => larikmc\admin\Module::class,
        'userClass' => common\models\User::class,
        'userModel' => common\models\User::class,
        'menu' => [
            [
                'icon' => 'dashboard',
                'label' => 'Панель управления',
                'url' => ['/admin/site/index'],
            ],
            [
                'icon' => 'admin_panel_settings',
                'label' => 'Администрирование',
                'items' => [
                    ['label' => 'RBAC', 'url' => ['/rbac/default/index']],
                    ['label' => 'Security Log', 'url' => ['/auth/security-log']],
                ],
            ],
        ],
        'secondaryMenu' => [
            [
                'icon' => 'palette',
                'label' => 'ADMIN-UI-KIT',
                'url' => ['/admin/site/ui-kit'],
            ],
        ],
    ],
],
```

## Авторизация

В пакет уже встроен модуль авторизации:

- форма входа
- brute-force защита
- CAPTCHA после нескольких попыток
- lock по IP и email
- security log

Маршруты:

- `GET/POST /admin/auth/login`
- `POST /admin/auth/logout`
- `GET /admin/auth/captcha`
- `GET /admin/auth/security-log`
- `POST /admin/auth/clear-security-log`

Внутри пакета используются короткие маршруты `/auth/*`, поэтому их нужно пробросить через `urlManager`.

## RBAC

В пакет уже встроен RBAC-раздел:

- роли
- действия
- назначения ролей пользователям

Маршруты:

- `/admin/rbac`
- `/admin/rbac/user`
- `/admin/rbac/role`
- `/admin/rbac/permission`
- `/admin/rbac/assignment`

### Системные элементы

При установке RBAC автоматически создаются:

- роль `admin`
- действие `adminPanel`
- связь `admin -> adminPanel`
- назначение роли `admin` пользователю `ID=1`

Системные инварианты:

- пользователь с `ID=1` является системным администратором и не может быть лишен роли `admin`
- роль `admin` является системной и не может быть удалена
- действие `adminPanel` является системным и не может быть удалено
- связь `admin -> adminPanel` должна существовать всегда
- у пользователя с `ID=1` роль `admin` должна сохраняться всегда

Связь системных прав:

- `adminPanel` считается системным permission для входа в админку
- роль `admin` должна включать `adminPanel`
- назначение только `adminPanel` без роли `admin` можно использовать как технический допуск на уровень входа, но не как доступ к backend-разделам

## Общий контейнер страниц

В пакет встроен виджет:

```php
larikmc\admin\widgets\AdminPage
```

Он нужен для единообразных внутренних страниц модулей. Его можно использовать и в собственных admin-разделах.

Пример:

```php
echo \larikmc\admin\widgets\AdminPage::widget([
    'title' => 'Моя страница',
    'subtitle' => 'Короткое описание раздела',
    'actions' => [
        \yii\bootstrap5\Html::a('Создать', ['create'], ['class' => 'btn btn-success']),
    ],
    'content' => '<div>Контент страницы</div>',
]);
```

### sz-panel

Базовый визуальный контейнер админки:

```css
.sz-panel
```

Он задает:

- белый фон
- внутренние отступы
- скругление
- тень карточки

Используйте его для списков, форм, таблиц и detail-экранов, если нужен единый стиль панели.

Пример ручного использования:

```php
<div class="sz-panel">
    <?= \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $columns,
    ]) ?>
</div>
```

Или для формы:

```php
<div class="sz-panel">
    <?= $this->render('_form', ['model' => $model]) ?>
</div>
```

Если страница строится через `AdminPage`, панель можно получить автоматически:

```php
echo \larikmc\admin\widgets\AdminPage::widget([
    'title' => 'Список товаров',
    'content' => \yii\grid\GridView::widget([...]),
]);
```

По умолчанию `AdminPage` оборачивает контент в `.sz-panel`.

Если панель нужна вручную в самом шаблоне, отключите автообертку:

```php
echo \larikmc\admin\widgets\AdminPage::widget([
    'title' => 'Журнал безопасности',
    'boxed' => false,
    'content' => '<div class="sz-panel">...</div>',
]);
```

Плоский вариант без карточки:

```css
.sz-panel--flat
```

Важно:

- `sz-panel` это базовый surface-контейнер для внутренних блоков админки
- таблицы, формы, detail-view и типовые контентные секции должны жить внутри `sz-panel`, если не нужен осознанно плоский вариант
- для ручных alert-блоков не нужно возвращаться к bootstrap-default рамкам и фонам, если экран уже собирается из `sz-panel` и toast-паттернов

## UI и grouped action-кнопки

В расширение уже встроены:

- общий layout админки
- базовые UI-стили
- переопределённые bootstrap-кнопки `.btn-*`
- стилизованные bootstrap-badges `.badge.text-bg-*`
- стили таблиц
- grouped action-кнопки в `GridView`
- showcase-страница `ADMIN-UI-KIT`

### Bootstrap-кнопки

Расширение не требует заводить отдельные "новые" классы для основных кнопок.

Вместо этого переопределяются стандартные bootstrap-классы:

- `.btn-primary`
- `.btn-secondary`
- `.btn-success`
- `.btn-danger`
- `.btn-warning`
- `.btn-info`
- `.btn-light`
- `.btn-outline-*`

Это сделано специально, чтобы существующие view на `Yii2 + Bootstrap 5` автоматически подхватывали новый стиль без массового переименования классов.

### Bootstrap badges

Аналогично, для статусов и коротких меток используется стандартный bootstrap-формат:

- `.badge`
- `.text-bg-primary`
- `.text-bg-secondary`
- `.text-bg-success`
- `.text-bg-danger`
- `.text-bg-warning`
- `.text-bg-info`
- `.text-bg-light`
- `.text-bg-dark`

То есть статусы и label-метки теперь лучше строить именно через bootstrap badges, а не через отдельные кастомные status-компоненты.

### ADMIN-UI-KIT

Внутри пакета есть отдельная служебная страница:

- `/admin/site/ui-kit`

Она нужна как showcase текущего визуального языка:

- solid buttons
- outline buttons
- badges
- metric card
- grouped action-column buttons

Удобно использовать её как reference, когда нужно повторить стиль в собственных backend-разделах.

То есть такие кнопки, как `view/edit/delete`, уже оформлены внутри пакета и не требуют ручного копирования CSS в проект, если используется штатный layout и asset bundle расширения.

## Topbar и H1

В текущем layout заголовок страницы показывается в topbar автоматически через `$this->title`.

Это значит:

- topbar `H1` приходит не из `AdminPage`, а из layout `src/views/layouts/main.php`
- параметр `showHeader` в `AdminPage` управляет только внутренним заголовком внутри контента страницы
- `showHeader => false` нужен как раз для того, чтобы не дублировать один и тот же заголовок и subtitle под topbar
- для RBAC, security log и типовых CRUD-экранов `showHeader => false` является нормальным сценарием, если `title` уже выведен в topbar

Важно:

- в пакете нет рабочего параметра `showTopbarTitle`
- попытка "чинить" отсутствие заголовка через `$this->params['showTopbarTitle'] = true` является неправильной, потому что layout этот параметр не использует
- если topbar `H1` пропал, нужно проверять `layout`, `$this->title`, переопределённые view и фактическую версию пакета, а не размазывать по vendor-view несуществующий флаг

Важно понимать:

- если в другом проекте grouped-кнопки "не получились", это обычно не значит, что нужно просто вручную вставить CSS из README
- чаще причина в одном из пунктов:
  - не подключился asset bundle расширения
  - в `ActionColumn` другой `template`
  - не проставлен класс `action-column`
  - не проставлен класс `sz-row-action`
  - не подключены `Material Symbols`

Подробная техническая шпаргалка по этим кнопкам лежит в:

- `UI-README.md`

Там описано:

- какой HTML должен генерировать `ActionColumn`
- почему шаблон лучше писать без пробелов
- какие классы обязательны
- какие CSS-правила уже используются внутри расширения

## Структура пакета

```text
yii2-admin/
├─ src/
│  ├─ assets/
│  ├─ auth/
│  ├─ config/
│  ├─ controllers/
│  ├─ rbac/
│  ├─ views/
│  ├─ web/
│  ├─ widgets/
│  └─ Module.php
├─ composer.json
└─ README.md
```

## Идея пакета

Это не набор из трех независимых расширений, а одна backend-платформа:

- `admin shell`
- `auth`
- `rbac`

Именно поэтому пакет удобнее ставить и поддерживать как одно расширение.

## Лицензия

BSD-3-Clause
