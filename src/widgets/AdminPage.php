<?php

namespace larikmc\admin\widgets;

use yii\base\Widget;
use yii\helpers\Html;

class AdminPage extends Widget
{
    public string $title = '';
    public ?string $subtitle = null;
    public string $content = '';
    public array $tabs = [];
    public ?string $tabsStyle = null;
    public array $actions = [];
    public bool $boxed = true;
    public string $panelClass = 'sz-panel';
    public string $actionsPosition = 'header';
    public bool $showHeader = true;

    public function run(): string
    {
        $header = Html::tag('h1', Html::encode($this->title), ['class' => 'mb-0']);
        $subtitle = $this->subtitle !== null && $this->subtitle !== ''
            ? Html::tag('p', Html::encode($this->subtitle), ['class' => 'text-muted mb-0 mt-2'])
            : '';

        $actions = '';
        if ($this->actions !== []) {
            $actionsClass = $this->actionsPosition === 'below_title'
                ? 'd-flex gap-2 flex-wrap align-items-center'
                : 'd-flex gap-2 flex-wrap';
            $actions = Html::tag('div', implode('', $this->actions), ['class' => $actionsClass]);
        }

        $tabs = '';
        if ($this->tabs !== []) {
            $tabsOptions = [
                'items' => $this->tabs,
            ];
            if ($this->tabsStyle !== null) {
                $tabsOptions['style'] = $this->tabsStyle;
            }
            $tabs = AdminTabs::widget($tabsOptions);
        }

        $headerActions = $this->actionsPosition === 'header' ? $actions : '';
        $bodyActions = $this->actionsPosition === 'below_title' ? Html::tag('div', $actions, ['class' => 'mb-4']) : '';
        $tabsActions = $this->actionsPosition === 'below_tabs' ? Html::tag('div', $actions, ['class' => 'mb-3']) : '';

        $headerBlock = '';
        if ($this->showHeader) {
            $headerBlock = Html::tag(
                'div',
                Html::tag(
                    'div',
                    Html::tag('div', $header . $subtitle, ['class' => 'flex-grow-1']) . $headerActions,
                    ['class' => 'd-flex justify-content-between align-items-start gap-3 flex-wrap mb-4']
                ) . $bodyActions,
                ['class' => 'sz-page__header']
            );
        }

        $content = $this->boxed
            ? Html::tag('div', $this->content, ['class' => $this->panelClass])
            : $this->content;

        return Html::tag(
            'section',
            $headerBlock . $tabs . $tabsActions . Html::tag('div', $content, ['class' => 'sz-page__body']),
            ['class' => 'sz-page']
        );
    }
}
