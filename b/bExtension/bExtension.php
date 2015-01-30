<?php
defined('_BLIB') or die;

class bExtension extends bBlib{	
	
    private static $_instance = null;    

    // Overload object factory for Singleton
    static public function create() {
        if (self::$_instance === null)self::$_instance = parent::create(func_get_args());
        return self::$_instance;
    }
     
    protected function input(){
        
    }
    
	public function output(){
        $this->_parent = null;
        return $this;
	}

    public function concatAllBlocks(){
        
        $list = bDecorator::create()->getList();

        foreach($list as $key => $value){
            $this->concat($key, $value);
        }

    }
    
    private function concat($block = '', $list = array()){
		
        $files = $this->cocatCode($block, array());
        
		foreach($list as $key => $value){
			$files = $this->cocatCode($value, $files);
		}

        if(!isset($files))return $this;
        
		foreach($files as $key => $value){
			file_put_contents(bBlib::path($block, $key), $value);
		}

	}
	
	private function cocatCode($name, $stack){
		$path = bBlib::path($name);
		$folder =  opendir($path);
		while($file = readdir($folder)){
			if(preg_match('/\w*.(\w+).dev$/', $file, $matches)){
				if(!isset($stack[$matches[1]]))$stack[$matches[1]]='';
				$stack[$matches[1]] .= file_get_contents($path.$file);
				continue;
			}
            
            if(is_dir($path.$file) && substr($file, 0,2) === '__'){
				$stack = $this->cocatCode($name.$file, $stack);
			};
		}
		
		return $stack;
	}
    
    
}

