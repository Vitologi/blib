<?php
defined('_BLIB') or die;

class bTemplate extends bBlib{	
	
	protected function inputSelf(){
		$this->version = '1.0.0';
		$this->parents = array('bSystem', 'bDatabase', 'bConfig');
	}
	
	protected function input($data, $caller){
		$this->caller = $caller;
		$this->type = 'array';
		$this->stack = array();
	}
	
	public function output(){
		return array(
			'bTemplate'=>$this
		);
	}
	
	private function addTempStack($id){
		if(!is_array($id)){
			$id = array(array('id', $id));
		}else{
			foreach($id as $key =>$value){
				$id[$key]=array('id', $value, '=', true);
			}
		}
		
		$Q = array(
			'select'	=> array(
				'bTemplate' => array('id', 'template', 'involved', 'name')
			),
			'where'		=> array(
				'bTemplate' => $id
			)
		);
		
		$result = $this->_query($Q);

		while($row = $result->fetch()){
			$this->local['stack']['"{'.$row['id'].'}"'] = $row['template'];
			$involved = ($row['involved']!==null)?explode(',', $row['involved']):false;
			if($involved){ $this->addTempStack($involved);}
		}
		
	}
	
	private function glueTempStack($template){
		$temp = str_replace(array_keys($this->stack), array_values($this->stack), $template);
		return (preg_match('/"{\S}"/', $temp))?$this->glueTempStack($temp):$temp;
	}
	
	public function _getTemplate($id, $caller = null){
		if($caller !== null){return $caller->local['bTemplate']->_getTemplate($id[0]);};
		$this->addTempStack($id);
		return $this->glueTempStack($this->stack['"{'.$id.'}"']);
	}
}