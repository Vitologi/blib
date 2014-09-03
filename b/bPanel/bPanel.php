<?php
defined('_BLIB') or die;

class bPanel extends bBlib{	
	
	private static $blocks = array();
	
	protected function inputSelf(){
		$this->version = '1.0.0';
		$this->bTemplate__dynamic = true;
	}
	
	protected function input($data, $caller){
		$this->data = $this->hook('getData', array($data));
		$this->caller = $caller;
	}
	
	public function output(){
		if($this->caller)return;
		$this->scanBlocks();
		
		
		$data = &$this->getTunnel();
		$option = ($data['option']?$data['option']:"bPanel");
		$view = ($data['view']?$data['view']:"navigation");
		$action = ($data['action']?$data['action']:"show");
		$answer = array();
		
		switch($action){
			case "add":
				
				break;
			
			case "show":
			default:
				if($view == 'navigation')$answer = $this->showBlocks();
				if($view == 'block'){
					$block = $this->getOverride($option);					
					$answer = $block->call('_'.$action, array($data));//bPanel::$blocks[$data['name']]->call($data['action'],array($data));
				}
				break;
		}
		
		
		return ($this->data['blib']=='bPanel')?json_encode($answer):$answer;
	}
	

	
	private function showBlocks(){
		return array("block"=>__class__, "elem"=>"blocks", "content"=>bPanel::$blocks);
	}
	
	private function getOverride($option){
		$temp = new $option();
		$override = $option.'__'.__class__;
		if(bPanel::$blocks[$option]){$temp->setParent($override, array());}
		$temp->setParent(__class__, array());
		return $temp;
	}
	
	private function scanBlocks(){
		$arr = opendir('b');
		$temp = array();
		while($v = readdir($arr)){
			if($v == '.' or $v == '..' or $v == 'bBlib') continue;
			$name = $v.'__'.__class__;
			$path = $this->path($name,'php');
			$temp[$v] = (file_exists($path)?true:false);
		}
		bPanel::$blocks = $temp;
	}
	
	protected function getData($data){
		return $data;
	}
	
	public function _show($data = array(), $caller = null){
		var_dump($data, $caller);
		if($caller == null){return;}
		
	}
}