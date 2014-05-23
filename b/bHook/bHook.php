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
		$this->local['list'] = $this->getHookList();
		$this->local['handlers'] = array();
		bBlib::$global['_bHook'] = $this;
	}


	//point of listening
	public function _hook($data, bBlib $caller){
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
				$className = $caller->_getMinion(__class__, $value);
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
	
	public function _scanHooks($data, $caller = null){
		
		if($caller === null){return;}
		
		$dir = $caller->_getBlockPath().'/__'.__class__;
		$arr = opendir($dir);
		
		while($v = readdir($arr)){
			if(!fnmatch('*.php', $v)) continue;
			
			require_once($dir.'/'.$v);
			$name = basename($v, '.php');
			$temp = explode (get_class($caller).'__'.__class__, $name);
			$r = substr($temp[1], 1);
			var_dump($r);
			//if(!$hook = new $name()){throw new Exception('Can`t create hook object.');}
			
			//$hook->
	
		}

			

		
		/*
		return array(
			'bExample' => array('plugin3', 'plugin2', 'plugin'),
			'bTest' => array('plugin', 'plugin2', 'plugin3')			
		);*/
	}
	
	private function getHookList(){
		return array(
			'bExample' => array('bExample__bHook_plugin3', 'bExample__bHook_plugin2', 'bExample__bHook_plugin'),
			'bTest' => array('bTest__bHook_plugin3', 'bTest__bHook_plugin2', 'bTest__bHook_plugin')			
		);
	}
	
	public function setHookList($block, $name){
		
		$_bHook = bBlib::$global['_bHook'];
		
		$Q = array(
			'insert'=>array(
				'bHook'=>array(
					array('id', 'blib', 'name', 'version', 'enabled', 'json')
				)
			)
		);
		
		
		
		//$_bHook->query();
		return array(
			'bExample' => array('plugin3', 'plugin2', 'plugin'),
			'bTest' => array('plugin', 'plugin2', 'plugin3')			
		);
	}
}
