<?php
namespace ItForFree\SimpleMVC\Interfaces;

/**
 *  Интерфейс для классов обработки ошибок
 */
interface ExceptionHandlerInterface
{
    /**
     * Метод для обработки перехваченной ошибки     
     */
    public function handleException(\Exception $exception): void;
}
