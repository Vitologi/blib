<?php
defined('_BLIB') or die;

/**
 * Class bConfig__database__bDataMapper - realisation of Data Mapper for configuration block
 */
class bConfig__database__bDataMapper extends bDataMapper{

    /**
     * Configuration object
     *
     * @typedef array \Configuration {
     * @type int $id                - config id
     * @type string $name           - config name
     * @type mixed $value           - config value
     * @type int|null $bconfig_id   - parent (for merging)
     * }
     */

    /**
     * Get configuration by name from table
     *
     * @return stdClass      - data-object {Configuration}
     */
    public function getItem(){

        // Empty config object
        $prototype = (object)array('id'=>null, 'name'=>null, 'value'=>null, 'bconfig_id'=>null);

        if(func_num_args()===0)return $prototype;

        $name = func_get_arg(0);

        $query = $this->getDatabase()->prepare('SELECT * FROM `bconfig` AS `table` WHERE `table`.`name` LIKE  :name');
        $query->bindParam(':name', $name, PDO::PARAM_STR);

        $query->execute();
        if(!$result = $query->fetch(PDO::FETCH_ASSOC))return $prototype;

        $result['value'] = json_decode($result['value'],true);

        return (object) $result;
    }

    /**
     * Get configuration by name from table
     *
     * @return stdClass      - data-object {Configuration}
     */
    public function getItemById(){

        // Empty config object
        $prototype = (object)array('id'=>null, 'name'=>null, 'value'=>null, 'bconfig_id'=>null);

        if(func_num_args()===0)return $prototype;

        $id = func_get_arg(0);

        $query = $this->getDatabase()->prepare('SELECT * FROM `bconfig` AS `table` WHERE `table`.`id` LIKE  :id');
        $query->bindParam(':id', $id, PDO::PARAM_STR);

        $query->execute();
        if(!$result = $query->fetch(PDO::FETCH_ASSOC))return $prototype;

        $result['value'] = json_decode($result['value'],true);

        return (object) $result;
    }

    /**
     * Merge to parent configs
     *
     * @param stdClass $obj     - data-object
     * @return stdClass         - modified data-object
     */
    public function mergeItem(stdClass $obj){


        if(!$obj->bconfig_id)return $obj;
        $parent = $obj->bconfig_id;

        do{

            $query = $this->getDatabase()->prepare('SELECT * FROM `bconfig` WHERE `bconfig`.`id`=:parent');
            $query->bindParam(':parent', $parent, PDO::PARAM_INT);
            $query->execute();
            $parent = null;

            if(!$result = $query->fetch(PDO::FETCH_ASSOC))break;

            // try to merge (if throw exception do nothing)
            try{
                $temp = json_decode($result['value'],true);
                if($obj->value == null) {
                    $obj->value = $temp;
                }elseif(is_array($temp) and is_array($obj->value)){
                    $obj->value = array_replace_recursive($temp, $obj->value);
                }
            }catch (Exception $e){}

            $parent = $result['bconfig_id'];

        }while($parent);

        return $obj;

    }

    /**
     * Get all config names
     *
     * @return array
     * @throws Exception
     */
    public function getConfigList(){
        $temp = array();
        $query = $this->getDatabase()->prepare('SELECT `table`.`name` FROM `bconfig` AS `table`');

        $query->execute();
        if(!$result = $query->fetchAll(PDO::FETCH_ASSOC))return array();

        foreach($result as $value)$temp[]=$value['name'];

        return $temp;
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

            if(is_array($obj->value))$obj->value=json_encode($obj->value,true);

            if(isset($obj->id)){
                $query = $this->getDatabase()->prepare('UPDATE `bconfig` SET `value` = :value, `bconfig_id` = :bconfig_id WHERE `id` = :id ;');
                $query->bindParam(':id', $obj->id, PDO::PARAM_INT);
                $query->bindParam(':value', $obj->value);
                $query->bindParam(':bconfig_id', $obj->bconfig_id, PDO::PARAM_INT);
                $query->execute();
            }else{
                $query = $this->getDatabase()->prepare('INSERT INTO `bconfig` (`name`,`value`,`bconfig_id`) VALUES (:name,:value,:bconfig_id);');
                $query->bindParam(':name', $obj->name, PDO::PARAM_STR);
                $query->bindParam(':value', $obj->value);
                $query->bindParam(':bconfig_id', $obj->bconfig_id, PDO::PARAM_INT);
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
        $query = $this->getDatabase()->prepare("
            CREATE TABLE IF NOT EXISTS `bconfig` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Table for store configuration',
              `name` varchar(45) DEFAULT NULL COMMENT 'config name',
              `value` text COMMENT 'JSON serialized configurations',
              `bconfig_id` int(10) unsigned DEFAULT NULL COMMENT 'id for default configurations',
              PRIMARY KEY (`id`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
        ");

        return $query->execute();
    }


    /**
     * Uninstall table in database
     *
     * @return bool
     */
    public function uninstall(){
        $query = $this->getDatabase()->prepare("DROP TABLE IF EXISTS `bconfig`;");
        return $query->execute();
    }

}