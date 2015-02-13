<?php
defined('_BLIB') or die;

class bConfig__local extends bBlib{

	protected      $_traits   = array('bSystem');

	/**
	 * @var array	- local config storage
     */
	private $_config = array();

	public function output(){
		return $this;
	}

	/**
	 * Get config from block`s file named like bBlock__bConfig.php
	 *
	 * @param string $selector		- config selector
	 * @return mixed[] - local configs
	 * @throws Exception
	 * @internal param string $name - block`s name
	 */
	public function getConfig($selector = ''){

		$name = (strpos($selector,'.'))?strstr($selector, '.', true):$selector;

		if(!array_key_exists($name,$this->_config)){

			$this->_config[$name]= array();
			$file = bBlib::path($name.'__bConfig','php');
			if(file_exists($file)){
				$strConfig = require_once($file);
				$this->_config[$name] = json_decode($strConfig, true);
			}

		}

		return $this->_navigate($this->_config, $selector);
	}

	/**
	 * Set config to block`s file named like bBlock__bConfig.php
	 *
	 * @param string $selector	- config selector
	 * @param null $value - config value
	 * @throws Exception
	 * @internal param string $name - block`s name
	 */
	public function setConfig($selector = '', $value = null){

		$name = (strpos($selector,'.'))?strstr($selector, '.', true):$selector;

		if(!array_key_exists($name,$this->_config)){
			$this->_config[$name] = array();
		}

		$this->_config = $this->_navigate($this->_config, $selector, $value);
		$config = json_encode($this->_config[$name], 256);
		$file = bBlib::path($name.'__bConfig','php');
		file_put_contents($file,"<?php defined('_BLIB') or die(); return '".$config."';");
	}

}
