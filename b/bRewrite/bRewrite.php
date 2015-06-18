<?php
defined('_BLIB') or die;

class bRewrite extends bBlib{

    /** @var bConfig $_config */
    protected $_config = null;
    /** @var bRewrite__bDataMapper $_db */
    protected $_db = null;

    /** @var null|static $_instance - Singleton instance */
    private static $_instance = null;

    /** @var null|array  $_url - url data */
    private   $_url       = null;

    /** @var bool  $_isDisable - disable flag */
    private   $_isDisable = false;

    /**
     * Overload object factory for Singleton
     *
     * @return bRewrite|null|static
     */
    static public function create() {
        if (self::$_instance === null)self::$_instance = parent::create(func_get_args());
        return self::$_instance;
    }

	protected function input(){
        $this->_config    = $this->getInstance('config', 'bConfig');
        $this->_db        = $this->getInstance('db', 'bRewrite__bDataMapper');
        $this->_isDisable = $this->_config->getConfig('bRewrite.isDisable');
        $this->_url       = parse_url($_SERVER['REQUEST_URI']);
	}

	
	public function output(){
        return $this;
	}

    public function get(){

        if($this->_isDisable)return array();

        $rewrite = $this->_db->getRewrite($this->_url['path']);
        return $rewrite->bindex_id?array('pageNo'=>$rewrite->bindex_id):array();
    }
}