<?php
defined('_BLIB') or die;

class bConfig__database extends bBlib{

	protected $_traits  = array('bSystem','bDataMapper');

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
	 * @internal param string $block - block`s name
	 */
	public function getConfig($selector = ''){

		if($temp = $this->_navigate($this->_config, $selector))return $temp;

		/**
		 * @var bConfig__database__bDataMapper	- config data mapper
		 */
		$dataMapper = $this->_getDataMapper();

		$path = explode('.', $selector);
		$curentPath ='';
		for($i=0; $i<count($path); $i++){
			$curentPath .= $path[$i];

			if(!$this->_navigate($this->_config, $curentPath)) {
				$config = $dataMapper->mergeItem($dataMapper->getItem($curentPath));
				$this->_config = $this->_navigate($this->_config, $curentPath, $config->value);

			}

			$curentPath .= '.';
		}

		return $this->_navigate($this->_config, $selector);
	}

	/**
	 * Set config to block`s file named like bBlock__bConfig.php
	 *
	 * @param string $selector		- config selector
	 * @param null $value - config value
	 */
	public function setConfig($selector = '', $value = null){

		/**
		 * @var bDataMapper__instance	- config data mapper
		 */
		$dataMapper = $this->_getDataMapper();
		$config = $dataMapper->getItem($selector);

		if(is_array($value) and is_array($config->value)){
			$config->value = array_replace_recursive($config->value,$value);
		}else{
			$config->value = $value;
		}

		$this->_config = $this->_navigate($this->_config, $selector, $config->value);

		$config->name = $selector;
		$dataMapper->save($config);
	}

}
