<?php
defined('_BLIB') or die;

/**
 * Class bConfig__database 	- strategy for store configuration in database
 * Included patterns:
 * 		Data Mapper - interface for interaction to data base
 * 		singleton	- one work object
 */
class bSession__phpsession extends bBlib{

	/** @var bSystem $_system */
	protected $_system = null;

	/** @var null|static $_instance - Singleton instance */
	private static $_instance  = null;
	private        $_storePath = null;		// Store path
	private        $_expire    = 0;			// Cookie lifetime
	private        $_data      = array();	// Local session storage


	/**
	 * Overload object factory for Singleton
	 *
	 * @return bConfig|null|static
	 */
	static public function create() {
		if (self::$_instance === null)self::$_instance = parent::create(func_get_args());
		return self::$_instance;
	}

	/**
	 * Configure php session
	 *
	 * @throws Exception
     */
	protected function input(){
		$this->_system = $this->getInstance('system', 'bSystem');
		$this->updateSession($this->_expire, bBlib::path('bSession__phpsession__storage'));
	}

	public function output(){
		return $this;
	}

	/**
	 * Get session from inner data
	 *
	 * @param string $selector	- session selector
	 * @return mixed[]			- local session
	 */
	public function getSession($selector = null){
		return $this->_system->navigate($this->_data, $selector);
	}

	/**
	 * Save configurations to database
	 *
	 * @param string $selector	- config selector
	 * @param mixed $value 		- config value
	 * @void 					- save configurations to database
	 */
	public function setSession($selector = null, $value = null){
		$this->_data = $this->_system->navigate($this->_data, $selector, $value);
		$_SESSION[__CLASS__] = $this->_data;
	}

	public function clearSession(){
		unset($_COOKIE[session_name()]);
		session_destroy();
		session_start();
	}


	public function updateSession($expire = null, $storePath=null){

        if ($expire !== null) $this->_expire = $expire;
        if (is_string($storePath))$this->_storePath = $storePath;

        if(session_id()){
            $tempStorage = $_SESSION;
            unset($_COOKIE[session_name()]);
            session_destroy();
        }else{
            $tempStorage = array();
        }

        if(
                ($this->_storePath !== null)
            &&	(ini_set('session.save_path', $this->_storePath) === false)
        ){
            throw new Exception('Can`t set php session save path');
        }

        if(
                ($this->_expire !== null)
            &&	(ini_set('session.cookie_lifetime', $this->_expire) === false)
        ){
            throw new Exception('Can`t set php session cookie lifetime');

        }

        if(!session_start() ){
            throw new Exception('Cannot use php session.');
        }

		$_SESSION = array_replace_recursive($tempStorage, $_SESSION);
		if(isset($_SESSION[__CLASS__]))$this->_data = $_SESSION[__CLASS__];

	}

}