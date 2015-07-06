<?php
defined('_BLIB') or die;

/**
 * Class bUser	- block for stor user authentication data (like id, login, password)
 */
class bVisit extends bController{

	public function initialize($data = null){
        $this->setInstance('user','bUser');
        $this->setInstance('view','bVisit__view');
        $this->setInstance('db','bVisit__bDataMapper');
    }

	public function indexAction(){

		/** @var bUser $_user */
		$_user = $this->getInstance('user');

		/** @var bVisit__bDataMapper $db */
		$_db = $this->getInstance('db');

		/** @var bVisit__view $_view */
		$_view = $this->getInstance('view');

		$login = $_user->getLogin();

		$visits = $_db->getList($login);

        $_view->set('data', $visits);


		switch($this->getView()){

			case "json":
                $_view->json();
				break;

			case "index":
			default:
                $_view->index();
				break;
		}
	}

	public function setVisitAction(){

        /** @var bVisit__bDataMapper $db */
        $_db = $this->getInstance('db');

		$visit = $_db->getItem();
		$visit->login = $this->getVars('login',null);
		$visit->ip = $_SERVER['REMOTE_ADDR'];
		$visit->note = $this->getVars('note',null);
        $_db->save($visit);
	}
}