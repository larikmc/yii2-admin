<?php

require_once 'D:/OpenServer6.5.0/home/vpluse64.loc/vendor/autoload.php';
require_once 'D:/OpenServer6.5.0/home/vpluse64.loc/vendor/yiisoft/yii2/Yii.php';

// Register autoloader for larikmc\admin\
spl_autoload_register(function ($class) {
    $prefix = 'larikmc\\admin\\';
    $baseDir = __DIR__ . '/../src/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});
