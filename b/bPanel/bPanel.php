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
		bPanel::$blocks = $this->getAdminBlocks();
	}
	
	public function output(){
		$a = &$this->getTunnel();
		var_dump($a);
		$a['ttt']='aaa';
		$a = $this->getTunnel();
		var_dump($a);
		
		$bPanel = $this->data['bPanel'] || array();
		$action = ($bPanel['action']?$bPanel['action']:"show");
		$view = ($bPanel['view']?$bPanel['view']:"blocks");
		$answer = array();
		
		switch($action){
			case "show":
				if($view == 'blocks')$answer = $this->showBlocks();				
				break;
			default:
				break;
		}
		
		$answer = array("block"=>__class__, "mods"=>array("view"=>$view, "action"=>$action), "content"=>array($answer));
		return ($this->data['blib']=='bPanel')?json_encode($answer):$answer;
	}
	

	
	private function showBlocks(){
		$keys = array_keys(bPanel::$blocks);
		$temp = array();
		foreach($keys as $key => $value){
			$temp[] = array("elem"=>"blockLink", "content"=>$value);
		}

		return array("block"=>__class__, "elem"=>"blocks", "content"=>$temp);
	}
	

	
	private function getAdminBlocks(){
		$arr = opendir('b');
		$temp = array();
		while($v = readdir($arr)){
			if($v == '.' or $v == '..' or $v == 'bBlib') continue;
			$name = $v.'__'.__class__;
			$path = $this->path($name,'php');
			$name = (file_exists($path)?$name:'bPanel__default');
			$temp[$v] = new $name();
		}
		return $temp;
	}
	
	protected function getData($data){
		return $data;
	}
}