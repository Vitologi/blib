<?php
defined('_BLIB') or die;

/**
 * Class bPanel - router for (blocks controllers) blocks administrative part
 */
class bPanel extends bBlib{

    /**
     * @var array $_data     - default data array
     */
    protected $_data    = array(
        "controller" => "bPanel__bPanel"
    );

    /**
     * Set concrete controller name
     *
     * @param array $data   - data from template
     */
    protected function input($data = array()){
        if(!is_array($data))$data = array();

        /** @var bRequest $_request  - request instance */
        $_request = $this->getInstance('request', 'bRequest');

        $tunnel = (array) $_request->get(__CLASS__);

        $this->_data = array_replace($this->_data, $tunnel, $data);

        if(!class_exists($this->_data['controller']))$this->_data['controller'] = "bPanel__bPanel";
    }

    /**
     * Redirect process to concrete controller
     *
     * @return mixed    - concrete controller answer
     */
    public function output(){

        /** @var bBlib $controller - block instance */
        $controller = $this->_data['controller'];

        $concreteController = $controller::create();

        return $concreteController->output();

    }

}
