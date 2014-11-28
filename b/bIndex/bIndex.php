<?php
defined('_BLIB') or die;

class bIndex extends bBlib{	
	
	protected function inputSelf(){
		$this->version = '1.0.0';
		$this->parents = array(/* 'bRewrite', 'bRbac' */ 'bSystem', 'bDatabase', 'bConfig', 'bCssreset', 'bTemplate');
	}
	
	protected function input($data, $caller){
		//block`s config
		$this->rewrite = true;
		if($this->rewrite)$this->setParent('bRewrite', $data);
		
		$this->cache = 0;
		$this->defaultPage = 1;
		$this->pageNo = $this->defaultPage;
		$this->skeleton = "bIndex__skeleton_default";
		
		//input data
		$data = $this->hook('getData', array($data));
		$this->local['pageNo'] = isset($data['pageNo'])?$data['pageNo']:$this->pageNo;
		
		//page`s config
		$default = array(
			"cache"			=> $this->cache,
			"skeleton"		=> $this->skeleton,
			"ajax"			=> false,
			"pageNo"		=> $this->pageNo,
			"locked"		=> false,
			"'{keywords}'"	=> "",
			"'{description}'"=> "",
			"'{title}'"		=> ""			
		);
		
		$config = $this->_getConfig($this->pageNo);
		$this->data = bBlib::extend($default, $data, $config);

	}

	
	public function output(){
		
		if(!$this->hook('checkAccess', array($this->data)))die(json_encode(array("container"=>"body", "content"=>"not access")));
		
		$Q = array(
			'select'	=> array(
				'bindex' => array('template', 'bcategory_id')
			),
			'where' => array(
				'bindex' => array('id'=>$this->data['pageNo'])
			)
		);
		
		if(!$result = $this->_query($Q)){throw new Exception('Can`t get chousen page ('.$this->data['pageNo'].').');}
		$row = $result->fetch();
		
		
		$data["'{keywords}'"] = $this->data["'{keywords}'"];
		$data["'{description}'"] = $this->data["'{description}'"];
		$data["'{title}'"] = $this->data["'{title}'"];
		$template = json_decode($row['template'], true);
		$data["'{template}'"] = $this->_getTemplateDiff(false, $template);
		
		if($this->data['ajax']){
			header('Content-Type: application/json; charset=UTF-8');
			$temp = json_decode($data["'{template}'"], true);
			$temp['ajax'] = true;
			echo json_encode($temp);
			exit;
		}else{
			$skeleton = file_get_contents($this->path($this->data['skeleton'],'tpl'));
			echo str_replace(array_keys($data), array_values($data), $skeleton);
		}

	}

	protected function getData($data){
		return $data;
	}
	
	protected function checkAccess($data){
		if($data['locked']){
			$this->setParent('bRbac', $data);
			if(!$this->_checkAccess('unlock',$data['pageNo']))return false;
		}
		return true;
	}
	
	
	
	public function _install($data = array(), $caller = null){
		if($caller !== null){return bDatabase::_install($data, $caller);};
		$this->_setConfig('bIndex', $this->_getDefaultConfig(), array('group'=>'blib'));
		$this->_setConfig('uncategorised', $this->_getDefaultConfig('item'));
		return bDatabase::_install($data, $this);;
	}
}