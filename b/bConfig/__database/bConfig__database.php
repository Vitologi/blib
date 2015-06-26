<?php
defined('_BLIB') or die;

/**
 * Class bConfig__database 	- strategy for store configuration in database
 * Included patterns:
 * 		Data Mapper - interface for interaction to data base
 */
class bConfig__database extends bBlib{

    /** @var bSystem $_system */
	protected $_system = null;
    /** @var bConfig__database__bDataMapper $_db */
    protected $_db = null;

	/** @var mixed[]	- local config storage */
	private $_config = array();

    protected function input(){
        $this->_system = $this->getInstance('system', 'bSystem');
        $this->_db = $this->getInstance('db', 'bConfig__database__bDataMapper');
    }

	public function output(){
		return $this;
	}


    /**
     * Get config item
     *
     * @param string $selector	- config selector
     * @return mixed[]			- local configs
     */
    public function getItem($selector = ''){
        $path = explode('.', $selector);

        // Protect from loop
        if($path[0]==='bDatabase') {
            /** @var bConfig $bConfig   - parent block */
            $bConfig = $this->_parent;

            /** @var bConfig__local $bConfig__local - default config strategy */
            $bConfig__local = $bConfig->getInstance('bConfig__local');

            return $bConfig__local->getItem($selector);
        }

        $item = $this->_db->getItem($selector);

        if($item->bconfig_id)$item->bconfig_id = $this->_db->getItemById($item->bconfig_id)->name;

        return array('name'=>$item->name, 'value'=>$item->value,'parent'=>$item->bconfig_id);
    }

    /**
     * Save configurations to database
     *
     * @param string $selector   config selector
     * @param mixed $value       config value
     * @param null $parent       parent config
     * @throws Exception
     * @void                    - save configurations to database
     */
    public function saveItem($selector = '', $value = null, $parent= null){
        // Protect from loop
        if($selector=='bDatabase') {
            /** @var bConfig $bConfig   - parent block */
            $bConfig = $this->_parent;

            /** @var bConfig__local $bConfig__local - default config strategy */
            $bConfig__local = $bConfig->getInstance('bConfig__local');

            return $bConfig__local->saveItem($selector, $value, $parent);
        }

        $config = $this->_db->getItem($selector);
        $config->value = $value;
        $config->name = $selector;
        $config->bconfig_id = $this->_db->getItem($parent)->id;

        $this->_config = $this->_system->navigate($this->_config, $selector, $config->value);
        $this->_db->save($config);
    }

	/**
	 * Get config from data base
	 *
	 * @param string $selector	- config selector
	 * @return mixed[]			- db configs
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
		if($temp = $this->_system->navigate($this->_config, $selector))return $temp;

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

			if(!$this->_system->navigate($this->_config, $currentPath)) {

				// Merge configurations with parents lines
				$config = $this->_db->mergeItem($this->_db->getItem($currentPath));

				// Concat with local config
				$this->_config = $this->_system->navigate($this->_config, $currentPath, $config->value);

			}

			$currentPath .= '.';
		}

		return $this->_system->navigate($this->_config, $selector);
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
        if($selector=='bDatabase') {
            /** @var bConfig $bConfig   - parent block */
            $bConfig = $this->_parent;

            /** @var bConfig__local $bConfig__local - default config strategy */
            $bConfig__local = $bConfig->getInstance('bConfig__local');

            return $bConfig__local->setConfig($selector, $value);
        }

		$config = $this->_db->getItem($selector);
		$config->value = $value;
		$config->name = $selector;

		$this->_config = $this->_system->navigate($this->_config, $selector, $config->value);
        $this->_db->save($config);
	}

    /**
     * Get all config names
     *
     * @return array
     * @throws Exception
     */
    public function getConfigList(){
        return $this->_db->getConfigList();
    }

}