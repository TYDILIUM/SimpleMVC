<?php

namespace ItForFree\SimpleMVC;

/**
 * Класс для работы с массивом $_SESSION
 * 
 * @todo Проверить на проблему блокировки сессии: @see http://php.net/manual/ru/session.examples.basic.php
 *
 * @author qwe
 */
class Session 
{
    public $session = null; //$_SESSION
    
    /**
    * Вернёт объект класса Session
    * 
    * @staticvar Session $instance
    */
    public static function get(): Session 
    {
        static $instance = null; // статическая переменная
        if (null === $instance) { // проверка существования
            $instance = new static();
        }
        return $instance;
    }
    
    /*скрываем конструктор
    - для того чтобы класс нельзя было создать в обход getInstance */
    public function __construct()
    {   
        session_start();
        $this->session = &$_SESSION;
        
    }
    
}
