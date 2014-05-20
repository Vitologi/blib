<?php
defined('_BLIB') or die;

class bHooks extends bBlib{	
	
	protected function inputSelf(){
		$this->version = '1.0.0';
		$this->parents = array('bSystem', 'bDatabase');
	}
	
	protected function input($data, $caller){
		
	}

	public function output(){
		
	}

	//point of listening
	protected function call($data, $caller = null){
		/*if($ref = self::$global['_reflection'][get_class($this)][$name]){
			
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
		*/
		
		//echo '['.get_class().']->'.$data[0].'<br>';
		$caller->local['version'] = '0000000000000000';
		var_dump($caller->local);
		//echo $caller->$data[0]($data[1]);
	}
	
}
