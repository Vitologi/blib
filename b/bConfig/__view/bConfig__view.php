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

        /** Configured blocks */
        $configBlocks = $this->getConfBlocks();

        /** Buttons */
        $save    = $_helper->buildButton('сохранить', array('configForm'), array(
            'action'=>'save',
            'view'   => 'index'
        ), 'bConfig__bPanel');

        /** Other interface elements */
        $blocks       = $_helper->buildBlocks($this->get('blocks', array()));


        $location   = $_helper->buildLocation('bConfig__bPanel',array('block'=>$this->get('block', null)));

        $message  = array($_helper->buildError($message));
        foreach($errors as $key => $value){
            $message[] = $_helper->buildError($value);
        }
        $message = $_helper->buildCollection($message);


        $collection = $_helper->buildCollection(array($message, $location));
        $tools      = $_helper->buildTools(array($configBlocks, $save));

        $this->setPosition('"{1}"', $blocks);
        $this->setPosition('"{2}"', $collection);
        $this->setPosition('"{3}"', $tools);
        $this->setPosition('"{4}"', $this->showConfigs());

    }


    /**
     * Get block witch can be configured
     *
     * @return array    bom for create list
     */
    public function getConfBlocks(){
        return array(
            'block'=>'bConfig',
            'elem'=>'list',
            'selected'=>$this->get('block'),
            'content'=>$this->get('confBlocks',array())
        );
    }

    /**
     * Show input form for change configuration
     *
     * @return array    bom
     */
    public function showConfigs(){

        $this->_converter->setData($this->get('configMap', array()))->setFormat('array')->convertTo('json');
        $config = $this->_converter->convertTo('string');
        $block = $this->get('block', null);

        return array(
            'block' => 'bForm',
            'mods'  => array('style' => 'default'),
            'meta'  => array(
                'name'=>'configForm',
                'tunnel' => array(),
                'method' => "POST",
                'action' => "/",
                'ajax'   => true,
                'select' => array(),
                'items'  => array()
            ),
            'content'	=> array(
                array('elem'=>'hidden', 'name'=>'block', 'content'=>$block),
                array('elem'=>'textarea', 'name'=>'config', 'content'=>$config)
            )
        );
    }

}