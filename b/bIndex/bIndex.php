<?php
defined('_BLIB') or die;

class bIndex extends bBlib{	
	
	protected function inputSelf(){
		$this->version = '1.0.0';
		$this->parents = array('bSystem', 'bDatabase', 'bConfig', 'bCssreset');
	}
	
	protected function input($data, $caller){
		$this->caller = $caller;
		$this->template = "bIndex__template_default";
		$this->cache = 0;
		$this->rewrite = $data['rewrite'];
	}
	
	public function output(){
		
		if($this->rewrite){
			header('Content-Type: application/json; charset=UTF-8');
			echo '
				{"content":[
					{"elem":"header", "content":"changed"},
					{"elem":"footer", "content":"changedf"}
				]}
			';
			exit;
		}
		
		
		
		
		$template = file_get_contents($this->path($this->template,'tpl'));
		$needle = array(
			'{keywords}',
			'{description}',
			'{title}',
			'{data}'
		);
		$replace = array(
			$this->keywords,
			$this->description,
			$this->title,
			'{test:1}'
		);

		echo str_replace($needle, $replace, $template);
	}
	
}