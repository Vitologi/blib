<?php
defined('_BLIB') or die;

class bPayonline extends bController{

    protected $_amount = 0;

    /**
     * Прием пользовательских параметров
     *
     * @param null $data    - templates data
     */
	protected function configure($data = null){

        $this->setInstance('model', 'bPayonline__model');
        $this->setInstance('view', 'bPayonline__view');
        $this->setInstance('this', 'bDecorator');

        //указанная пользователем сумма платежа
        $this->_amount = $this->get('amount', 0);

	}

    public function getSecureDataAction(){

        $_model = $this->getInstance('model');
        $_view = $this->getInstance('view');
        $_this = $this->getInstance('this');


        $_model->setVars('amount', $this->_amount);

        $_this->hook();

        $data  = $_model->getSecureData();
        $error = $_model->getError();

        if($error){
            $_view->setVars('error', $error);
        }else{
            $_view->setVars('data', $data);
        };

        switch($this->getView()){

            default:
                return $_view->json();
                break;
        }

    }

    public function hook(){}

}