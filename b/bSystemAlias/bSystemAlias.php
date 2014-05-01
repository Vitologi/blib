<?php
defined('_BLIB') or die;

class bSystemAlias extends bBlib{	
	
	protected function inputSelf(){
		$this->version = '1.0.0';
	}
	
	protected function inputUser($data){
		$this->caller = $data['caller'];
	}
	
	public function outputParents(){
		
		$block = get_class($this->caller);
		$path = sprintf('%1$s/%2$s',$block{0},$block);
		
		return array(
			'block'	=>	$block,
			'path'	=>	$path
		);
	}
	
}