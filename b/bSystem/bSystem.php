<?php
defined('_BLIB') or die;

/**
 * Class bSystem    - library for many needed function
 */
class bSystem extends bBlib{

    public function output(){
        return $this;
    }

    /**
     * Navigate in array by dotted notation.
     * Can get concrete array value, or extend it and return modified array.
     * For example for $array = array('item'=>array('name'=>array('prefix'=>1111))):
     *
     *      is equal
     *      $this->navigate($array, 'item.name.prefix')
     *      $array['item']['name']['prefix']
     *
     *      is equal
     *      $this->navigate($array, 'item.name', array('postfix'=>222))
     *      $array['item']['name'] = array_replace_recursive($array['item']['name'], array('postfix'=>222))
     *
     * @return array|null   - handle value or modified array
     * @throws Exception
     */
    public static function navigate(){
        if(func_num_args()===3) {

            /**
             * @var array $obj          -
             * @var string $selector    -
             * @var mixed $value        -
             */
            list($obj, $selector, $value) = func_get_args();

            $isValue = true;
        }else if(func_num_args()===2){
            list($obj, $selector) = func_get_args();
            $isValue = false;
        }else{
            throw new Exception('Wrong function arguments');
        }

        if(!is_array($obj) || !is_string($selector)) return $obj;

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

    /**
     * Create interface for work with array like object
     * @param array $array              - some array
     * @return bSystem__objectaccess    - object
     */
    public static function objectAccess(Array $array = array()){
        return new bSystem__objectaccess($array);
    }
}