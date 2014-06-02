<?php
defined('_BLIB') or die;

class bIndex extends bBlib{	
	
	protected function inputSelf(){
		$this->version = '1.0.0';
		$this->parents = array('bSystem', 'bDatabase', 'bConfig', 'bCssreset', 'bTemplate');
	}
	
	protected function input($data, $caller){
		$this->caller = $caller;
		$this->skeleton = "bIndex__skeleton_default";
		$this->cache = 0;
		$this->ajax = $data['ajax'];
	}
	
	public function output(){
		
		$this->data = json_decode($this->_getTemplate(1), true);
		
		
		if($this->ajax){
			header('Content-Type: application/json; charset=UTF-8');
			$data = $this->data;
			$data['ajax'] = true;
			$data['content'][0] = null;
			echo json_encode($data);
			exit;
		}else{
			
			
			$skeleton = file_get_contents($this->path($this->skeleton,'tpl'));
			$needle = array(
				"'{keywords}'",
				"'{description}'",
				"'{title}'",
				"'{data}'"
			);
			$replace = array(
				$this->keywords,
				$this->description,
				$this->title,
				json_encode($this->data)
			);

			echo str_replace($needle, $replace, $skeleton);
		}
		
	}
	
}