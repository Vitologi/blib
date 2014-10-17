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
	
	public static function _getBlockName($data, $caller){
		return get_class($caller);
	}
	
	public static function _getBlockPath($data, $caller){
		$block = get_class($caller);
		$path = sprintf('%1$s/%2$s',$block{0},$block);
		return $path;
	}
	
	public static function _getMinion($name, bBlib $caller){
		
		$block = $caller->_getBlockName();
		$path = $caller->_getBlockPath();
		
		$localInstall = sprintf('%1$s/__%3$s/%2$s__%3$s_%4$s.php', $path, $block, $name[0], $name[1]);

		if(file_exists($localInstall)){
			return require($localInstall);
		}else{
			return null;
		}
	}
	
}