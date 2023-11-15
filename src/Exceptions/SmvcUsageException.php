<?php
namespace ItForFree\SimpleMVC\Exceptions;

/**
 * Класс исключений, 
 * предупреждающих клиенский код о неправильном использовании SimpleMVC
 */
class SmvcUsageException extends SmvcException
{
    // Переопределим исключение так, что параметр message станет обязательным
    public function __construct(string $message, int $code = 0, \Exception $previous = null) {
        // некоторый код 
    
        // убедитесь, что все передаваемые параметры верны
        parent::__construct($message, $code, $previous);
    }

    // Переопределим строковое представление объекта.
    public function __toString() {
        return __CLASS__ . ":[{$this->code}]: {$this->message}  Некорректное использование SimpleMVC \n";
    }

}