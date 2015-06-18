<?php
defined('_BLIB') or die;

/**
 * Created by PhpStorm.
 * User: morozov
 * Date: 25.03.2015
 * Time: 16:42
 *
 * Class bPanel__model - concrete model for bPanel block
 * Included patterns:
 * 		MVC	- localise business logic into one class
 */
class bPanel__model extends bBlib{

    /**
     * @return $this - return self to parent block
     */
    public function output(){
        if($this->_parent instanceof bBlib) return $this;
    }


    /**
     * Get all blocks name which have bPanel controller
     *
     * @param string $elem  - element filter
     * @return array - blocks list
     * @throws Exception
     */
    final public function getBlocks($elem = null){
        $arr = opendir('b');
        $temp = array();
        while($v = readdir($arr)){
            if($v == '.' or $v == '..' or $v == 'bBlib') continue;
            $name = $v.$elem;
            $path = bBlib::path($name,'php');
            if(file_exists($path))$temp[$v] = $name;
        }
        return $temp;
    }

    /**
     * Get template from template blocks
     *
     * @return mixed    - string template
     */
    final public function getTemplate(){

        /** @var bTemplate $_template - template instance */
        $_template = $this->getInstance('template', 'bTemplate');

        return $_template->getOwnTemplate('template', 'bPanel');
    }
}