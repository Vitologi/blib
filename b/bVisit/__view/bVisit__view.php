<?php
defined('_BLIB') or die;

/**
 * Class bUser__view - view collection for bMenu
 * Included patterns:
 * 		MVC	- localise view into one class
 */
class bVisit__view extends bView{

    protected $_template = array(
        'block'   => 'bVisit',
        'mods'    => array(),
        'attrs'   => array(),
        'content' => ''
    );


    protected function input(){
        $this->setInstance('converter', 'bConverter');
    }

    public function index(){

        $this->_template['content'] = $this->get('data', array());

        return $this->_template;

    }

    public function json(){

        /** @var bConverter__instance $_converter */
        $_converter = $this->getInstance('converter');

        $visitList = $this->get('data', array());

        $_converter->setData($visitList)->setFormat('array')->convertTo('json');

        return $_converter->output();

    }

}