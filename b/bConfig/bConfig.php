<?php
defined('_BLIB') or die;

class bConfig extends bBlib{	
	
	protected function inputSelf(){
		$this->version = '1.0.0';
		$this->parents = array('bSystemAlias', 'bDatabase');
		
	}
	
	protected function input($data, $caller){
		$this->caller = get_class($caller);
	}
	
	
	public function output(){
		
		
		
	}
	
}