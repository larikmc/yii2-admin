<?php

namespace larikmc\admin\auth\assets;

use yii\web\AssetBundle;

class AuthAsset extends AssetBundle
{
    public $sourcePath = '@larikmc/admin/auth/assets/dist';

    public $css = [
        'auth.css',
    ];

    public $js = [
        'auth.js',
    ];

    public $publishOptions = [
        'forceCopy' => YII_ENV_DEV,
    ];

    public $depends = [
        'yii\bootstrap5\BootstrapAsset',
        'yii\bootstrap5\BootstrapPluginAsset',
    ];
}
