<?php

namespace larikmc\admin;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'larikmc\admin\controllers';
    public $defaultRoute = 'site/index';
    public $menu = [];
    public $secondaryMenu = [];

    public function init()
    {
        parent::init();

        $defaults = require __DIR__ . '/config/menu.php';

        if (empty($this->menu)) {
            $this->menu = $defaults['primary'] ?? [];
        }

        if (empty($this->secondaryMenu)) {
            $this->secondaryMenu = $defaults['secondary'] ?? [];
        }
    }
}
