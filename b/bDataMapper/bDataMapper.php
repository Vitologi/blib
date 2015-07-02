<?php
defined('_BLIB') or die;


/**
 * Class bDataMapper	- factory class for set Data Mappers in blocks
 * Included patterns:
 * 		singleton		- one factory
 * 		factory method	- for create needed Data Mapper to block
 * 		data mapper		- collection method for work with database
 *
 */
class bDataMapper extends bBlib{

    /** @var bDatabase $_db  */
    protected $_db = null;

	/** @var bDataMapper[] $_mappers - singleton collections */
	private static $_mappers = array();

	/** @var string      - name of default connection to db */
	protected $_connectionName = 'default';

	/**
	 * Overload object factory for Singleton
	 *
	 * @return null|static
	 */
	final static public function create() {
		$caller = get_called_class();

		if (!isset(self::$_mappers[$caller]))self::$_mappers[$caller] = parent::create(func_get_args());

		return self::$_mappers[$caller];
	}


    protected function input(){
        $this->_db = $this->getInstance('db', 'bDatabase');
    }

	/**
	 * Extend child class by Data Mapper
	 *
	 * @return bDataMapper	- block's Data Mapper or default instance
     */
	final public function output(){
		return $this;
	}

	/**
	 * Get needed connection from database-controller
	 *
	 * @return null|PDO     - PDO object
	 * @throws Exception
	 */
	final protected function getDatabase(){
        return $this->_db->getDatabase($this->_connectionName);
	}

	/**
	 * Change default connection to database (change word database)
	 *
	 * @param string $name      - connection settings name
	 * @return $this            - for chaining
	 */
	final public function connect($name = 'default'){
		$this->_connectionName = $name;
		return $this;
	}


	/**
	 * Mappers factory
	 *
	 * @param string|bBlib $name 		- mapper class name
	 * @return bDataMapper			 	- mapper instance
	 */
	final public function getDataMapper($name = ''){

		// if already have
		if(array_key_exists($name, self::$_mappers)){
			return self::$_mappers[$name];
		}

		// if can create
		if(class_exists($name)){
			return self::$_mappers[$name] = $name::create();
		}

		// default Data Mapper
		return $this;
	}


	/**
	 * Example for get single Item from table
	 *
	 * @return null|stdClass    - data-object
	 */
	public function getItem(){

		// empty object
		if(func_num_args()===0){
			return (object)array('id'=>null,'value'=>null);
		}


		$id = func_get_arg(0);
		$query = $this->getDatabase()->prepare('SELECT * FROM `bdatamapper` AS `table` WHERE `table`.`id`=:id');
		$query->bindParam(':id', $id, PDO::PARAM_INT);
		$query->execute();

		if(!$result = $query->fetch(PDO::FETCH_ASSOC))return null;
		return (object)$result;
	}

	/**
	 * Get list of data (not completed) 0_0
	 *
	 * @return null|array   - data-array
	 */
	public function getList(){
		$query = $this->getDatabase()->prepare('SELECT * FROM `bdatamapper`');
		$query->execute();

		if(!$result = $query->fetchAll(PDO::FETCH_ASSOC))return null;
		return $result;
	}

	/**
	 * Handler for saving or update single Item
	 *
	 * @param stdClass $obj     - instance of data-object
	 * @return $this            - for chaining
	 * @throws Exception
	 */
	public function save(stdClass &$obj){

		try{

			if(isset($obj->id)){
				$query = $this->getDatabase()->prepare('UPDATE `bdatamapper` SET `value` = :value WHERE `id` = :id ;');
				$query->bindParam(':id', $obj->id, PDO::PARAM_INT);
				$query->bindParam(':value', $obj->value, PDO::PARAM_INT);
				$query->execute();
			}else{
				$query = $this->getDatabase()->prepare('INSERT INTO `bdatamapper` (`value`) VALUES (:value) ;');
				$query->bindParam(':value', $obj->value, PDO::PARAM_INT);
				$query->execute();
				$obj->id = $this->getDatabase()->lastInsertId();
			}

		}catch (PDOException $e){
			throw new Exception('Database error('.$e->getCode().') '.$e->errorInfo);
		}

		return $this;
	}


	/**
	 * Install empty table in database
	 *
	 * @return bool
	 */
	public function install(){
		$query = $this->getDatabase()->prepare('
            CREATE TABLE IF NOT EXISTS `bdatamapper` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `value` INT,
                PRIMARY KEY (`id`)
            )
            ENGINE = MyISAM
            DEFAULT CHARACTER SET = utf8
            COLLATE = utf8_general_ci;
        ');

		return $query->execute();
	}

	/**
	 * Uninstall table in database
	 *
	 * @return bool
	 */
	public function uninstall(){
		$query = $this->getDatabase()->prepare('DROP TABLE IF EXISTS `bdatamapper`;');
		return $query->execute();
	}

}