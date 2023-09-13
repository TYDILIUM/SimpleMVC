<?php
namespace ItForFree\SimpleMVC\Router;

/**
 * Класс-маршрутизатор, его задача по переданной строке (предположительно это какой-то адресе на сайте),
 * определить какой контролеер и какое действие надо вызывать.
 */

class WebRouter extends Router
{
    public static function getRoute(): string
    {
        $getValue = isset($_GET['route'] ) ? $_GET['route'] : "";
        return $getValue;
    } 
    
    /**
     * Формирование ссылок
     */
    public static function link(string $route): string
    {
        $path = "/index.php?route=$route"; 
        return $path;
    }
}