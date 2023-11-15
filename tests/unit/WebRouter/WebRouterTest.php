<?php

use ItForFree\SimpleMVC\Application;

require __DIR__ . '/support/WebController.php';
require __DIR__ . '/support/ExampleUser.php';

class WebRouterTest extends \Codeception\Test\Unit
{
    protected $tester;
    
    public function testRouteForWebTest()
    {
	$I = $this->tester;
	
        $config = require(codecept_data_dir() . 'web/web-config.php');
        $App = Application::get();
        $App->setConfiguration($config);
        $Router = $App->getConfigObject('core.router.class');
        $Router->baseControllersNamespace = '\\support\\';        
        $_SERVER['DOCUMENT_ROOT'] = __DIR__ . '/support';
        $_GET['route'] = 'web/';
        $routeFirst = $Router::getRoute();
        $Router->callControllerAction($routeFirst);
        $I->assertSame($Router->getControllerActionName($routeFirst), 'index');
        $_GET['route'] = 'web/new';
        $routeSecond = $Router::getRoute();
        $Router->callControllerAction($routeSecond);
        $I->assertSame($Router->getControllerActionName($routeSecond), 'new');
    }
}