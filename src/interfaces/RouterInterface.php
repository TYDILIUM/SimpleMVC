<?php
namespace ItForFree\SimpleMVC\interfaces;

/**
 * Класс-маршрутизатор
 */

interface RouterInterface
{
    /**
     * 
     * @param type $url
     */
    public function callScript(string $url): void;
    
}
