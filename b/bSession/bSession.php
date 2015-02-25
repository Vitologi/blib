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
	protected $_traits   = array('bSystem', 'bConfig');	// Also include some session strategy

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
		$config = $this->_getConfig();

		// get config
		if (isset($config['strategy'])) $this->_strategy 	= $config['strategy'];
		if (isset($config['expire'])) 	$this->_expire 		= $config['expire'];

		// set session strategy
		$this->setTrait($this->_strategy);
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
		return $strategy->setSession($selector, $value);
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
		return $strategy->clearSession();
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
		return $strategy->updateSession($expire);
	}


	/**
	 * Clear session from child block
	 *
	 * @param bBlib $caller	- block-initiator
	 * @return mixed
     */
	public static function _clearSession(bBlib $caller){
		/** @var bSession $bSession - session instance */
		$bSession = $caller->getInstance(__CLASS__);
		return $bSession->clearSession();
	}

	/**
	 * Update session from child block
	 *
	 * @param null $expire	- session live time
	 * @param bBlib $caller	- block-initiator
	 * @return mixed
     */
	public static function _updateSession($expire = null, bBlib $caller = null){
		/** @var bSession $bSession - session instance */
		$bSession = $caller->getInstance(__CLASS__);
		return $bSession->updateSession($expire);
	}

	/**
	 * Get session from child block
	 *
	 * @return mixed		- configuration
	 * @throws Exception
	 */
	public static function _getSession(){

		if(func_num_args()===2){

			/**
			 * @var string $selector 	- session selector
			 * @var bBlib $caller		- block-initiator
			 */
			list($selector, $caller) = func_get_args();
			$selector	= get_class($caller).".".$selector;

		}else if(func_num_args()===1){
			$caller 	= func_get_arg(0);
			$selector 	= get_class($caller);
		}else{
			throw new Exception('Not correct arguments given.');
		}

		if(!($caller instanceof bBlib))throw new Exception('Not correct arguments given.');

		/** @var bSession $bSession - session instance */
		$bSession = $caller->getInstance(__CLASS__);

		return $bSession->getSession($selector);
	}

	/**
	 * Set session from child block
	 *
	 * @return bool|void	- set/update session and operation result
	 * @throws Exception
	 */
	public static function _setSession(){
		if(func_num_args()===3){

			/**
			 * @var string $selector 	- session selector
			 * @var mixed $value 		- session value
			 * @var bBlib $caller		- block-initiator
			 */
			list($selector, $value, $caller) = func_get_args();
			$selector = get_class($caller).".".$selector;

		}else if(func_num_args()===2){
			list($value, $caller) = func_get_args();
			$selector = get_class($caller);
		}else{
			throw new Exception('Not correct arguments given.');
		}

		if(!($caller instanceof bBlib))throw new Exception('Not correct arguments given.');

		/** @var bSession $bSession - session instance */
		$bSession = $caller->getInstance(__CLASS__);

		return $bSession->setSession($selector, $value);
	}
	
}