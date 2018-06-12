<?php
namespace ItForFree\SimpleMVC;

use ItForFree\rusphp\PHP\ArrayLib\DotNotation\Dot;
use ItForFree\SimpleMVC\exceptions\SmvcCoreException;
use ItForFree\SimpleMVC\Router;

/**
 * Класс-"точка входа" для работы фреймворка SimpleMVC
 * 
 * Реализует паттерн "Одиночка" @see http://fkn.ktu10.com/?q=node/5572
 */
class Application
{
    
    /**
     * Массив конфигурации приложенияъ
     * 
     * @var ItForFree\rusphp\PHP\ArrayLib\DotNotation\Dot
     */
    protected $config = null;
    
    
    /**
     * Cкрываем конструктор для того чтобы класс нельзя было создать в обход get() 
     */
    protected function __construct() {}
    
    /**
     * Метод для получения текущего объекта приложения
     * 
     * @staticvar type $instance
     * @return ItForFree\SimpleMVC\Applicaion объект приложения
     */
    public static function get()
    {
        static $instance = null; // статическая переменная
        if (null === $instance) { // проверка существования
            $instance = new static();
        }
        return $instance;
    }
    

    /**
     * Запускает приложение
     * 
     * @param array $config Конфигурационный масиив приложения
     */
    public function run() {
        
        if (!empty($this->config)) {
            
            
            $route = $this->getConfigObject('core.url.class')->getRoute();
            $Router = new Router($route);
            
        } else {
            throw new SmvcCoreException ('Не задан конфигурационный массив приложения!');
        }

        
        return $this;
    }
    
    
    public function setConfiguration($config)
    {
        $this->config = new Dot($config);
        return $this;
    }
    

    /**
     * Вернёт элемент из массива конфигурации приложения
     * 
     * @param string $inConfigArrayPath ключ в виде строки, разделёной точками -- путь в массиве
     * @return type
     */
    public static function getConfigElement($inConfigArrayPath)
    {
        if ($this->config) {
            throw new SmvcCoreException ('Не задан конфигурационный массив приложения!');
        }
        
        $configValue = Application::get()->config($inConfigArrayPath);
        return $configValue;
    }
    
    /**
     * Создаст и вернёт объект по его имени из массива
     * 
     * @param string $inConfigArrayPath ключ в виде строки, разделёной точками -- путь в массиве
     * @return type
     */
    public function getConfigObject($inConfigArrayPath)
    {
        $configValue = self::config($inConfigArrayPath);
        return (new $configValue);
    }
}
