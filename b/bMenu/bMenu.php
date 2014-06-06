<?php
defined('_BLIB') or die;

class bMenu extends bBlib{	
	
	protected function inputSelf(){
		$this->version = '1.0.0';
		$this->parents = array('bSystem', 'bDatabase', 'bConfig');
	}
	
	protected function input($data, $caller){
		$this->data = $this->hook('getData', array($data));
		$this->defaultPage = 1;
		if(!$this->data['pageId']){$this->local['data']['pageId'] = $this->defaultPage;}
	}
	
	public function output(){
		
		return array(
			"block"=>"bMenu",
			"content"=>"excelent menushka"
		);
		
		
		
		$Q = array(
			'select'	=> array(
				'bIndex' => array('bConfig_id', 'bTemplate_id', 'bCategory_id')
			),
			'where' => array(
				'bIndex' => array('id'=>$this->data['pageId'])
			)
		);
		
		$result = $this->_query($Q);
		$row = $result->fetch();
		$config = $this->_getConfig($this->data['pageId']);
		
		$data["'{keywords}'"] = $config["'{keywords}'"];
		$data["'{description}'"] = $config["'{description}'"];
		$data["'{title}'"] = $config["'{title}'"];
		$data["'{template}'"] = $this->_getTemplate($row['bTemplate_id']);
		
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
	
	private function setbIndex(){
		$newConfig = $this->_setConfig($this->data['pageId'], $this->_getDefaultConfig());
		var_dump($newConfig, $this->_getConfig($this->data['pageId']));
	}
	
	public function _install($data = array(), $caller = null){
		if($caller !== null){return $caller->local['bDatabase']->install;}
		
		//var_dump($this->_getDefaultConfig('block'));
		$this->_setConfig('bIndex', $this->_getDefaultConfig('block'), array('group'=>'blib', 'correct'=>false));
		$this->_setConfig('uncategorised', $this->_getDefaultConfig(), array('correct'=>false));
		return $this->local['bDatabase']->install;
	}
}