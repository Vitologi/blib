<?php
defined('_BLIB') or die;

/**
 * Class bConfig__local - strategy for store configuration in block's file (also it is default strategy)
 */
class bConfig__local extends bBlib{

	/** @var bSystem $_system */
	protected $_system = null;

	/** @var mixed[]	- local config storage */
	private $_config = array();

	protected function input(){
		$this->_system = $this->getInstance('system', 'bSystem');
	}

	public function output(){
		return $this;
	}

	/**
	 * Get config from block`s file named like bBlock__bConfig.php
	 *
	 * @param string $selector 	- config selector
	 * @return mixed[] 			- local configs
	 * @throws Exception
	 */
	public function getConfig($selector = ''){

		/** @var string $name - block`s name */
		$name = (strpos($selector,'.'))?strstr($selector, '.', true):$selector;

		if(!array_key_exists($name,$this->_config)){

			$this->_config[$name]= array();
			$file = bBlib::path($name.'__bConfig','php');
			if(file_exists($file)){
				$strConfig = stripcslashes(require_once($file));
				$this->_config[$name] = json_decode($strConfig, true);
			}

		}

		return $this->_system->navigate($this->_config, $selector);
	}

	/**
	 * Set config to block`s file named like bBlock__bConfig.php
	 *
	 * @param string $selector	- config selector
	 * @param mixed $value		- config value
	 * @throws Exception
	 * @void					- store configuration in block's file
	 */
	public function setConfig($selector = '', $value = null){

		/** @var string $name - block`s name */
		$name = (strpos($selector,'.'))?strstr($selector, '.', true):$selector;

		if(!array_key_exists($name,$this->_config)){
			$this->_config[$name] = array();
		}

		// Extend local configuration
		$this->_config = $this->_system->navigate($this->_config, $selector, $value);

		// Convert it to string
		$config = json_encode($this->_config[$name], 256);

		// Check folder
		$path = bBlib::path($name.'__bConfig');
		if(!is_dir($path))mkdir($path);

		// Save file
		$file = bBlib::path($name.'__bConfig','php');
		file_put_contents($file,"<?php defined('_BLIB') or die(); return '".addslashes($config)."';");
	}

}