<?php
/**
 * Конфигурационный файл приложения
 */
$config = [
    'core' => [ // подмассив используемый самим ядром фреймворка        
        'router' => [ // подсистема маршрутизации
            'class' => \ItForFree\SimpleMVC\Router\WebRouter::class
        ],
        'mvc' => [ // настройки MVC
            'views' => [
                'base-template-path' => '',
                'base-layouts-path' => ''
            ]
        ],
        'user' => [ // подсистема авторизации
            'class' => \App\ExampleUser::class
        ]
    ]    
];

return $config;