<?php
namespace ItForFree\SimpleMVC\exceptions;

/**
 * Исключения маршрутизации
 */
class SmvcRoutingException extends SmvcUsageException
{
    // Переопределим исключение так, что параметр message станет обязательным
    public function __construct(string $message, int $code = 404, \Exception $previous = null) {
        // некоторый код 

        // убедитесь, что все передаваемые параметры верны
        parent::__construct($message, $code, $previous);
    }

    // Переопределим строковое представление объекта.
    public function __toString() {
        return __CLASS__ . ":[{$this->code}]: {$this->message}  // Ошибка маршрутизации SimpleMVC \n";
    }

}