<?php
defined('_BLIB') or die;

class bRewrite extends bBlib{

    /** @var null|static $_instance - Singleton instance */
    private static $_instance = null;

    protected $_traits    = array('bConfig', 'bRewrite__bDataMapper');

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

        /** @var bConfig $bConfig - config instance */
        $bConfig = $this->getInstance('bConfig');

        $this->_isDisable   = $bConfig->getConfig('bRewrite.isDisable');

        $this->_url         = parse_url($_SERVER['REQUEST_URI']);

	}

	
	public function output(){
        return $this;
	}

    public function get(){

        if($this->_isDisable)return array();

        /** @var bRewrite__bDataMapper $bDataMapper  - rewrite data mapper instance */
        $bDataMapper = $this->getInstance('bRewrite__bDataMapper');

        $rewrite = $bDataMapper->getRewrite($this->_url['path']);
        return $rewrite->bindex_id?array('pageNo'=>$rewrite->bindex_id):array();
    }
}