<?php
defined('_BLIB') or die;


/**
 * Class bConfig__bPanel - concrete controller for bConfig block
 * Included patterns:
 * 		MVC	- use input property like action and view for create code logic
 */
class bConfig__bPanel extends bController{

    protected $_configName = 'bConfig';

    /**
     * Get tunnel data for business logic, set view template and configure it
     *
     * @param array $data   - provided data
     * @throws Exception
     */
    protected function configure($data = array()){

        $_model = $this->getInstance('model', 'bConfig__model');
        $_view   = $this->getInstance('view', 'bConfig__view');
        $_helper = $this->getInstance('helper', 'bPanel__model');

        // Save blocks (panel and config)
        $blocks = $_helper->getBlocks('__bPanel');
        $_view->set("blocks", $blocks);


        // Set template for menu view
        if ($template = $_helper->getTemplate()) $_view->setTemplate($template);

    }


    public function indexAction(){

        $_model = $this->getInstance('model');
        $_view   = $this->getInstance('view');

        $configList = $_model->getConfigList();
        $_view->set("configList", $configList);

        $configName = $this->get('configName', $this->_configName);
        $configParent = $_model->getConfigParent($configName);
        $configMap = $_model->getConfigMap($configName);


        $_view->set("configName", $configName);
        $_view->set("configParent", $configParent);
        $_view->set("configMap", $configMap);
        $_view->build();


        return $_view->generate();
    }

    /**
     * Save configuration
     */
    public function saveAction(){

        /** @var bConfig__model $_model */
        $_model = $this->getInstance('model');

        $bForm = array_replace_recursive(array('configForm'=>array('configName'=>null,'config'=>array(),'parent'=>null)),$this->get('bForm', array()));
        $config = $bForm['configForm']['config'];
        $configName = $bForm['configForm']['configName'];
        $parent = $bForm['configForm']['configParent'];

        $_model->saveConfig($configName, $config, $parent);
        $this->_configName = $configName;

        return $this->indexAction();
    }

}