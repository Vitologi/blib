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
		$this->skeleton = "bIndex__skeleton_default";
		$this->cache = 0;		
	}
	
	public function output(){
		
		$Q = array(
			'select'	=> array(
				'bIndex' => array('meta', 'bTemplate_id', 'bCategory_id')
			),
			'where' => array(
				'bIndex' => array('id'=>$this->data['pageId'])
			)
		);
		
		$result = $this->_query($Q);
		$row = $result->fetch();
		$data = json_decode($row['meta'], true);
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
	
}