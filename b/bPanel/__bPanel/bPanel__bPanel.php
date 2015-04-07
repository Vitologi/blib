<?php
defined('_BLIB') or die;

/**
 * Class bPanel__bPanel - concrete controller for bPanel block
 * Included patterns:
 * 		MVC	- use input property like action and view for create code logic
 */
class bPanel__bPanel extends bBlib{

    /**
     * @var array $_traits  - included traits
     */
    protected $_traits = array('bPanel__model', 'bPanel__view');

    /**
     * @var array $_mvc     - default request data
     */
    protected $_mvc    = array(
        'action' => 'index',
        'view'   => 'index'
    );

    /**
     * Set template and MVC data
     *
     * @param array $data
     * @throws Exception
     */
    protected function input($data = array()){

        /** @var bPanel__model $bPanel__model */
        $bPanel__model = $this->getInstance('bPanel__model');

        /** @var bPanel__view $bPanel__view */
        $bPanel__view = $this->getInstance('bPanel__view');

        if($template = $bPanel__model->getTemplate()){
            $bPanel__view->setTemplate($template);
        }

        $this->_mvc = array_replace_recursive($this->_mvc, $data);

    }

    /**
     * Provide controller function
     *
     * @return array|string
     */
    public function output(){

        /** @var bPanel__model $bPanel__model */
        $bPanel__model = $this->getInstance('bPanel__model');

        /** @var bPanel__view $bPanel__view */
        $bPanel__view = $this->getInstance('bPanel__view');

        $mvc = $this->_mvc;

        switch($mvc['action']){
            case "index":
            default:
                $blocks = $bPanel__model->getBlocks();
                $bPanel__view->set("blocks", $blocks);
                break;
        }

        switch($mvc['view']){
            case "index":
            default:
                $bPanel__view->indexPanel();
                break;
        }

        return $bPanel__view->generate();

    }

}