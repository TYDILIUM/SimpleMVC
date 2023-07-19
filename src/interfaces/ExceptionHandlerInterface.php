<?php
namespace ItForFree\SimpleMVC\interfaces;

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
