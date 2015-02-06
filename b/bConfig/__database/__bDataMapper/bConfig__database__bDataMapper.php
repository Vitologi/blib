<?php
defined('_BLIB') or die;

/**
 * Class bDatabase__mapper - realisation of Data Mapper pattern
 */
class bConfig__database__bDataMapper extends bDataMapper__instance{

    /**
     * Example for get single Item from table
     *
     * @return null|object      - data-object
     */
    public function getItem(){
        if(func_num_args()===0){
            return (object)array('id'=>null,'group'=>null, 'name'=>null, 'value'=>null, 'bconfig_id'=>null);
        }


        $id = func_get_arg(0);
        $query = $this->getDatabase()->prepare('SELECT * FROM `bconfig` AS `table` WHERE `table`.`id`=:id');
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
        $query = $this->getDatabase()->prepare("
            CREATE TABLE IF NOT EXISTS `bconfig` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Table for store configuration in JSON format',
              `group` varchar(45) DEFAULT NULL COMMENT 'settings owner',
              `name` varchar(45) DEFAULT NULL COMMENT 'owner identificator',
              `value` text COMMENT 'JSON serialized configurations',
              `bconfig_id` int(10) unsigned DEFAULT NULL COMMENT 'id for default configurations',
              PRIMARY KEY (`id`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;
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



/**
 * Private method for get configuration
 *
 * @param {string} $name - name of configuration
 * @param {mixed}[] $param - other parameters
 *   {string} group - change config group (default 'blib')
 *   {bollean} deep - get config concat with parents value (default true)
 * @return {array} - associative array with configuration
 *//*
	public function getConfig($name){
		if($name === 'bDatabase')return array();

		return array();

		$param = (array) $param + array('group'=>'blib', 'deep'=>true);
		$used = array();
		$config = array();
		$default = null;

		do{
			$Q = array(
				'select' => array(
					'bconfig'=>array('id', 'value', 'bconfig_id')
				),
				'where' => array(
					'bconfig'=>array('group'=>$param['group'], 'name'=>$name)
				)
			);

			if($default){
				$Q['where']['bconfig']=array('id'=>$default);
				$default = null;
			}

			if($result = $this->_query($Q)){
				$row = $result->fetch();
				$config = (array)$config + (array)json_decode($row['value'],true);
				if($param['deep'] && !in_array($row['bconfig_id'], $used)){
					$used[] = $default = $row['bconfig_id'];
				}
			}

		}while($default);
		return $config;


	}

*/

/**
 * Private method for set configuration
 *
 * @param {string} $name - name of configuration
 * @param {array} $value - configuration array
 * @param {mixed}[] $param - other parameters
 *   {string} group - change config group (default 'blib')
 *   {bollean} correct - set on old configuration values (default true)
 *   {number} parent - change parent config
 * @return {number} - id updated or new item
 *//*
	public function setConfig($name, Array $value, $param){

		return array();

		$param = (array) $param + array('group'=>'blib', 'correct'=>false);

		$value = is_array($value)?$value:array();

		$Q = array(
			'select' => array('bconfig'=>array('id', 'value', 'bconfig_id')),
			'where' => array('bconfig'=>array('group'=>$param['group'], 'name'=>$name))
		);

		$result = $this->_query($Q);

		if($result->rowCount()){
			$row = $result->fetch();

			if($param['correct']){
				$value = $value + (array) json_decode($row['value'], true);
			}

			$value = json_encode($value);

			$Q = array(
				'update' => array('bconfig'=>array('value'=>$value)),
				'where' => array('bconfig'=>array('id'=>$row['id']))
			);

			if(isset($param['parent'])){$Q['update']['bconfig']['bconfig_id'] = $param['parent'];}
			if(!$this->_query($Q)){	throw new Exception('Can`t rewrite config');}
			return $row['id'];


		}else{

			$value = json_encode($value);

			$Q = array(
				'insert' => array(
					'bconfig'=>array(
						'group'=>$param['group'],
						'name'=>$name,
						'value'=>$value
					)
				)
			);

			if(isset($param['parent'])){$Q['insert']['bconfig']['bconfig_id'] = $param['parent'];}
			if(!$this->_query($Q)){throw new Exception('Can`t rewrite config');}
			return $this->_lastInsertId();
		}



	}

	*/