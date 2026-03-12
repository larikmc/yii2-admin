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
        <div class="table-responsive border rounded-3 overflow-hidden bg-white">
            <table class="table table-hover align-middle mb-0">
                <thead>
                <tr>
                    <th>Дата</th>
                    <th>IP</th>
                    <th>Уровень</th>
                    <th>Сообщение</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?= Html::encode($log['date']) ?></td>
                        <td><?= Html::encode($log['ip']) ?></td>
                        <td>
                        <span class="badge rounded-pill text-bg-warning">
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
