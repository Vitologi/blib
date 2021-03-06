<?php
defined('_BLIB') or die;

/**
 * Class bDecorator - realisation of pattern Decorator
 * Allows to create decorated instance of block and save it into self.
 * For access to it use $this->getInstance('bDecorator')
 * Included patterns:
 * 		singleton	- one decorator factory
 * 		decorator 	- wrap initiator object in decorator and save it in initiator
 *
 */
class bDecorator extends bBlib{

    /** @var bConfig $_config */
    protected $_config = null;

    /** @var null|static $_instance - Singleton instance */
    private static $_instance = null;

    /**
     * @var array $_list - decorated rules
     *
     * For example:
     * $list = array('bBlock'=>array('bBlock_modifier', 'bBlock_modifier2'));
     * means that block bBlock will be decorate by class 'bBlock_modifier' and 'bBlock_modifier2' in order
     * this decorated object will be save in bBlock instance
     */
    private $_list = array();

    /**
     * Overload object factory for Singleton
     *
     * @return null|static|bDecorator
     */
    static public function create() {
        if (self::$_instance === null)self::$_instance = parent::create(func_get_args());
        self::$_instance->_parent = null;
        return self::$_instance;
    }

    /**
     * Set decor rules
     */
    protected function input(){
        $this->_config = $this->getInstance('config', 'bConfig');
        $this->_list = $this->_config->getConfig(__CLASS__);
    }

    /**
     * Decorator factory
     *
     * @return null|bDecorator__instance    - n-wrapped instance of block decorator
     */
    public function output(){
        $block  = $this->_parent;
        $blockName = get_class($block);

        if( isset($this->_list[$blockName]) && $list = $this->_list[$blockName]){
			foreach( $list as $key=>$decorator){

                /** @var  bDecorator__instance $decorator   - concrete block's decorator */
                $block = $decorator::create()->setParent($block);
			}
		}

        return $block;
        
	}


    /**
     * Getter for decor rules
     *
     * @return array    - decor rules
     */
    final public function getList(){
        return $this->_list;
	}

    /**
     * Setter for  decor rules
     *
     * @param array $list   - decor rules
     */
    final public function setList($list = array()){

        // reset storing strategy by default
        $this->_config->setDefault();

        // save list
        $this->_config->setConfig(__CLASS__, $list);
	}
    
}