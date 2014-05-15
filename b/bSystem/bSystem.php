<?php
defined('_BLIB') or die;

class bSystem extends bBlib{	
	
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
	
	public function getMinion($name, $caller = null){
		if($caller === null){return;}
		$localInstall = $caller->getBlockPath().'/__'.$name[0].'/'.$name[1].'.php';

		if(file_exists($localInstall)){
			return require($localInstall);
		}elseif($caller->$name[1]){
			return $caller->$name[1];
		}else{
			return null;
		}
	}
	
}