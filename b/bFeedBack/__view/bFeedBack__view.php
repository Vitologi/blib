<?php
defined('_BLIB') or die;

/**
 * Class bFeedBack__view - view collection for feedback block
 * Included patterns:
 * 		MVC	- localise view into one class
 */
class bFeedBack__view extends bView{

    /** @var bConverter__instance $_converter */
    protected $_converter = null;

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

    protected function input(){
        $this->_converter = $this->getInstance('converter', 'bConverter');
    }

    public function index(){

        $this->_template['mods']  = $this->get('mods', array());
        $this->_template['meta']['themes']  = $this->get('themes', array());
        $this->_template['meta']['threads'] = $this->get('threads', array());

        return $this->_template;

    }

    public function json(){

        $_converter = $this->_converter;

        $visitList = $this->get('data', array());

        $_converter->setData($visitList)->setFormat('array')->convertTo('json');

        return $_converter->output();

    }

}