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
    $url = (string)($inviteData['url'] ?? '');
    $expiresAt = isset($inviteData['expiresAt']) ? date('d.m.Y H:i', (int)$inviteData['expiresAt']) : null;

    $content .= '<div class="alert alert-success mt-3 mb-0">';
    $content .= '<div class="mb-2"><strong>Ссылка создана.</strong></div>';
    if ($expiresAt !== null) {
        $content .= '<div class="small text-muted mb-2">Действует до: ' . Html::encode($expiresAt) . '</div>';
    }
    $content .= Html::textInput('inviteLink', $url, [
        'id' => 'admin-invite-link',
        'class' => 'form-control mb-2',
        'readonly' => true,
    ]);
    $content .= Html::button('Скопировать ссылку', [
        'type' => 'button',
        'class' => 'btn btn-outline-primary',
        'id' => 'admin-invite-copy-btn',
    ]);
    $content .= '</div>';

    $this->registerJs(<<<JS
(function () {
    var input = document.getElementById('admin-invite-link');
    var btn = document.getElementById('admin-invite-copy-btn');
    if (!input || !btn) return;

    async function copyText(text) {
        if (navigator.clipboard && window.isSecureContext) {
            await navigator.clipboard.writeText(text);
            return true;
        }

        input.removeAttribute('readonly');
        input.select();
        input.setSelectionRange(0, 99999);
        var ok = false;
        try {
            ok = document.execCommand('copy');
        } catch (e) {
            ok = false;
        }
        input.setAttribute('readonly', 'readonly');
        return ok;
    }

    btn.addEventListener('click', async function () {
        var text = input.value || '';
        if (!text) return;

        var original = btn.textContent;
        try {
            var ok = await copyText(text);
            btn.textContent = ok ? 'Скопировано' : 'Не удалось скопировать';
        } catch (e) {
            btn.textContent = 'Не удалось скопировать';
        }
        setTimeout(function () {
            btn.textContent = original;
        }, 1500);
    });

    btn.click();
})();
JS);
}

echo AdminPage::widget([
    'title' => $this->title,
    'tabs' => $this->params['rbacTabs'] ?? [],
    'showHeader' => false,
    'content' => $content,
]);
