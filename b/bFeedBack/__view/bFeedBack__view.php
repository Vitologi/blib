<?php
defined('_BLIB') or die;

/**
 * Class bFeedBack__view - view collection for feedback block
 * Included patterns:
 * 		MVC	- localise view into one class
 */
class bFeedBack__view extends bView{

    protected $_template = array(
        'block'   => 'bFeedBack',
        'mods'    => array(),
        'attrs'   => array(),
        'meta'    => array(
            'themes' => array(),
            'threads'=> array()
        ),
        'content' => ''
    );
    protected $_traits = array('bConverter');

    public function index(){

        $this->_template['mods']  = $this->get('mods', array());
        $this->_template['meta']['themes']  = $this->get('themes', array());
        $this->_template['meta']['threads'] = $this->get('threads', array());

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