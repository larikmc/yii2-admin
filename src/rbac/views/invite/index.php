<?php

use larikmc\admin\widgets\AdminPage;
use yii\bootstrap5\Html;

$this->title = 'Приглашение администратора';
$this->params['breadcrumbs'] = [
    ['label' => 'Администрирование'],
    ['label' => 'RBAC', 'url' => ['/rbac/default/index']],
    ['label' => $this->title],
];

$content = '<p class="mb-3">Используйте одноразовую ссылку, чтобы дать доступ новому администратору.</p>';
$content .= Html::beginForm(['/rbac/invite/index'], 'post');
$content .= Html::submitButton('Сгенерировать ссылку', ['class' => 'btn btn-primary']);
$content .= Html::endForm();

if ($inviteData !== null) {
    $copyText = \yii\helpers\Json::htmlEncode($inviteData['url']);
    $this->registerJs(<<<JS
(async function () {
    const text = {$copyText};
    try {
        await navigator.clipboard.writeText(text);
    } catch (e) {}
})();
JS);
}

echo AdminPage::widget([
    'title' => $this->title,
    'tabs' => $this->params['rbacTabs'] ?? [],
    'showHeader' => false,
    'content' => $content,
]);
