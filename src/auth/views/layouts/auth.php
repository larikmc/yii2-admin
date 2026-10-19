<?php

use yii\helpers\Html;
use larikmc\admin\auth\assets\AuthAsset;

AuthAsset::register($this);

$this->beginPage();
?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <script>
        (function () {
            try {
                var theme = localStorage.getItem('admin-theme') || localStorage.getItem('auth-theme');
                if (!theme) {
                    theme = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                }
                document.documentElement.setAttribute('data-theme', theme);
                if (theme === 'dark') {
                    document.documentElement.classList.add('dark');
                }
            } catch (e) {}
        })();
    </script>
    <?php $this->head(); ?>
</head>
<body class="auth-body">
<?php $this->beginBody(); ?>

<div class="auth-glow-orb auth-glow-orb--1" aria-hidden="true"></div>
<div class="auth-glow-orb auth-glow-orb--2" aria-hidden="true"></div>

<header class="auth-topbar">
    <?= Html::a(
        '<span class="material-symbols-rounded">arrow_back</span><span>На сайт</span>',
        '/',
        [
            'class' => 'auth-back-link',
            'title' => 'Вернуться на главную сайта',
        ]
    ) ?>

    <button type="button" class="auth-theme-switch" data-auth-theme-toggle aria-pressed="false" title="Переключить тему" aria-label="Переключить тему">
        <span class="auth-theme-track">
            <span class="auth-theme-stars" aria-hidden="true">
                <span class="star star--1"></span>
                <span class="star star--2"></span>
                <span class="star star--3"></span>
            </span>
            <span class="auth-theme-clouds" aria-hidden="true">
                <span class="cloud cloud--1"></span>
                <span class="cloud cloud--2"></span>
            </span>
            <span class="auth-theme-thumb">
                <span class="material-symbols-rounded auth-icon-sun">light_mode</span>
                <span class="material-symbols-rounded auth-icon-moon">dark_mode</span>
            </span>
        </span>
    </button>
</header>

<main role="main">
    <div class="container d-flex justify-content-center">
        <?= $content ?>
    </div>
</main>

<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>
