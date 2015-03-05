<?php
defined('_BLIB') or die;

/**
 * Class bDecorator__instance   - basic template for create concrete decorators
 * Redirect all request to protected property $_parent, what contain decorated block
 */
class bDecorator__instance extends bBlib{
	
	function __get($key){
        return $this->_parent->$key;
    }
    
    function __set($key, $value){
        return $this->_parent->$key = $value;
    }
    
    function __isset($key){
        return isset($this->_parent->$key);
    }
    
    function __call($method = '', $args = array()){
        return call_user_func_array(array($this->_parent, $method), $args);
    } 
	
}