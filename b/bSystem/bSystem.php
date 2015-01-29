<?php
defined('_BLIB') or die;

class bSystem extends bBlib{
    
    // Set default value for block
    public static function _default($data, $parent){
        list($key, $value, $default) = $data;
        if(is_string($key)){
            $parent->$key = ($value?$value:$default);
        }
        return $parent;
        
    }
    
}