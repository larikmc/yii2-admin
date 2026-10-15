<?php

namespace larikmc\admin\controllers;

use common\models\User;
use Yii;
use yii\caching\DummyCache;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\FileHelper;
use yii\web\Controller;
use yii\web\Response;

class SiteController extends Controller
{
    public $layout = '@larikmc/admin/views/layouts/main';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'ui-kit', 'clear-cache', 'flush-cache'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['get', 'post'],
                    'clear-cache' => ['post'],
                    'flush-cache' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
            ],
        ];
    }

    public function actionIndex()
    {
        $identity = Yii::$app->user->identity;

        $health = [
            ['title' => 'Кеш', 'ok' => false, 'value' => 'Не проверено', 'hint' => 'Yii::$app->cache'],
            ['title' => 'Режим приложения', 'ok' => !YII_DEBUG, 'value' => sprintf('%s (%s)', YII_ENV, YII_DEBUG ? 'debug' : 'prod'), 'hint' => 'YII_ENV'],
            ['title' => 'База данных', 'ok' => false, 'value' => 'Не проверено', 'hint' => 'Yii::$app->db'],
            ['title' => 'Использование диска', 'ok' => false, 'value' => 'Неизвестно'],
            ['title' => 'Память PHP', 'ok' => false, 'value' => 'Неизвестно'],
            ['title' => 'Логи backend', 'ok' => false, 'value' => 'Неизвестно', 'hint' => '@backend/runtime/logs'],
            ['title' => 'Логи frontend', 'ok' => false, 'value' => 'Неизвестно', 'hint' => '@frontend/runtime/logs'],
        ];

        $formatBytes = static function (float $bytes): string {
            $units = ['B', 'KB', 'MB', 'GB', 'TB'];
            $i = 0;
            while ($bytes >= 1024 && $i < count($units) - 1) {
                $bytes /= 1024;
                $i++;
            }

            return sprintf('%.1f %s', $bytes, $units[$i]);
        };

        $parseSizeToBytes = static function (string $size): ?float {
            $value = trim($size);
            if ($value === '' || $value === '-1') {
                return null;
            }

            if (!preg_match('/^([0-9]+(?:\.[0-9]+)?)\s*([kmgtpe]?)[b]?$/i', $value, $matches)) {
                return null;
            }

            $number = (float) $matches[1];
            $unit = strtolower($matches[2]);
            $scale = ['' => 0, 'k' => 1, 'm' => 2, 'g' => 3, 't' => 4, 'p' => 5, 'e' => 6];
            if (!array_key_exists($unit, $scale)) {
                return null;
            }

            return $number * (1024 ** $scale[$unit]);
        };

        $resolveDirSize = static function (string $dir): ?float {
            if (!is_dir($dir)) {
                return null;
            }

            $size = 0.0;
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS)
            );
            foreach ($iterator as $fileInfo) {
                if ($fileInfo->isFile()) {
                    $size += (float) $fileInfo->getSize();
                }
            }

            return $size;
        };

        $dbOk = false;
        if (Yii::$app->has('db')) {
            try {
                Yii::$app->db->createCommand('SELECT 1')->queryScalar();
                $dbOk = true;
            } catch (\Throwable $e) {
                $dbOk = false;
            }
        }
        $health[2]['ok'] = $dbOk;
        $health[2]['value'] = $dbOk ? 'Подключена' : 'Ошибка соединения';

        if (Yii::$app->has('cache')) {
            $cache = Yii::$app->cache;
            if ($cache instanceof DummyCache) {
                $health[0]['value'] = 'DummyCache (без хранилища)';
            } else {
                try {
                    $key = '__backend-health-' . time();
                    $cache->set($key, 'ok', 10);
                    $health[0]['ok'] = $cache->get($key) === 'ok';
                    $health[0]['value'] = $health[0]['ok'] ? 'Работает' : 'Проверьте конфигурацию';
                    $cache->delete($key);
                } catch (\Throwable $e) {
                    $health[0]['value'] = 'Ошибка кеша';
                }
            }
        } else {
            $health[0]['value'] = 'Компонент кеша не настроен';
        }

        $diskPath = Yii::getAlias('@webroot');
        if (!is_dir($diskPath)) {
            $diskPath = __DIR__;
        }

        $diskTotal = @disk_total_space($diskPath);
        $diskFree = @disk_free_space($diskPath);
        if (is_numeric($diskTotal) && is_numeric($diskFree) && $diskTotal > 0) {
            $diskUsed = max(0, $diskTotal - $diskFree);
            $diskUsedPercent = ($diskUsed / $diskTotal) * 100;

            $health[3]['ok'] = $diskUsedPercent < 85;
            $health[3]['value'] = sprintf('занято %.1f%%', $diskUsedPercent);
            $health[3]['progress'] = max(0, min(100, $diskUsedPercent));
            $health[3]['footHint'] = sprintf('Свободно %s из %s', $formatBytes((float) $diskFree), $formatBytes((float) $diskTotal));
        }

        $memoryUsage = (float) memory_get_usage(true);
        $memoryLimitRaw = (string) ini_get('memory_limit');
        $memoryLimitBytes = $parseSizeToBytes($memoryLimitRaw);
        if ($memoryLimitBytes !== null && $memoryLimitBytes > 0) {
            $memoryPercent = ($memoryUsage / $memoryLimitBytes) * 100;
            $health[4]['ok'] = $memoryPercent < 85;
            $health[4]['value'] = sprintf(
                '%s из %s',
                $formatBytes($memoryUsage),
                $formatBytes($memoryLimitBytes)
            );
            $health[4]['progress'] = max(0, min(100, $memoryPercent));
            $health[4]['footHint'] = sprintf('Использовано %.1f%%', $memoryPercent);
        } else {
            $health[4]['ok'] = true;
            $health[4]['value'] = $formatBytes($memoryUsage);
            $health[4]['hint'] = 'Лимит не ограничен';
        }

        try {
            $backendLogsSize = $resolveDirSize(Yii::getAlias('@runtime/logs'));
            if ($backendLogsSize === null) {
                $health[5]['value'] = 'Каталог не найден';
            } else {
                $health[5]['ok'] = $backendLogsSize < (200 * 1024 * 1024);
                $health[5]['value'] = $formatBytes($backendLogsSize);
            }
        } catch (\Throwable $e) {
            $health[5]['value'] = 'Ошибка чтения';
        }

        try {
            $frontendLogsSize = $resolveDirSize(Yii::getAlias('@frontend/runtime/logs'));
            if ($frontendLogsSize === null) {
                $health[6]['value'] = 'Каталог не найден';
            } else {
                $health[6]['ok'] = $frontendLogsSize < (200 * 1024 * 1024);
                $health[6]['value'] = $formatBytes($frontendLogsSize);
            }
        } catch (\Throwable $e) {
            $health[6]['value'] = 'Ошибка чтения';
        }

        $usersTotal = null;
        $usersActive = null;
        if ($dbOk) {
            try {
                $usersTotal = (int) User::find()->count();
                $usersActive = (int) User::find()->where(['status' => User::STATUS_ACTIVE])->count();
            } catch (\Throwable $e) {
                $usersTotal = null;
                $usersActive = null;
            }
        }

        return $this->render('@larikmc/admin/views/site/index', [
            'summary' => [
                'email' => $identity->email ?? 'Администратор',
                'phpVersion' => PHP_VERSION,
                'yiiVersion' => Yii::getVersion(),
                'timeZone' => Yii::$app->timeZone,
                'serverTime' => date('H:i:s'),
                'serverTimestamp' => time(),
            ],
            'health' => $health,
            'metricsData' => [
                'env' => [
                    'name' => YII_ENV,
                    'debug' => YII_DEBUG,
                    'isProd' => !YII_DEBUG && YII_ENV === 'prod',
                ],
                'disk' => [
                    'usedPercent' => isset($diskUsedPercent) ? round($diskUsedPercent, 1) : null,
                    'freeFormatted' => is_numeric($diskFree) ? $formatBytes((float)$diskFree) : 'н/д',
                    'totalFormatted' => is_numeric($diskTotal) ? $formatBytes((float)$diskTotal) : 'н/д',
                    'ok' => $health[3]['ok'] ?? true,
                ],
                'memory' => [
                    'usedFormatted' => $formatBytes($memoryUsage),
                    'limitFormatted' => ($memoryLimitBytes !== null && $memoryLimitBytes > 0) ? $formatBytes($memoryLimitBytes) : 'Без ограничений',
                    'percent' => isset($memoryPercent) ? round($memoryPercent, 1) : null,
                    'ok' => $health[4]['ok'] ?? true,
                ],
                'logs' => [
                    'backend' => [
                        'size' => $health[5]['value'] ?? 'н/д',
                        'path' => '@backend/runtime/logs',
                        'ok' => $health[5]['ok'] ?? true,
                    ],
                    'frontend' => [
                        'size' => $health[6]['value'] ?? 'н/д',
                        'path' => '@frontend/runtime/logs',
                        'ok' => $health[6]['ok'] ?? true,
                    ],
                ],
                'users' => [
                    'total' => $usersTotal,
                    'active' => $usersActive,
                ],
            ],
            'usersTotal' => $usersTotal,
            'usersActive' => $usersActive,
        ]);
    }

    public function actionUiKit()
    {
        return $this->render('@larikmc/admin/views/site/ui-kit');
    }

    public function actionLogin()
    {
        return $this->redirect(['/auth/login']);
    }

    public function actionLogout()
    {
        if (!Yii::$app->user->isGuest) {
            Yii::$app->user->logout();
        }

        return $this->redirect(['/auth/login']);
    }

    public function actionClearCache()
    {
        $flushedComponents = [];
        $clearedDirectories = [];

        try {
            if (Yii::$app->has('cache')) {
                $cache = Yii::$app->cache;
                if (!$cache instanceof DummyCache) {
                    $cache->flush();
                    $flushedComponents[] = 'app cache';
                }
            }

            foreach ($this->resolveRuntimeCacheDirectories() as $cacheDir) {
                if (!is_dir($cacheDir)) {
                    continue;
                }

                $this->clearDirectoryContents($cacheDir);
                $clearedDirectories[] = $cacheDir;
            }

            if ($flushedComponents === [] && $clearedDirectories === []) {
                Yii::$app->session->setFlash('warning', 'Кеш не найден: нет настроенного хранилища или каталогов runtime/cache.');
            } else {
                Yii::$app->session->setFlash('success', 'Кеш очищен.');
            }
        } catch (\Throwable $e) {
            Yii::$app->session->setFlash('error', 'Не удалось очистить кеш: ' . $e->getMessage());
        }

        return $this->redirect(Yii::$app->request->referrer ?: ['/']);
    }

    public function actionFlushCache()
    {
        return $this->actionClearCache();
    }

    /**
     * @return string[]
     */
    protected function resolveRuntimeCacheDirectories(): array
    {
        $directories = [];
        $aliases = ['@backend', '@frontend', '@common', '@runtime'];

        foreach ($aliases as $alias) {
            $basePath = Yii::getAlias($alias, false);
            if (!is_string($basePath) || $basePath === '') {
                continue;
            }

            $candidate = rtrim($basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'cache';
            if ($alias === '@runtime') {
                $candidate = rtrim($basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'cache';
            }

            $directories[] = $candidate;
        }

        return array_values(array_unique($directories));
    }

    protected function clearDirectoryContents(string $directory): void
    {
        $items = FileHelper::findDirectories($directory, ['recursive' => false]);
        foreach ($items as $item) {
            FileHelper::removeDirectory($item);
        }

        foreach (FileHelper::findFiles($directory, ['recursive' => false]) as $file) {
            @unlink($file);
        }
    }
}
