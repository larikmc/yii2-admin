<?php

/** @var yii\web\View $this */
/** @var array $summary */
/** @var array $health */
/** @var int|null $usersTotal */
/** @var int|null $usersActive */

use yii\helpers\Html;

$this->title = 'Добро пожаловать, ' . ($summary['email'] ?? 'Администратор') . '.';
?>

<div class="home-dashboard">
    <section class="home-dashboard__hero home-surface">
        <div class="home-dashboard__hero-top">
            <div class="home-dashboard__hero-content home-dashboard__hero-content--metrics">
                <div class="home-dashboard__hero-badges">
                    <div class="home-hero-chip">
                        <span class="home-hero-chip__label">PHP</span>
                        <strong class="home-hero-chip__value"><?= Html::encode($summary['phpVersion']) ?></strong>
                    </div>
                    <div class="home-hero-chip">
                        <span class="home-hero-chip__label">Yii</span>
                        <strong class="home-hero-chip__value"><?= Html::encode($summary['yiiVersion']) ?></strong>
                    </div>
                    <div class="home-hero-chip">
                        <span class="home-hero-chip__label">Timezone</span>
                        <strong class="home-hero-chip__value"><?= Html::encode($summary['timeZone']) ?></strong>
                    </div>
                </div>
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
        <article class="home-panel home-surface">
            <p class="home-stat-card__label">Пользователи</p>
            <p class="home-stat-card__value"><?= $usersTotal === null ? 'н/д' : Html::encode((string) $usersTotal) ?></p>
            <span class="home-status home-status--ok">Всего</span>
        </article>
    </section>
</div>
