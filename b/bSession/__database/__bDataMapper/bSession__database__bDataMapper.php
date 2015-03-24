<?php
defined('_BLIB') or die;

/**
 * Class bSession__database__bDataMapper - realisation of Data Mapper for session block
 */
class bSession__database__bDataMapper extends bDataMapper{

    /**
     * Session object
     *
     * @typedef array \Session {
     * @type string $id             - session id
     * @type string $date           - session expire date
     * @type mixed $value           - session value
     * }
     */

    /**
     * Get session by id from table
     *
     * @return stdClass      - data-object {Session}
     */
    public function getItem(){

        // Empty session object
        $prototype = (object)array('id'=>null, 'date'=>null, 'value'=>null);

        if(func_num_args()===0)return $prototype;

        $id = func_get_arg(0);
        $expire = func_get_arg(1);

        if($expire){
            $query = $this->getDatabase()->prepare('SELECT * FROM `bsession` AS `table` WHERE `table`.`id` LIKE  :id AND (UNIX_TIMESTAMP()-:expire < UNIX_TIMESTAMP(`table`.`date`))');
            $query->bindParam(':expire', $expire, PDO::PARAM_INT);
        }else{
            $query = $this->getDatabase()->prepare('SELECT * FROM `bsession` AS `table` WHERE `table`.`id` LIKE  :id');
        }

        $query->bindParam(':id', $id, PDO::PARAM_STR);
        $query->execute();

        if(!$result = $query->fetch(PDO::FETCH_ASSOC))return $prototype;

        // Decode to array
        $result['value'] = json_decode($result['value'],true);

        return (object) $result;
    }

    /**
     * Get list of data (not completed) 0_0
     *
     * @param array $params
     * @return null|object - data-array
     */
    public function getList($params = array()){

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

            // Convert to string
            $value = (is_array($obj->value))?json_encode($obj->value,true):$obj->value;

            if(isset($obj->id)){
                $query = $this->getDatabase()->prepare('UPDATE `bsession` SET `value` = :value WHERE `id` = :id ;');
                $query->bindParam(':id', $obj->id, PDO::PARAM_STR);
                $query->bindParam(':value', $value, PDO::PARAM_STR);
                $query->execute();
            }else{

                $obj->id = md5(microtime(true).$_SERVER['REMOTE_ADDR']);

                $query = $this->getDatabase()->prepare('INSERT INTO `bsession` (`id`,`value`) VALUES (:id,:value);');
                $query->bindParam(':id',  $obj->id, PDO::PARAM_STR);
                $query->bindParam(':value', $value, PDO::PARAM_STR);
                $query->execute();
            }

        }catch (PDOException $e){
            throw new Exception('Database error('.$e->getCode().') '.$e->errorInfo);
        }

        return $this;
    }


    /**
     * Install empty table to database
     *
     * @return bool
     */
    public function install(){
        $query = $this->getDatabase()->prepare("
            CREATE TABLE IF NOT EXISTS `bsession` (
                `id` char(32) NOT NULL COMMENT 'Table for store session data in JSON format, as key use md5',
                `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Session date',
                `value` text COMMENT 'JSON serialized session data',
                PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
        ");

        return $query->execute();
    }


    /**
     * Uninstall table from database
     *
     * @return bool
     */
    public function uninstall(){
        $query = $this->getDatabase()->prepare("DROP TABLE IF EXISTS `bsession`;");
        return $query->execute();
    }

}