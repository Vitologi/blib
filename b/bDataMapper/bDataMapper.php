<?php
defined('_BLIB') or die;


/**
 * Class bDataMapper	- factory class for set Data Mappers in blocks
 * Included patterns:
 * 		singleton		- one factory
 * 		factory method	- for create needed Data Mapper to block
 * 		decorator		- decorate base Data Mapper by concrete block's Data Mapper
 *
 */
class bDataMapper extends bBlib{

	/** @var null|static - singleton instance */
	private static $_instance = null;

	/** @var string[] - included traits */
	protected $_traits = array('bSystem');

	/** @var bDataMapper__instance[] - associative array of mappers */
	private $_mappers = array();


	/**
	 * Overload object factory for Singleton
	 *
	 * @return null|static
	 */
	static public function create() {
		if (self::$_instance === null)self::$_instance = parent::create(func_get_args());
		return self::$_instance;
	}


	/**
	 * Extend child class by Data Mapper
	 *
	 * @return bDataMapper__instance	- block's Data Mapper or default instance
     */
	public function output(){
		$concreteMapper = get_class($this->_parent).'__'.__CLASS__;
		return $this->getDataMapper($concreteMapper);
	}

	/**
	 * Mappers factory
	 *
	 * @param string|bBlib $name 		- mapper class name
	 * @return bDataMapper__instance 	- mapper instance
	 */
	public function getDataMapper($name = ''){

		// if already have
		if(array_key_exists($name, $this->_mappers)){
			return $this->_mappers[$name];
		}

		// if can create
		if(class_exists($name)){
			return $this->_mappers[$name] = $name::create()->setParent(bDataMapper__instance::create());
		}

		// default Data Mapper
		return $this->_mappers[$name] = bDataMapper__instance::create();
	}

	/**
	 * Get Data Mapper from child block
	 *
	 * @param bBlib $caller					- block-initiator
	 * @return bDataMapper__instance|null	- block's Data Mapper
     */
	public static function _getDataMapper(bBlib $caller){
		return $caller->getInstance('bDataMapper');
	}
}