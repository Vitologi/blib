<?php
defined('_BLIB') or die;

/**
 * Class bInclude   - Allows get all script or style file in one. And store in local cache.
 */
class bInclude extends bBlib{

    protected   $_traits        = array('bSystem', 'bRequest', 'bConfig');
    /**
     * @var array       - requested list of blocks
     */
    private     $_list          = array();
    /**
     * @var null|string - callback function for jsonp realisation
     */
    private     $_callback      = null;
    /**
     * @var null|string - path to store cache file
     */
    private     $_path          = null;
    /**
     * @var null|string - path to file with meta data
     */
    private     $_cache         = null;
    /**
     * @var boolean     - disable cache flag
     */
    private     $_disableCache  = false;


    /**
     * Set basic data:
     *  - name of cache folder
     *  - path to cache file
     *  - disable cache flag
     *  - list of needed files
     *  - name of callback function
     */
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

    /**
     * Main output method
     *
     * @void - return name and list of blocks
     */
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

    /**
     * Generate name of created cache
     *
     * @return string   - name of file (without extension)
     */
    private function getCacheName(){
		$arr = $this->_list;
		sort($arr);
		return md5(implode("",$arr));
	}

    /**
     * Save cached files and create/edit meta file
     *
     * @param string $name  - cache file name
     * @param array $list   - included blocks list
     */
    private function setCache($name = '', $list = array()){
		
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
	
    /**
     * Scan all directory and glue code pointed by extension
     *
     * @param string $dir       - folder name
     * @param string $extension - extension (js, css)
     * @param array $list       - block`s list like array('bIndex', 'bTemplate', 'bCssreset')
     * @param array $cache      - temp code storage like array('bIndex'=>array(array('bSystem','bConfig','bTemplate'), '%some file code%'))
     * @param int $deep         - counter of scanning depth
     * @return array|mixed      - code and files names like array('code'=>'% some code %', 'list'=>array('bIndex', 'bTemplate', 'bSystem'))
     */
    private function scan($dir = '', $extension='', $list = array(), $cache = array(), $deep = 0){

		$arr = opendir($dir);
		while($v = readdir($arr)){
			if($v == '.' or $v == '..' or $v == 'bInclude' or $v == 'bBlib') continue;
			
			if(is_dir($dir.'/'.$v)){
				$cache = array_merge($cache, $this->scan($dir.'/'.$v, $extension, $list, $cache, $deep+1));
				continue;
			}
			
			if(!fnmatch('*.'.$extension, $v)) continue;
			
			$name = basename($v, '.'.$extension);
			
			if(count($list) && !in_array($name, $list)) continue;
			
			$block = $name::create();
			$code = @file_get_contents($dir.'/'.$v);
			$cache[$name] = array($block->getTraits(), $code);
	
		}
		
		return ($deep?$cache:$this->glueCache($cache));
		
	}


    /**
     * Sort files code by priority ($_traits property) and glue them
     *
     * @param array $cache  - temp code storage like array('bIndex'=>array(array('bSystem','bConfig','bTemplate'), '%some file code%'))
     * @param array $answer - returned variable prototype
     * @param int $deep     - counter of scanning depth
     * @return array        - gluing code like array('list'=>array('bIndex', 'bTemplate'), 'code'=>'%some code%'))
     */
    private function glueCache($cache = array(), $answer = array('code'=>null, 'list'=>array()), $deep = 0){
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