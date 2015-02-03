<?php
defined('_BLIB') or die;

class bConfig__local extends bBlib{

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
	 * @param string $name		- block`s name
	 * @return mixed[]			- local configs
	 * @throws Exception
     */
	public function getConfig($name = ''){

		if(!array_key_exists($name,$this->_config)){

			$this->_config[$name]= array();
			$file = bBlib::path($name.'__bConfig','php');
			if(file_exists($file)){
				$strConfig = require_once($file);
				$this->_config[$name] = json_decode($strConfig, true);
			}

		}

		return $this->_config[$name];
	}

	/**
	 * Set config to block`s file named like bBlock__bConfig.php
	 *
	 * @param string $name		- block`s name
	 * @param null $value		- config value
	 * @throws Exception
     */
	public function setConfig($block = '', $value = null){

		if(!array_key_exists($block,$this->_config)){
			$this->_config[$block]=array();
		}

		$this->_config[$block] = $value;
		$config = json_encode($this->_config[$block], 256);
		$file = bBlib::path($block.'__bConfig','php');
		file_put_contents($file,"<?php defined('_BLIB') or die(); return '".$config."';");
	}

}
