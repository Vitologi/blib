<?php
/**
 * Created by PhpStorm.
 * User: morozov
 * Date: 07.04.2015
 * Time: 15:29
 */

class bController extends bBlib{

    /** @var bRequest $_request */
    protected $_request = null;
    /** @var string $_action_            - current action */
    protected $_action_  = 'index';
    /** @var string $_view_              - current view layout */
    protected $_view_    = 'index';

    public function getView(){return $this->_view_;}
    public function getAction(){return $this->_action_.'Action';}


    public function setAction($action){
        if(is_string($action)){$this->_action_ = $action;}
        return $this;
    }
    public function setView($view){
        if(is_string($view)){$this->_view_ = $view;}
        return $this;
    }


    protected function input(Array $data = array()){

        $this->_request = $this->getInstance('request','bRequest');
        $_this = $this->getInstance('this','bDecorator');
        
        if(isset($data['action'])){
            $this->setAction($data['action']);
        }else{
            $this->setAction($this->get('action', $this->_action_));
        }

        if(isset($data['view'])){
            $this->setView($data['view']);
        }else{
            $this->setView($this->get('view', $this->_view_));
        }

        $_this->initialize($data);
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
     * Get property from [set data]->[tunnel data]->[request data]->[some default value]->null (use this order)
     *
     * @param string $name  - property name
     * @param null $default - default value
     * @return mixed|null   - property value
     */
    final protected function get($name = '', $default = null){

        $tunnel = (array)$this->_request->get(get_class($this));
        $request = $this->_request->get($name, $default);

        return(isset($tunnel[$name])?$tunnel[$name]:$request);

    }

    /**
     * Default action
     */
    public function indexAction(){

    }

    /**
     * Default configuration method
     */
    public function initialize(){

    }

}