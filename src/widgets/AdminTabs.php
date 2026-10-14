<?php

namespace larikmc\admin\widgets;

use yii\base\Widget;
use yii\helpers\Html;
use yii\web\View;

class AdminTabs extends Widget
{
    public array $items = [];
    public string $style = 'line'; // 'line' or 'pills'
    public string $containerClass = '';
    public ?string $navClass = null;
    public bool $encodeLabels = true;

    public function run(): string
    {
        if ($this->items === []) {
            return '';
        }

        $baseNavClass = $this->navClass;
        if ($baseNavClass === null) {
            $baseNavClass = $this->style === 'pills' ? 'nav-pills-solid' : 'nav nav-tabs mb-3';
        }

        $hasPanels = $this->hasPanels();
        $widgetId = $this->getId();
        $activeIndex = $this->resolveActiveIndex();
        $navItems = [];
        $panelItems = [];

        foreach (array_values($this->items) as $index => $item) {
            $isActive = $index === $activeIndex;
            $label = (string) ($item['label'] ?? '');
            $label = $this->encodeLabels ? Html::encode($label) : $label;

            if ($hasPanels) {
                $panelId = $item['id'] ?? ($widgetId . '-panel-' . $index);
                $tabId = $widgetId . '-tab-' . $index;

                $navItems[] = Html::tag(
                    'li',
                    Html::button($label, [
                        'type' => 'button',
                        'id' => $tabId,
                        'class' => 'nav-link' . ($isActive ? ' active' : ''),
                        'role' => 'tab',
                        'aria-selected' => $isActive ? 'true' : 'false',
                        'aria-controls' => $panelId,
                        'data-sz-tab-trigger' => true,
                        'data-sz-tab-target' => '#' . $panelId,
                    ]),
                    ['class' => 'nav-item', 'role' => 'presentation']
                );

                $panelItems[] = Html::tag(
                    'div',
                    (string) ($item['content'] ?? ''),
                    [
                        'id' => $panelId,
                        'class' => 'sz-tab-panel' . ($isActive ? ' is-active' : ''),
                        'role' => 'tabpanel',
                        'aria-labelledby' => $tabId,
                        'tabindex' => '0',
                        'data-sz-tab-panel' => true,
                    ]
                );

                continue;
            }

            $navItems[] = Html::tag(
                'li',
                Html::a($label, $item['url'] ?? '#', [
                    'class' => 'nav-link' . ($isActive ? ' active' : ''),
                    'aria-current' => $isActive ? 'page' : null,
                ]),
                ['class' => 'nav-item']
            );
        }

        $html = Html::tag('ul', implode('', $navItems), [
            'class' => trim($baseNavClass . ' sz-tabs__nav'),
            'role' => $hasPanels ? 'tablist' : null,
            'data-sz-tabs' => $hasPanels ? true : null,
        ]);

        if ($hasPanels) {
            $html .= Html::tag('div', implode('', $panelItems), ['class' => 'sz-tab-panels']);
            $this->registerClientScript();
        }

        return Html::tag('div', $html, ['class' => trim('sz-tabs ' . $this->containerClass)]);
    }

    private function hasPanels(): bool
    {
        foreach ($this->items as $item) {
            if (array_key_exists('content', $item)) {
                return true;
            }
        }

        return false;
    }

    private function resolveActiveIndex(): int
    {
        foreach (array_values($this->items) as $index => $item) {
            if (!empty($item['active'])) {
                return $index;
            }
        }

        return 0;
    }

    private function registerClientScript(): void
    {
        $view = $this->getView();
        $key = __CLASS__ . ':tabs';

        if (($view->js[$key][View::POS_READY] ?? null) !== null) {
            return;
        }

        $view->registerJs(<<<'JS'
(function () {
    if (window.szAdminTabsInitialized) {
        return;
    }

    window.szAdminTabsInitialized = true;

    document.addEventListener('click', function (event) {
        const trigger = event.target.closest('[data-sz-tab-trigger]');
        if (!trigger) {
            return;
        }

        const root = trigger.closest('.sz-tabs');
        const nav = trigger.closest('[data-sz-tabs]');
        const selector = trigger.getAttribute('data-sz-tab-target');
        if (!root || !nav || !selector) {
            return;
        }

        const panel = root.querySelector(selector);
        if (!panel) {
            return;
        }

        nav.querySelectorAll('[data-sz-tab-trigger]').forEach(function (item) {
            item.classList.remove('active');
            item.setAttribute('aria-selected', 'false');
        });

        root.querySelectorAll('[data-sz-tab-panel]').forEach(function (item) {
            item.classList.remove('is-active');
        });

        trigger.classList.add('active');
        trigger.setAttribute('aria-selected', 'true');
        panel.classList.add('is-active');
    });
})();
JS, View::POS_READY, $key);
    }
}
