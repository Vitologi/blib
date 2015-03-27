<?php
/**
 * Created by PhpStorm.
 * User: morozov
 * Date: 26.03.2015
 * Time: 17:09
 */

class bSystem__objectaccess{

    /**
     * @var null|array $_tempArray - current active array
     */
    protected $_tempArray = null;
    /**
     * @var bool $_recursion - detect recursion for inner logic
     */
    protected $_recursion = false;
    /**
     * @var array $_array   - root array
     */
    protected $_array     = array();

    /**
     * Set root array
     *
     * @constructor
     * @param array $array
     */
    final public function __construct(Array $array = array()){
        $this->_array = $array;
    }

    /**
     * Get current array
     *
     * @return array|null
     */
    final public function get(){
        if($this->_tempArray === null)$this->_tempArray = &$this->_array;
        $temp = &$this->_tempArray;
        $this->_tempArray = &$this->_array;
        return $temp;
    }

    /**
     * Overload getter
     *
     * @param mixed $index  - some key
     * @return $this|array|null - value or this for chaining
     */
    final public function __get($index){

        if($this->_tempArray === null)$this->_tempArray = &$this->_array;

        if(isset($this->_tempArray[$index])){
            if(is_array($this->_tempArray[$index])){
                $this->_tempArray = &$this->_tempArray[$index];
                return $this;
            }else{
                $temp = &$this->_tempArray[$index];
                $this->_tempArray = &$this->_array;
                return $temp;
            }
        }else{

            if($this->_recursion){
                $this->_recursion = false;
                $this->_tempArray = &$this->_array;
                return $this->_tempArray;
            }else{
                $this->_recursion = true;
                $this->_tempArray = &$this->_array;
                return $this;
            }

        }
    }

    /**
     * Overload setter
     *
     * @param mixed $index  - key
     * @param mixed $value  - value
     * @return null
     */
    final public function __set($index, $value){
        if($index === '_tempArray')return $this->_tempArray = null;
        if($this->_tempArray === null)$this->_tempArray = &$this->_array;

        $this->_tempArray[$index] = $value;
        $this->_tempArray = &$this->_array;
    }

}