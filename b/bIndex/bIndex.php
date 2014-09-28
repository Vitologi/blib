<?php
defined('_BLIB') or die;

class bIndex extends bBlib{	
	
	protected function inputSelf(){
		$this->version = '1.0.0';
		$this->parents = array(/* 'bRbac' */ 'bSystem', 'bDatabase', 'bConfig', 'bCssreset', 'bTemplate');
	}
	
	protected function input($data, $caller){
		$this->data = $this->hook('getData', array($data));
		$this->defaultPage = 1;
		if(!$this->data['pageId']){$this->local['data']['pageId'] = $this->defaultPage;}
		$this->ajax = $data['ajax'];
		$this->template = $data['template'];
		$this->skeleton = "bIndex__skeleton_default";
		$this->cache = 0;		
	}

	
	public function output(){
		
		$config = $this->_getConfig($this->data['pageId']);
		
		if($config['locked']){
			$this->setParent('bRbac', $this->data);
			if(!$this->_checkAccess('unlock',$this->data['pageId']))return;
		}
		
		$Q = array(
			'select'	=> array(
				'bIndex' => array('template', 'bCategory_id')
			),
			'where' => array(
				'bIndex' => array('id'=>$this->data['pageId'])
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