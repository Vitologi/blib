<?php
defined('_BLIB') or die;

class bUser extends bBlib{

	/** @var null|static $_instance - Singleton instance */
	private static $_instance = null;

	protected $_traits            = array('bSystem', 'bConfig', 'bRequest', 'bUser__bDataMapper', 'bDecorator', 'bSession');
	protected $bTemplate__dynamic = true;
	private   $_template          = array();
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

		self::$_instance->setTemplate(func_get_args(0));
		return self::$_instance;
	}

	protected function input(){

		/** @var bRequest $bRequest - request data */
		$bRequest 	= $this->getInstance('bRequest');
		$login 		= $bRequest->get('login');
		$password 	= $bRequest->get('password');
		$logout 	= $bRequest->get('logout');
		$save 		= $bRequest->get('save');

		// Authentication
		$this->_decorate()->authorize($login, $password, $save);

		// Logout
		if($logout)$this->logout();
	}

	/**
	 * Extend child block or return template
	 *
	 * @return array|bUser
     */
	public function output(){
		return ($this->_parent?$this:$this->getTemplate());
	}

	protected function setTemplate($template = array()){
		$this->_template = array_replace_recursive(array('block'=>__CLASS__, 'mods'=>array(), 'attrs'=>array(), 'meta'=>array(), 'content'=> ''), $template);
	}

	protected function getTemplate(){
		$this->_template['content'] = $this->login;
		return $this->_template;
	}


	protected function authorize($login = null, $password = null, $remember = false){

		/** @var bDataMapper $bDataMapper - user Data Mapper */
		$bDataMapper = $this->getInstance('bUser__bDataMapper');
		/** @var bConfig $bConfig - config block */
		$bConfig	 = $this->getInstance('bConfig');


		// Authentication
		if($login){

			$user = $bDataMapper->getItem($login, $password);

			if($user->id){

				$userConfig 	= $bConfig->getConfig(__CLASS__ . '.' . $this->id);
				$this->id 		= $user->id;
				$this->login 	= $user->login;
				$this->config 	= $userConfig;

				if ($remember == 'on') {
					$sessionExpire = $bConfig->getConfig(__CLASS__ . '.expire');
					$sessionExpire = ($sessionExpire) ? $sessionExpire : 604800;
					$this->_updateSession($sessionExpire);
				}

				$this->_setSession('id', $this->id);
				$this->_setSession('login', $this->login);
				$this->_setSession('config', $this->config);

			}

		// Authentication from session
		}else{
			$this->id = $this->_getSession('id');

			if($this->id){
				$this->config = $this->_getSession('config');
				$this->login = $this->_getSession('login');
			}else{
				$this->config = null;
				$this->login = null;
			}
		}

	}

	/**
	 * Logout user (clear session data and user info)
     */
	protected function logout(){

		/** @var bRequest $bRequest - request object*/
		$bRequest = $this->getInstance('bRequest');
		$bRequest->set('logout', null);

		$this->id 		= null;
		$this->login	= null;
        $this->config 	= null;

		$this->_clearSession();
	}
}