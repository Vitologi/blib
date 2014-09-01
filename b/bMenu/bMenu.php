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
				'bmenu' => array('id', 'name', 'link' ,'bconfig_id' ,'bmenu_id')
			),
			'where' => array(
				'bmenu' => array('menu'=>$this->data['menu'])
			),
			'sql'=>' ORDER BY `id` ASC'
		);
		$result = $this->_query($Q);
		
		$menu = array();
		
		while($row = $result->fetch()){
			if($row['bconfig_id']){$config = $this->_getConfig($row['bconfig_id']);}
			$menu[] = array('id'=>$row['id'], 'name'=>$row['name'], 'config'=>$config, 'link'=>$row['link'], 'parent'=>$row['bmenu_id']);
		}

		return array('block'=>__class__, 'mods'=>$this->data['mods'], 'content'=>$menu, 'id'=>$this->data['menu']);
	}
	
	protected function getData($data){
		return $data;
	}
	
}