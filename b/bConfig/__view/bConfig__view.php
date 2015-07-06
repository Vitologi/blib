<?php
defined('_BLIB') or die;

/**
 * Class bConfig__view - view collection for bConfig
 * Included patterns:
 * 		MVC	- localise view into one class
 */
class bConfig__view extends bView{

    /** @var bPanel__view $_helper */
    protected $_helper = null;

    /** @var bConverter__instance $_converter */
    protected $_converter = null;


    protected function input(){
        $this->_helper = $this->getInstance('helper', 'bPanel__view');
        $this->_converter = $this->getInstance('converter', 'bConverter');
    }


    /**
     * Create basic view for change configuration
     *
     * @throws Exception
     */
    public function build(){

        $_helper = $this->_helper;

        $message = $this->get('message', "Панель редактирования конфигураций блоков");
        $errors  = $this->get('errors', array());

        /** Buttons */
        $save    = $_helper->buildButton('сохранить', array('configForm'), array(
            'action'=>'save',
            'view'   => 'index'
        ), 'bConfig__bPanel');

        /** Other interface elements */
        $blocks       = $_helper->buildBlocks($this->get('blocks', array()));


        $location   = $_helper->buildLocation('bConfig__bPanel',array('configName'=>$this->get('configName', null)));

        $message  = array($_helper->buildError($message));
        foreach($errors as $key => $value){
            $message[] = $_helper->buildError($value);
        }
        $message = $_helper->buildCollection($message);


        $collection = $_helper->buildCollection(array($message, $location));
        $tools      = $_helper->buildTools(array(/*$configBlocks,*/ $save));

        $this->setPosition('"{1}"', $blocks);
        $this->setPosition('"{2}"', $collection);
        $this->setPosition('"{3}"', $tools);
        $this->setPosition('"{4}"', $this->showConfigs());

    }


    /**
     * Get block witch can be configured
     *
     * @param null|array $list
     * @return array bom for create list
     */
    public function parseConfigList($list = null){

        if($list == null)$list = $this->get('configList', array());

        $temp = array();

        foreach($list as $value){
            $blockName = (strpos($value,'.')?$value:($value.'.name'));
            $temp[]=array('name'=>$value,'value'=>$blockName);
        }

        return $temp;
    }

    /**
     * Show input form for change configuration
     *
     * @return array    bom
     */
    public function showConfigs(){

        $config       = $this->get('configMap', array());
        $configName   = $this->get('configName', null);
        $configParent = $this->get('configParent', null);
        $list         = $this->parseConfigList();

        return array(
            'block' => 'bForm',
            'mods'  => array('style' => 'default'),
            'meta'  => array(
                'name'=>'configForm',
                'tunnel' => array(),
                'method' => "POST",
                'action' => "/",
                'ajax'   => true,
                'select' => array(
                    'list' => $list
                ),
                'items'  => array(
                    array(
                        'configName'=>$configName,
                        'configParent'=>$configParent
                    )
                )
            ),
            'content'	=> array(
                array('elem'=>'label', 'content'=>'bConfig.__bPanel.setName', 'attrs'=>array('title'=>'bConfig.__bPanel.titleName')),
                array('elem'=>'selectplus', 'isRequired'=>true, 'mods'=>array('configer'=>true), 'name'=>'configName', 'select'=>'list', 'key'=>'name', 'show'=>array('value')),
                array('elem'=>'label', 'content'=>'bConfig.__bPanel.setParent', 'attrs'=>array('title'=>'bConfig.__bPanel.titleParent')),
                array('elem'=>'select', 'name'=>'configParent', 'select'=>'list', 'key'=>'name', 'show'=>array('value')),
                array('elem'=>'jsoneditor', 'name'=>'config', 'content'=>array('theme'=>'bootstrap2','schema'=>$config))
            )
        );
    }

}