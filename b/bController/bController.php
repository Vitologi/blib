<?php
/**
 * Created by PhpStorm.
 * User: morozov
 * Date: 07.04.2015
 * Time: 15:29
 */

class bController extends bBlib{

    /** @var string $_action            - current action */
    protected $_action  = 'index';
    /** @var string $_view              - current view layout */
    protected $_view    = 'index';

    public function getView(){return $this->_view;}
    public function getAction(){return $this->_action.'Action';}


    public function setAction($action){
        if(is_string($action)){$this->_action = $action;}
        return $this;
    }
    public function setView($view){
        if(is_string($view)){$this->_view = $view;}
        return $this;
    }


    /**
     * Get property from [set data]->[tunnel data]->[request data]->[some default value]->null (use this order)
     *
     * @param string $name  - property name
     * @param null $default - default value
     * @return mixed|null   - property value
     */
    final protected function get($name = '', $default = null){

        /** @var bRequest $bRequest */
        $bRequest = $this->setTrait('bRequest')->getInstance('bRequest');
        $tunnel = $bRequest->getTunnel(get_class($this));
        $request = $bRequest->get($name, $default);

        return(isset($tunnel[$name])?$tunnel[$name]:$request);

    }


    protected function input(Array $data = array()){

        if(isset($data['action'])){
            $this->setAction($data['action']);
        }else{
            $this->setAction($this->get('action', $this->_action));
        }

        if(isset($data['view'])){
            $this->setView($data['view']);
        }else{
            $this->setView($this->get('view', $this->_view));
        }

        $this->configure($data);
    }

    /**
     * Do router function
     *  - provide self into child
     *  - call needed action
     * 
     * @return mixed    - action result
     */
    final public function output(){
        if ($this->_parent instanceof bBlib) return $this;

        $action = $this->getAction();

        if (method_exists($this, $action)){
            return $this->$action();
        }else{
            return $this->indexAction();
        }

    }

    /**
     * Default action
     */
    public function indexAction(){

    }

    /**
     * Default configuration method
     * @param $data     - some input data
     */
    protected function configure($data = null){

    }

}