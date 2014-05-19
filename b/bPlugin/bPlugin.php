<?php
defined('_BLIB') or die;

class bPlugin extends bBlib{	
	
	protected function inputSelf(){
		$this->version = '1.0.0';
		$this->parents = array('bSystem', 'bDatabase');
	}
	
	protected function input($data, $caller){
		$this->caller = $caller;
	}

	public function output(){
		
	}

	//point of listening
	public function call($name, $args){
		if($ref = self::$global['_reflection'][get_class($this)][$name]){
			
			foreach($ref as $key => $value){
				$listener = new $value($args, $this);
				$args = $value->output();
			}
			
			$answer = $this->$name($args);
			
			foreach($ref as $key => $value){
				$listener = new $value($answer, $this);
				$answer = $value->output();
			}
			
			return $answer;
		}
		echo '['.get_class($this).']->'.$name.'<br>';
		return $this->$name($args);
	}
	
}
