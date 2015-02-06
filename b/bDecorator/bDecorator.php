<?php
defined('_BLIB') or die;

class bDecorator extends bBlib{

    private static $_instance = null;
    private        $_list     = array();
    protected      $_traits   = array('bSystem', 'bConfig');
    
    
    // Overload object factory for Singleton
    static public function create() {
        if (self::$_instance === null)self::$_instance = parent::create(func_get_args());
        self::$_instance->_parent = null;
        return self::$_instance;
    }
     
    protected function input(){
        $this->_list = $this->_getConfig();
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

    
    final public static function _decorate(bBlib $caller){
        return $caller->getInstance('bDecorator');
	}
    
    final public function getList(){
        return $this->_list;
	}
    
    final public function setList($list){
       $this->_setConfig($list);
	}
    
}

