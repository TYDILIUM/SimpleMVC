<?php
namespace ItForFree\SimpleMVC;

class DebugPrinter
{
    /**
     * Вывод строки/массива/объекта на экран в удобном виде с целью отладки
     */
    static public function debug(mixed $obj, string $comment = 'Тест'): void
    {
        echo "<pre> $comment: ";
        print_r($obj);
        echo "</pre>";
    }
}
    
