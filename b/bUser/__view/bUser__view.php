<?php
defined('_BLIB') or die;

/**
 * Class bUser__view - view collection for bMenu
 * Included patterns:
 * 		MVC	- localise view into one class
 */
class bUser__view extends bView{

    protected $_template = array(
        'block'   => 'bUser',
        'mods'    => array(),
        'attrs'   => array(),
        'meta'    => array(),
        'content' => ''
    );

    public function index(){

        $login = $this->get('login', array());

        if($login)$this->_template['content'] = $login;

        return $this->_template;

    }

}