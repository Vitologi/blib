<?php
defined('_BLIB') or die;

class bUser extends bBlib{	
	
	private static $singleton;
	private $id;
	private $login;
	
	protected function inputSelf(){
		$this->version = '1.0.0';
		$this->parents = array('bSystem', 'bDatabase', 'bConfig', 'bSession');
	}
	
	protected function input($data, $caller){
		$this->data = $data;
		$this->caller = $caller;
		$this->expire = 604800;
	}
	
	public function output(){
		if(array_key_exists('logout', $this->_request))$this->logout();
		$bUser = $this->getSingleton();

		//for system
		if($this->caller)return array('bUser'=>$bUser);

		//for template
		return array('block'=>__class__, 'mods'=>$this->data['mods'], 'content'=>$bUser->getLogin());

	}
	
	private function getSingleton(){
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
	
	protected function getLogin(){
		return $this->login;
	}
	
	public function getId(){
		return $this->id;
	}
	
	protected function logout(){
		$this->_setSession();
		unset(bBlib::$global['_request']['logout']);
		bUser::$singleton = null;
		$this->_clearSession();
	}
}