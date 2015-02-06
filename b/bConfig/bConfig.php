<?php
defined('_BLIB') or die;

class bConfig extends bBlib{

	private static $_instance        = null;					// Singleton instance
	private        $_config          = array();					// All configuration stack
	private        $_strategy        = array('bConfig__local');	// Used strategy get/set config
	private        $_defaultStrategy = 'bConfig__local';		// Default strategy
	protected      $_traits          = array('bSystem', 'bConfig__local'/** some other element-components */);


	// Overload object factory for Singleton
	static public function create() {
		if (self::$_instance === null)self::$_instance = parent::create(func_get_args());
		return self::$_instance;
	}

	public function input(){
		$config = $this->getConfig(__CLASS__);
		$components = $config["strategy"];

		foreach($components as $key => $component){
			$this->setTrait($component);
			$this->_strategy[] = $component;
		}

	}

	public function output(){
		return $this;
	}


	/**
	 * Get full configuration data from all included strategy
	 *
	 * @param string $block	- from what block
	 * @return null|mixed	- configuration data
     */
	private function getConfig($block =''){

		if(array_key_exists($block, $this->_config))return $this->_config[$block];

		$config = array();

		foreach($this->_strategy as $i => $strategy){
			$config = $config + (array)$this->getInstance($strategy)->getConfig($block);
		}

		return $this->_config[$block] = $config;
	}

	/**
	 * Set default strategy
	 *
	 * @param null|string $strategyName		- what strategy use for default get/set configs
	 * @return $this						- for chaining
     */
	private function setStrategy($strategyName = null){
		if(!is_string($strategyName) || !in_array($strategyName, $this->_strategy))return $this;
		$this->_defaultStrategy = $strategyName;
		return $this;
	}

	/**
	 * Set/update configuration for block
	 *
	 * @param array $newConfig	- configurations
	 * @param string $block		- for what block
	 * @return $this			- for chaining
     */
	private function setConfig($newConfig = array(), $block = ''){
		$strategy = $this->getInstance($this->_defaultStrategy);
		$config = $strategy->getConfig($block);
		$config = array_replace($config, (array)$newConfig);
		$strategy->setConfig($block, $config);
		return $this;
	}

	/**
	 * Get configuration from child block
	 *
	 * @param bBlib $caller		- block-initiator
	 * @return mixed			- configuration
     */
	public static function _getConfig(bBlib $caller){
		return $caller->getInstance(__CLASS__)->getConfig(get_class($caller));
	}


	/**
	 * Set configuration from child block
	 *
	 * @param string $value		- config value
	 * @param bBlib $caller		- block-initiator
	 * @return void|bool		- set/update configuration and operation result
     */
	public static function _setConfig($value = array(), bBlib $caller){
		return $caller->getInstance(__CLASS__)->setConfig($value, get_class($caller));
	}
	
	
}