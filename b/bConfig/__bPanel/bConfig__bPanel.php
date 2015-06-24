<?php
defined('_BLIB') or die;


/**
 * Class bConfig__bPanel - concrete controller for bConfig block
 * Included patterns:
 * 		MVC	- use input property like action and view for create code logic
 */
class bConfig__bPanel extends bController{

    protected $_block = 'bConfig';

    /** @var bConfig__model $_model */
    protected $_model = null;
    /** @var bConfig__view $_view */
    protected $_view = null;

    /**
     * Get tunnel data for business logic, set view template and configure it
     *
     * @param array $data   - provided data
     * @throws Exception
     */
    protected function configure($data = array()){

        $_model = $this->_model = $this->getInstance('model', 'bConfig__model');
        $_view = $this->_view = $this->getInstance('view', 'bConfig__view');

        // Save blocks (panel and config)
        $blocks = $_model->getBlocks('__bPanel');
        $confBlocks = $_model->getBlocks();

        $_view->set("blocks", $blocks);
        $_view->set("confBlocks", $confBlocks);

        // Set template for menu view
        if ($template = $_model->getTemplate()) $_view->setTemplate($template);

    }


    public function indexAction(){

        $_view = $this->_view;
        $_model = $this->_model;

        $block = $this->get('block', $this->_block);
        $configMap = $_model->getConfigMap($block);

        $_view->set("block", $block);
        $_view->set("configMap", $configMap);
        $_view->build();


        return $_view->generate();
    }

    /**
     * Save configuration
     */
    public function saveAction(){

        $_model = $this->_model;

        $bForm = $this->get('bForm', array('configForm'=>array('block'=>null,'config'=>array())));
        $config = $bForm['configForm']['config'];
        $block = $bForm['configForm']['block'];

        $_model->saveConfig($block, $config);
        $this->_block = $block;

        return $this->indexAction();
    }

}