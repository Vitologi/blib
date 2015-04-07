<?php
/**
 * Created by PhpStorm.
 * User: morozov
 * Date: 07.04.2015
 * Time: 15:29
 */

class bController extends bBlib{

    /** @var null|bBlib|mixed $_model   - default model */
    protected $_model   = null;
    /** @var null|bView $_view          - default view */
    protected $_view    = null;
    /** @var string $_action            - current action */
    protected $_action  = 'index';
    /** @var string $_layout            - current view layout */
    protected $_layout  = 'index';

    public function getModel(){return $this->_model;}
    public function getView(){return $this->_view;}
    public function getAction(){return $this->_action.'Action';}
    public function getLayout(){return $this->_layout;}

    public function setModel(bBlib $model){
        $this->_model = $model;
        return $this;
    }
    public function setView(bView $view){
        $this->_view = $view;
        return $this;
    }
    public function setAction($action){
        if(is_string($action)){$this->_action = $action;}
        return $this;
    }
    public function setLayout($layout){
        if(is_string($layout)){$this->_layout = $layout;}
        return $this;
    }


    /**
     * Get property from [set data]->[tunnel data]->[request data]->[some default value]->null (use this order)
     *
     * @param string $name  - property name
     * @param array $data   - main data
     * @return null|mixed   - property value
     */
    final protected function get($name = '', Array $data = array()){

        /** @var bRequest $bRequest */
        $bRequest = $this->setTrait('bRequest')->getInstance('bRequest');
        $tunnel = $bRequest->getTunnel(get_class($this));
        $request = $bRequest->get($name);

        if(isset($data[$name])){
            return $data[$name];
        }elseif(isset($tunnel[$name])){
            return $tunnel[$name];
        }elseif($request !== null){
            return $request;
        }elseif($name == 'action'){
            return $this->getAction();
        }elseif($name == 'view'){
            return $this->getLayout();
        }

        return null;
    }

    /**
     * Do router function
     *  - provide self into child
     *  - call needed action
     *  - parse return data chosen view
     * 
     * @return mixed
     */
    final public function output(){
        if ($this->_parent instanceof bBlib) return $this;

        $data   = array();
        $action = $this->getAction();
        $view   = $this->getView();
        $layout = $this->getLayout();


        if (method_exists($this, $action)) $data = $this->$action();
        if (method_exists($view, $layout)) $view->$layout($data);

        return $view->generate();
    }
}