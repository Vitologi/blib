<?php
defined('_BLIB') or die;

class bExample extends bBlib{	
	
	protected function inputSelf(){
		$this->version = '1.112.1';
		$this->parents = array('bSystemAlias', 'bConfig', 'bDatabase');

	}
	
	protected function input($data, $caller){
		$this->caller = $caller;
	}
	

	
	public function output(){
				
			var_dump($this);

	}

}

class bExample__install{
	
	public function __construct($data, $caller){
		
	}

}