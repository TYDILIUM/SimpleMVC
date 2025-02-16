<?php
namespace ItForFree\SimpleMVC\Router;

/**
 * Класс-маршрутизатор, его задача по переданным аргументам в консоли,
 * определить какой контроллер и какое действие надо вызывать.
 */
class ConsoleRouter extends Router
{
    
    public static function getRoute(): string 
    {
	global $argv;
        return str_contains($argv[1], ':') ? str_replace(':', '/', $argv[1]) :
                $argv[1] . '/';
    }

    public function callControllerAction(string $route, mixed $data = null): object
    {
        $controller = $this->getController($route);
        $actionName = $this->getControllerActionName($route);
        $this->runControllerAction($actionName, $controller, $data);        
        return $this;
    }
}