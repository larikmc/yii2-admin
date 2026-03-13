<?php

return [
    'primary' => [
        [
            'icon' => 'dashboard',
            'label' => 'Панель управления',
            'url' => ['/admin/site/index'],
        ],
        [
            'icon' => 'admin_panel_settings',
            'label' => 'Администрирование',
            'items' => [
                [
                    'label' => 'RBAC',
                    'url' => ['/rbac/default/index'],
                ],
                [
                    'label' => 'Security Log',
                    'url' => ['/auth/security-log'],
                ],
            ],
        ],
    ],
    'secondary' => [
        [
            'icon' => 'palette',
            'label' => 'ADMIN-UI-KIT',
            'url' => ['/admin/site/ui-kit'],
        ],
    ],
];
