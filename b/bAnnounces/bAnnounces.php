<?php
defined('_BLIB') or die;

/**
 * Class bAnnounces	- for work with announces(news block)
 * Included patterns:
 * 		MVC	- use input property like action and view for create code logic
 */
class bAnnounces extends bBlib{

	/**
	 * @var array	- traits
     */
	protected $_traits = array('bRequest', 'bAnnounces__bDataMapper', 'bDecorator', 'bConverter');

	/**
	 * @var array	default params
     */
	protected $_mvc = array(
		'action' => 'index',
		'view'   => 'index',
		'count'  => 0,			// news count
		'limit'  => 8			// get limit rows from db
	);


	/**
	 * @param array $data	- get request data
     */
	protected function input($data = array()){

		/** @var bRequest $bRequest */
		$bRequest = $this->getInstance('bRequest');

		$tunnel   = $bRequest->getTunnel(__CLASS__);
		$request = array('count' => $bRequest->get('count'), 'limit' => $bRequest->get('limit'));

		// Glue request params
		$this->_mvc     = array_replace($this->_mvc, $request, $tunnel, $data);
	}

	/**
	 *	Handle request
     */
	public function output(){

		/** @var static $bDecorator */
		$bDecorator = $this->getInstance('bDecorator');

		/** @var bConverter__instance $bConverter */
		$bConverter = $this->getInstance('bConverter');

		$mvc = $this->_mvc;

		switch($mvc['action']){
			case 'index':
			default:
				$list = $bDecorator->getAnnounces($mvc['count'], $mvc['limit']);
				break;
		}

		switch($mvc['view']){

			case 'index':
			default:

				$bConverter->setData($list)->setFormat('array')->convertTo('json');
				$bConverter->output();
				exit;
				break;
		}

	}

	/**
	 * Get announce from database
	 *
	 * @param int $count	- count curent news
	 * @param int $limit	- row count
	 * @return null|object
	 * @throws Exception
     */
	public function getAnnounces($count = 0, $limit = 8){

		/** @var bAnnounces__bDataMapper $bDataMapper */
		$bDataMapper = $this->getInstance('bAnnounces__bDataMapper');

		return $bDataMapper->getList(array('from'=>$count, 'count'=>$limit));

	}

}