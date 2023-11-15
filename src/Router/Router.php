<?php
namespace ItForFree\SimpleMVC\Router;

use ItForFree\SimpleMVC\Exceptions\SmvcRoutingException;
use ItForFree\SimpleMVC\Exceptions\SmvcAccessException;

/**
 * Класс-маршрутизатор, его задача по переданной строке,
 * определить какой контроллер и какое действие надо вызывать.
 */

abstract class Router
{
    
    public string $baseControllersNamespace = '\\application\\controllers\\';

   /**
    * Имя контроллера, которое надо указывать, если иное не найдено
    * @var string 
    */
   protected static string $defaultControllerName = 'Homepage';
   
   
   
   /**
    * Вернёт объект юзера 
    * 
    * @todo Оставлено для обратной совместимости, уходим от паттерна "одиночка",
    * в дальнейшем лучше перейти на использование обычного конструктора   
    */
    public static function get(): Router
    {
        return new static();
    }
    
    /** 
     * Скрываем конструктор для того чтобы класс нельзя было создать в обход get 
     */
    protected function __construct() {}
    
    /**
     * Вызовет действие контроллера, разобрав переданный маршрут
     * 
     * @param srting $route маршрут: Любая строка (подразумевается, что это url или фрагмент),
     *	    по которой можно определить вызываемый контроллер (класс) и его действие (метод)
     * @throws SmvcRoutingException
     */
    public function callControllerAction(string $route, mixed $data=null): object
    {
        $controllerName = $this->getControllerClassName($route);
        
        $controllerFile = $this->getControllerFileName($controllerName);
        if(!file_exists($controllerFile)) {
            throw new SmvcRoutingException("Файл контроллера [$controllerFile] не найден.");
        } else {
            if (!class_exists($controllerName)) {
                throw new SmvcRoutingException("Контроллер [$controllerName] не найден.");
            } 
        } 
        $controller = new $controllerName();
        $actionName = $this->getControllerActionName($route);
 
        if ($controller->isEnabled($actionName)) {
            $this->runControllerAction($actionName, $controllerName, $controller, $data);
        } else {
            throw  new SmvcAccessException("Доступ к маршруту $route запрещен.");
        }
        
        return $this;
    }
    
    /**
     * Сформаирует имя класса контроллера, на основании переданного маршрута
     * 
     * @param string $route маршрут, запрошенный пользотелем
     */
    public function getControllerClassName(string $route): string
    {
        $result = self::$defaultControllerName;
                
        $urlFragments = explode('/', $route);
        
        if (!empty($urlFragments[0])) {
            
            $result = "";
            
            $classNameIndex = count($urlFragments)-2;
            $className = $urlFragments[$classNameIndex];
            $firstletterToUp = ucwords($className); // поднимаем первую букву в имени класса
            if (count($urlFragments) > 2) {  // следовательно присутствует доп подпространство внутри кcontrollers
                $i = 0;
                while($i < $classNameIndex) {
                    $result .= $urlFragments[$i] . "\\"; //прибавляем подпространство к имени класса
                    $i++;
                }
            }
            $result .= $firstletterToUp;
//            \DebugPrinter::debug($result, 'результат после сложения неймспейса и имени контроллера');
        } 
        return $this->baseControllersNamespace . $result. "Controller";
    }
    
    /**
     * Формирует полное имя метода контроллера по  переданному маршруту
     * 
     * @param  string $route маршрут
     */
    public function getControllerActionName(string $route): string
    {
        $result =  'index';
         
        $urlFragments = explode('/', $route);
        $n = count($urlFragments);
        if (!empty($urlFragments[$n-1])) {
            $result = $urlFragments[$n-1];
        } 
         
         return $result;
    }
    
   /**
     * Формирует имя метода контроллера по GET-параметру
     * @param string $action -- строка GET-параметр
     */
    public function getControllerMethodName(string $action): string
    {
        return $action . 'Action';
    }
    
    /**
     * Возвращает путь до файла контроллера относительно корневой дирректории
     */
    protected function getControllerFileName(string $controllerName): string
    {
        global $projectRoot;
        $urlFragments = explode('\\', $controllerName);
        $res = implode('/', $urlFragments) . '.php';
        return $projectRoot ?? ($_SERVER['DOCUMENT_ROOT'] . '/..') . $res;
    }
    
    /**
     * Выполнить действие контроллера
     */
    public function runControllerAction(string $actionName, string $controllerName,
            object $controller, mixed $data = null): void {
        $methodName =  $this->getControllerMethodName($actionName);
            
        if (!method_exists($controller, $methodName)) {
            throw new SmvcRoutingException("Метод контроллера ([$controllerName])"
		    . " [$methodName] для данного действия [$actionName] не найден.");
        }

        if($data !== null) {
            $controller->$methodName($data); // вызываем действие контроллера
        } else {
            $controller->$methodName();
        }
    }

    /**
     * Получаем URL
     */
    abstract public static function getRoute(): string;
}
