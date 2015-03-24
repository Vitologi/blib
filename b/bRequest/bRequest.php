<?php
defined('_BLIB') or die;

/**
 * Class bRequest
 */
class bRequest extends bBlib{

    protected $_traits    = array('bRewrite');

    /** @var null|static $_instance - Singleton instance */
    private static $_instance = null;

    /** @var array $_request    - request data */
    private $_request   = array();

    /** @var array $_tunnel     - tunnel data */
    private $_tunnel    = array();

    /**
     * Overload object factory for Singleton
     *
     * @return bRequest|null|static
     */
    static public function create() {
        if (self::$_instance === null)self::$_instance = parent::create(func_get_args());
        return self::$_instance;
    }
   
    /**
     * Grab request data and tunnel (data send direct to block)
     */
    protected function input(){

        /** @var bRewrite $bRewrite - rewrite instance */
        $bRewrite = $this->getInstance('bRewrite');

        $rewriteData = $bRewrite->get();

        $this->_request = (array)json_decode(file_get_contents("php://input"),true)+(array)$_POST +(array)$_GET+(array)$rewriteData;



        if(isset($this->_request['_tunnel'])){
            $this->_tunnel = (array)$this->_request['_tunnel'];
            unset($this->_request['_tunnel']);
        }
        
    }

    /**
     * Return instance without parent
     *
     * @return bRequest|null
     */
    public function output(){
        self::$_instance->_parent = null;
        return self::$_instance;
	}


    /**
     * Get request variable
     *
     * @param string $name  - property name
     * @return null
     */
    public function get($name = ''){
        return (isset($this->_request[$name])?$this->_request[$name]:null);        
    }

    /**
     * Set request variable
     *
     * @param string $name  - property name
     * @param null $value   - property value
     * @return null
     */
    public function set($name = '', $value = null){
        return $this->_request[$name] = $value;
    }


    /**
     * Get tunnel data from child block
     *
     * @param $caller
     * @return mixed
     * @throws Exception
     */
    public static function _getTunnel(bBlib $caller){
        if(!($caller instanceof bBlib)){throw new Exception('Inherited methods need have bBlib caller.');}
		$block = get_class($caller);

        /** @var bRequest $bRequest - request block's instance */
        $bRequest = $caller->getInstance('bRequest');

		return $bRequest->getTunnel($block);
    }


    /**
     * Protected method for get tunnel data
     * @param string $name  - block's name
     * @return mixed        - some data designed for block
     */
    public function getTunnel($name = ''){
        return (isset($this->_tunnel[$name])?$this->_tunnel[$name]:array());
	}
 
}

