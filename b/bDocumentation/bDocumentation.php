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

		$answer = array();
		
		if($this->data['id']){
		
			$Q = array(
				'select'	=> array(
					'bDocumentation' => array('id', 'name', 'description' ,'group', 'parent'=>'bDocumentation_id'),
					'bDocumentation__group' => array('groupName'=>'name'),
				),
				'where' => array(
					'bDocumentation' => array('id'=>$this->data['id'])
				)
			);
			$result = $this->_query($Q);
			$answer = $result->fetch(PDO::FETCH_ASSOC);
			$answer['description'] = json_decode($answer['description']);
			$group = json_decode($answer['group']);
			unset($answer['group']);
			
			if($group){
				foreach($group as $id){
					$where[] = array('id',$id,false,true);
				}
				$Q = array(
					'select'	=> array(
						'bDocumentation' => array('id', 'name', 'note'),
						'bDocumentation__group' => array('groupName'=>'name'),
					),
					'where' => array(
						'bDocumentation' => $where
					)
				);
				$result = $this->_query($Q);
				$content = $result->fetchALL(PDO::FETCH_ASSOC);
			}
			
			$answer['block'] = __class__;
			$answer['mods'] = $this->data['mods'];
			$answer['content'] = $content;
			$answer['id'] = $this->data['id'];
		}
		
		if($this->data['group']){
			$Q = array(
				'select'	=> array(
					'bDocumentation__group' => array('id', 'name', 'group'=>'bDocumentation__group_id')
				)
			);
			$result = $this->_query($Q);
			$navigation = $result->fetchALL(PDO::FETCH_ASSOC);
			
			$Q = array(
				'select'	=> array(
					'bDocumentation' => array('id', 'name', 'group'=>'bDocumentation__group_id')
				)
			);
			$result = $this->_query($Q);
			$content = $result->fetchALL(PDO::FETCH_ASSOC);
			
			$answer['group'] = array(
				'navigation'=>$navigation,
				'content'=>$content,
				'start'=>$this->data['group']
			);
		}
		
		return $answer;
		
	}
	
	protected function getData($data){
		return $data;
	}
	
}