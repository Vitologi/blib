<?php
defined('_BLIB') or die;

class bPayonline extends bController{

    protected $_traits = array('bPayonline__view', 'bPayonline__model', 'bDecorator');
    protected $_amount = 0;

    /**
     * Прием пользовательских параметров
     *
     * @param null $data    - templates data
     */
	protected function configure($data = null){

        //указанная пользователем сумма платежа
        $this->_amount = $this->get('amount', 0);

	}

    public function getSecureDataAction(){

        /** @var bPayonline__model $bPayonline__model */
        $bPayonline__model = $this->getInstance('bPayonline__model');

        /** @var bPayonline__view $bPayonline__view */
        $bPayonline__view = $this->getInstance('bPayonline__view');

        /** @var self $bDecorator   - decorator instance */
        $bDecorator = $this->getInstance('bDecorator');


        $bPayonline__model->setVars('amount', $this->_amount);

        $bDecorator->hook();

        $data  = $bPayonline__model->getSecureData();
        $error = $bPayonline__model->getError();

        if($error){
            $bPayonline__view->setVars('error', $error);
        }else{
            $bPayonline__view->setVars('data', $data);
        };

        switch($this->getView()){

            default:
                return $bPayonline__view->json();
                break;
        }

    }

    public function hook(){}

}