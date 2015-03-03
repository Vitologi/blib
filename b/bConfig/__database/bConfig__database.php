<?php
defined('_BLIB') or die;

/**
 * Class bConfig__database 	- strategy for store configuration in database
 * Included patterns:
 * 		Data Mapper - interface for interaction to data base
 */
class bConfig__database extends bBlib{

	protected $_traits  = array('bSystem','bDataMapper');

	/** @var mixed[]	- local config storage */
	private $_config = array();

	public function output(){
		return $this;
	}

	/**
	 * Get config from block`s file named like bBlock__bConfig.php
	 *
	 * @param string $selector	- config selector
	 * @return mixed[]			- local configs
	 */
	public function getConfig($selector = ''){
        $path = explode('.', $selector);

        // Protect from loop
        if($path[0]==='bDatabase') {
            /** @var bConfig $bConfig   - parent block */
            $bConfig = $this->_parent;

            /** @var bConfig__local $bConfig__local - default config strategy */
            $bConfig__local = $bConfig->getInstance('bConfig__local');

            return $bConfig__local->getConfig($selector);
        }


		// Return stored configuration if it already exists
		if($temp = $this->_navigate($this->_config, $selector))return $temp;

		/** @var bConfig__database__bDataMapper $dataMapper	- config data mapper */
		$dataMapper = $this->_getDataMapper();


		/** Recursive(string based) grab configuration from database
		 * For example:
		 * bBlock.item.subItem
		 *  - means that cycle get configuration for
		 * bBlock , bBlock.item , bBlock.item.subItem
		 *  - store it in local configuration array $_config
		 *  - and return bBlock.item.subItem config
		 */
		$currentPath ='';
		for($i=0; $i<count($path); $i++){
			$currentPath .= $path[$i];

			if(!$this->_navigate($this->_config, $currentPath)) {

				// Merge configurations with parents lines
				$config = $dataMapper->mergeItem($dataMapper->getItem($currentPath));

				// Concat with local config
				$this->_config = $this->_navigate($this->_config, $currentPath, $config->value);

			}

			$currentPath .= '.';
		}

		return $this->_navigate($this->_config, $selector);
	}

	/**
	 * Save configurations to database
	 *
	 * @param string $selector	- config selector
	 * @param mixed $value 		- config value
	 * @void 					- save configurations to database
	 */
	public function setConfig($selector = '', $value = null){

        // Protect from loop
        $path = explode('.', $selector);
        if($path[0]==='bDatabase') {
            /** @var bConfig $bConfig   - parent block */
            $bConfig = $this->_parent;

            /** @var bConfig__local $bConfig__local - default config strategy */
            $bConfig__local = $bConfig->getInstance('bConfig__local');

            return $bConfig__local->setConfig($selector, $value);
        }

		/** @var bDataMapper__instance $dataMapper	- config data mapper */
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