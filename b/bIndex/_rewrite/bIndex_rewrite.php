<?php
defined('_BLIB') or die;

class bIndex_rewrite extends bBlib{	
	
	protected function inputSelf(){
		$this->version = '1.0.0';
		$this->parents = array('bSystem', 'bDatabase');
	}

	public function getData($data, $caller = null){
		
		$url = parse_url($_SERVER['REQUEST_URI']);
		
		$Q = array(
			'select' => array(
				'bIndex_rewrite' => array('data')
			),
			'where' => array(
				'bIndex_rewrite' => array(
					'url' =>  $url['path']
				)
			)
		);
		
		$result = $this->_query($Q);
		$row = $result->fetch();
		$rewrite = (array)json_decode($row['data'], true);

		return array(
			'output'=>array_merge($data['input'][0], $rewrite)
		);
	}
	

}