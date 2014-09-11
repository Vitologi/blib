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
	final public function setTemplate($value){$this->template = $value;}	
	final public function setModule($name, $value){$this->module[$name] = $value;}
	public function getController(){return $this->controller;}
	public function setController($value){return $this->controller = $value;}
	public function getLayout(){return $this->layout;}
	public function setLayout($value){return $this->layout = $value;}
	public function getView(){return $this->view;}
	public function setView($value){return $this->view = $value;}
	
	
	protected function inputSelf(){
		$this->version = '1.0.0';
		$this->bTemplate__dynamic = true;
		$this->parents = array('bTemplate');
	}
	
	protected function input($data, $caller){
		$this->data = $data;
		$this->caller = $caller;
		$tunnel = ($caller)?$caller->getTunnel():$this->getTunnel();

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
		$this->setTemplate($this->_getTemplateByName('template')); //default template
		
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
	

	
	final public function showBlocks(){
		return array("block"=>__class__, "elem"=>"blocks", "content"=>bPanel::$blocks);
	}
	
	final public function showError($text = "Module is not defined"){
		return array("block"=>__class__, "elem"=>"error", "content"=>$text);
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
		
		if(is_array($this->template))$this->template = json_encode($this->template);
		
		foreach($this->module as $key =>$value){
			if(is_array($value))$this->module[$key] = json_encode($value);
		}		
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
						$pannel->setModule('"{1}"', $pannel->showBlocks());
						$pannel->setModule('"{2}"', $pannel->showError('tools'));
						$pannel->setModule('"{3}"', $pannel->showError('operation'));
						$pannel->setModule('"{4}"', $pannel->showError('content'));
						break;
				}
				break;
		}
	}
	
	
}