<?php
defined('_BLIB') or die;

class bRewrite extends bBlib{	
	
	protected function inputSelf(){
		$this->version = '1.0.0';
		$this->parents = array('bSystem', 'bDatabase');
	}
	
	protected function input($data, $caller){
		
	}

	
	public function output(){
		$url = parse_url($_SERVER['REQUEST_URI']);
		
		$Q = array(
			'select' => array(
				'brewrite' => array('bindex_id')
			),
			'where' => array(
				'brewrite' => array(
					'url' =>  $url['path']
				)
			)
		);
		
		$result = $this->_query($Q);
		$row = ($result)?$result->fetch():array();

		return isset($row['bindex_id'])?array('pageNo'=>$row['bindex_id']):array();
	}

}