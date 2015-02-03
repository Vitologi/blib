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
		$components = $this->getConfig("strategy",__CLASS__);
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
	 * @param string $key	- config key
	 * @param string $block	- from what block
	 * @return null|mixed	- configuration data
     */
	private function getConfig($key = '', $block =''){

		if(!array_key_exists($key, $this->_config)){

			$config = array();

			foreach($this->_strategy as $i => $strategy){
				$config = $config + (array)$this->getInstance($strategy)->getConfig($block);
			}

			$this->_config[$block] = $config;
		}

		return isset($this->_config[$block][$key])?$this->_config[$block][$key]:null;
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
	 * @param string $name		- config key
	 * @param null|mixed $value	- config value
	 * @param string $block		- for what block
	 * @return $this			- for chaining
     */
	private function setConfig($name = '', $value = null, $block = ''){
		$strategy = $this->getInstance($this->_defaultStrategy);
		$config = $strategy->getConfig($block);
		$config[$name] = $value;
		$strategy->setConfig($block, $config);
		return $this;
	}

	/**
	 * Get configuration from child block
	 *
	 * @param string $key		- config name
	 * @param bBlib $caller		- block-initiator
	 * @return mixed			- configuration
     */
	public static function _getConfig($key = '', bBlib $caller){
		return $caller->getInstance(__CLASS__)->getConfig($key, get_class($caller));
	}


	/**
	 * Set configuration from child block
	 *
	 * @param string $key		- config name
	 * @param string $value		- config value
	 * @param bBlib $caller		- block-initiator
	 * @return void|bool		- set/update configuration and operation result
     */
	public static function _setConfig($key = '', $value = '', bBlib $caller){
		return $caller->getInstance(__CLASS__)->setConfig($key, $value, get_class($caller));
	}
	
	
}