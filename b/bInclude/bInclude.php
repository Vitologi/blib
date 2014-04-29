<?php
defined('_BLIB') or die;

class bInclude extends bBlib{	
	
	protected function input($data){
		$this->local['parent'] = array('bIncludePseudo', 'bIncludePseudo2');
		$this->local['cache'] = $this->path."/__cache/bInclude__cache.ini";
		$this->local['list'] = (array)$data['list'] or array();
	}	
	
	public function output(){
		
		//get name
		$name = $this->getCacheName();
		
		if(!file_exists($this->path.'/__cache/'.$name.'.js')){
			$this->setCache($name, $this->list);
		}
		
		//get version
		$version = (file_exists($this->cache)?filemtime($this->cache):0);
		
		//get list
		$ini = @file_get_contents($this->cache);
		$ini = (array)json_decode($ini);
		$list = $ini[$name];
		
		$answer = array(
			"version"	=> $version,
			"name"		=> $name,
			"list"		=> $list
		);
		
		header('Content-type: application/json');
		echo json_encode($answer);
		exit;
	}
	
	private function getCacheName(){
		$arr = $this->list;
		sort($arr);
		return md5(implode("",$arr));
	}

	private function setCache($name, $list){
		
		$cache = scan('b', '*.css', $list);
		$css = @fopen ($this->path.'/__cache/'.$name.'.css', "w");
		@fwrite ($css, $cache['code']);
		@fclose ($css);
		
		
		$cache = scan('b','*.js', $list);
		$js = @fopen ($this->path.'/__cache/'.$name.'.js', "w");
		@fwrite ($js, $cache['code']);
		@fclose ($js);
		
		$this->local['list'] = $cache['list']
		
		$temp = @file_get_contents($this->cache);
		$temp = ($temp)?(array)json_decode($temp):array();
		$temp = array_merge($temp,array($name => $this->list));
		$temp = json_encode($temp);
		$ini = @fopen ($this->cache, "w");
		@fwrite ($ini, $temp);
		@fclose ($ini);
		
	}
	
	/*
//сканирует все директории и склеивает весь код указанного типа файлов
//[param] $dir-директория , $mask-какие типы файлов склеивать, $code - куда поместить склееянный код
//[answer] массив имен склеянных файлов, пишет код в $code
*/
	private function scan($dir, $mask, $list){

		$d = array('code'=>array(), 'list');
		$arr = opendir($dir);
		while($v = readdir($arr)){
			if($v == '.' or $v == '..' or $v == '__cache') continue;
			
			if(is_dir($dir.'/'.$v)){
				$d = array_merge($d, scan($dir.'/'.$v, $mask));
			}elseif(fnmatch($mask, $v)){
				//array_search($temp, $order);
				$order['code'][$key]=@file_get_contents($dir.'/'.$v);
				
			}
		}
		return $d;
	}
	
}