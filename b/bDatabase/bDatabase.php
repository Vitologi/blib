<?php
defined('_BLIB') or die;

class bDatabase extends bBlib{	
	
	private $db = array(
		'host'=>'127.0.0.1',
		'user'=>'root',
		'password'=>'',
		'database'=>'wf'
	);
	
	protected function inputSelf(){
		$this->version = '1.0.0';
	}

	protected function input($data, $caller){
		$this->caller = $caller;
		$this->callerName = get_class($this->caller);
		
		if($this->_bDatabase){
			$this->mysqli = $this->_bDatabase;
		}else{
			$this->mysqli = new mysqli($this->db['host'],$this->db['user'],$this->db['password'],$this->db['database']);
			if($this->mysqli->connect_errno) die("Connect Error #".$this->mysqli->connect_errno.". ".$this->mysqli->connect_error);
			$this->mysqli->query("SET NAMES utf8"); 
		}		
	}
	
	
	public function output(){
		return array(
			'install' => sprintf('CREATE TABLE IF NOT EXISTS `%1$s` (`%1$s__id` INT)', $this->callerName),
			'uninstall' => sprintf('DROP TABLE IF EXISTS `%s`', $this->callerName)
		);
	}

	
	/** ------------- */
	public function query($sql){
		$this->mysqli->query($sql);
	}
	
	//methods for child blocks
	public function install($data, $caller){
		$caller->bDatabase->query($caller->install);
	}
	
	public function uninstall($data, $caller){
		$caller->bDatabase->query($caller->uninstall);
	}

}

class bDatabase__object{
	
	public function __construct($data, $bDatabase){
		//var_dump($data, $bDatabase);
		//var_dump('select * from '.$data['table'], $bDatabase->query('select * from '.$data['table']));
	}

}