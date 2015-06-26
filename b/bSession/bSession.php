<?php
defined('_BLIB') or die;

/**
 * Class bSession 	- store session data
 * Included patterns:
 * 		strategy 	- use unique strategy for store session data
 * 		singleton	- one session object
 */
class bSession extends bBlib{

	/** @var null|static $_instance - Singleton instance */
	private static $_instance = null;

	private   $_strategy = 'bSession__phpsession';		// Default session strategy
	private   $_expire   = 0;              				// Session expire time

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
	 * Include session strategy
     */
	protected function input(){

        /** @var bConfig $_config */
		$_config = $this->getInstance('config', 'bConfig');

		$config = $_config->getConfig(__CLASS__);

		// get config
		if (isset($config['strategy'])) $this->_strategy 	= $config['strategy'];
		if (isset($config['expire'])) 	$this->_expire 		= $config['expire'];

		// set session strategy
        $this->setInstance($this->_strategy, $this->_strategy);
	}


	/**
	 * Return session object instance
	 * @return $this
     */
	public function output(){
		return $this;
	}

	/**
	 * Provide set request to strategy
	 *
	 * @param string $selector	- session property
	 * @param null $value		- session value
     */
	public function setSession($selector = null, $value = null){
		/** @var bSession__phpsession $strategy - some session strategy */
		$strategy = $this->getInstance($this->_strategy);
		$strategy->setSession($selector, $value);
	}

	/**
	 * Provide get request to strategy
	 * @param string $selector	- session property
	 * @return mixed
     */
	public function getSession($selector = null){
		/** @var bSession__phpsession $strategy - some session strategy */
		$strategy = $this->getInstance($this->_strategy);
		return $strategy->getSession($selector);
	}

	/**
	 * Clear strategy session
	 *
	 * @return mixed
     */
	public function clearSession(){
		/** @var bSession__phpsession $strategy - some session strategy */
		$strategy = $this->getInstance($this->_strategy);
		$strategy->clearSession();
	}

	/**
	 * Update strategy session
	 *
	 * @param null|int $expire	- session live time
	 * @return mixed
     */
	public function updateSession($expire = null){

		if ($expire === null) $expire = $this->_expire;

		/** @var bSession__phpsession $strategy - some session strategy */
		$strategy = $this->getInstance($this->_strategy);
		$strategy->updateSession($expire);
	}

}