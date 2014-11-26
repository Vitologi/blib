<?php
defined('_BLIB') or die;

class bAnnounces extends bBlib{

	protected function inputSelf(){
		$this->version = '1.0.0';
		$this->parents = array('bDatabase');
	}
	
	protected function input($data, $caller){
		$this->data = $data;
	}
	
	public function output(){
		$answer = $this->hook('getAnnounces', array($this->data));
		header('Content-Type: application/json; charset=UTF-8');
		echo json_encode($answer);
		exit;
	}
	
	public function getAnnounces($data){
		
		$params = array('count'=>0, 'limit'=>8);
		$params = bBlib::extend($params, $data);
		
		$sql = sprintf('ORDER BY `id` DESC LIMIT %1$d, %2$d',$params['count'], $params['limit']);
		
		$Q = array(
			'select'=>array(
				'bannounces'=>array('id', 'date', 'title', 'content')
			),
			'where'=>array(
				'bannounces'=>array('published'=>'1')
			),
			'sql'=>$sql
		);
		
		$result = $this->_query($Q);
		$answer = $result->fetchAll();
		
		return $answer;
	}

}