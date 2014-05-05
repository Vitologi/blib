<?php
defined('_BLIB') or die;

class bSystemAlias extends bBlib{	
	
	protected function inputSelf(){
		$this->version = '1.0.0';
	}
	
	protected function input($data, $caller){
		$this->caller = $caller;
	}
	
	public function output(){
		
	}
	
	
	public function getBlockName($data, $caller){
		return get_class($caller);
	}
	
	public function getBlockPath($data, $caller){
		$block = get_class($caller);
		$path = sprintf('%1$s/%2$s',$block{0},$block);
		return $path;
	}
	
}