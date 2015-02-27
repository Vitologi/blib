<?php
defined('_BLIB') or die;

class bInclude extends bBlib{

    protected $_traits       = array('bSystem', 'bRequest', 'bConfig');
    private   $_list         = array();
    private   $_callback     = null;
    private   $_path         = null;
    private   $_cache        = null;
    private   $_disableCache = null;

	protected function input(){

        /** @var bConfig $bConfig   - configuration instance */
        $bConfig = $this->getInstance('bConfig');

        /** @var bRequest $bRequest - request instance */
        $bRequest = $this->getInstance('bRequest');


        $this->_path            = bBlib::path('bInclude__cache');
        $this->_cache           = bBlib::path('bInclude__cache', 'ini');
        $this->_disableCache    = $bConfig->getConfig('bInclude.disableCache');

        if($list = $bRequest->get('list')){
            if(!is_array($list))$list = (array)json_decode($list);
            $this->_list = $list;
        }

        if($callback = $bRequest->get('callback')){
            $this->_callback = $callback;
        }

	}
	
	public function output(){
		
		//get name
		$name = $this->getCacheName();
		
		if($this->_disableCache || !file_exists($this->_path.$name.'.js')){
			$this->setCache($name, $this->_list);
		}
		
		//get version
		$version = (file_exists($this->_cache)?filemtime($this->_cache):0);
		
		//get list
		$ini = @file_get_contents($this->_cache);
		$ini = (array)json_decode($ini);
		$list = $ini[$name];
		
		$answer = json_encode(array(
			"version"	=> $version,
			"name"		=> $name,
			"list"		=> $list
		));
		
		header('Content-Type: application/json; charset=UTF-8');
		echo ($this->_callback?sprintf('%1$s(%2$s);',$this->_callback, $answer):$answer);
		exit;

	}
	
	/** ---------------------- */
	
	private function getCacheName(){
		$arr = $this->_list;
		sort($arr);
		return md5(implode("",$arr));
	}

	private function setCache($name, $list){
		
		$cache = $this->scan('b', 'css', $list);
		$css = @fopen ($this->_path.$name.'.css', "w");
		@fwrite ($css, $cache['code']);
		@fclose ($css);
		
		
		$cache = $this->scan('b', 'js', $list);
		$js = @fopen ($this->_path.$name.'.js', "w");
		@fwrite ($js, $cache['code']);
		@fclose ($js);
		
		$this->_list = $cache['list'];
		
		$temp = @file_get_contents($this->_cache);
		$temp = ($temp)?(array)json_decode($temp):array();
		$temp = array_merge($temp,array($name => $this->_list));
		$temp = json_encode($temp);
		$ini = @fopen ($this->_cache, "w");
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
			if($v == '.' or $v == '..' or $v == 'bInclude' or $v == 'bBlib') continue;
			
			if(is_dir($dir.'/'.$v)){
				$cache = array_merge($cache, $this->scan($dir.'/'.$v, $extention, $list, $cache, $deep+1));
				continue;
			}
			
			if(!fnmatch('*.'.$extention, $v)) continue;
			
			$name = basename($v, '.'.$extention);
			
			if(count($list) && !in_array($name, $list)) continue;
			
			$block = $name::create();
			$code = @file_get_contents($dir.'/'.$v);
			$cache[$name] = array($block->getTraits(), $code);
	
		}
		
		return ($deep?$cache:$this->glueCache($cache, array('code'=>null, 'list'=>array())));
		
	}

		
	private function glueCache($cache, $answer, $deep = 0){
		$i=0;
		foreach($cache as $key => $value){
			
			foreach($cache as $key2 => $value2){
				if(in_array($key,(array)$value2[0])) continue 2;					
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
			$answer['code'] = implode("\n\n",array_reverse((array)$answer['code']));
		}
		
		return $answer;
	}
	
}