<?php
defined('_BLIB') or die;

class bIndex extends bBlib{	
	
	protected function inputSelf(){
		$this->version = '1.0.0';
		$this->parents = array(/* 'bRbac' */ 'bSystem', 'bDatabase', 'bConfig', 'bCssreset', 'bTemplate');
	}
	
	protected function input($data, $caller){
		$this->cache = 0;
		$this->defaultPage = 1;
		$this->skeleton = "bIndex__skeleton_default";
		
		$data = $this->hook('getData', array($data));
		$default = array('ajax'=>false, 'template'=>array(), 'pageId'=>$this->defaultPage);

		$this->data = bBlib::extend($default, $data);

		$this->ajax = $this->data['ajax'];
		$this->template = $this->data['template'];
		
	}

	
	public function output(){

		$config = array("'{keywords}'"=>"","'{description}'"=>"","'{title}'"=>"") + (array) $this->_getConfig($this->data['pageId']);
		
		if(isset($config['locked'])){
			$this->setParent('bRbac', $this->data);
			if(!$this->_checkAccess('unlock',$this->data['pageId']))return;
		}
		
		$Q = array(
			'select'	=> array(
				'bindex' => array('template', 'bcategory_id')
			),
			'where' => array(
				'bindex' => array('id'=>$this->data['pageId'])
			)
		);
		
		$result = $this->_query($Q);
		$row = $result->fetch();
		
		
		$data["'{keywords}'"] = $config["'{keywords}'"];
		$data["'{description}'"] = $config["'{description}'"];
		$data["'{title}'"] = $config["'{title}'"];
		$template = json_decode($row['template'], true);
		
		$data["'{template}'"] = $this->_getTemplateDiff($this->template, $template);
		
		if($this->ajax){
			header('Content-Type: application/json; charset=UTF-8');
			$temp = json_decode($data["'{template}'"], true);
			$temp['ajax'] = true;
			echo json_encode($temp);
			exit;
		}else{
			$skeleton = file_get_contents($this->path($this->skeleton,'tpl'));
			echo str_replace(array_keys($data), array_values($data), $skeleton);
		}

	}

	protected function getData($data){
		return $data;
	}
	
	public function _install($data = array(), $caller = null){
		if($caller !== null){return $caller->local['bDatabase']->install;}
		$this->_setConfig('bIndex', $this->_getDefaultConfig(), array('group'=>'blib'));
		$this->_setConfig('uncategorised', $this->_getDefaultConfig('item'));
		return $this->local['bDatabase']->install;
	}
}