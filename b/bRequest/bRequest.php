<?php
defined('_BLIB') or die;

class bRequest extends bBlib{	
	
    private static $_instance = null;
    private $_request = array();
    private $_tunnel = array();
    
    
    // Overload object factory for Singleton
    static public function create() {
        if (self::$_instance === null)self::$_instance = parent::create(func_get_args());
        return self::$_instance;
    }
   
    // 0_0 work on security 
    protected function input(){
        $this->_request = (array)json_decode(file_get_contents("php://input"),true)+(array)$_POST +(array)$_GET;
		
        if(isset($this->_request['_tunnel'])){
            $this->_tunnel = (array)$this->_request['_tunnel'];
            unset($this->_request['_tunnel']);
        }
        
    }
    
	public function output(){
        self::$_instance->_parent = null;
        return self::$_instance;
	}

    
    public function get($name){
        return (isset($this->_request[$name])?$this->_request[$name]:null);        
    }
    
    
    public static function _getTunnel($data, $caller){
        if(!($caller instanceof bBlib)){throw new Exception('Inherited methods need have bBlib caller.');}
		$block = get_class($caller);
		return $caller->getInstance('bRequest')->getTunnel($block);
    }
    
    
    protected function getTunnel($name){
        return (isset($this->_tunnel[$name])?$this->_tunnel[$name]:null);
	}
 
}

