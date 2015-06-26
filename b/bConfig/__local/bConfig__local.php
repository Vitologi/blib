<?php
defined('_BLIB') or die;

/**
 * Class bConfig__local - strategy for store configuration in block's file (also it is default strategy)
 */
class bConfig__local extends bBlib{

    /** @var null|static $_instance - Singleton instance */
    private static $_instance = null;

	/** @var bSystem $_system */
	protected $_system = null;

	/** @var mixed[]	- local config storage */
	private $_config = array();


    /**
     * Overload object factory for Singleton
     *
     * @return bConfig|null|static
     */
    static public function create() {
        if (self::$_instance === null)self::$_instance = parent::create(func_get_args());
        return self::$_instance;
    }

	protected function input(){
		$this->_system = $this->getInstance('system', 'bSystem');
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

        $config = array('name' => '', 'value' => array(), 'parent' => null);

        if(!$old = $this->_system->navigate($this->_config, $selector)) {

            $file   = bBlib::path($selector . '__bConfig', 'php');
            if (file_exists($file)) {
                $strConfig = stripcslashes(require_once($file));
                $temp      = json_decode($strConfig, true);

                if (is_array($temp)) {
                    $config = array_replace_recursive($config, $temp);
                } else {
                    $config['value'] = $temp;
                }



            }
        }else{
            $config['value'] = $old;
        }

        return $config;
	}

    /**
     * Save configurations to database
     *
     * @param string $selector - config selector
     * @param mixed $value - config value
     * @param null $parent
     * @void                    - save configurations to database
     */
	public function saveItem($selector = '', $value = null, $parent= null){
		return $this->setConfig($selector, $value, $parent);
	}


	/**
	 * Get config from block`s file named like bBlock__bConfig.php
	 *
	 * @param string $selector 	- config selector
	 * @return mixed[] 			- local configs
	 * @throws Exception
	 */
	public function getConfig($selector = ''){

        if(!$this->_system->navigate($this->_config, $selector)){

            /** @var string $name - block`s name */
            $name = (strpos($selector,'.'))?strstr($selector, '.', true):$selector;

            if(!array_key_exists($name,$this->_config)){

                $this->_config[$name]= array();

                $config = $this->getItem($name);

                if($config['parent']){
                    $config = array_replace_recursive($this->getConfig($config['parent']),$config['value']);
                }else{
                    $config = $config['value'];
                }

                $this->_config[$name] = $config;


            }
        }

		return $this->_system->navigate($this->_config, $selector);
	}

    /**
     * Set config to block`s file named like bBlock__bConfig.php
     *
     * @param string $selector - config selector
     * @param mixed $value - config value
     * @return int
     * @throws Exception
     * @void                    - store configuration in block's file
     */
	public function setConfig($selector = '', $value = null, $parent= null){

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

        $content = sprintf('{
            "name":"%s",
            "value":%s,
            "parent":"%s"
        }',$name, addslashes($config), $parent);

		return file_put_contents($file,"<?php defined('_BLIB') or die(); return '".$content."';");
	}

    /**
     * Get all config names
     *
     * @return array
     * @throws Exception
     */
    public function getConfigList(){

        $arr = opendir('b');
        $temp = array();
        while($v = readdir($arr)){
            if($v == '.' or $v == '..' or $v == 'bBlib') continue;
            $path = bBlib::path($v,'php');
            if(file_exists($path))$temp[] = $v;
        }
        return $temp;
    }

}