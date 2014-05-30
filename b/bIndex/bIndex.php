<?php
defined('_BLIB') or die;

class bIndex extends bBlib{	
	
	protected function inputSelf(){
		$this->version = '1.0.0';
	}
	
	public function output(){
		require_once("b/bIndex/bIndex.html");
	}
	
}