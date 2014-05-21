<?php
defined('_BLIB') or die;

class bHook extends bBlib{	
	
	protected function inputSelf(){
		if(bBlib::$global['_bHook']){return;}
		$this->version = '1.0.0';
		$this->parents = array('bSystem', 'bDatabase');
	}
	
	protected function input($data, $caller){
		if(bBlib::$global['_bHook']){return;}
		
		$this->local['list'] = array(
			'bExample' => array('plugin3', 'plugin2', 'plugin'),
			'bTest' => array('plugin', 'plugin2', 'plugin3')			
		);
		
		$this->local['handlers'] = array(
			
		);
		
		bBlib::$global['_bHook'] = $this;
	}


	//point of listening
	public function hook($data, bBlib $caller){
		$method = $data[0];
		$input = $data[1];
		
		$block = get_class($caller);
		$_bHook = bBlib::$global['_bHook'];
		$list = $_bHook->local['list'][$block];
		$answer = array();
		$returnFlag = false;
		
		if(!$handlers = $_bHook->local['handlers'][$block]){
			$handlers = array();
			foreach($list as $value){
				$className = $caller->getMinion(__class__, $value);
				if(is_string($className)){
					$handlers[$value] = new $className(array(), $caller);
				}
			}
			$_bHook->local['handlers'][$block] = $handlers;
		}
		
		foreach($handlers as $value){
			if(!method_exists($value, $method)){continue;}
			$answer = (array)$value->$method(array('input'=> $input, 'output'=> $output), $caller);

			if($answer['input']){
				$input = $answer['input'];
			}
			if($answer['output']){
				$output = $answer['output'];
				$returnFlag = true;
			}
		}
		
		if(!$returnFlag && method_exists($caller, $method)){
			$output = $caller->call($method, $input);
		}
		
		return $output;
	}
	
}
