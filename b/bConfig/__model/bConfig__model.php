<?php
defined('_BLIB') or die;

/**
 * Class bConfig__model - model for generate and work with config components
 * Included patterns:
 * 		MVC	- localise business logic into one class
 * 		singleton	- one configuration controller
 * 		strategy 	- many types of config storage
 */
class bConfig__model extends bBlib{


    /** @var null|static $_instance - Singleton instance */
    private static $_instance = null;

    private   $_config   = array();                            // All configuration stack
    private   $_default  = 'bConfig__local';                   // Default get/set strategy
    private   $_strategy = array('bConfig__local');            // Used strategy get/set config
    private   $_merge = false;                                 // Merge all strategy


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

    /**
     *  Set config and data mapper
     */
    protected function input(){
        $this->setInstance('system', 'bSystem');
        $this->setInstance('bConfig__local', 'bConfig__local');
        $this->setInstance('converter', 'bConverter');
        $this->setInstance('rbac', 'bRbac');
    }

    /**
     * Grab config by self, set config store strategy and point default config strategy
     * ATTENTION: this operation execute after save singleton into static property
     * this is need for prevent error(loop) when strategy based on block which use configuration
     */
    public function initialize(){
        $config = $this->getConfig('bConfig');
        $components = isset($config["strategy"])?$config["strategy"]:array();
        if(isset($config["default"]) && in_array($config["default"],$components))$this->_default = $config["default"];
        if(isset($config["merge"]))$this->_merge = $config["merge"];

        foreach($components as $key => $component){
            $this->setInstance($component, $component);
            $this->_strategy[] = $component;
        }

        $this->_strategy = array_unique($this->_strategy);
    }

    /**
     * Get full configuration data from all included strategy
     *
     * @param string $selector	- config selector
     * @return null|mixed		- configuration data
     */
    public function getConfig($selector =''){

        $_system = $this->getInstance('system');

        if(!$_system->navigate($this->_config, $selector)){
            $config = null;

            if($this->_merge){

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
            }else{
                $strategyObject = $this->getInstance($this->_default);
                $config = $strategyObject->getConfig($selector);
            }

            $this->_config = $_system->navigate($this->_config, $selector, $config);
        }

        return $_system->navigate($this->_config, $selector);
    }

    /**
     * Set/update configuration for block
     *
     * @param string $selector	- configuration selector
     * @param mixed $value		- configuration
     * @return $this			- for chaining
     */
    public function setConfig($selector = '', $value = array()){

        $_system = $this->getInstance('system');

        /** @var bConfig__local $strategy - Get default strategy */
        $strategy = ($selector == 'bConfig')?$this->getInstance('bConfig__local'):$this->getInstance($this->_default);

        // Extend inner configuration storage
        $this->_config = $_system->navigate($this->_config, $selector, $value);

        // forwards request to the strategy
        $strategy->setConfig($selector, $value);

        return $this;
    }


    /**
     * Create default configuration for block (use .json files)
     *
     * @param $block
     * @throws Exception
     */
    public function setDefault($block){
        $map = $this->getMap($block);
        $default = $this->map2config($map);
        $this->setConfig($block, $default);
    }



    /**
     * Get configuration map for single block
     *
     * @param string $block block`s name
     * @return array        config map
     */
    public function getConfigMap($block = null){

        /** @var bConfig__local $strategy - Get default strategy */
        $strategy = ($block == 'bConfig')?$this->getInstance('bConfig__local'):$this->getInstance($this->_default);

        $configMap = $this->getMap($block);
        $config = $strategy->getItem($block);
        $configMap['default'] = $config['value'];
        return $configMap;
    }

    /**
     * Get configuration map for single block
     *
     * @param string $block block`s name
     * @return array        config map
     */
    public function getConfigParent($block = null){

        /** @var bConfig__local $strategy - Get default strategy */
        $strategy = ($block == 'bConfig')?$this->getInstance('bConfig__local'):$this->getInstance($this->_default);
        $config = $strategy->getItem($block);
        return $config['parent'];
    }


    /**
     * Save configuration
     *
     * @param string $block block`s name
     * @param array $config block`s new configuration
     * @param null $parent config parent
     * @return bool|void
     * @throws Exception
     */
    public function saveConfig($block = null, $config = array(), $parent = null){

        /** @var bRbac $_rbac */
        $_rbac = $this->getInstance('rbac');

        if(!is_string($block) || !$_rbac->checkAccess('edit'))return false;

        /** @var bConfig__local $strategy - Get default strategy */
        $strategy = ($block == 'bConfig')?$this->getInstance('bConfig__local'):$this->getInstance($this->_default);

        $_converter = $this->getInstance('converter');
        $config = $_converter->setData($config)->convertTo('array');

        return $strategy->saveItem($block, $config, $parent);

    }


    /**
     * Get only default config from config map
     *
     * @param array $content    config map
     * @param bool|false $prop  object prop flag (for prevent return default form option)
     * @return array|null
     */
    public function map2config($content, $prop = false){
        if(!$prop && isset($content['default']))return $content['default'];

        if(isset($content['properties']))return $this->map2config($content['properties'], true);

        $arr = array();
        foreach($content as $key => $value){
            if(is_array($value) && ($temp =$this->map2config($value)))$arr[$key] = $temp;
        }

        return (empty($arr))?null:$arr;
    }


    /**
     * Get default configuration map
     *
     * @param string $block        block name
     * @return array|mixed  configuration map
     * @throws Exception
     */
    public function getMap($block = ''){
        $path = bBlib::path($block.'__bConfig','json');
        if(!file_exists($path))return array();

        try{
            $map = json_decode(file_get_contents($path),true);
        }catch (Exception $e){
            throw new Exception('Not correct config map format.');
        }

        return $map;
    }


    /**
     * Get all config name from all strategy
     *
     * @return array
     * @throws Exception
     */
    public function getConfigList(){

        $config = array();


        foreach($this->_strategy as $i => $strategy){
            /** @var bConfig__local $strategyObject - strategy instance */
            $strategyObject = $this->getInstance($strategy);

            $temp = $strategyObject->getConfigList();

            if($temp === null)continue;

            if(is_array($temp) and is_array($config)){
                $config = array_merge($config,$temp);
            }else{
                $config = $temp;
            }

        }

        return array_unique($config);
    }


}