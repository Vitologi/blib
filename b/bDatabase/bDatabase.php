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
			$config = $this->_config->getConfig(__CLASS__);
			$connections = $config["connections"];
			foreach($connections as $key => $value){
				if($value['name'] === $name) $connect = $value;
			}
			if(!isset($connect)) throw new Exception('Can`t find connection "'.$name.'" in db configuration');

			$db = array_replace(array('provider' => '', 'host' => '', 'database' => '', 'user' => '', 'password' => '', 'persistent' => false), $connect);
			$dsn = sprintf('%1$s:host=%2$s;dbname=%3$s', $db['provider'], $db['host'], $db['database']);

			$attrs = array();
			if($db['persistent']) $attrs[PDO::ATTR_PERSISTENT] = true;
			$pdo = new PDO($dsn, $db['user'], $db['password'], $attrs);
			if($db['provider']==='mysql') $pdo->query("SET NAMES utf8");
			$this->_db[$name] = $pdo;
		}

		return $this->_db[$name];
	}

}