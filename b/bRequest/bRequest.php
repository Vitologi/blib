<?php
defined('_BLIB') or die;

/**
 * Class bRequest
 */
class bRequest extends bBlib{

    /** @var null|static $_instance - Singleton instance */
    private static $_instance = null;

    /** @var array $_request    - request data */
    private $_request   = array();

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

        /** @var bRewrite $_rewrite - rewrite instance */
        $_rewrite = $this->getInstance('rewrite', 'bRewrite');

        $rewriteData = $_rewrite->get();

        $this->_request = (array)json_decode(file_get_contents("php://input"),true)+(array)$_POST +(array)$_GET+(array)$rewriteData;

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
     * @param null $default - default property value
     * @return null
     */
    public function get($name = '', $default = null){
        return (isset($this->_request[$name])?$this->_request[$name]:$default);
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

}

