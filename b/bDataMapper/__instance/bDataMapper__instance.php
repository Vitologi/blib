<?php
defined('_BLIB') or die;

/**
 * Class bDatabase__mapper - realisation of Data Mapper pattern
 */
class bDataMapper__instance extends bBlib{

    /**
     * @var string[]    - included traits
     */
    protected $_traits = array('bDatabase', 'bDecorator');

    /**
     * @var string      - name of default connection to db
     */
    protected $_connectionName = 'default';

    /**
     * Get needed connection from database-controller
     *
     * @return null|PDO     - PDO object
     * @throws Exception
     */
    final public function getDatabase(){
        return $this->getInstance('bDatabase')->getDatabase($this->_connectionName);
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
     * Example for get single Item from table
     *
     * @return null|object      - data-object
     */
    public function getItem(){
        if(func_num_args()===0){
            return (object)array('id'=>null,'value'=>null);
        }


        $id = func_get_arg(0);
        $query = $this->getDatabase()->prepare('SELECT * FROM `bdatabase__mapper` AS `table` WHERE `table`.`id`=:id');
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();

        if(!$result = $query->fetch(PDO::FETCH_ASSOC))return null;
        return (object)$result;
    }

    /**
     * Get list of data (not completed) 0_0
     *
     * @return null|object      - data-array
     */
    public function getList(){
        $query = $this->getDatabase()->prepare('SELECT * FROM `bdatabase__mapper`');
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
                $query = $this->getDatabase()->prepare('UPDATE `bdatabase__mapper` SET `value` = :value WHERE `id` = :id ;');
                $query->bindParam(':id', $obj->id, PDO::PARAM_INT);
                $query->bindParam(':value', $obj->value, PDO::PARAM_INT);
                $query->execute();
            }else{
                $query = $this->getDatabase()->prepare('INSERT INTO `bdatabase__mapper` (`value`) VALUES (:value) ;');
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
            CREATE TABLE IF NOT EXISTS `bdatabase__mapper` (
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
        $query = $this->getDatabase()->prepare('DROP TABLE IF EXISTS `bdatabase__mapper`;');
        return $query->execute();
    }

}