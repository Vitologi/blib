<?php
defined('_BLIB') or die;

class bForm extends bBlib{	
	
	protected function inputSelf(){
		$this->version = '1.112.1';
		$this->parents = array('bSystem', 'bDatabase', 'bSession', 'bConfig');
	}
	
}

