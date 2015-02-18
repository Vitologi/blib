<?php
defined('_BLIB') or die;

class bUser extends bBlib{

	/** @var null|static $_instance - Singleton instance */
	private static $_instance = null;

	protected $_traits            = array('bSystem', 'bConfig', 'bRequest', 'bDataMapper', 'bDecorator', 'bSession');
	protected $bTemplate__dynamic = true;
	private   $_template          = array();
	protected $id                 = null;
	protected $login              = null;


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

		$config 	= $this->_getConfig();
		$this->_default('expire', $config['expire'], 604800);

		// Authentication
		$this->_decore()->authentication($login, $password);

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


	public function authentication($login = null, $password = null){

		/** @var bDataMapper__instance $bDataMapper	- user Data Mapper */
		$bDataMapper 	= $this->getInstance('bDataMapper');

		/** @var bRequest $bRequest 		- request data */
		$bRequest 		= $this->getInstance('bRequest');
		$save 			= $bRequest->get('save');


		if($login){

			$user = $bDataMapper->getItem($login, $password);

			if($user->id){
				$this->id = $user->id;
				$this->login = $user->login;

			}


		}

		if($this->id === null){

			//autentication
			if($login){
				$Q = array(
					'select' => array(
						'buser' => array('id','bconfig_id')
					),
					'where' => array(
						'buser' => array('login' => $login, 'password' => md5($password))
					)
				);

				$result = $this->_query($Q);
				if($result->rowCount()>1)throw new Exception("Find many users for this authorisation data");

				if($result->rowCount()==1){
					$row = $result->fetch();
					$this->login = $login;
					$this->id = $row['id'];
					$this->config = $this->_getConfig($row['bconfig_id']);

					if($save == 'on')$this->_updateSession(array('expire'=>$this->expire));

					$this->_setSession('id', $this->id);
					$this->_setSession('login', $this->login);
					$this->_setSession('config', $this->config);
				}

				//autentication from session
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

			bUser::$singleton = $this;
		}

		return bUser::$singleton;
	}



	/*
	private static $singleton;
	
	public function getSingleton(){return bUser::$singleton;}
	protected function setSingleton($value){bUser::$singleton = $value;}

	
	protected function inputSelf(){
		$this->version = '1.0.0';
		$this->parents = array('bSystem', 'bDatabase', 'bConfig', 'bSession');
		$this->bTemplate__dynamic = true;
	}


	protected function input($data, $caller){
		$this->data = $data;
		$this->caller = $caller;
		$this->expire = 604800;
	}


	public function output(){
		if(array_key_exists('logout', $this->_request))$this->logout();
		$bUser = $this->hook('createSingleton',array());

		//for system
		if($this->caller)return array('bUser'=>$bUser);
	
		//for template
		$this->local['data'] = bBlib::extend($this->local['data'], array('mods'=>array(), 'attrs'=>array(), 'meta'=>array()));
		return array('block'=>__class__, 'mods'=>$this->data['mods'], 'attrs'=>$this->data['attrs'], 'meta'=>$this->data['meta'], 'content'=>$bUser->login);

	}



	protected function createSingleton(){
		if(!bUser::$singleton){
			$login = bBlib::extend(bBlib::$global['_request'], 'login', null);
			$password = bBlib::extend(bBlib::$global['_request'], 'password', null);
			$save = bBlib::extend(bBlib::$global['_request'], 'save', null);

			//autentication
			if($login){
				$Q = array(
					'select' => array(
						'buser' => array('id','bconfig_id')
					),
					'where' => array(
						'buser' => array('login' => $login, 'password' => md5($password))
					)
				);
				
				$result = $this->_query($Q);
				if($result->rowCount()>1)throw new Exception("Find many users for this authorisation data");
				
				if($result->rowCount()==1){
					$row = $result->fetch();
					$this->login = $login;
					$this->id = $row['id'];
					$this->config = $this->_getConfig($row['bconfig_id']);
					
					if($save == 'on')$this->_updateSession(array('expire'=>$this->expire));
					
					$this->_setSession('id', $this->id);
					$this->_setSession('login', $this->login);
					$this->_setSession('config', $this->config);
				}
			
			//autentication from session
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

			bUser::$singleton = $this;
		}
		
		return bUser::$singleton;
	}
	*/


	/**
	 * Logout user (clear session data and user info)
     */
	protected function logout(){

		/** @var bRequest $bRequest - request object*/
		$bRequest = $this->getInstance('bRequest');
		$bRequest->set('logout', null);

		$this->id 		= null;
		$this->login	= null;

		$this->_setSession();
		$this->_clearSession();
	}
}