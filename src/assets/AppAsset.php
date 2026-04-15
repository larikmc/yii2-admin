<?php

namespace larikmc\admin\assets;

use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{
    public $sourcePath = '@larikmc/admin/web';
    public $css = [
        'css/dashboard.css',
        'css/home.css',
        'css/style.css',
        'css/image-viewer-admin.css',
        'https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap',
        'https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0',
    ];
    public $js = [
        'js/dashboard.js',
        'js/lazyloader.js',
        'js/image-viewer-admin.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
    ];
}
