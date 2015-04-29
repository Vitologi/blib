<?php
defined('_BLIB') or die;

/**
 * Class bUser__bDataMapper - realisation of Data Mapper for configuration block
 */
class bVisit__bDataMapper extends bDataMapper{

    /**
     * Visit object
     *
     * @typedef array \Visit {
     * @type int $id            - template id
     * @type string $login      - template owner (block)
     * @type string $ip       - template name
     * @type string $time    - block-handler (template provide to  block`s constructor for create concrete instance)
     * @type string $note   - json serialized string
     * }
     */


    /**
     * Get template by id
     *
     * @param null $id
     * @return stdClass - data-object {Template}
     */
    public function getItem($id = null){

        // Empty config object
        $prototype = (object)array('id'=>null, 'login'=>null, 'ip'=>null, 'time'=>null, 'note'=>null);

        if($id === null)return $prototype;

        $query = $this->getDatabase()->prepare('SELECT * FROM `bvisit` AS `table` WHERE `table`.`login` LIKE  :login');
        $query->bindParam(':login', $login, PDO::PARAM_STR);

        $query->execute();
        if(!$result = $query->fetch(PDO::FETCH_ASSOC))return $prototype;


        return (object) $result;
    }


    /**
     * Get templates list from database
     *
     * @param array $list
     * @param string $owner     - template block - owner
     * @return null|object - data-array
     */
    public function getList($login = null){

        $prototype = array();

        if($login == null)return $prototype;

        $query = $this->getDatabase()->prepare('SELECT * FROM `bvisit` AS `table` WHERE `table`.`login` LIKE  :login ORDER BY `time` DESC LIMIT 0,10');
        $query->bindParam(':login', $login, PDO::PARAM_STR);
        $query->execute();

        if(!$result= $query->fetchAll(PDO::FETCH_ASSOC))return $prototype;

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
                $query = $this->getDatabase()->prepare('UPDATE `bvisit` SET `login` = :login,  `ip` = :ip, `time` = :time, `note` = :note  WHERE `id` = :id ;');
                $query->bindParam(':id', $obj->id, PDO::PARAM_INT);
                $query->bindParam(':login', $obj->login, PDO::PARAM_INT);
                $query->bindParam(':ip', $obj->ip, PDO::PARAM_STR);
                $query->bindParam(':time', $obj->time, PDO::PARAM_STR);
                $query->bindParam(':note', $obj->note, PDO::PARAM_STR);
                $query->execute();
            }else{
                $query = $this->getDatabase()->prepare('INSERT INTO `bvisit` (`login`,`ip`,`time`,`note`) VALUES (:login,:ip,:time,:note);');
                $query->bindParam(':login', $obj->login, PDO::PARAM_INT);
                $query->bindParam(':ip', $obj->ip, PDO::PARAM_STR);
                $query->bindParam(':time', $obj->time, PDO::PARAM_STR);
                $query->bindParam(':note', $obj->note, PDO::PARAM_STR);
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

        // Create script
        $query = $this->getDatabase()->prepare("
           CREATE TABLE IF NOT EXISTS `bvisit` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary key',
              `login` varchar(45) NOT NULL COMMENT 'user id',
              `ip` varchar(15) NOT NULL COMMENT 'ip address',
              `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'entry time',
              `note` varchar(64) NULL COMMENT 'arbitrary comment',
              PRIMARY KEY (`id`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Table for store users visits';
        ");

        return $query->execute();
    }


    /**
     * Uninstall table in database
     *
     * @return bool
     */
    public function uninstall(){
        $query = $this->getDatabase()->prepare("DROP TABLE IF EXISTS `bvisit`;");
        return $query->execute();
    }

}