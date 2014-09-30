<?php
defined('_BLIB') or die;

class bSlider extends bBlib{	
	
	protected function inputSelf(){
		$this->version = '1.0.0';
		$this->parents = array('bDatabase', 'bTemplate');
	}
	
	protected function input($data, $caller){
		$this->caller = $caller;
		$this->data = $data;
		if(!isset($this->data['length']))$this->local['data']['length'] = 0;
		if(!isset($this->data['delay']))$this->local['data']['delay'] = 10000;
	}
	
	public function output(){
		
		if(!$this->data['id']){return array();}
		
		$Q = array(
			'select'	=> array(
				'bslider' => array(),
				'btemplate' => array('template')
			),
			'where' => array(
				'bslider' => array('id'=>$this->data['id'])
			),
			'sql'=>' LIMIT '.$this->data['length'].', 5'
		);
		$result = $this->_query($Q);

		$slider = array();
		
		while($row = $result->fetch()){
			$slider[] = array('elem'=>'slide', 'content'=>array(json_decode($row['template'])));
		}

		return array('block'=>__class__, 'mods'=>$this->data['mods'], 'delay'=>$this->data['delay'], 'content'=>$slider);
		
	}
	
}