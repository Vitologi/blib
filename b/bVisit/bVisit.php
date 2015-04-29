<?php
defined('_BLIB') or die;

/**
 * Class bUser	- block for stor user authentication data (like id, login, password)
 */
class bVisit extends bController{

	protected $_traits  = array(/* 'bUser', */ 'bVisit__view', 'bVisit__bDataMapper');

	public function indexAction(){

		/** @var bUser $bUser */
		$bUser = $this->getInstance('bUser');

		/** @var bVisit__bDataMapper $bDataMapper */
		$bDataMapper = $this->getInstance('bVisit__bDataMapper');

		/** @var bVisit__view $bVisit__view */
		$bVisit__view = $this->getInstance('bVisit__view');

		$login = $bUser->getLogin();

		$visits = $bDataMapper->getList($login);

		$bVisit__view->set('data', $visits);


		switch($this->getView()){

			case "json":
				$bVisit__view->json();
				break;

			case "index":
			default:
				$bVisit__view->index();
				break;
		}
	}

	public function setVisitAction(){

		/** @var bVisit__bDataMapper $bDataMapper */
		$bDataMapper = $this->getInstance('bVisit__bDataMapper');

		$visit = $bDataMapper->getItem();
		$visit->login = $this->getVars('login',null);
		$visit->ip = $_SERVER['REMOTE_ADDR'];
		$visit->note = $this->getVars('note',null);
		$bDataMapper->save($visit);
	}
}