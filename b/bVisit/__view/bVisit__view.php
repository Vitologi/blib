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
    protected $_traits = array('bConverter');

    public function index(){

        $this->_template['content'] = $this->get('data', array());

        return $this->_template;

    }

    public function json(){

        /** @var bConverter__instance $bConverter */
        $bConverter = $this->getInstance('bConverter');

        $visitList = $this->get('data', array());

        $bConverter->setData($visitList)->setFormat('array')->convertTo('json');

        return $bConverter->output();

    }

}