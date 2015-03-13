<?php
defined('_BLIB') or die;

/**
 * Class bConverter__default    - default converter
 * Used for conversion 'string', 'boolean', 'object', 'array', 'number' types between it self
 */
class bConverter__default extends bConverter__instance{

    /**
     * @var array $_allowedFormats  - list of supported formats
     */
    private $_allowedFormats = array('string', 'boolean', 'object', 'array', 'number');

    /**
     * Check format of current data. Sets format if it was detected.
     *
     * @return null|string  - format name
     */
    public function checkFormat(){
        $data = $this->getData();

        switch(true) {

            case is_string($data):
                $format = 'string';
                break;

            case is_bool($data):
                $format = 'boolean';
                break;

            case is_object($data):
                $format = 'object';
                break;

            case is_array($data):
                $format = 'array';
                break;

            case is_float($data):
            case is_int($data):
                $format = 'number';
                break;

            default:
                $format = $this->_component->checkFormat();
                break;

        }

        if($format !== null)$this->setFormat($format);


        return $format;
    }


    /**
     * Convert data to new format.
     *
     * @param null|string $newFormat    - format name
     * @return mixed|null   - converted data(or old data, if conversion failed)
     */
    public function convertTo($newFormat = null){

        $oldFormat = $this->getFormat();

        if($oldFormat === null)$oldFormat = $this->checkFormat();

        if($oldFormat !== $newFormat){

            if(
                in_array($oldFormat, $this->_allowedFormats)
                && in_array($newFormat, $this->_allowedFormats)
            ){
                $data = $this->getData();

                switch($newFormat) {

                    case 'string':

                        if($oldFormat === 'object'){
                            $data = serialize($data);
                        }else{
                            $data = (string) $data;
                        }

                        break;

                    case 'boolean':
                        $data = (boolean) $data;
                        break;

                    case 'object':
                        $data = (object) $data;
                        break;

                    case 'array':
                        $data = (array) $data;
                        break;

                    case 'number':
                        if($oldFormat === 'object')$data = (array)$data;
                        $data = (int) $data;
                        break;

                    default:
                        $format = null;
                        break;

                }

                $this->setData($data);
                $this->setFormat($newFormat);
            }

        }

        return $this->getData();
    }

    /**
     * Displays data like dump
     *
     * @return null|mixed   - can return something else data
     */
    public function output(){
        $format = $this->getFormat();
        $data   = $this->getData();

        if(in_array($format,array('string','boolean','object','array','number'))){
            var_dump($data);
            return null;
        }else{
            return $this->_component->output();
        }
    }

}