<?php

/** @var yii\web\View $this */
/** @var array $summary */
/** @var array $health */
/** @var int|null $usersTotal */
/** @var int|null $usersActive */

use yii\helpers\Html;

$this->title = 'Добро пожаловать, ' . ($summary['email'] ?? 'Администратор') . '.';
?>

<div class="home-dashboard" id="dashboard">
    <!-- 1. Hero Grid: PHP, Yii, Timezone -->
    <section class="hero" aria-label="Информация об окружении">
        <div class="hero-grid">
            <article class="hero-card hero-blue">
                <span class="overline">PHP</span>
                <strong><?= Html::encode($summary['phpVersion'] ?? PHP_VERSION) ?></strong>
                <p>Версия интерпретатора PHP</p>
                <span class="hero-ico">
                    <span class="material-symbols-rounded">code</span>
                </span>
            </article>
            <article class="hero-card hero-mint">
                <span class="overline">YII</span>
                <strong><?= Html::encode($summary['yiiVersion'] ?? Yii::getVersion()) ?></strong>
                <p>Версия фреймворка Yii</p>
                <span class="hero-ico">
                    <span class="material-symbols-rounded">eco</span>
                </span>
            </article>
            <article class="hero-card hero-peach timezone">
                <span class="overline">TIMEZONE</span>
                <strong><?= Html::encode($summary['timeZone'] ?? date_default_timezone_get()) ?></strong>
                <p>Часовой пояс сервера <span style="display:inline-flex;align-items:center;gap:5px;font-weight:700;font-variant-numeric:tabular-nums;color:inherit;margin-left:6px;"><span style="display:inline-block;width:6px;height:6px;border-radius:50%;background:#38d39f;box-shadow:0 0 6px #38d39f;"></span><span id="serverClock"><?= Html::encode($summary['serverTime'] ?? date('H:i:s')) ?></span></span></p>
                <span class="hero-ico">
                    <span class="material-symbols-rounded">schedule</span>
                </span>
            </article>
        </div>
    </section>

    <!-- 2. Metrics Grid -->
    <section class="metrics" aria-label="Состояние системы">
        <?php foreach ($health as $item): ?>
            <?php
            $title = $item['title'] ?? '';
            $val = $item['value'] ?? '';
            $hint = $item['hint'] ?? '';
            $footHint = $item['footHint'] ?? '';
            $ok = !empty($item['ok']);
            $progress = isset($item['progress']) ? round((float)$item['progress'], 1) : null;
            
            // Choose color scheme based on title or ok state
            $iconName = 'database';
            $iconClass = 'mint';
            $barClass = 'blue';
            $lowerTitle = mb_strtolower($title);
            if (str_contains($lowerTitle, 'кеш') || str_contains($lowerTitle, 'cache')) {
                $iconName = 'dns';
                $iconClass = 'mint';
            } elseif (str_contains($lowerTitle, 'база') || str_contains($lowerTitle, 'db') || str_contains($lowerTitle, 'данных')) {
                $iconName = 'database';
                $iconClass = 'mint';
            } elseif (str_contains($lowerTitle, 'режим') || str_contains($lowerTitle, 'env')) {
                $iconName = 'view_in_ar';
                $iconClass = '';
            } elseif (str_contains($lowerTitle, 'лог') || str_contains($lowerTitle, 'log')) {
                $iconName = 'description';
                $iconClass = '';
            } elseif (str_contains($lowerTitle, 'диск') || str_contains($lowerTitle, 'disk') || str_contains($lowerTitle, 'место')) {
                $iconName = 'hard_drive';
                $iconClass = '';
                $barClass = $ok ? 'blue' : 'rose';
            } elseif (str_contains($lowerTitle, 'памят') || str_contains($lowerTitle, 'memory')) {
                $iconName = 'memory';
                $iconClass = 'lavender';
                $barClass = $ok ? 'green' : 'rose';
            }
            ?>
            <article class="metric">
                <div class="metric-head">
                    <span class="metric-icon <?= $iconClass ?>">
                        <span class="material-symbols-rounded"><?= $iconName ?></span>
                    </span>
                    <div>
                        <div class="metric-label"><?= Html::encode(mb_strtoupper($title)) ?></div>
                        <div class="metric-value"><?= Html::encode($val) ?></div>
                        <?php if ($hint !== ''): ?>
                            <div class="metric-hint"><?= Html::encode($hint) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if ($progress !== null): ?>
                    <div class="bar <?= $barClass ?>">
                        <span style="width: <?= min(100, max(0, $progress)) ?>%;<?= $progress > 0 ? 'min-width:4px;' : '' ?>"></span>
                    </div>
                <?php endif; ?>
                <div class="metric-foot">
                    <?php if ($footHint !== ''): ?>
                        <div class="metric-hint"><?= Html::encode($footHint) ?></div>
                    <?php endif; ?>
                    <span class="pill <?= $ok ? '' : 'danger' ?>">
                        <span class="material-symbols-rounded"><?= $ok ? 'check' : 'priority_high' ?></span>
                    </span>
                </div>
            </article>
        <?php endforeach; ?>

        <!-- Пользователи -->
        <article class="metric">
            <div class="metric-head">
                <span class="metric-icon lavender">
                    <span class="material-symbols-rounded">group</span>
                </span>
                <div>
                    <div class="metric-label">ПОЛЬЗОВАТЕЛИ</div>
                    <div class="metric-value"><?= $usersTotal === null ? 'н/д' : Html::encode((string)$usersTotal) ?></div>
                    <div class="metric-hint">Всего в базе данных</div>
                </div>
            </div>
            <div class="metric-foot">
                <span class="pill">
                    <span class="material-symbols-rounded">check</span>
                </span>
            </div>
        </article>
    </section>
</div>

<?php
$tzJson = json_encode($summary['timeZone'] ?? Yii::$app->timeZone);
$ts = (int)($summary['serverTimestamp'] ?? time()) * 1000;
$clockJs = <<<JS
(function() {
    const clock = document.getElementById('serverClock');
    if (!clock) return;
    const tz = {$tzJson};
    let serverTime = {$ts};
    const formatter = new Intl.DateTimeFormat('ru-RU', {
        timeZone: tz,
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false
    });
    setInterval(() => {
        serverTime += 1000;
        try {
            clock.textContent = formatter.format(new Date(serverTime));
        } catch (e) {
            const d = new Date(serverTime);
            clock.textContent = String(d.getHours()).padStart(2, '0') + ':' +
                                String(d.getMinutes()).padStart(2, '0') + ':' +
                                String(d.getSeconds()).padStart(2, '0');
        }
    }, 1000);
})();
JS;
$this->registerJs($clockJs, \yii\web\View::POS_READY);
?>

