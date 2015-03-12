<?php
defined('_BLIB') or die;

class bConverter extends bBlib{

    /** @var null|static $_instance - Singleton instance */
    private static $_instance = null;

    protected $_traits     = array('bSystem');
    protected $_converters = array('bConverter__instance');
    private   $_data       = null;
    private   $_format     = null;

    /**
     * Overload object factory for Singleton
     *
     * @return bConverter|null|static
     */
    static public function create() {
        if (self::$_instance === null)parent::create(func_get_args());
        return self::$_instance;
    }

    protected function input($data = null){
        $this->_data = $data;
        $decorConverter = $this;

        foreach($this->_converters as $converter){

            /** @var bConverter__instance $converter - decor Class */
            $decorConverter = $converter::create()->setParent($decorConverter);

        }

        self::$_instance = $decorConverter;
    }


    final public function getData(){
        return $this->_data;
    }

    final public function setData($data = null){
        $this->_data = $data;
        return $this;
    }


    final public function setFormat($format = null){
        if(is_string($format)){
            $this->_format = $format;
        }

        return $this;
    }

    final public function getFormat(){
        return $this->_format;
    }


    final  public function convertTo($newFormat = null){
        if($this->_format === null)$this->getFormat();
        return $this->_data;
    }

    public function output(){
        return ($this->_parent?$this:$this->view());
    }

    public function view(){
        return null;
    }
}

