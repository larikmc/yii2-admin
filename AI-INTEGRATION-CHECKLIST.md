# AI Integration Checklist (Admin UI)

Этот документ нужен для нейросети как строгий стандарт при интеграции `larikmc/yii2-admin` в новый проект.

Цель: чтобы модель не забывала базовые UI-правила и не требовала повторных уточнений про фон, заголовки, badges и grouped action-кнопки.

## 1) Что сделать в первую очередь

1. Прочитать `README.md` и `UI-README.md` полностью.
2. Принять `UI-README.md` как визуальный source of truth.
3. Проверить, что используется layout пакета:
   - `@larikmc/admin/views/layouts/main`
4. Проверить, что подключены asset-файлы пакета:
   - `src/assets/AppAsset.php`
   - `src/web/css/style.css`
   - `src/web/css/dashboard.css`
   - `src/web/js/dashboard.js`
5. Проверить страницу-эталон:
   - `/admin/site/ui-kit`

Если любой пункт выше не выполнен, сначала исправить это, и только потом править конкретные экраны.

## 2) Обязательные UI-правила (MUST)

1. Заголовок страницы (`H1`) должен быть только один: в topbar.
2. Внутри контента дублирующий `H1`/header не показывать.
3. Для экранов на `AdminPage` использовать:
   - `'showHeader' => false`
4. У страницы всегда должен быть заполнен `$this->title`.
5. Не возвращаться к старым bootstrap-alert блокам как основному паттерну.
6. Использовать bootstrap-семантику кнопок:
   - `.btn`, `.btn-primary`, `.btn-secondary`, `.btn-success`, `.btn-danger`, `.btn-warning`, `.btn-info`, `.btn-light`, `.btn-outline-*`
7. Использовать bootstrap badges:
   - `.badge.text-bg-*`
8. Не использовать legacy-статус-классы, если можно заменить на `.badge.text-bg-*`.
9. Фоны/поверхности должны соответствовать текущему UI kit (panel/glass стиль), не плоский дефолт bootstrap.
10. Для таблиц `GridView` action-кнопки должны быть grouped, без щелей между сегментами.

## 3) ActionColumn и удаление (MUST)

Для колонок с `view/edit/delete`:

1. `template` писать без пробелов:
   - `'{view}{update}{delete}'`
2. У ячейки action-колонки должен быть класс:
   - `'contentOptions' => ['class' => 'action-column']`
3. У каждой кнопки должен быть класс:
   - `'class' => 'sz-row-action'`
4. Delete-кнопка должна быть post/confirm-действием:
   - `data-method="post"` и/или `data-confirm`
5. Delete-сегмент в hover-состоянии должен уходить в danger (красный) по стилям UI kit.
6. Иконки: `Material Symbols Rounded` (или эквивалент, если проект осознанно переопределил иконпак).

## 4) Правила для Vue-экранов в проекте

Если в проекте есть админские экраны на Vue, применять те же правила:

1. Верхний `H1` только в общем topbar/layout-компоненте.
2. Внутри Vue-страницы повторный `h1` не рендерить.
3. Использовать те же токены/классы UI kit для фонов и панелей.
4. Для статусов применять `.badge.text-bg-*` (или эквивалентный компонент, дающий тот же визуальный результат).
5. Для row-actions повторять grouped-паттерн (`view/edit/delete`), delete hover = red.
6. Не смешивать “старый bootstrap вид” и новый admin UI на одной странице.

Минимальная проверка для Vue-файлов:

- нет второго `h1` в контентной зоне;
- кнопки и badges соответствуют UI kit;
- row-action delete красный на hover;
- фон и panel-слои визуально совпадают с `/admin/site/ui-kit`.

## 5) Definition of Done (проверка перед сдачей)

Перед завершением задачи модель обязана проверить:

1. На странице нет дублирующегося заголовка (`topbar H1` + контентный `H1` одновременно).
2. Кнопки используют bootstrap-классы UI kit, а не legacy-замены.
3. Статусы/метки оформлены через `.badge.text-bg-*`.
4. `GridView` action-кнопки визуально grouped.
5. У delete-кнопки hover-состояние danger (красный).
6. Изменения не ломают breadcrumbs/topbar.
7. Проверена страница `/admin/site/ui-kit` как эталон.

## 6) Типовые ошибки, которые нельзя повторять

1. Добавлять второй заголовок внутри `AdminPage`, забыв `'showHeader' => false`.
2. Использовать `ActionColumn` шаблон с пробелами (`'{view} {update} {delete}'`).
3. Не ставить `sz-row-action` на все action-кнопки.
4. Стилизовать delete как обычную кнопку без danger-hover.
5. Возвращать старые статусные классы вместо bootstrap badges.
6. Править локальную страницу без проверки, что asset bundle вообще подключен.

## 7) Короткий промпт для любой нейросети

Скопируй и используй как стартовый системный/рабочий промпт:

> Интегрируй изменения строго по `AI-INTEGRATION-CHECKLIST.md`, `README.md`, `UI-README.md`.
> Обязательно:
> 1) один H1 только в topbar (внутри контента без дубля, для `AdminPage` ставь `showHeader=false`),
> 2) bootstrap buttons и `.badge.text-bg-*`,
> 3) grouped action-кнопки `view/edit/delete` с `sz-row-action`, template без пробелов,
> 4) delete hover = red (danger),
> 5) визуально сверяйся с `/admin/site/ui-kit`.
> В конце дай отчет по каждому пункту DoD.

