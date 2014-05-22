<?php
defined('_BLIB') or die;

class bSession extends bBlib{	
	
	private $id = false;
	private $storePath = false;
	private $data = array();
	
	protected function inputSelf(){
		$this->version = '1.0.0';
		$this->parents = array('bSystem', 'bDatabase', 'bConfig');
		
	}
	
	protected function input($data, $caller){
		$this->caller = get_class($caller);
		$this->sessionType = false;
	}
	
	
	public function output(){

		if($this->_bSession){return array('bSession'=>$this->_bSession);}

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
							'bSession'=>array('value')
						),
						'where'=>array(
							'bSession'=>array('id'=>$_COOKIE['bSession'])
						)
					);
					if(!$result = $this->_query($Q)){ throw new Exception('Can`t get session information.'); }
					if($result->rowCount()){
						$row = $result->fetch();
						$this->id = $_COOKIE['bSession'];
						$this->data = (array)json_decode($row['value'], true);
						bBlib::$global['_bSession'] = $this;
					}
				}
				
				if(!$this->id){
					$this->id = md5(microtime(true).$_SERVER['REMOTE_ADDR']);
					$Q = array(
						'insert'=>array(
							'bSession'=>array('id'=>$this->id, 'value'=>json_encode($this->data))
						)
					);
					if(!$this->_query($Q)){
						throw new Exception('Can`t insert session information.');
					}
					bBlib::$global['_bSession'] = $this;
					$expire = ($this->expire)?(time()+$this->expire):0;
					setcookie('bSession', $this->id, $expire, $this->path, $this->domen, $this->secure, $this->httponly);
				}
				
				break;
			
			default:
				if ( !isset($_SESSION) && !session_id() ) { 
					
					if(!$this->storePath){
						$this->storePath = $this->_getBlockPath().'/__storage';
						if(!ini_set('session.save_path', $this->storePath)){
							throw new Exception('Canot set php session save path');
						};
					}
					
					if(!session_start()){throw new Exception('Cannot use php session.');}; 
				}
				
				
				
				$this->data = $_SESSION[__class__];
				bBlib::$global['_bSession'] = $this;
				break;
			
		}
		
		return array('bSession'=>$this);
		
	}
	
	/** for child */
	public function _getSession($data, bBlib $caller = null){
		
		if($caller != null){
			$block = get_class($caller);
			return $caller->bSession->_getSession(array($block, $data[0]));
		}
		
		return $this->data[$data[0]][$data[1]];

	}
	
	public function _setSession($data, bBlib $caller = null){
		
		if($caller != null){
			$block = get_class($caller);
			return $caller->bSession->_setSession(array('block'=>$block, 'data'=>$data));
		}

		$this->data[$data['block']][$data['data'][0]] = $data['data'][1];
		
		switch($this->sessionType){
			case "database":
				$Q = array(
					'update'=>array(
						'bSession'=>array('value'=>json_encode($this->data))
					),
					'where' => array(
						'bSession'=>array('id'=>$this->id)
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
				$_SESSION[__class__] = $this->data;	
				break;
			
		}

		bBlib::$global['_bSession'] = $this;
	}
}