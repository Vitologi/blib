<?php
defined('_BLIB') or die;

/**
 * Class bConverter__json   - json converter
 * Used for conversion json format to other or into it from other
 */
class bConverter__json extends bConverter__instance{

    /**
     * Check format of current data. Sets format if it was detected.
     *
     * @return string - format name
     */
    public function checkFormat(){
        $data = $this->getData();

        if(is_string($data)){
            $json = json_decode($data,true);
            if(json_last_error() === JSON_ERROR_NONE){
                $this->setFormat('json');
                return 'json';
            }
        }

        return $this->_component->checkFormat();
    }


    /**
     * Convert data to new format.
     *
     * @param null|string $newFormat    - format name
     * @return mixed|null               - converted data(or old data, if conversion failed)
     * @throws Exception
     */
    public function convertTo($newFormat = null){

        $data = $this->getData();
        $oldFormat = $this->getFormat();

        if($oldFormat === null)$oldFormat = $this->checkFormat();

        if($oldFormat === 'json' || $newFormat === 'json'){

            if ($oldFormat === 'array') {
                $data = json_encode($data, 256);
            } elseif ($oldFormat === 'object') {
                $data = json_encode((array)$data, 256);
            }

            if ($newFormat === 'array') {
                $data = json_decode($data, true);
            } elseif ($newFormat === 'object') {
                $data = (object)json_decode($data, true);
            }

            if(json_last_error() !== JSON_ERROR_NONE)throw new Exception('Have error in convert process.');

            $this->setData($data);
            $this->setFormat($newFormat);

        }else{
            return $this->_component->convertTo($newFormat);
        }

        return $this->getData();
    }

    /**
     * Displays data like json, set header. Or provide to decor component
     *
     * @return null|mixed   - can return something else data
     */
    public function output(){
        $format = $this->getFormat();
        $data   = $this->getData();

        if($format === 'json'){
            header('Content-Type: application/json; charset=UTF-8');
            echo $data;
            exit;
        }else{
            return $this->_component->output();
        }
    }

}