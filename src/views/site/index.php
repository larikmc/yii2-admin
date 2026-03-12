<?php

/** @var yii\web\View $this */
/** @var array $summary */
/** @var array $health */
/** @var int|null $usersTotal */
/** @var int|null $usersActive */

use yii\helpers\Html;

$this->title = 'Панель управления';
?>

<div class="home-dashboard">
    <section class="home-dashboard__hero home-surface">
        <div class="home-dashboard__hero-top">
            <div class="home-dashboard__hero-content">
                <h1 class="home-dashboard__title">
                    Вы вошли как <?= Html::encode($summary['email']) ?>.
                </h1>
            </div>
            <div class="home-dashboard__hero-actions">
                <a class="home-btn home-btn--ghost" href="/" target="_blank" rel="noopener">Открыть сайт</a>
            </div>
        </div>
    </section>

    <section class="home-stats">
        <?php foreach ($health as $item): ?>
            <article class="home-stat-card home-surface">
                <p class="home-stat-card__label"><?= Html::encode($item['title']) ?></p>
                <p class="home-stat-card__value"><?= Html::encode($item['value']) ?></p>
                <span class="home-status <?= $item['ok'] ? 'home-status--ok' : 'home-status--warn' ?>">
                    <?= $item['ok'] ? 'OK' : 'Внимание' ?>
                </span>
            </article>
        <?php endforeach; ?>
    </section>

    <section class="home-panels">
        <article class="home-panel home-panel--span-2 home-surface">
            <h2 class="home-panel__title">Система</h2>

            <table class="home-table">
                <tbody>
                <tr>
                    <th>Версия PHP</th>
                    <td><?= Html::encode($summary['phpVersion']) ?></td>
                </tr>
                <tr>
                    <th>Версия Yii</th>
                    <td><?= Html::encode($summary['yiiVersion']) ?></td>
                </tr>
                <tr>
                    <th>Пользователь</th>
                    <td><?= Html::encode($summary['email']) ?></td>
                </tr>
                <tr>
                    <th>Часовой пояс</th>
                    <td><?= Html::encode($summary['timeZone']) ?></td>
                </tr>
                </tbody>
            </table>
        </article>

        <article class="home-panel home-surface">
            <h2 class="home-panel__title">Пользователи</h2>

            <div class="home-metrics">
                <div class="home-metrics__row">
                    <span class="home-metrics__label">Всего</span>
                    <span class="home-metrics__value"><?= $usersTotal === null ? 'н/д' : Html::encode((string) $usersTotal) ?></span>
                </div>
                <div class="home-metrics__row">
                    <span class="home-metrics__label">Активных</span>
                    <span class="home-metrics__value"><?= $usersActive === null ? 'н/д' : Html::encode((string) $usersActive) ?></span>
                </div>
            </div>

            <p class="home-note">
                Если видите "н/д", проверьте настройки пользователей и доступность сервисов.
            </p>
        </article>
    </section>
</div>
