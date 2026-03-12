# larikmc/yii2-admin

Готовое расширение для `Yii2 backend`: одна установка, один модуль `admin`, внутри уже есть:

- dashboard и общий layout админки
- авторизация и страница входа
- RBAC: роли, действия и назначения
- security log
- очистка кеша
- системный bootstrap для `admin`, `adminPanel` и пользователя `ID=1`

Пакет рассчитан на сценарий, где админка, auth и RBAC живут вместе как единая backend-платформа.

## Требования

- PHP 8.1+
- Yii2 2.0.x
- `yiisoft/yii2-bootstrap5`
- `authManager` = `yii\rbac\DbManager`

## Что дает пакет

После подключения у вас есть:

- `/admin`
- `/admin/login`
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
                'icon' => 'restart_alt',
                'label' => 'Очистить кеш',
                'url' => ['/admin/site/clear-cache'],
                'method' => 'post',
            ],
            [
                'icon' => 'logout',
                'label' => 'Выйти',
                'url' => ['/auth/logout'],
                'method' => 'post',
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
