<?php
namespace ItForFree\SimpleMVC\exceptions;

/**
 * Класс исключений, которые следует бросать, при нарушении доступа
 */
class SmvcAccessException extends SmvcException
{
    // Переопределим исключение так, что параметр message станет обязательным
    public function __construct(string $message, int $code = 403, Exception $previous = null) {
        // некоторый код 
    
        // убедитесь, что все передаваемые параметры верны
        parent::__construct($message, $code, $previous);
    }

    // Переопределим строковое представление объекта.
    public function __toString() {
        return __CLASS__ . ": [!] Ошибка доступа [{$this->code}]: {$this->message}\n";
    }

}