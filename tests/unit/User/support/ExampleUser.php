<?php

namespace application\models\user;

class ExampleUser extends \ItForFree\SimpleMVC\User 
{
    public function __construct($session = null, $router = null)
    {
        parent::__construct($session, $router); 
    }
    
    protected function checkAuthData(string $login, string $pass): bool 
    {
    }
  
    protected function getRoleByUserName(string $userName): string
    {
    }
}
