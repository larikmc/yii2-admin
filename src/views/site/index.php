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
            $themeClass = 'metric--mint';
            $barClass = 'green';
            $lowerTitle = mb_strtolower($title);

            if (str_contains($lowerTitle, 'кеш') || str_contains($lowerTitle, 'cache')) {
                $iconName = 'dns';
                $themeClass = 'metric--mint';
                $barClass = 'green';
            } elseif (str_contains($lowerTitle, 'база') || str_contains($lowerTitle, 'db') || str_contains($lowerTitle, 'данных')) {
                $iconName = 'database';
                $themeClass = 'metric--teal';
                $barClass = 'green';
            } elseif (str_contains($lowerTitle, 'режим') || str_contains($lowerTitle, 'env')) {
                $iconName = 'view_in_ar';
                $themeClass = $ok ? 'metric--indigo' : 'metric--amber';
            } elseif (str_contains($lowerTitle, 'лог') || str_contains($lowerTitle, 'log')) {
                $iconName = 'description';
                $themeClass = 'metric--cyan';
            } elseif (str_contains($lowerTitle, 'диск') || str_contains($lowerTitle, 'disk') || str_contains($lowerTitle, 'место')) {
                $iconName = 'hard_drive';
                $themeClass = 'metric--blue';
                $barClass = $ok ? 'blue' : 'rose';
            } elseif (str_contains($lowerTitle, 'памят') || str_contains($lowerTitle, 'memory')) {
                $iconName = 'memory';
                $themeClass = 'metric--violet';
                $barClass = $ok ? 'violet' : 'rose';
            }
            ?>
            <article class="metric <?= $themeClass ?>">
                <div class="metric-head">
                    <div class="metric-icon-wrap">
                        <span class="metric-icon">
                            <span class="material-symbols-rounded"><?= $iconName ?></span>
                        </span>
                    </div>
                    <div class="metric-title-group">
                        <span class="metric-label"><?= Html::encode(mb_strtoupper($title)) ?></span>
                        <?php if ($hint !== ''): ?>
                            <span class="metric-badge-code"><?= Html::encode($hint) ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="metric-status-badge <?= $ok ? 'is-ok' : 'is-alert' ?>" title="<?= $ok ? 'В норме' : 'Требует внимания' ?>">
                        <span class="status-pulse-dot"></span>
                        <span class="status-badge-icon material-symbols-rounded"><?= $ok ? 'check' : 'priority_high' ?></span>
                    </div>
                </div>

                <div class="metric-body">
                    <div class="metric-value"><?= Html::encode($val) ?></div>
                </div>

                <?php if ($progress !== null): ?>
                    <div class="metric-progress-wrap">
                        <div class="bar <?= $barClass ?>">
                            <span style="width: <?= min(100, max(0, $progress)) ?>%;<?= $progress > 0 ? 'min-width:6px;' : '' ?>"></span>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($footHint !== ''): ?>
                    <div class="metric-foot">
                        <span class="metric-foot-hint"><?= Html::encode($footHint) ?></span>
                    </div>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>

        <!-- Пользователи -->
        <article class="metric metric--purple">
            <div class="metric-head">
                <div class="metric-icon-wrap">
                    <span class="metric-icon">
                        <span class="material-symbols-rounded">group</span>
                    </span>
                </div>
                <div class="metric-title-group">
                    <span class="metric-label">ПОЛЬЗОВАТЕЛИ</span>
                    <span class="metric-badge-code">Всего в БД</span>
                </div>
                <div class="metric-status-badge is-ok" title="В норме">
                    <span class="status-pulse-dot"></span>
                    <span class="status-badge-icon material-symbols-rounded">check</span>
                </div>
            </div>
            <div class="metric-body">
                <div class="metric-value"><?= $usersTotal === null ? 'н/д' : Html::encode((string)$usersTotal) ?></div>
            </div>
            <div class="metric-foot">
                <span class="metric-foot-hint"><?= $usersActive !== null ? ('Активных: ' . Html::encode((string)$usersActive)) : 'Зарегистрированные аккаунты' ?></span>
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

