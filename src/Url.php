<?php
namespace ItForFree\SimpleMVC;

/**
 * Класс для работы с URL и формирования ссылок
 */
class Url
{
    /**
     * Получаем URL
     */
    public static function getRoute(): string
    {
        $getValue = isset($_GET['route'] ) ? $_GET['route'] : "";
        return $getValue;
    }
         
    public static function link(string $route): string
    {
        $path = "/index.php?route=$route"; 
        return $path;
    }
}

