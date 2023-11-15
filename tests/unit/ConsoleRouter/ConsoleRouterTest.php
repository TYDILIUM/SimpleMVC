<?php

use ItForFree\SimpleMVC\Application;

require __DIR__ . '/support/ConsoleController.php';

class ConsoleRouterTest extends \Codeception\Test\Unit
{
    protected $tester;
    
    public function testRouteForConsoleTest()
    {
	$I = $this->tester;
	
        $config = require(codecept_data_dir() . 'console/console-config.php');
        $App = Application::get();
        $App->setConfiguration($config);
        $Router = $App->getConfigObject('core.router.class');
        $Router->baseControllersNamespace = '\\support\\';
        global $argv, $projectRoot;
        $projectRoot = __DIR__;
        $argv[1] = 'console';
        $routeOne = $Router::getRoute();
        $Router->callControllerAction($routeOne);
        $I->assertSame($Router->getControllerActionName($routeOne), 'index');
        $argv[1] = 'console:new';
        $routeTwo = $Router::getRoute();
        $Router->callControllerAction($routeTwo);
        $I->assertSame($Router->getControllerActionName($routeTwo), 'new');
    }
}