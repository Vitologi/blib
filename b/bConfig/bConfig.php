<?php
defined('_BLIB') or die;

class bConfig extends bBlib{

	private static $_instance = null;                               // Singleton instance
	private        $_config   = array();                            // All configuration stack
	private        $_default  = 'bConfig__local';            		// Default get/set strategy
	private        $_strategy = array('bConfig__local');            // Used strategy get/set config
	protected      $_traits   = array('bSystem', 'bConfig__local'); // Some other strategy instance


	// Overload object factory for Singleton
	static public function create() {
		if (self::$_instance === null)self::$_instance = parent::create(func_get_args());
		return self::$_instance;
	}

	public function input(){
		$config = $this->getConfig(__CLASS__);
		//var_dump($config);
		$components = isset($config["strategy"])?$config["strategy"]:array();
		if(isset($config["default"]))$this->setDefault($config["default"]);

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
	 * @param string $strategy 	- name strategy for get/set
	 * @void    				- set default strategy
	 * @return $this    		- for chaining
	 */
	public function setDefault($strategy ='bConfig__local'){
		$this->_default = $strategy;
		return $this;
	}

	/**
	 * Get full configuration data from all included strategy
	 *
	 * @param string $selector	- config selector
	 * @return null|mixed		- configuration data
     */
	public function getConfig($selector =''){

		if(!$this->_navigate($this->_config, $selector)){
			$config = null;

			foreach($this->_strategy as $i => $strategy){
				$temp = $this->getInstance($strategy)->getConfig($selector);

				if($temp === null)continue;

				if(is_array($temp) and is_array($config)){
					$config = array_replace_recursive($config,$temp);
				}else{
					$config = $temp;
				}

			}

			$this->_config = $this->_navigate($this->_config, $selector, $config);
		}

		return $this->_navigate($this->_config, $selector);
	}

	/**
	 * Set/update configuration for block
	 *
	 * @param string $selector	- configuration selector
	 * @param mixed $value		- configuration
	 * @return $this			- for chaining
     */
	public function setConfig($selector = '', $value = array()){
		$strategy = $this->getInstance($this->_default);
		$this->_config = $this->_navigate($this->_config, $selector, $value);
		$strategy->setConfig($selector, $value);
		return $this;
	}

	/**
	 * Get configuration from child block
	 *
	 * @param string|null $selector		- config selector
	 * @param bBlib $caller 		- block-initiator
	 * @return mixed 				- configuration
	 */
	public static function _getConfig(){
		if(func_num_args()===2){
			list($selector, $caller) = func_get_args();
			$selector = get_class($caller).".".$selector;
		}else if(func_num_args()===1){
			$caller = func_get_arg(0);
			$selector = get_class($caller);
		}else{
			throw new Exception('Not correct arguments given.');
		}

		if(!($caller instanceof bBlib))throw new Exception('Not correct arguments given.');

		return $caller->getInstance(__CLASS__)->getConfig($selector);
	}


	/**
	 * Set configuration from child block
	 *
	 * @param string $selector		- config selector
	 * @param mixed $value 			- config value
	 * @param bBlib $caller 		- block-initiator
	 * @return bool|void 			- set/update configuration and operation result
	 */
	public static function _setConfig(){
		if(func_num_args()===3){
			list($selector, $value, $caller) = func_get_args();
			$selector = get_class($caller).".".$selector;
		}else if(func_num_args()===2){
			list($value, $caller) = func_get_args();
			$selector = get_class($caller);
		}else{
			throw new Exception('Not correct arguments given.');
		}

		if(!($caller instanceof bBlib))throw new Exception('Not correct arguments given.');

		return $caller->getInstance(__CLASS__)->setConfig($selector, $value);
	}
	
	
}