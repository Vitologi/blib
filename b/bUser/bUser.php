<?php
defined('_BLIB') or die;

class bUser extends bBlib{	
	
	private $id;
	private $login;
	
	protected function inputSelf(){
		$this->version = '1.0.0';
		$this->parents = array('bSystem', 'bDatabase', 'bConfig', 'bSession');
	}
	
	protected function input($data, $caller){
		$this->data = $data;
		$this->caller = $caller;
	}
	
	public function output(){
		if($this->_request['logout'])$this->logout();
		$bUser = $this->getSingleton();

		//for system
		if($this->caller)return array('bUser'=>$bUser);

		//for template
		return array('block'=>__class__, 'mods'=>$this->data['mods'], 'content'=>$bUser->getLogin());

	}
	
	private function getSingleton(){
		if(!$this->_bUser){
			
			$this->id = $this->_getSession('id');
			
			if($this->id){
				$this->config = $this->_getSession('config');
				$this->login = $this->_getSession('login');
			}else{
				$login = $this->_request['login'];
				$password = $this->_request['password'];
			
				$Q = array(
					'select' => array(
						'bUser' => array('id','bConfig_id')
					),
					'where' => array(
						'bUser' => array('login' => $login, 'password' => md5($password))
					)
				);
				
				$result = $this->_query($Q);
				if($result->rowCount()>1)throw new Exception("Find many users for this authorisation data");
				
				if($result->rowCount()==1){
					$row = $result->fetch();
					$this->login = $login;
					$this->id = $row['id'];
					$this->config = $this->_getConfig($row['bConfig_id']);
					
					$this->_setSession('id', $this->id);
					$this->_setSession('login', $this->login);
					$this->_setSession('config', $this->config);
				}
			}
		
			$this->_bUser = $this;
		}
		
		return $this->_bUser;
	}
	
	protected function getLogin(){
		return $this->login;
	}
	
	public function getId(){
		return $this->id;
	}
	
	protected function logout(){
		$this->_setSession('id');
		unset(bBlib::$global['_request']['logout']);
		unset(bBlib::$global['bUser']);
	}
}