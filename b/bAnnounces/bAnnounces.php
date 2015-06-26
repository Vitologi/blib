<?php
defined('_BLIB') or die;

/**
 * Class bAnnounces	- for work with announces(news block)
 * Included patterns:
 * 		MVC	- use input property like action and view for create code logic
 */
class bAnnounces extends bBlib{

    /** @var bRequest  $_request */
    protected $_request   = null;
    /** @var bAnnounces__bDataMapper $_db */
    protected $_db        = null;
    /** @var static $_decorator */
    protected $_decorator = null;
    /** @var bConverter__instance $_converter */
    protected $_converter = null;

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

        $this->_request   = $this->getInstance('request', 'bRequest');
        $this->_db        = $this->getInstance('db', 'bAnnounces__bDataMapper');
        $this->_decorator = $this->getInstance('decorator', 'bDecorator');
        $this->_converter = $this->getInstance('converter', 'bConverter');

        $tunnel  = (array)$this->_request->get(__CLASS__);
        $request = array(
            'count' => $this->_request->get('count'),
            'limit' => $this->_request->get('limit')
        );

		// Glue request params
		$this->_mvc     = array_replace($this->_mvc, $request, $tunnel, $data);
	}

	/**
	 *	Handle request
     */
	public function output(){

		$mvc = $this->_mvc;

		switch($mvc['action']){
			case 'index':
			default:
				$list = $this->_decorator->getAnnounces($mvc['count'], $mvc['limit']);
				break;
		}

		switch($mvc['view']){

			case 'index':
			default:

                $this->_converter->setData($list)->setFormat('array')->convertTo('json');
                $this->_converter->output();
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
        return $this->_db->getList(array('from'=>$count, 'count'=>$limit));
	}

}