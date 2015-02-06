<?php
defined('_BLIB') or die;

class bConfig__database extends bBlib{

	protected $_traits  = array('bSystem', 'bDataMapper');

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

			$dataMapper = $this->_getDataMapper();




			$this->_config[$name] = json_decode($strConfig, true);


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
	}

}
