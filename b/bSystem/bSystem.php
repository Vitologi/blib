<?php
defined('_BLIB') or die;

/**
 * Class bSystem    - library for many needed function
 */
class bSystem extends bBlib{


    /**
     * Set default value for block
     *
     * @param string $key   - property name
     * @param null $value   - property value
     * @param null $default - default value for property
     * @param bBlib $parent - block-initiator
     * @return bBlib        - for chaining
     */
    public static function _default($key = '', $value = null, $default = null, bBlib $parent){

        if(is_string($key)){
            $parent->$key = ($value?$value:$default);
        }
        return $parent;

    }

    /**
     * Navigate in array by dotted notation.
     * Can get concrete array value, or extend it and return modified array.
     * For example for $array = array('item'=>array('name'=>array('prefix'=>1111))):
     *
     *      is equal
     *      $this->_navigate($array, 'item.name.prefix')
     *      $array['item']['name']['prefix']
     *
     *      is equal
     *      $this->_navigate($array, 'item.name', array('postfix'=>222))
     *      $array['item']['name'] = array_replace_recursive($array['item']['name'], array('postfix'=>222))
     *
     * @return array|null   - handle value or modified array
     * @throws Exception
     */
    public static function _navigate(){
        if(func_num_args()===4) {

            /**
             * @var array $obj          -
             * @var string $selector    -
             * @var mixed $value        -
             * @var bBlib $parent       -
             */
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