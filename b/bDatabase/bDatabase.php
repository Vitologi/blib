<?php
defined('_BLIB') or die;

/**
 * Class bDatabase - main controller for work with databases
 * Included patterns:
 * 		singleton					- one database controller
 * 		(in next version)strategy 	- many types database object
 */
class bDatabase extends bBlib{

    /** @var bConfig $_config */
    protected $_config = null;

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

	protected function input(){
        $this->_config = $this->getInstance('config', 'bConfig');
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

			// get connections properties
			$connections = $this->_config->getConfig(__CLASS__);
			$connections = $connections["connections"];

			if(!isset($connections[$name]))throw new Exception('Can`t find connection "'.$name.'" in db configuration');

			$db = array_replace(array('host'=>'', 'database'=>'', 'user'=>'', 'password'=>'', 'persistent'=>false), $connections[$name]);

			$dsn = sprintf('mysql:host=%1$s;dbname=%2$s', $db['host'], $db['database']);
			$attrs = array();
            if($db['persistent'])$attrs[PDO::ATTR_PERSISTENT]=true;

			$pdo = new PDO($dsn, $db['user'], $db['password'], $attrs);
			$pdo->query("SET NAMES utf8");

			$this->_db[$name] = $pdo;
		}

		return $this->_db[$name];
	}

}