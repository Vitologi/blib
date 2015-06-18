<?php
defined('_BLIB') or die;

/**
 * Class bUser	- block for stor user authentication data (like id, login, password)
 */
class bUser extends bBlib{

	/** @var null|static $_instance - Singleton instance */
	private static $_instance = null;
	protected $id                 = null;
	protected $login              = null;
	protected $config             = null;


    /**
     * @return null|int     - get user id
     */
    public function getId(){return $this->id;}

    /**
     * @return null|string  - get user login
     */
    public function getLogin(){return $this->login;}

    /**
     * @return mixed        - get user configuration
     */
    public function getConfig(){return $this->config;}

	/**
	 * Overload object factory for Singleton
	 *
	 * @return bUser|null|static
	 */
	static public function create() {
		if (self::$_instance === null)self::$_instance = parent::create();
		return self::$_instance;
	}

	protected function input(){

        $this->setInstance('config', 'bConfig');
        $this->setInstance('db', 'bUser__bDataMapper');
        $this->setInstance('session', 'bSession');
        $this->setInstance('view', 'bUser__view');

        /** @var self $_this */
        $_this = $this->getInstance('this', 'bDecorator');
        /** @var bRequest $_request - request data */
		$_request = $this->getInstance('request','bRequest');

		$login 		= $_request->get('login');
		$password 	= $_request->get('password');
		$logout 	= $_request->get('logout');
		$save 		= $_request->get('save');

		// Authentication
        $_this->authorize($login, $password, $save);

		// Logout
		if($logout)$_this->logout();

		$this->id 		= $_this->getId();
		$this->login 	= $_this->getLogin();
		$this->config 	= $_this->getConfig();
	}

	/**
	 * Extend child block or return template
	 *
	 * @return array|bUser
     */
	public function output(){
		if($this->_parent instanceof bBlib) return $this;

		/** @var bUser__view $_view */
		$_view = $this->getInstance('view');
        $_view->set('login',$this->login);

		return $_view->index();
	}

	/**
	 * Authenticate user for requested login and password
	 *
	 * @param null|string $login		- user login
	 * @param null|string $password		- user password
	 * @param bool $remember			- remember flag, for save cookie after close browser
     */
	public function authorize($login = null, $password = null, $remember = false){

		/** @var bDataMapper $_db - user Data Mapper */
		$_db = $this->getInstance('db');
		/** @var bConfig $_config - config block */
		$_config	 = $this->getInstance('config');
        /** @var bSession $_session */
        $_session = $this->getInstance('session');

		// Authentication
		if($login){

			$user = $_db->getItem($login, $password);

			if($user->id){

				$userConfig 	= $_config->getConfig(__CLASS__ . '.' . $this->id);
				$this->id 		= $user->id;
				$this->login 	= $user->login;
				$this->config 	= $userConfig;

				if ($remember == 'on') {
					$sessionExpire = $_config->getConfig('expire');
					$sessionExpire = ($sessionExpire) ? $sessionExpire : 604800;
                    $_session->updateSession($sessionExpire);
				}

                $_session->setSession(__CLASS__.'.id', $this->id);
                $_session->setSession(__CLASS__.'.login', $this->login);
                $_session->setSession(__CLASS__.'.config', $this->config);

			}

		// Authentication from session
		}else{
			$this->id = $_session->getSession(__CLASS__.'.id');

			if($this->id){
				$this->config = $_session->getSession(__CLASS__.'.config');
				$this->login = $_session->getSession(__CLASS__.'.login');
			}else{
				$this->config = null;
				$this->login = null;
			}
		}

	}

	/**
	 * Logout user (clear session data and user info)
     */
	public function logout(){

        /** @var bSession $_session */
        $_session = $this->getInstance('session');

        /** @var bRequest $_request - request data */
        $_request = $this->getInstance('request');

        $_request->set('logout', null);

		$this->id 		= null;
		$this->login	= null;
        $this->config 	= null;

        $_session->clearSession();
	}
}