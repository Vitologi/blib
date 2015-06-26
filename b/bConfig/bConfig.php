<?php
defined('_BLIB') or die;

/**
 * Class bConfig - interface for store some configuration for blocks.
 * Included patterns:
 * 		singleton	- one configuration controller
 */
class bConfig extends bBlib{

	/** @var null|static $_instance - Singleton instance */
	private static $_instance = null;


	/**
	 * Overload object factory for Singleton
	 *
	 * @return bConfig|null|static
     */
	static public function create() {
		if (self::$_instance === null)self::$_instance = parent::create(func_get_args());
		return self::$_instance;
	}

    protected function input(){
        $this->setInstance('model', 'bConfig__model');
    }

	public function output(){
        return $this;
    }

	/**
	 * Get full configuration data from all included strategy
	 *
	 * @param string $selector	- config selector
	 * @return null|mixed		- configuration data
     */
	public function getConfig($selector =''){
        return $this->getInstance('model')->getConfig($selector);
	}

	/**
	 * Set/update configuration for block
	 *
	 * @param string $selector	- configuration selector
	 * @param mixed $value		- configuration
	 * @return $this			- for chaining
     */
	public function setConfig($selector = '', $value = array()){
        return $this->getInstance('model')->setConfig($selector, $value);
	}

    /**
     * Create default configuration for block (use .json files)
     *
     * @param $block
     * @throws Exception
     */
    public function setDefault($block){
        return $this->getInstance('model')->setDefault($block);
    }
}