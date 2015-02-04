<?php
defined('_BLIB') or die;

/**
 * Class bDatabase - main controller for work with databases
 */
class bDatabase extends bBlib{

	/**
	 * @var null|static - singleton instance
	 */
	private static $_instance = null;
	/**
	 * @var string[] - included traits
	 */
	protected $_traits = array('bSystem', 'bConfig', 'bDecorator');
	/**
	 * @var PDO[] - associative array of connections
	 */
	private $_db = array();
	/**
	 * @var bDatabase__mapper[] - associative array of mappers
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

	/**
	 * @return $this
     */
	public function output(){
		return $this;
	}

	/**
	 * Mappers factory
	 *
	 * @param string|bBlib $name 	- mapper class name
	 * @return bDatabase__mapper 	- mapper instance
	 */
	public function getDataMapper($name = 'bDatabase__mapper')
	{
		if (!array_key_exists($name, $this->_mappers)) {
			$this->_mappers[$name] = $name::create()->setParent($this);
		}
		return $this->_mappers[$name];
	}

	/**
	 * @param string $name	- config name
	 * @return null|PDO		- database object
	 * @throws Exception
     */
	public function getDataBase($name = 'default'){
		if(!array_key_exists($name, $this->_db)){

			$config = $this->_getConfig("connections");
			if(!isset($config[$name]))throw new Exception('Can`t find connection "'.$name.'" in db configuration');

			$db = array_replace(array('host'=>'', 'database'=>'', 'user'=>'', 'password'=>''), $config[$name]);

			$dsn = sprintf('mysql:host=%1$s;dbname=%2$s', $db['host'], $db['database']);
			$pdo = new PDO($dsn, $db['user'], $db['password'], array(PDO::ATTR_PERSISTENT => true));
			$pdo->query("SET NAMES utf8");

			$this->_db[$name] = $pdo;
		}

		return $this->_db[$name];
	}

}