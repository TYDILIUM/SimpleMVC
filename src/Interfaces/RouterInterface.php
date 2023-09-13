<?php
namespace ItForFree\SimpleMVC\Interfaces;

/**
 * Класс-маршрутизатор
 */

interface RouterInterface
{
    /**
     * 
     * @param string $url
     */
    public function callScript(string $url): void;
    
}
