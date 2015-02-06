<?php
defined('_BLIB') or die;

/**
 * Class bDatabase - main controller for work with databases
 */
class bDataMapper extends bBlib{

	/**
	 * @var null|static - singleton instance
	 */
	private static $_instance = null;

	/**
	 * @var string[] - included traits
	 */
	protected $_traits = array('bSystem', 'bConfig');

	/**
	 * @var bDataMapper__instance[] - associative array of mappers
	 */
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


	public function output(){
		$concreteMapper = get_class($this->_parent).'__'.__CLASS__;
		return $this->getDataMapper($concreteMapper);
	}

	/**
	 * Mappers factory
	 *
	 * @param string|bBlib $name 				- mapper class name
	 * @return bDataMapper__instance 	- mapper instance
	 */
	public function getDataMapper($name = ''){

		if(array_key_exists($name, $this->_mappers)){
			return $this->_mappers[$name];
		}

		if(class_exists($name)){
			return $this->_mappers[$name] = $name::create()->setParent(bDataMapper__instance::create());
		}

		return $this->_mappers[$name] = bDataMapper__instance::create();
	}

	public static function _getDataMapper(bBlib $caller){
		return $caller->getInstance('bDataMapper');
	}
}