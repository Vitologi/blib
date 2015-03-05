<?php
defined('_BLIB') or die;

/**
 * Class bRewrite__bDataMapper - realisation of Data Mapper for rewrite block (output web pages)
 */
class bRewrite__bDataMapper extends bDataMapper__instance{

    /**
     * Rewrite object
     *
     * @typedef array \Rewrite {
     * @type int $id        - page id
     * @type array $tree    - template tree of page
     * }
     */

    /**
     * Get single Item by url from table
     *
     * @param null|string $url      - rewrite url
     * @return stdClass             - data-object {Rewrite}
     */
    public function getRewrite($url = null){

        // Empty config object
        $prototype = (object)array('id'=>null, 'url'=>null, 'bindex_id'=>null, 'data'=>null);

        if($url===null)return $prototype;

        $query = $this->getDatabase()->prepare('SELECT * FROM `brewrite` AS `table` WHERE `table`.`url` LIKE  :url');
        $query->bindParam(':url', $url, PDO::PARAM_INT);

        $query->execute();
        if(!$result = $query->fetch(PDO::FETCH_ASSOC))return $prototype;

        $result['data'] = json_decode($result['data'],true);

        return (object) $result;
    }

    /**
     * Get single Item by id from table
     *
     * @param null|int $id      - rewrite id
     * @return stdClass         - data-object {Rewrite}
     */
    public function getItem($id = null){

        // Empty config object
        $prototype = (object)array('id'=>null, 'url'=>null, 'bindex_id'=>null, 'data'=>null);

        if($id===null)return $prototype;

        $query = $this->getDatabase()->prepare('SELECT * FROM `brewrite` AS `table` WHERE `table`.`id` LIKE  :id');
        $query->bindParam(':id', $id, PDO::PARAM_INT);

        $query->execute();
        if(!$result = $query->fetch(PDO::FETCH_ASSOC))return $prototype;

        $result['data'] = json_decode($result['data'],true);

        return (object) $result;
    }


    /**
     * Get list of data (not completed)
     *
     * @param null|array $list  - rewrite rules list
     * @return null|object - data-array
     */
    public function getList($list = null){
        $prototype = array();

        if($list == null)return $prototype;

        $whereIn = implode(',', array_fill(0, count($list), '?'));
        $query = $this->getDatabase()->prepare('SELECT * FROM `brewrite` AS `table` WHERE `table`.`id` IN  ('.$whereIn.')');

        $query->execute($list);

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

            if(is_array($obj->data))$obj->data = json_encode($obj->data,true);

            if(isset($obj->id)){
                $query = $this->getDatabase()->prepare('UPDATE `brewrite` SET `url` = :url, SET `bindex_id` = :bindex_id, SET `data` = :data WHERE `id` = :id ;');
                $query->bindParam(':id', $obj->id, PDO::PARAM_INT);
                $query->bindParam(':url', $obj->url, PDO::PARAM_STR);
                $query->bindParam(':bindex_id', $obj->bindex_id, PDO::PARAM_INT);
                $query->bindParam(':data', $obj->data, PDO::PARAM_STR);
                $query->execute();
            }else{
                $query = $this->getDatabase()->prepare('INSERT INTO `brewrite` (`url`,`bindex_id`,`data`) VALUES (:url,:bindex_id,:data);');
                $query->bindParam(':url', $obj->url, PDO::PARAM_STR);
                $query->bindParam(':bindex_id', $obj->bindex_id, PDO::PARAM_INT);
                $query->bindParam(':data', $obj->data, PDO::PARAM_STR);
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
            CREATE TABLE IF NOT EXISTS `brewrite` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Rewrite compliance',
                `url` text NOT NULL COMMENT 'rewrite url',
                `data` text NOT NULL COMMENT 'rewrite data',
                `bindex_id` int(10) unsigned NOT NULL COMMENT 'foreign key to bindex table',
                PRIMARY KEY (`id`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

            --
            -- Дамп данных таблицы `brewrite`
            --

            INSERT INTO `brewrite` (`id`, `url`, `bindex_id`, `data`) VALUES
                (1, '/adminpanel/', 2),
                (2, '/analitics/grafics/', 3),
                (3, '/analitics/examples/', 4),
                (4, '/documentation/api/frontend/', 5),
                (5, '/documentation/api/backend/', 6),
                (6, '/documentation/base/', 2),
                (7, '/documentation/faq/', 7),
                (8, '/downloads/', 8);
        ");

        return $query->execute();
    }


    /**
     * Uninstall table in database
     *
     * @return bool
     */
    public function uninstall(){
        $query = $this->getDatabase()->prepare("DROP TABLE IF EXISTS `brewrite`;");
        return $query->execute();
    }

}