<?php
defined('_BLIB') or die;

class bSession extends bBlib{	
	
	private $id = false;
	private $data = array();
	
	protected function inputSelf(){
		$this->version = '1.0.0';
		$this->parents = array('bSystem', 'bDatabase', 'bConfig');
		
	}
	
	protected function input($data, $caller){
		$this->caller = get_class($caller);
		$this->expire = 0;
		$this->path = '/';
		$this->domen = '';
		$this->secure = false;
		$this->httponly = false;
	}
	
	
	public function output(){
		
		if($this->_bSession){return array('bSession'=>$this->_bSession);}
		
		
		if(isset($_COOKIE) && $_COOKIE['bSession']){
			
			$Q = array(
				'select'=>array(
					'bSession'=>array('value')
				),
				'where'=>array(
					'bSession'=>array('id'=>$_COOKIE['bSession'])
				)
			);
			
			if(!$result = $this->query($Q)){ throw new Exception('Can`t get session information.'); }
			if($result->rowCount()){
				$row = $result->fetch();
				$this->id = $_COOKIE['bSession'];
				$this->data = (array)json_decode($row['value'], true);
				$this->_bSession = $this;
			}
		}
		
		if(!$this->id){
			
			$this->id = md5(microtime(true).$_SERVER['REMOTE_ADDR']);
			
			$Q = array(
				'insert'=>array(
					'bSession'=>array('id'=>$this->id, 'value'=>json_encode($this->data))
				)
			);
			
			if(!$this->query($Q)){
				throw new Exception('Can`t insert session information.');
			}
			
			$this->_bSession = $this;
			
			
			
			if($this->expire){
				$expire = time()+$this->expire;
			}else{
				$expire = 0;
			}
			
			setcookie('bSession', $this->id, $expire, $this->path, $this->domen, $this->secure, $this->httponly);

		}
		
		var_dump(microtime(true), $_SERVER['REMOTE_ADDR'], $this->id,'||||||');
		
		return array(
			'bSession'=>$this
		);
	}
	
}