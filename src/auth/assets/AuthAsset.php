<?php

namespace larikmc\admin\auth\assets;

use yii\web\AssetBundle;

class AuthAsset extends AssetBundle
{
    public $sourcePath = '@larikmc/admin/auth/assets/dist';

    public $css = [
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap',
        'https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0',
        'auth.css',
    ];

    public $js = [
        'auth.js',
    ];

    public $publishOptions = [
        'forceCopy' => YII_ENV_DEV || YII_DEBUG,
    ];

    public $depends = [
        'yii\bootstrap5\BootstrapAsset',
        'yii\bootstrap5\BootstrapPluginAsset',
    ];
}
