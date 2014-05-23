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

	private function getHookList(){
		return array(
			'bExample' => array('bExample_color', 'bExample__bHook_plugin2', 'bExample__bHook_plugin'),
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
		
		return array(
			'bExample' => array('plugin3', 'plugin2', 'plugin'),
			'bTest' => array('plugin', 'plugin2', 'plugin3')			
		);
	}
}
