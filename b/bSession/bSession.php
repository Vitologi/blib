<?php
defined('_BLIB') or die;

class bSession extends bBlib{	
	
	private static $singleton = null;
	private static $data = array();
	private $id = false;
	private $storePath = false;
	
	
	protected function inputSelf(){
		$this->version = '1.0.0';
		$this->parents = array('bSystem', 'bDatabase', 'bConfig');
		
	}
	
	protected function input($data, $caller){
		$this->sessionType = false;
	}
	
	
	public function output(){

		if(bSession::$singleton){return array('bSession'=>bSession::$singleton);}

		switch($this->sessionType){
			case "database":
				
				//defaults
				$this->expire = 0;
				$this->path = '/';
				$this->domen = '';
				$this->secure = false;
				$this->httponly = false;
				
				
				if(isset($_COOKIE) && $_COOKIE['bSession']){
					$Q = array(
						'select'=>array(
							'bsession'=>array('value')
						),
						'where'=>array(
							'bsession'=>array('id'=>$_COOKIE['bSession'])
						)
					);
					if(!$result = $this->_query($Q)){ throw new Exception('Can`t get session information.'); }
					if($result->rowCount()){
						$row = $result->fetch();
						$this->id = $_COOKIE['bSession'];
						bSession::$data = (array)json_decode($row['value'], true);
						bSession::$singleton = $this;
					}
				}
				
				if(!$this->id){
					$this->id = md5(microtime(true).$_SERVER['REMOTE_ADDR']);
					$Q = array(
						'insert'=>array(
							'bsession'=>array('id'=>$this->id, 'value'=>json_encode(bSession::$data))
						)
					);
					if(!$this->_query($Q)){
						throw new Exception('Can`t insert session information.');
					}
					bSession::$singleton = $this;
					$expire = ($this->expire)?(time()+$this->expire):0;
					setcookie('bSession', $this->id, $expire, $this->path, $this->domen, $this->secure, $this->httponly);
				}
				
				break;
			
			default:
				if ( !isset($_SESSION) && !session_id() ) { 
					
					if(!$this->storePath){
						$this->storePath = bBlib::path('bSession').'__storage';
						if(!ini_set('session.save_path', $this->storePath)){
							throw new Exception('Canot set php session save path');
						};
					}
					
					if(!session_start()){throw new Exception('Cannot use php session.');}; 
				}
				
				
				
				bSession::$data = $_SESSION[__class__];
				bSession::$singleton = $this;
				break;
			
		}
		
		return array('bSession'=>$this);
		
	}
	
	/** for child */
	public static function _getSession($data, bBlib $caller = null){
		if($caller === null)return array();
		$block = get_class($caller);
		return bSession::$data[$block][$data[0]];
	}
	
	public static function _setSession($data, bBlib $caller = null){
		if($caller === null)return array();
		return $caller->local['bSession']->setSession($data);
	}
	
	private function setSession($data){
		
		$block = get_class($this);
		$data[1] = isset($data[1])?$data[1]:null;
		bSession::$data[$block][$data[0]] = $data[1];
		
		switch($this->sessionType){
			case "database":
				$Q = array(
					'update'=>array(
						'bsession'=>array('value'=>json_encode(bSession::$data))
					),
					'where' => array(
						'bsession'=>array('id'=>$this->id)
					)
				);
				
				if(!$this->_query($Q)){
					throw new Exception('Can`t insert session information.');
				}

				break;
			
			default:
				if ( !isset($_SESSION) && !session_id() ) { 
					if(!session_start()){throw new Exception('Cannot use php session.');}; 
				}
				$_SESSION[__class__] = bSession::$data;	
				break;
			
		}

	}
}