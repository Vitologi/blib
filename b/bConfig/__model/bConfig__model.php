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
     * Get configuration map for single block
     *
     * @param string $block block`s name
     * @return array        config map
     */
    public function getConfigMap($block = null){
        $configMap = $this->getDefault($block, true);
        $default = $this->_config->getConfig($block);
        $configMap['default'] = $default;
        return $configMap;
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



    public function parseDefaultConfig($content, $prop = false){
        if(!$prop && isset($content['default']))return $content['default'];

        if(isset($content['properties']))return $this->parseDefaultConfig($content['properties'], true);

        $arr = array();
        foreach($content as $key => $value){
            if(is_array($value) && ($temp =$this->parseDefaultConfig($value)))$arr[$key] = $temp;
        }

        if(!empty($arr))return $arr;
    }

    public function getDefault($block, $map = false){
        $path = bBlib::path($block.'__bConfig','json');
        if(!file_exists($path))return array();

        $content = json_decode(file_get_contents($path),true);
        return $map?$content:$this->parseDefaultConfig($content);
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