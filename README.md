# larikmc/yii2-admin

Админский модуль для Yii2 с боковым меню, вложенными разделами и готовым dashboard.

## Возможности

- модуль `admin` для `backend`
- боковое меню с вложенными пунктами
- компактный layout для админки
- готовая главная страница `/admin`
- поддержка `GET` и `POST` пунктов меню
- локальное подключение через Composer `path`

## Требования

- PHP `>=7.4`
- Yii2 `~2.0`
- `yiisoft/yii2-bootstrap5`

## Установка

### 1. Добавьте пакет в `composer.json`

```json
{
    "require": {
        "larikmc/yii2-admin": "@dev"
    },
    "repositories": [
        {
            "type": "path",
            "url": "../yii2-admin",
            "options": {
                "symlink": true,
                "relative": true
            }
        }
    ]
}
```

### 2. Установите зависимость

```bash
php composer.phar update larikmc/yii2-admin
```

### 3. Подключите модуль в `backend/config/main.php`

```php
'defaultRoute' => 'admin/site/index',
'modules' => [
    'admin' => [
        'class' => larikmc\admin\Module::class,
    ],
],
'components' => [
    'urlManager' => [
        'enablePrettyUrl' => true,
        'showScriptName' => false,
        'rules' => [
            '' => 'admin/site/index',
        ],
    ],
],
```

## Быстрый старт

По умолчанию модуль уже содержит базовое меню:

- Панель управления
- На сайт
- Очистить кеш
- Выйти

Главная страница доступна по маршруту:

```php
/admin/site/index
```

Если в `backend` root перенаправлен на модуль, то просто:

```php
/admin
```

## Настройка меню

Меню задается через свойства модуля:

- `$menu` для основного меню
- `$secondaryMenu` для нижнего меню

Если их не передать, используются значения по умолчанию из [`src/config/menu.php`](./src/config/menu.php).

### Пример простого меню

```php
'modules' => [
    'admin' => [
        'class' => larikmc\admin\Module::class,
        'menu' => [
            [
                'icon' => 'dashboard',
                'label' => 'Главная',
                'url' => ['/admin/site/index'],
            ],
            [
                'icon' => 'inventory_2',
                'label' => 'Товары',
                'url' => ['/products/index'],
            ],
        ],
    ],
],
```

### Пример вложенного меню

```php
'modules' => [
    'admin' => [
        'class' => larikmc\admin\Module::class,
        'menu' => [
            [
                'icon' => 'dashboard',
                'label' => 'Главная',
                'url' => ['/admin/site/index'],
            ],
            [
                'icon' => 'settings',
                'label' => 'Каталог',
                'items' => [
                    [
                        'label' => 'Категории',
                        'url' => ['/category/index'],
                    ],
                    [
                        'label' => 'Товары',
                        'url' => ['/products/index'],
                    ],
                    [
                        'label' => 'История цен',
                        'url' => ['/price-history/index'],
                    ],
                ],
            ],
        ],
    ],
],
```

### Пример нижнего меню

```php
'modules' => [
    'admin' => [
        'class' => larikmc\admin\Module::class,
        'secondaryMenu' => [
            [
                'icon' => 'language',
                'label' => 'На сайт',
                'url' => '/',
                'linkOptions' => [
                    'target' => '_blank',
                ],
            ],
            [
                'icon' => 'logout',
                'label' => 'Выйти',
                'url' => ['/admin/site/logout'],
                'method' => 'post',
            ],
        ],
    ],
],
```

## Формат пункта меню

### Обычная ссылка

```php
[
    'icon' => 'dashboard',
    'label' => 'Главная',
    'url' => ['/admin/site/index'],
]
```

### Вложенный раздел

```php
[
    'icon' => 'settings',
    'label' => 'Настройки',
    'items' => [
        ['label' => 'Общие', 'url' => ['/settings/index']],
        ['label' => 'Пользователи', 'url' => ['/user/index']],
    ],
]
```

### POST-действие

```php
[
    'icon' => 'restart_alt',
    'label' => 'Очистить кеш',
    'url' => ['/admin/site/clear-cache'],
    'method' => 'post',
    'linkOptions' => [
        'data-confirm' => 'Очистить кеш?',
    ],
]
```

### Поддерживаемые ключи

- `icon` - имя иконки Material Symbols
- `label` - текст пункта
- `url` - маршрут Yii2 или строковый URL
- `items` - вложенные пункты
- `method` - `get` или `post`
- `linkOptions` - HTML-атрибуты ссылки или кнопки

## Структура пакета

```text
yii2-admin/
├─ src/
│  ├─ assets/
│  ├─ config/
│  ├─ controllers/
│  ├─ views/
│  ├─ web/
│  └─ Module.php
├─ composer.json
└─ README.md
```

## Статика

Пакет публикует ресурсы из:

```php
@larikmc/admin/web
```

Там лежат:

- `css/dashboard.css`
- `css/home.css`
- `css/style.css`
- `js/dashboard.js`
- `js/lazyloader.js`
- `img/logo.png`

## Расширение

Если нужно:

- добавить активные состояния по маскам маршрутов
- вынести меню в отдельный PHP-конфиг проекта
- добавить бейджи, счетчики и разделители
- скрывать пункты по ролям

это лучше делать на уровне массива меню, не меняя layout.

## Лицензия

BSD-3-Clause
