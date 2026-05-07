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
                    'label' => 'Инвайт администратора',
                    'url' => ['/rbac/invite/index'],
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
            'visible' => static function (): bool {
                $identity = Yii::$app->user->identity;

                return $identity !== null && (string) $identity->getId() === SystemRbacHelper::ROOT_USER_ID;
            },
        ],
    ],
];
