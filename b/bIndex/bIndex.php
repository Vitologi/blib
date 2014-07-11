<?php
defined('_BLIB') or die;

class bIndex extends bBlib{	
	
	protected function inputSelf(){
		$this->version = '1.0.0';
		$this->parents = array('bSystem', 'bDatabase', 'bConfig', 'bCssreset', 'bTemplate');
	}
	
	protected function input($data, $caller){
		$this->data = $this->hook('getData', array($data));
		$this->defaultPage = 1;
		if(!$this->data['pageId']){$this->local['data']['pageId'] = $this->defaultPage;}
		$this->ajax = $data['ajax'];
		$this->template = json_decode($data['template'],true);
		$this->skeleton = "bIndex__skeleton_default";
		$this->cache = 0;		
	}
	
	public function output(){
		

		$Q = array(
			'select'	=> array(
				'bIndex' => array('template', 'bConfig_id', 'bCategory_id')
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
		$template = json_decode($row['template'], true);
		
		if($this->template[0] === $template[0]){
			$template = $this->templateDiff($this->template, $template);
		}
		
		$data["'{template}'"] = $this->_getTemplate($template);
		
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
	
	protected function templateDiff($old, $new) {
		if($old[0] !== $new[0]){return $new;}
		$difference=array($new[0]);
		foreach($new as $key => $value) {
			if( is_array($value) ) {
				if( !isset($old[$key]) || !is_array($old[$key]) ) {
					$difference[$key] = $value;
				} else {
					$new_diff = $this->templateDiff($old[$key], $value);
					if( !empty($new_diff) )
						$difference[$key] = $new_diff;						
				}
			} else if( !array_key_exists($key,$old) || $old[$key] !== $value ) {
				$difference[$key] = $value;
			}
			unset($old[$key]);
		}
		
		foreach($old as $key => $value) {
			$difference[$key] = null;
		}
		
		return $difference;
	}
	
	
	protected function getData($data){
		return $data;
	}
	
	/*
	private function setbIndex(){
		$newConfig = $this->_setConfig($this->data['pageId'], $this->_getDefaultConfig('item'));
		var_dump($newConfig, $this->_getConfig($this->data['pageId']));
	}
	*/
	
	public function _install($data = array(), $caller = null){
		if($caller !== null){return $caller->local['bDatabase']->install;}
		$this->_setConfig('bIndex', $this->_getDefaultConfig(), array('group'=>'blib'));
		$this->_setConfig('uncategorised', $this->_getDefaultConfig('item'));
		return $this->local['bDatabase']->install;
	}
}