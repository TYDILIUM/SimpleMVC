<?php
namespace ItForFree\SimpleMVC\interfaces;

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
