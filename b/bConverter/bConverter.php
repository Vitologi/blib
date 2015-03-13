<?php
defined('_BLIB') or die;

/**
 * Class bConverter     - for change type of data and display it correctly
 *  Included patterns:
 * 	  singleton	    - one converter factory
 * 	  decorator 	- many rules of format change
 */
class bConverter extends bBlib{

    /** @var null|static $_instance - Singleton instance */
    private static $_instance   = null;

    /** @var null|bConverter__instance  $_converter */
    private        $_converter  = null;
    /**
     * @var array $_converters  - list of convert rules
     */
    protected      $_converters = array('bConverter__default','bConverter__json');

    protected      $_traits     = array('bSystem');

    /**
     * Overload object factory for Singleton
     *
     * @return bConverter|null|static
     */
    static public function create() {
        if (self::$_instance === null)self::$_instance = parent::create(func_get_args());
        return self::$_instance;
    }

    /**
     *  Create decorated converter
     */
    protected function input(){

        /** @var  bConverter__instance $decorConverter */
        $decorConverter = bConverter__instance::create()->setParent($this);

        foreach($this->_converters as $converter){

            /** @var bConverter__instance $converter - decor Class */
            $decorConverter = $converter::create()->setParent($this)->decor($decorConverter);

        }

        $this->_converter = $decorConverter;
    }

    /**
     * Return decorated converter instead self
     *
     * @return bConverter__instance|null    - converter
     */
    public function output(){
        return $this->_converter;
    }
}