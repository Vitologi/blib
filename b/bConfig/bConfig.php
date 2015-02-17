<?php
defined('_BLIB') or die;

/**
 * Class bConfig - interface for store some configuration for blocks.
 * Included patterns:
 * 		strategy - many types of config storage
 */
class bConfig extends bBlib{

	/**
	 * @var null|static - Singleton instance
     */
	private static $_instance = null;

	private   $_config   = array();                            // All configuration stack
	private   $_default  = 'bConfig__local';                    // Default get/set strategy
	private   $_strategy = array('bConfig__local');            // Used strategy get/set config
	protected $_traits   = array('bSystem', 'bConfig__local'); // Some other strategy instance


	/**
	 * Overload object factory for Singleton
	 *
	 * @return bConfig|null|static
     */
	static public function create() {
		if (self::$_instance === null)self::$_instance = parent::create(func_get_args());
		return self::$_instance;
	}


	/**
	 * Grab config by self, set config store strategy and point default config strategy
     */
	public function input(){
		$config = $this->getConfig(__CLASS__);
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

				/** @var bConfig__local $strategyObject - strategy instance */
				$strategyObject = $this->getInstance($strategy);

				$temp = $strategyObject->getConfig($selector);

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

		/** @var bConfig__local $strategy - Get default strategy */
		$strategy = $this->getInstance($this->_default);

		// Extend inner configuration storage
		$this->_config = $this->_navigate($this->_config, $selector, $value);

		// forwards request to the strategy
		$strategy->setConfig($selector, $value);

		return $this;
	}

	/**
	 * Get configuration from child block
	 *
	 * @return mixed		- configuration
	 * @throws Exception
	 */
	public static function _getConfig(){
		if(func_num_args()===2){

			/**
			 * @var string $selector 	- config selector
			 * @var bBlib $caller		- block-initiator
			 */
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
	 * @return bool|void	- set/update configuration and operation result
	 * @throws Exception
	 */
	public static function _setConfig(){
		if(func_num_args()===3){

			/**
			 * @var string $selector 	- config selector
			 * @var mixed $value 		- config value
			 * @var bBlib $caller		- block-initiator
			 */
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