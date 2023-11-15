<?php

namespace ItForFree\SimpleMVC;

use ItForFree\SimpleMVC\Config;
use ItForFree\SimpleMVC\Interfaces\ExceptionHandlerInterface;
use ItForFree\SimpleMVC\Exceptions\SmvcUsageException;

class ExceptionHandler
{
   
    
    /**
     * Метод обработки исключения. Проверяет существует ли пользовательский обработчик.
     * Если пользовательский обработчик найден, то исключение передаётся ему на обработку.
     * Если пользовательского обработчика нет то исключение никак не обрабатывается.
     * @throws \Exception
     */
    public function handleException(\Exception $exception): void
    {
        $handlers = Config::get('core.handlers');
        
        if(array_key_exists(get_class($exception), $handlers)){
            
            $exceptionName = get_class($exception);
            $thatHandler = Config::getObject('core.handlers.'.$exceptionName);
            
            if($thatHandler instanceof ExceptionHandlerInterface){
                $thatHandler->handleException($exception);
            } else {
                throw new SmvcUsageException("Обработчик [$exceptionName] должен реализовывать интерфейс ItForFree\SimpleMVC\Interfaces\ExceptionHandlerInterface.");
            }
            
        } else {
            throw $exception;
        }
    }
}
