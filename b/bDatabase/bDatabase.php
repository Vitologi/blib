<?php
defined('_BLIB') or die;

/**
 * Class bDatabase - main controller for work with databases
 * Included patterns:
 * 		singleton					- one database controller
 * 		(in next version)strategy 	- many types database object
 */
class bDatabase extends bBlib{

	/** @var string[] - included traits */
	protected $_traits = array('bSystem', 'bConfig');

	/** @var null|static - singleton instance */
	private static $_instance = null;

	/** @var PDO[] - associative array of connections */
	private $_db = array();


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
		return $this;
	}

	/**
	 * Get database object
	 *
	 * @param string $name	- config name
	 * @return null|PDO		- database object
	 * @throws Exception
     */
	public function getDataBase($name = 'default'){
		if(!array_key_exists($name, $this->_db)){

			/** @var bConfig $config 	- configuration block instance */
			$config = $this->getInstance('bConfig');

			// get connections properties
			$connections = $config->getConfig(__CLASS__)["connections"];

			if(!isset($connections[$name]))throw new Exception('Can`t find connection "'.$name.'" in db configuration');

			$db = array_replace(array('host'=>'', 'database'=>'', 'user'=>'', 'password'=>''), $connections[$name]);

			$dsn = sprintf('mysql:host=%1$s;dbname=%2$s', $db['host'], $db['database']);
			$pdo = new PDO($dsn, $db['user'], $db['password'], array(PDO::ATTR_PERSISTENT => true));
			$pdo->query("SET NAMES utf8");

			$this->_db[$name] = $pdo;
		}

		return $this->_db[$name];
	}

}