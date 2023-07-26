<?php

namespace ItForFree\SimpleMVC\mvc;
/**
 * Базовый класс для работы с конроллерами
 */
class Controller 
{
    use \ItForFree\SimpleMVC\traits\AccessControl;
    
    /**
     * @var \ItForFree\SimpleMVC\mvc\View Хранит экземпляр класса View (Представления)
     */
    public ?View $view = null;
    
    /**
     * @var string Имя (путь относительно базовой папки шаблонов,
     * определяемой в классе конфиге приложения) шаблона (для представлений)
     */
    public string $layoutPath = 'main.php';
    
    /**
     * Создаёт экземпляр класса View для работы с представлениями
     */
    public function __construct() {
        $this->view = new View($this->layoutPath);
    }
    
    /**
     * Редирект на указанный адрес 
     * (устанавливает заголовок location)
     * 
     * @todo Проверить не нужен ли exit после установления заголовка.
     */
    public function redirect(string $path): void { // 302 редирект
        header("Location: $path");
    }
}
