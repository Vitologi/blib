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
				'brewrite' => array('pageno')
			),
			'where' => array(
				'brewrite' => array(
					'url' =>  $url['path']
				)
			)
		);
		
		$result = $this->_query($Q);
		$row = $result->fetch();

		return isset($row['pageno'])?array('pageNo'=>$row['pageno']):array();
	}

}