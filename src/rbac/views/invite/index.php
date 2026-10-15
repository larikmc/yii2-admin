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
            try {
                await navigator.clipboard.writeText(text);
                return true;
            } catch (err) {
                // fall through to fallback
            }
        }

        var textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.top = '0';
        textArea.style.left = '0';
        textArea.style.width = '2em';
        textArea.style.height = '2em';
        textArea.style.padding = '0';
        textArea.style.border = 'none';
        textArea.style.outline = 'none';
        textArea.style.boxShadow = 'none';
        textArea.style.background = 'transparent';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();

        var ok = false;
        try {
            ok = document.execCommand('copy');
        } catch (e) {
            ok = false;
        }
        document.body.removeChild(textArea);
        return ok;
    }

    btn.addEventListener('click', async function () {
        var text = (input.value || '').trim();
        if (!text) return;

        var originalText = labelEl ? labelEl.textContent : 'Скопировать';
        var originalIcon = iconEl ? iconEl.textContent : 'content_copy';

        try {
            var ok = await copyText(text);
            if (ok) {
                if (labelEl) labelEl.textContent = 'Скопировано!';
                if (iconEl) iconEl.textContent = 'done';
            } else {
                input.focus();
                input.select();
                if (labelEl) labelEl.textContent = 'Не удалось скопировать';
            }
        } catch (e) {
            input.focus();
            input.select();
            if (labelEl) labelEl.textContent = 'Не удалось скопировать';
        }

        setTimeout(function () {
            if (labelEl) labelEl.textContent = originalText;
            if (iconEl) iconEl.textContent = originalIcon;
        }, 2200);
    });

    input.addEventListener('click', function () {
        input.select();
    });
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
