<?php
defined('_BLIB') or die;

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
    
    function __call($method, $args){
        return call_user_func_array(array($this->_parent, $method), $args);
    } 
	
}