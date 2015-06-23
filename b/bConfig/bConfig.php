<?php
defined('_BLIB') or die;

/**
 * Class bConfig - interface for store some configuration for blocks.
 * Included patterns:
 * 		singleton	- one configuration controller
 * 		strategy 	- many types of config storage
 */
class bConfig extends bBlib{

    /** @var bSystem $_system */
	protected $_system = null;

	/** @var null|static $_instance - Singleton instance */
	private static $_instance = null;

	private   $_config   = array();                            // All configuration stack
	private   $_default  = 'bConfig__local';                   // Default get/set strategy
	private   $_strategy = array('bConfig__local');            // Used strategy get/set config


	/**
	 * Overload object factory for Singleton
	 *
	 * @return bConfig|null|static
     */
	static public function create() {
		if (self::$_instance === null){
            self::$_instance = parent::create(func_get_args());
            self::$_instance->initialize();
        }
		return self::$_instance;
	}

    protected function input(){
        $this->_system = $this->getInstance('system', 'bSystem');
        $this->setInstance('bConfig__local', 'bConfig__local');
        $this->setInstance('model', 'bConfig__model');
    }

	public function output(){
		return $this;
	}

    /**
     * Grab config by self, set config store strategy and point default config strategy
     * ATTENTION: this operation execute after save singleton into static property
     * this is need for prevent error(loop) when strategy based on block which use configuration
     */
    public function initialize(){
        $config = $this->getConfig(__CLASS__);
        $components = isset($config["strategy"])?$config["strategy"]:array();
        if(isset($config["default"]))$this->setDefaultStrategy($config["default"]);

        foreach($components as $key => $component){
            $this->setInstance($component, $component);
            $this->_strategy[] = $component;
        }
    }

	/**
	 * Get full configuration data from all included strategy
	 *
	 * @param string $strategy 	- name strategy for get/set
	 * @void    				- set default strategy
	 * @return $this    		- for chaining
	 */
	public function setDefaultStrategy($strategy ='bConfig__local'){
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

		if(!$this->_system->navigate($this->_config, $selector)){
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

			$this->_config = $this->_system->navigate($this->_config, $selector, $config);
		}

		return $this->_system->navigate($this->_config, $selector);
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
		$strategy = ($selector == __CLASS__)?$this->getInstance('bConfig__local'):$this->getInstance($this->_default);

		// Extend inner configuration storage
		$this->_config = $this->_system->navigate($this->_config, $selector, $value);

		// forwards request to the strategy
		$strategy->setConfig($selector, $value);

		return $this;
	}

    public function setDefault($block){
        $_model = $this->getInstance('model');
        $default = $_model->getDefault($block);
        $this->setConfig($block, $default);
    }
}