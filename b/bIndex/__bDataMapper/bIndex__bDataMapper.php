<?php
defined('_BLIB') or die;

/**
 * Class bIndex__bDataMapper - realisation of Data Mapper for index block (output web pages)
 */
class bIndex__bDataMapper extends bDataMapper__instance{

    /**
     * Page object
     *
     * @typedef array \Configuration {
     * @type int $id        - page id
     * @type array $tree    - template tree of page
     * }
     */

    /**
     * Get page by id from table
     *
     * @param null|int $id      - page id
     * @return stdClass         - data-object {Page}
     */
    public function getItem($id = null){

        // Empty config object
        $prototype = (object)array('id'=>null, 'tree'=>null);

        if($id===null)return $prototype;

        $query = $this->getDatabase()->prepare('SELECT * FROM `bindex` AS `table` WHERE `table`.`id` LIKE  :id');
        $query->bindParam(':id', $id, PDO::PARAM_INT);

        $query->execute();
        if(!$result = $query->fetch(PDO::FETCH_ASSOC))return $prototype;

        $result['tree'] = json_decode($result['tree'],true);

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

            if(is_array($obj->tree))$obj->value = json_encode($obj->tree,true);

            if(isset($obj->id)){
                $query = $this->getDatabase()->prepare('UPDATE `bindex` SET `tree` = :tree WHERE `id` = :id ;');
                $query->bindParam(':id', $obj->id, PDO::PARAM_INT);
                $query->bindParam(':tree', $obj->tree, PDO::PARAM_STR);
                $query->execute();
            }else{
                $query = $this->getDatabase()->prepare('INSERT INTO `bindex` (`tree`) VALUES (:tree);');
                $query->bindParam(':tree', $obj->tree, PDO::PARAM_STR);
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
            CREATE TABLE IF NOT EXISTS `bindex` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Table for store page carcase',
                `tree` text COMMENT 'used templates tree like {0:1, 1:7, 2:6, 3:{0:4, 1:6}}',
                PRIMARY KEY (`id`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

            --
            -- Дамп данных таблицы `bindex`
            --

            INSERT INTO `bindex` (`id`, `tree`) VALUES
                (1, '{\"0\":1, \"1\":{\"0\":2, \"1\":{\"0\":4}}, \"2\":{\"0\":3}, \"4\":{\"0\":5}}'),
                (2, '{\"0\":10, \"2\":{\"0\":3}, \"3\":{\"0\":11}, \"4\":{\"0\":5}}'),
                (3, '{\"0\":1, \"2\":{\"0\":3}, \"3\":{\"0\":4}, \"4\":{\"0\":1, \"1\":{\"0\":2}, \"2\":{\"0\":3}}}'),
                (4, '{\"0\":1, \"1\":{\"0\":2, \"1\":{\"0\":4}}, \"2\":{\"0\":3}, \"3\":{\"0\":6}, \"4\":{\"0\":5}}'),
                (5, '{\"0\":1, \"1\":{\"0\":2, \"1\":{\"0\":4}}, \"2\":{\"0\":3}, \"3\":{\"0\":8}, \"4\":{\"0\":5}}'),
                (6, '{\"0\":1, \"1\":{\"0\":2, \"1\":{\"0\":4}}, \"2\":{\"0\":3}, \"3\":{\"0\":9}, \"4\":{\"0\":5}}'),
                (7, '{\"0\":5}'),
                (8, '{\"0\":1, \"1\":{\"0\":2, \"1\":{\"0\":4}}, \"2\":{\"0\":3}, \"3\":{\"0\":14}, \"4\":{\"0\":5}}');
        ");

        return $query->execute();
    }


    /**
     * Uninstall table in database
     *
     * @return bool
     */
    public function uninstall(){
        $query = $this->getDatabase()->prepare("DROP TABLE IF EXISTS `bindex`;");
        return $query->execute();
    }

}