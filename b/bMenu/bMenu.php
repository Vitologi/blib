<?php
defined('_BLIB') or die;

class bMenu extends bBlib{	
	
	protected function inputSelf(){
		$this->version = '1.0.0';
		$this->parents = array('bSystem', 'bDatabase', 'bConfig');
	}
	
	protected function input($data, $caller){
		$this->data = $this->hook('getData', array($data));
	}
	
	public function output(){
	
		if(!$this->data['menu']){return array();}
		
		$Q = array(
			'select'	=> array(
				'bMenu' => array('id', 'name', 'link' ,'bConfig_id' ,'bMenu_id')
			),
			'where' => array(
				'bMenu' => array('menu'=>$this->data['menu'])
			)
		);
		$result = $this->_query($Q);
		
		$menu = array();
		
		while($row = $result->fetch()){
			if($row['bConfig_id']){$config = $this->_getConfig($row['bConfig_id']);}
			$menu[] = array('id'=>$row['id'], 'name'=>$row['name'], 'config'=>$config, 'link'=>$row['link'], 'parent'=>$row['bMenu_id']);
		}

		return array('block'=>__class__, 'mods'=>$this->data['mods'], 'content'=>$menu);
	}
	
	protected function getData($data){
		return $data;
	}
	
}