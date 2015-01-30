<?php
defined('_BLIB') or die;

class bDecorator extends bBlib{	
	
    private static $_instance = null;
    private $_list = array();
    
    
    // Overload object factory for Singleton
    static public function create() {
        if (self::$_instance === null)self::$_instance = parent::create(func_get_args());
        return self::$_instance;
    }
     
    protected function input(){
        $path = bBlib::path('bDecorator', 'ini');
        if(!file_exists($path)){return;}
        $this->_list = json_decode(file_get_contents($path), true);
    }
    
	public function output(){
        $block  = get_class($this->_parent);
        
        if( isset($this->_list[$block]) && $list = $this->_list[$block]){
                
			foreach( $list as $key=>$decorator){
				$this->_parent = $decorator::create()->setParent($this->_parent);
			}
		}
        
        return $this->_parent;
        
	}

    
    final public static function _decorate($data, $caller){
        return $caller->getInstance('bDecorator');
	}
    
    final public function getList(){
        return $this->_list;
	}
    
    final public function setList($list){
       file_put_contents(bBlib::path('bDecorator', 'ini'), json_decode($list));
	}
    
}

