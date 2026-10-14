<?php
/** @var yii\web\View $this */
/** @var array<int, array<string, string>> $logs */
use larikmc\admin\widgets\AdminPage;
use yii\helpers\Html;

$this->title = 'Журнал безопасности';
$this->params['breadcrumbs'] = [
    ['label' => 'Администрирование'],
    ['label' => $this->title],
];

ob_start();
?>
<div class="sz-panel">
    <?php if ($logs === []): ?>
        <div class="alert alert-secondary mb-0">Лог безопасности пуст или файл логов еще не создан.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                <tr>
                    <th style="width: 190px;">Дата</th>
                    <th style="width: 140px;">IP</th>
                    <th style="width: 120px;">Уровень</th>
                    <th>Сообщение</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><code><?= Html::encode($log['date']) ?></code></td>
                        <td><?= !empty($log['ip']) && $log['ip'] !== '-' ? Html::encode($log['ip']) : '<span class="text-muted">—</span>' ?></td>
                        <td>
                            <span class="badge text-bg-warning">
                                <?= Html::encode($log['level']) ?>
                            </span>
                        </td>
                        <td><?= Html::encode($log['message']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();

$this->params['topbarActions'] = [
    Html::beginForm(['/auth/clear-security-log'], 'post', ['class' => 'd-inline'])
    . Html::submitButton('Очистить лог', [
        'class' => 'btn btn-danger',
        'data-confirm' => 'Очистить лог безопасности?',
    ])
    . Html::endForm(),
];

echo AdminPage::widget([
    'title' => $this->title,
    'boxed' => false,
    'showHeader' => false,
    'content' => $content,
]);
