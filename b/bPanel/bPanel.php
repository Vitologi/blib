<?php
defined('_BLIB') or die;

class bPanel extends bBlib{	
	
	private static $blocks = null;
	private $controller = "bPanel";
	private $layout = "show";
	private $view = "error";
	private $template = '{}';
	private $module = array();
	
	protected function inputSelf(){
		$this->version = '1.0.0';
		$this->bTemplate__dynamic = true;
		$this->parents = array('bTemplate');
	}
	
	protected function input($data, $caller){
		$this->data = $data;
		$this->caller = $caller;
		$tunnel = $this->getTunnel();
		
		if($this->data['controller']){
			$this->controller = $this->data['controller'];
		}elseif($tunnel['controller']){
			$this->controller = $tunnel['controller'];
		}
		
		if($this->data['layout']){
			$this->layout = $this->data['layout'];
		}elseif($tunnel['layout']){
			$this->layout = $tunnel['layout'];
		}
		
		if($this->data['view']){
			$this->view = $this->data['view'];
		}elseif($tunnel['view']){
			$this->view = $tunnel['view'];
		}
		
		if(bPanel::$blocks == null)$this->scanBlocks();
	}
	
	public function output(){
		if($this->caller)return array('bPanel'=>$this);


		$block = ($this->controller == "bPanel")?$this:$this->getOverride($this->controller);
		$block->_controller();
		$temp = $block->_assembly();
		
		$answer = array('block'=>'bPanel', 'mods'=>array("style"=>"default"), "content"=>array($temp));
		
		
		
		if($this->data['blib']=='bPanel'){
			header('Content-Type: application/json; charset=UTF-8');
			echo json_encode($answer);
			exit;
		}else{
			return $answer;
		}

	}
	

	
	private function showBlocks(){
		return array("block"=>__class__, "elem"=>"blocks", "content"=>bPanel::$blocks);
	}
	private function showError(){
		return array("block"=>__class__, "elem"=>"error", "content"=>"404");
	}
	
	//extend block to admin function
	private function getOverride($controller){
		$temp = new $controller();
		$override = $controller.'__'.__class__;
		if(bPanel::$blocks[$controller]){$temp->setParent($override, array());}
		$temp->setParent(__class__, array());
		return $temp;
	}
	
	//get all installed blocks
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
	
	private function controller(){
		if($this->layout == "show"){
			if($this->view == "error"){
				$this->template = $this->_getTemplateByName('template');
				$this->module['"{1}"'] = json_encode($this->showBlocks());
				$this->module['"{2}"'] = json_encode($this->showError());
			}
			
		}
	}
	
	private function assembly(){
		return json_decode(str_replace(array_keys($this->module), array_values($this->module), $this->template),true);
	}
	
	protected function setPanelTemplate($name, $value){
		$this->template = $this->_getTemplateByName($name);
	}
	
	protected function setPanelModule($name, $value){
		$this->module[$name] = $value;	
	}
	
	/** COMPILING ADMIN PANEL */
	public function _assembly($data = array(), $caller = null){
		if($caller == null)return $this->assembly();
		return $caller->local['bPanel']->_assembly();
	}
	
	public function _controller($data = array(), $caller = null){
		if($caller == null)return $this->controller();
		return $caller->local['bPanel']->_controller();
	}
	
	public function _setPanelTemplate($data = array(), $caller = null){
		if($caller !== null)return $caller->local['bPanel']->call('setPanelTemplate',$data);					
	}
	
	public function _setPanelModule($data = array(), $caller = null){
		if($caller !== null)return $caller->local['bPanel']->call('setPanelModule',$data);
	}
	
}