<?php
defined('_BLIB') or die;

class bConverter__instance extends bBlib{

    /** @var static|bConverter $_parent - decor block */
    protected $_parent = null;


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



    final public function getFormat(){
        $data = $this->_parent->getData();

        switch(true) {

            case is_string($data):
                $format = 'string';
                break;

            case is_bool($data):
                $format = 'boolean';
                break;

            case is_object($data):
                $format = 'object';
                break;

            case is_array($data):
                $format = 'array';
                break;

            case is_float($data):
            case is_int($data):
                $format = 'number';
                break;

            default:
                $format = null;
                break;

        }

        if($format !== null)$this->_parent->setFormat($format);


        return $this->_parent->getFormat();
    }


    final  public function convertTo($newFormat = null){
        $parent = $this->_parent;
        $format = $parent->getFormat();

        if($format === null)$this->getFormat();
        $oldFormat = $parent->getFormat();

        if($oldFormat !== $newFormat){
            $converterMethod = $oldFormat.'TO'.$newFormat;

            foreach($this->_instances as $converter){

                if (method_exists($converter, $converterMethod)){
                    $this->_data = $converter->$converterMethod($this->_data);
                    $this->_format = $newFormat;
                    break;
                };

            }


        }

        return $parent->getData();
    }

    public function output(){
        return ($this->_parent?$this:$this->view());
    }

    public function view(){
        $parent = $this->_parent;
        $format = $parent->getFormat();
        $data   = $parent->getData();

        if(in_array($format,array('string','boolean','object','array','number'))){
            var_dump($data);
            return null;
        }else{
            return $parent->view();
        }

    }
}

