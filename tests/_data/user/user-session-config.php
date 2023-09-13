<?php
/**
 * Конфигурационной файл приложения
 */
$config = [
    'core' => [ // подмассив используемый самим ядром фреймворка
        'user' => [ // подсистема авторизации
            'class' => \application\models\user\ExampleUser::class,
            'params' => [
                'param2' => 'param2',
                'param3' => 'param3',
                'param4' => 'param4',
                'param5' => 'param5'
            ],
            'construct' => [
                'session' => '@session',
                'router' => '@router'
              ],  
        ],
        'session' => [ // подсистема работы с сессиями
            'class' => ItForFree\SimpleMVC\Session::class,
            'alias' => '@session'
        ],
        'userTestTwo' => [
            'class' => \application\models\user\ExampleUser::class
        ],
        'router' => [ // подсистема маршрутизация
            'class' => \ItForFree\SimpleMVC\Router\WebRouter::class,
            'alias' => '@router'
        ],
    ]    
];

return $config;