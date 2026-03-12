<?php

return [
    'primary' => [
        [
            'icon' => 'dashboard',
            'label' => 'Панель управления',
            'url' => ['/admin/site/index'],
        ],
    ],
    'secondary' => [
        [
            'icon' => 'language',
            'label' => 'На сайт',
            'url' => '/',
            'linkOptions' => [
                'target' => '_blank',
            ],
        ],
        [
            'icon' => 'restart_alt',
            'label' => 'Очистить кеш',
            'url' => ['/admin/site/clear-cache'],
            'method' => 'post',
            'linkOptions' => [
                'data-confirm' => 'Очистить кеш админки?',
            ],
        ],
        [
            'icon' => 'logout',
            'label' => 'Выйти',
            'url' => ['/admin/site/logout'],
            'method' => 'post',
        ],
    ],
];
