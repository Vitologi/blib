<?php
defined('_BLIB') or die;

class bInclude extends bBlib{	
	
	protected function input($data){
		$this->local['parent'] = array('bJquery', 'bIndex');
		$this->local['callback'] = self::$global['REQUEST']['callback'];
		$this->local['cache'] = $this->path."/__cache/bInclude__cache.ini";
		$this->local['list'] = $this->callback?json_decode(self::$global['REQUEST']['list']):(array)$data['list'] or array();
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
		
		$answer = json_encode(array(
			"version"	=> $version,
			"name"		=> $name,
			"list"		=> $list
		));
		
		echo ($this->callback?sprintf('%1$s(%2$s);',$this->callback, $answer):$answer);
		exit;

	}
	
	private function getCacheName(){
		$arr = $this->list;
		sort($arr);
		return md5(implode("",$arr));
	}

	private function setCache($name, $list){
		
		$cache = $this->scan('b', 'css', $list);
		$css = @fopen ($this->path.'/__cache/'.$name.'.css', "w");
		@fwrite ($css, $cache['code']);
		@fclose ($css);
		
		
		$cache = $this->scan('b', 'js', $list);
		$js = @fopen ($this->path.'/__cache/'.$name.'.js', "w");
		@fwrite ($js, $cache['code']);
		@fclose ($js);
		
		$this->local['list'] = $cache['list'];
		
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
	private function scan($dir, $extention, $list, $cache = array(), $deep = 0){

		$arr = opendir($dir);
		while($v = readdir($arr)){
			if($v == '.' or $v == '..' or $v == '__cache' or $v == 'bBlib') continue;
			
			if(is_dir($dir.'/'.$v)){
				$cache = array_merge($cache, $this->scan($dir.'/'.$v, $extention, $list, $cache, $deep+1));
				continue;
			}
			
			if(!fnmatch('*.'.$extention, $v)) continue;
			
			$name = basename($v, '.'.$extention);
			
			if(count($list) && !in_array($name, $list)) continue;
			
			$block = new $name();
			$parent = $block->parent;
			$code = @file_get_contents($dir.'/'.$v);
			$cache[$name] = array($parent, $code);
	
		}
		
		return ($deep?$cache:$this->glueCache($cache, array('code'=>null, 'list'=>array())));
		
	}

		
	private function glueCache($cache, $answer, $deep = 0){
		$i=0;
		foreach($cache as $key => $value){
			
			foreach($cache as $key2 => $value2){
				if(in_array($key,$value2[0])) continue 2;					
			}
			
			$answer['list'][] = $key;
			$answer['code'][] = $value[1];
			$i++;
			unset($cache[$key]);

		}
		
		if($i){
			$answer = $this->glueCache($cache, $answer, $deep+1);
		}
		
		if(!$deep){
			$answer['list'] = array_reverse ($answer['list']);
			$answer['code'] = implode(array_reverse ($answer['code']));
		}
		
		return $answer;
	}
	
}