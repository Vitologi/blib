<?php
defined('_BLIB') or die;

class bSystem extends bBlib{

    // Set default value for block
    public static function _default($key, $value, $default, $parent){

        if(is_string($key)){
            $parent->$key = ($value?$value:$default);
        }
        return $parent;
        
    }

    // Navigate in array by dotted notation
    public static function _navigate(){
        if(func_num_args()===4) {
            list($obj, $selector, $value, $parent) = func_get_args();
            $isValue = true;
        }else if(func_num_args()===3){
            list($obj, $selector, $parent) = func_get_args();
            $isValue = false;
        }else{
            throw new Exception('Wrong function arguments');
        }

        if(!is_array($obj) || !is_string($selector)) return null;

        $temp = &$obj;
        $needle = explode('.',$selector);
        $i=0;
        $len=count($needle);

        while($i<$len){
            if(!is_array($temp) || $isValue and $i == $len-1) break;

            if(!isset($temp[$needle[$i]])){
                if(!$isValue)return null;
                $temp[$needle[$i]] = array();
            }

            $temp = &$temp[$needle[$i]];
            $i++;
        };

        if(!$isValue) return $temp;
        $temp[$needle[$i]] = $value;

        return $obj;
    }

}