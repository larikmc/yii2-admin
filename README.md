# larikmc/yii2-admin

Готовое расширение для `Yii2 backend`: одна установка, один модуль `admin`, внутри уже есть:

- dashboard и общий layout админки
- авторизация и страница входа
- RBAC: роли, действия и назначения
- security log
- очистка кеша
- системный bootstrap для `admin`, `adminPanel` и пользователя `ID=1`

Пакет рассчитан на сценарий, где админка, auth и RBAC живут вместе как единая backend-платформа.

Перед интеграцией UI обязательно прочитайте `UI-README.md`.

В нём описаны визуальные правила, layout-паттерны, popup/lazyload для изображений, grouped action-кнопки, topbar и частые ошибки интеграции. Без этого файла легко подключить пакет технически правильно, но получить визуальные расхождения в админке.

Связанные документы:

- `UI-README.md`
- `AI-INTEGRATION-CHECKLIST.md` (обязательный чеклист для нейросети при интеграции в новый проект)

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

        // Необязательно: свой placeholder для lazyload.
        // Если не задано, используется стандартный src/web/img/load.svg из расширения.
        'lazyloadPlaceholderUrl' => null,
    ],
],
'components' => [
    'request' => [
        'baseUrl' => '/admin',
    ],
    'user' => [
        'identityClass' => common\models\User::class,
        'enableAutoLogin' => true,
        'loginUrl' => ['/login'],
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

### Важные пути для входа (рекомендуемый вариант)

Чтобы страница входа открывалась по короткому URL:

- `https://your-domain/admin/login`

используйте такую связку:

1. `request.baseUrl = '/admin'`
2. `user.loginUrl = ['/login']`
3. правило `urlManager`: `'login' => 'admin/auth/auth/login'`
4. правило `urlManager`: `'auth/captcha' => 'admin/auth/auth/captcha'`

Если у вас есть глобальная проверка доступа в `on beforeRequest`, обязательно добавьте в whitelist:

- `login`
- `auth/login`
- `auth/captcha`
- `admin/login`
- `admin/auth/login`
- `admin/auth/captcha`

Иначе можно получить цикл редиректов (`ERR_TOO_MANY_REDIRECTS`) на странице входа.

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

## Lazyload и popup для изображений

В расширение уже встроены:

- lazyload для изображений: `src/web/js/lazyloader.js`
- popup-просмотрщик оригинала: `src/web/js/image-viewer-admin.js`
- стили popup: `src/web/css/image-viewer-admin.css`

Все эти файлы подключаются через `larikmc\admin\assets\AppAsset`, поэтому при штатном layout `@larikmc/admin/views/layouts/main` ничего отдельно регистрировать не нужно.

Стандартный placeholder для lazyload уже лежит в расширении:

- `src/web/img/load.svg`

По умолчанию используется именно он. Если нужен свой placeholder, задайте его в конфиге модуля:

```php
'modules' => [
    'admin' => [
        'class' => larikmc\admin\Module::class,
        'lazyloadPlaceholderUrl' => '@web/img/load.svg',
    ],
],
```

В view URL placeholder лучше получать через модуль:

```php
$placeholder = Yii::$app->getModule('admin')->getLazyloadPlaceholderUrl($this);
```

### Базовый пример

Миниатюра грузится лениво из `data-src`, а по клику открывается оригинал из `data-image-full`:

```php
use yii\bootstrap5\Html;

$placeholder = Yii::$app->getModule('admin')->getLazyloadPlaceholderUrl($this);

echo Html::a(
    Html::img($placeholder, [
        'data-src' => '/uploads/images/thumbs/item-123.webp',
        'class' => 'sz-thumb__img',
        'alt' => 'Изображение #123',
        'loading' => 'lazy',
        'decoding' => 'async',
    ]),
    '/uploads/images/original/item-123.jpg',
    [
        'data-pjax' => '0',
        'data-image-viewer' => true,
        'data-image-full' => '/uploads/images/original/item-123.jpg',
        'data-image-title' => 'Изображение #123',
        'class' => 'sz-thumb',
        'style' => 'width:100px;height:72px;',
    ]
);
```

Что здесь важно:

- `img[data-src]` — URL миниатюры, которую нужно показать в таблице или списке
- `img[src]` — placeholder, по умолчанию стандартный `load.svg` из расширения
- `a[data-image-full]` — URL полного изображения для popup
- `a[href]` — fallback для popup и обычная ссылка, если JS не загрузился
- `a.sz-thumb` — готовый контейнер, который центрирует placeholder и миниатюру, но не диктует размер
- `img.sz-thumb__img` — картинка внутри контейнера, вписывается без растягивания
- `data-pjax="0"` — желательно для ссылок внутри `GridView`/PJAX, чтобы клик не перехватывался PJAX

Размер задаётся проектом:

```php
['class' => 'sz-thumb', 'style' => 'width:100px;height:72px;']
```

Или готовым модификатором для типовых случаев:

- `sz-thumb sz-thumb--sm` — 72x56
- `sz-thumb sz-thumb--lg` — 140x96
- `sz-thumb sz-thumb--cover` — изображение заполняет контейнер через `object-fit: cover`

### Если миниатюра и оригинал совпадают

Можно не указывать `data-image-full`: popup возьмёт URL из `href`.

```php
$placeholder = Yii::$app->getModule('admin')->getLazyloadPlaceholderUrl($this);

echo Html::a(
    Html::img($placeholder, [
        'data-src' => '/uploads/images/item-123.jpg',
        'class' => 'sz-thumb__img',
        'alt' => 'Изображение #123',
    ]),
    '/uploads/images/item-123.jpg',
    [
        'data-pjax' => '0',
        'data-image-viewer' => true,
        'data-image-title' => 'Изображение #123',
        'class' => 'sz-thumb',
        'style' => 'width:100px;height:72px;',
    ]
);
```

### Пример для GridView

```php
$placeholder = Yii::$app->getModule('admin')->getLazyloadPlaceholderUrl($this);

[
    'attribute' => 'image',
    'format' => 'raw',
    'value' => static function ($model) use ($placeholder) {
        $thumb = '/uploads/images/items/thumbs/' . $model->image . '.webp';
        $original = '/uploads/images/items/original/' . $model->image . '.jpg';

        return \yii\bootstrap5\Html::a(
            \yii\bootstrap5\Html::img($placeholder, [
                'data-src' => $thumb,
                'class' => 'sz-thumb__img',
                'alt' => '',
                'loading' => 'lazy',
                'decoding' => 'async',
            ]),
            $original,
            [
                'data-pjax' => '0',
                'data-image-viewer' => true,
                'data-image-full' => $original,
                'data-image-title' => 'Изображение #' . $model->id,
                'class' => 'sz-thumb',
                'style' => 'width:100px;height:72px;',
            ]
        );
    },
]
```

### Legacy-вариант с onclick

Старый backend-паттерн тоже поддерживается:

```php
$placeholder = Yii::$app->getModule('admin')->getLazyloadPlaceholderUrl($this);

echo '<a class="sz-thumb" style="width:100px;height:72px;" data-pjax="0" data-image-viewer data-image-full="/uploads/images/original/item-123.jpg" data-image-title="Изображение #123" href="/uploads/images/original/item-123.jpg" onclick="return openAdminImageViewer(this)">' .
    '<img class="sz-thumb__img" src="' . $placeholder . '" data-src="/uploads/images/thumbs/item-123.webp" alt="">' .
    '</a>';
```

Новый вариант без inline `onclick` предпочтительнее: достаточно атрибута `data-image-viewer`.

### Поддерживаемые атрибуты

- `data-src` на `img` — лениво подставляется в `src`
- `data-srcset` на `img` — лениво подставляется в `srcset`
- `data-no-placeholder` на `img` — не добавлять класс `lazy-img`
- `data-image-viewer` на ссылке — включает popup по клику
- `data-image-full` на ссылке — оригинал для popup
- `data-image-title` на ссылке — подпись под изображением
- `title` на ссылке — fallback для подписи
- `href` на ссылке — fallback для оригинала

### Где должен лежать placeholder

Если вы ничего не настраиваете, placeholder берётся из расширения:

```text
src/web/img/load.svg
```

После публикации Yii asset'ов он будет доступен по URL опубликованного `AppAsset`.

Если в конкретном проекте нужен другой spinner/placeholder, положите его в web-root проекта, например:

```text
backend/web/img/load.svg
```

и укажите:

```php
'lazyloadPlaceholderUrl' => '@web/img/load.svg',
```

Можно указать и абсолютный URL:

```php
'lazyloadPlaceholderUrl' => 'https://cdn.example.com/admin/load.svg',
```

### Обновление после AJAX/PJAX

При обычной загрузке страницы lazyload стартует сам. Если контент добавлен динамически, можно переинициализировать изображения внутри контейнера:

```js
lazyloader.init(document.querySelector('#pjax-container'));
```

Если используется Yii PJAX:

```js
document.addEventListener('pjax:end', function (event) {
    if (window.lazyloader) {
        window.lazyloader.init(event.target);
    }
});
```

Popup использует делегирование клика на `document`, поэтому для новых ссылок с `data-image-viewer` повторная инициализация не нужна.

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
