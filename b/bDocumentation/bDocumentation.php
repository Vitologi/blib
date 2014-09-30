<?php
defined('_BLIB') or die;

class bDocumentation extends bBlib{	
	
	protected function inputSelf(){
		$this->version = '1.0.0';
		$this->parents = array('bSystem', 'bDatabase', 'bConfig');
	}
	
	protected function input($data, $caller){
		$this->data = $this->hook('getData', array($data));
	}
	
	public function output(){

		$answer = array(
			'block' => __class__,
			'mods' => isset($this->data['mods'])?$this->data['mods']:array()
		);

		if(isset($this->data['id'])){
		
			$Q = array(
				'select'	=> array(
					'bdocumentation' => array('id', 'name', 'description' ,'group', 'parent'=>'bdocumentation_id')
				),
				'where' => array(
					'bdocumentation' => array('id'=>$this->data['id'])
				)
			);
			$result = $this->_query($Q);
			$temp = $result->fetch(PDO::FETCH_ASSOC);
			$temp['description'] = json_decode($temp['description']);
			$group = json_decode($temp['group']);
			$content = array();
			unset($temp['group']);
			
			if($group){
				foreach($group as $id){
					$where[] = array('id',$id,false,true);
				}
				$Q = array(
					'select'	=> array(
						'bdocumentation' => array('id', 'name', 'note', 'parent'=>'bdocumentation_id')
					),
					'where' => array(
						'bdocumentation' => $where
					)
				);
				$result = $this->_query($Q);
				$content = $result->fetchALL(PDO::FETCH_ASSOC);
			}
			
			$temp['content'] = $content;
			$answer['item'] = $temp;
		}
		
		if(isset($this->data['chapter'])){
			$Q = array(
				'select'	=> array(
					'bdocumentation' => array('id', 'name', 'parent'=>'bdocumentation_id')
				)
			);
			$result = $this->_query($Q);
			$navigation = $result->fetchALL(PDO::FETCH_ASSOC);
						
			$answer['navigation'] = $navigation;
			$answer['chapter'] = $this->data['chapter'];
		}
		
		if(isset($this->data['ajax'])){
			echo json_encode($answer);
		}else{
			return $answer;
		}
		
	}
	
	protected function getData($data){
		return $data;
	}
	
}