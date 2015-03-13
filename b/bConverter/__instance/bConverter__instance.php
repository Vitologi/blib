<?php
defined('_BLIB') or die;

/**
 * Class bConverter__instance   - base decor component
 * Used for extends other converters
 */
class bConverter__instance extends bBlib{

    /** @var static $_component - decor block */
    protected $_component = null;
    /**
     * @var null|mixed $_data   - stored data
     */
    private   $_data   = null;
    /**
     * @var null|string $_format - data type (number, string, xml, json, some different type)
     */
    private   $_format = null;


    /**
     * Interface method for check format of current data.
     * If format will not be detected, then it will be nulled.
     *
     * @return null|string  - format name
     */
    public function checkFormat(){
        if($this->_component !== null)return $this->_component->checkFormat();

        return $this->_format = null;
    }

    /**
     * Interface method for convert data to new format.
     *
     * @param null|string $newFormat    - name of format
     * @return null|mixed               - converted data
     */
    public function convertTo($newFormat = null){
        if($this->_component !== null)return $this->_component->convertTo($newFormat);

        if($this->_format === null)$this->getFormat();
        return $this->_data;
    }

    /**
     * Displays data according to the format. Sets php headers or does something else for correct use.
     *
     * @return null|mixed
     */
    public function output(){
        if($this->_component !== null)return $this->_component->output();

        return $this->getData();
    }


    /**
     * @param bConverter__instance $component - decor component
     * @return $this    - for chaining
     */
    final public function decor(bConverter__instance $component){
        $this->_component = $component;
        return $this;
    }

    /**
     * Get data or provide this request to inner component (decor it)
     *
     * @return null|mixed
     */
    final public function getData(){
        if($this->_component !== null)return $this->_component->getData();

        return $this->_data;
    }


    /**
     * Set data and clear format. Or provide this request to inner component (decor it)
     *
     * @param null|mixed $data  - some data
     * @return null|static      - for chaining (used fully decorated component which stored in parent property)
     */
    final public function setData($data = null){
        if($this->_component !== null)return $this->_component->setData($data);

        $this->_data = $data;
        $this->_format = null;
        return $this->_parent->output();
    }

    /**
     * Set format if it type is string. Or provide this request to inner component (decor it)
     *
     * @param null $format
     * @return null
     */
    final public function setFormat($format = null){
        if($this->_component !== null)return $this->_component->setFormat($format);

        if(is_string($format)){
            $this->_format = $format;
        }

        return $this->_parent->output();
    }

    /**
     * Get format or provide this request to inner component (decor it)
     *
     * @return null|string      - format name
     */
    final public function getFormat(){
        if($this->_component !== null)return $this->_component->getFormat();

        return $this->_format;
    }

}