<?php

namespace application\models\containerElementsCaching;

use ItForFree\SimpleMVC\MVC\Model;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of OneClassCache
 *
 * @author qwe
 */
class OneClassCache extends Model{
    
    public static $countCreateObject = 0;
    
    public function __construct() {
        
        static::$countCreateObject++;
        
    }
    
    public static function get() {
        
    }
    
}
