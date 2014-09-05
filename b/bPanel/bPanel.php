<?php
defined('_BLIB') or die;

class bPanel extends bBlib{	
	
	private static $blocks = null;
	private $template = '{}';
	private $module = array();
	private $controller = "bPanel";
	private $layout = "show";
	private $view = "error";
	
	//getters & setters
	public function getController(){return $this->controller;}
	public function getLayout(){return $this->layout;}
	public function getView(){return $this->view;}
	public function setController($value){return $this->controller = $value;}
	public function setLayout($value){return $this->layout = $value;}
	public function setView($value){return $this->view = $value;}
	
	
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
		
		if(bPanel::$blocks == null)$this->scanBlocks(); //filling blocks stack
		$this->setPanelTemplate($this->_getTemplateByName('template')); //default template
		
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
	

	
	public function showBlocks(){
		return array("block"=>__class__, "elem"=>"blocks", "content"=>bPanel::$blocks);
	}
	
	public function showError(){
		return array("block"=>__class__, "elem"=>"error", "content"=>"404");
	}
	
	public function setPanelTemplate($value){
		if(is_array($value))$value = json_encode($value);
		$this->template = $value;
	}
	
	public function setPanelModule($name, $value){
		if(is_array($value))$value = json_encode($value);
		$this->module[$name] = $value;	
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

	
	private function assembly(){
		return json_decode(str_replace(array_keys($this->module), array_values($this->module), $this->template),true);
	}
	

	
	/** COMPILING ADMIN PANEL */
	public function _assembly($data = array(), $caller = null){
		if($caller == null)return $this->assembly();
		return $caller->local['bPanel']->_assembly();
	}
	
	public function _controller($data = array(), $caller = null){
		$block = ($caller == null)?$this:$caller;
		$pannel = ($caller == null)?$this:$caller->local['bPanel'];
		
		switch($pannel->getLayout()){
			case "show":
			default:
				
				switch($pannel->getView()){
					case "error":
					default:
						$pannel->setPanelModule('"{1}"', $pannel->showBlocks());
						$pannel->setPanelModule('"{2}"', $pannel->showError());
						break;
				}
				break;
		}
	}
	
	
}