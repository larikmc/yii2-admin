<?php

use larikmc\admin\widgets\AdminPage;
use yii\bootstrap5\Html;

$this->title = 'Приглашение администратора';
$this->params['breadcrumbs'] = [
    ['label' => 'Администрирование'],
    ['label' => 'RBAC', 'url' => ['/rbac/default/index']],
    ['label' => $this->title],
];

$content = '<div class="sz-invite-box">';
$content .= '<div class="sz-invite-box__header">';
$content .= '<div class="sz-invite-box__icon"><span class="material-symbols-rounded">person_add</span></div>';
$content .= '<div class="sz-invite-box__copy">';
$content .= '<h4 class="sz-invite-box__title">Генерация ссылки приглашения</h4>';
$content .= '<p class="sz-invite-box__desc">Используйте защищенную одноразовую ссылку, чтобы дать доступ новому администратору к системе.</p>';
$content .= '</div>';
$content .= '</div>';

$content .= Html::beginForm(['/rbac/invite/index'], 'post', ['class' => 'sz-invite-form']);
$content .= Html::submitButton('<span class="material-symbols-rounded">add_link</span><span>Сгенерировать ссылку</span>', [
    'class' => 'btn btn-primary d-inline-flex align-items-center gap-2',
    'encode' => false,
]);
$content .= Html::endForm();

if ($inviteData !== null) {
    $url = (string)($inviteData['url'] ?? '');
    $expiresAt = isset($inviteData['expiresAt']) ? date('d.m.Y H:i', (int)$inviteData['expiresAt']) : null;

    $content .= '<div class="sz-invite-result">';
    $content .= '<div class="sz-invite-result__meta">';
    $content .= '<div class="sz-invite-result__badge"><span class="material-symbols-rounded">check_circle</span> Ссылка создана</div>';
    if ($expiresAt !== null) {
        $content .= '<div class="sz-invite-result__time"><span class="material-symbols-rounded">schedule</span> Действует до: <strong>' . Html::encode($expiresAt) . '</strong></div>';
    }
    $content .= '</div>';

    $content .= '<div class="sz-invite-input-wrap">';
    $content .= Html::textInput('inviteLink', $url, [
        'id' => 'admin-invite-link',
        'class' => 'form-control sz-invite-input',
        'readonly' => true,
        'spellcheck' => 'false',
    ]);
    $content .= Html::button('<span class="material-symbols-rounded">content_copy</span><span class="btn-copy-label">Скопировать</span>', [
        'type' => 'button',
        'class' => 'btn btn-primary sz-invite-copy-btn',
        'id' => 'admin-invite-copy-btn',
        'encode' => false,
    ]);
    $content .= '</div>';
    $content .= '</div>';

    $this->registerJs(<<<JS
(function () {
    var input = document.getElementById('admin-invite-link');
    var btn = document.getElementById('admin-invite-copy-btn');
    if (!input || !btn) return;

    var labelEl = btn.querySelector('.btn-copy-label');
    var iconEl = btn.querySelector('.material-symbols-rounded');

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

        var originalText = labelEl ? labelEl.textContent : btn.textContent;
        var originalIcon = iconEl ? iconEl.textContent : '';
        try {
            var ok = await copyText(text);
            if (labelEl) labelEl.textContent = ok ? 'Скопировано!' : 'Не удалось скопировать';
            if (iconEl && ok) iconEl.textContent = 'done';
        } catch (e) {
            if (labelEl) labelEl.textContent = 'Не удалось скопировать';
        }
        setTimeout(function () {
            if (labelEl) labelEl.textContent = originalText;
            if (iconEl) iconEl.textContent = originalIcon;
        }, 1800);
    });

    btn.click();
})();
JS);
}
$content .= '</div>';

echo AdminPage::widget([
    'title' => $this->title,
    'tabs' => $this->params['rbacTabs'] ?? [],
    'showHeader' => false,
    'content' => $content,
]);
