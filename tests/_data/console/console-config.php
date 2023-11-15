<?php
/**
 * Конфигурационый файл приложения
 */
$config = [
    'core' => [ // подмассив используемый самим ядром фреймворка        
        'router' => [ // подсистема маршрутизации
            'class' => \ItForFree\SimpleMVC\Router\ConsoleRouter::class
        ],
    ]    
];

return $config;