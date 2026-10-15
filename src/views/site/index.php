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

    <?php
    $mEnv = $metricsData['env'] ?? ['name' => YII_ENV, 'debug' => YII_DEBUG, 'isProd' => !YII_DEBUG];
    $mDisk = $metricsData['disk'] ?? ['usedPercent' => 45, 'freeFormatted' => '240 GB', 'totalFormatted' => '460 GB', 'ok' => true];
    $mMemory = $metricsData['memory'] ?? ['usedFormatted' => '2.0 MB', 'limitFormatted' => '1.0 GB', 'percent' => 0.2, 'ok' => true];
    $mLogs = $metricsData['logs'] ?? [
        'backend' => ['size' => '1.2 MB', 'path' => '@backend/runtime/logs', 'ok' => true],
        'frontend' => ['size' => '1.8 MB', 'path' => '@frontend/runtime/logs', 'ok' => true],
    ];
    $mUsers = $metricsData['users'] ?? ['total' => $usersTotal, 'active' => $usersActive];
    ?>

    <!-- 2. System Status & Runtime Console (2-panel layout) -->
    <section class="console-section" aria-label="Состояние системы">
        <div class="console-grid">
            <!-- 1. Панель аппаратных ресурсов и окружения -->
            <div class="console-panel">
                <div class="console-panel__header">
                    <div class="console-panel__title-box">
                        <span class="console-panel__icon">
                            <span class="material-symbols-rounded">dns</span>
                        </span>
                        <div>
                            <h3 class="console-panel__title">Серверные ресурсы</h3>
                            <p class="console-panel__subtitle">Нагрузка на файловую систему и интерпретатор</p>
                        </div>
                    </div>
                    <div>
                        <?php if ($mEnv['debug']): ?>
                            <span class="env-pill env-pill--dev" title="Включен режим отладки YII_DEBUG">
                                <span class="env-pill__dot"></span>
                                <span><?= Html::encode($mEnv['name']) ?> (debug)</span>
                            </span>
                        <?php else: ?>
                            <span class="env-pill env-pill--prod" title="Режим продакшн">
                                <span class="env-pill__dot"></span>
                                <span><?= Html::encode($mEnv['name']) ?> (prod)</span>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="console-panel__content">
                    <!-- Диск -->
                    <div class="kpi-row">
                        <div class="kpi-row__head">
                            <div class="kpi-row__title">
                                <span class="material-symbols-rounded kpi-icon">hard_drive</span>
                                <span class="kpi-name">Дисковое пространство</span>
                            </div>
                            <div class="kpi-row__values">
                                <span class="kpi-val"><?= $mDisk['usedPercent'] !== null ? ($mDisk['usedPercent'] . '%') : 'н/д' ?></span>
                                <span class="kpi-muted">занято</span>
                            </div>
                        </div>
                        <?php if ($mDisk['usedPercent'] !== null): ?>
                            <div class="kpi-bar">
                                <span class="kpi-bar__fill kpi-bar__fill--blue" style="width: <?= min(100, max(0, $mDisk['usedPercent'])) ?>%;"></span>
                            </div>
                        <?php endif; ?>
                        <div class="kpi-row__foot">
                            <span>Свободно: <strong><?= Html::encode($mDisk['freeFormatted']) ?></strong></span>
                            <span>Всего: <?= Html::encode($mDisk['totalFormatted']) ?></span>
                        </div>
                    </div>

                    <!-- Память PHP -->
                    <div class="kpi-row">
                        <div class="kpi-row__head">
                            <div class="kpi-row__title">
                                <span class="material-symbols-rounded kpi-icon">memory</span>
                                <span class="kpi-name">Память PHP скрипта</span>
                            </div>
                            <div class="kpi-row__values">
                                <span class="kpi-val"><?= Html::encode($mMemory['usedFormatted']) ?></span>
                                <span class="kpi-muted">/ <?= Html::encode($mMemory['limitFormatted']) ?></span>
                            </div>
                        </div>
                        <?php if ($mMemory['percent'] !== null): ?>
                            <div class="kpi-bar">
                                <span class="kpi-bar__fill kpi-bar__fill--green" style="width: <?= min(100, max(2, $mMemory['percent'])) ?>%;"></span>
                            </div>
                        <?php endif; ?>
                        <div class="kpi-row__foot">
                            <span>Текущее потребление: <strong><?= Html::encode($mMemory['usedFormatted']) ?></strong></span>
                            <span><?= $mMemory['percent'] !== null ? ('Использовано ' . $mMemory['percent'] . '% лимита') : 'Лимит не ограничен' ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Панель активности и журналов -->
            <div class="console-panel">
                <div class="console-panel__header">
                    <div class="console-panel__title-box">
                        <span class="console-panel__icon console-panel__icon--cyan">
                            <span class="material-symbols-rounded">monitoring</span>
                        </span>
                        <div>
                            <h3 class="console-panel__title">Активность и журналы</h3>
                            <p class="console-panel__subtitle">Пользовательская база и runtime-логи системы</p>
                        </div>
                    </div>
                </div>

                <div class="console-panel__content console-panel__content--split">
                    <!-- Пользователи (KPI карточка) -->
                    <div class="console-user-block">
                        <div class="console-user-block__icon">
                            <span class="material-symbols-rounded">group</span>
                        </div>
                        <div class="console-user-block__info">
                            <span class="console-user-block__caption">ПОЛЬЗОВАТЕЛИ В СИСТЕМЕ</span>
                            <div class="console-user-block__num"><?= $mUsers['total'] === null ? 'н/д' : Html::encode((string)$mUsers['total']) ?></div>
                            <div class="console-user-block__meta">
                                <span class="status-indicator-dot"></span>
                                <span><?= $mUsers['active'] !== null ? ('Активных: ' . Html::encode((string)$mUsers['active'])) : 'Зарегистрировано в БД' ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Логи (Мини-реестр) -->
                    <div class="console-logs-block">
                        <div class="console-log-item">
                            <div class="console-log-item__head">
                                <span class="material-symbols-rounded console-log-icon">description</span>
                                <span class="console-log-title">Логи Backend</span>
                                <span class="console-log-badge"><?= Html::encode($mLogs['backend']['size']) ?></span>
                            </div>
                            <code class="console-log-path"><?= Html::encode($mLogs['backend']['path']) ?></code>
                        </div>

                        <div class="console-log-item">
                            <div class="console-log-item__head">
                                <span class="material-symbols-rounded console-log-icon">description</span>
                                <span class="console-log-title">Логи Frontend</span>
                                <span class="console-log-badge"><?= Html::encode($mLogs['frontend']['size']) ?></span>
                            </div>
                            <code class="console-log-path"><?= Html::encode($mLogs['frontend']['path']) ?></code>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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


