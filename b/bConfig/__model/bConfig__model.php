<?php
defined('_BLIB') or die;

/**
 * Class bConfig__model - model for generate and work with config components
 * Included patterns:
 * 		MVC	- localise business logic into one class
 */
class bConfig__model extends bBlib{

    /** @var null|bConfig $_config                 menu item configuration */
    private $_config       = null;

    /** @var null|bPanel__model $_helper     for get some method */
    private $_helper = null;

    /** @var bConverter__instance $_converter */
    private $_converter = null;

    /**
     *  Set config and data mapper
     */
    protected function input(){
        $this->_config = $this->getInstance('config', 'bConfig');
        $this->_helper = $this->getInstance('helper', 'bPanel__model');
        $this->_converter = $this->getInstance('converter', 'bConverter');
    }


    /**
     * Get configuration for single block
     *
     * @param string $block block`s name
     * @return array        configuration
     */
    public function getConfigMap($block = null){
        return $this->_config->getConfig($block);
    }

    /**
     * Save configuration
     *
     * @param string $block block`s name
     * @param array $config block`s new configuration
     */
    public function saveConfig($block = null, $config = array()){
        if(!is_string($block))return;
        $config = $this->_converter->setData($config)->convertTo('array');
        $this->_config->setConfig($block, $config);
    }

    /**
     * Get template from helper model
     *
     * @return mixed    - string template
     */
    public function getTemplate(){
        return $this->_helper->getTemplate();
    }


    /**
     * Get blocks list from helper model
     *
     * @param null|string $block    block name
     * @return array                blocks array (associative)
     */
    public function getBlocks($block = null){
        return $this->_helper->getBlocks($block);
    }

}