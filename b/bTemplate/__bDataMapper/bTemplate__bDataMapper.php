<?php
defined('_BLIB') or die;

/**
 * Class bTemplate__bDataMapper - realisation of Data Mapper for template block
 */
class bTemplate__bDataMapper extends bDataMapper{

    /**
     * Template object
     *
     * @typedef array \Template {
     * @type int $id            - template id
     * @type string $owner      - template owner (block)
     * @type string $name       - template name
     * @type string $handler    - block-handler (template provide to  block`s constructor for create concrete instance)
     * @type string $template   - json serialized string
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
        $prototype = (object)array('id'=>null, 'owner'=>null, 'name'=>null, 'handler'=>null, 'template'=>null);

        if($id === null)return $prototype;

        $query = $this->getDatabase()->prepare('SELECT * FROM `btemplate` AS `table` WHERE `table`.`id` LIKE  :id');
        $query->bindParam(':id', $id, PDO::PARAM_INT);

        $query->execute();
        if(!$result = $query->fetch(PDO::FETCH_ASSOC))return $prototype;

        $items = array($result);
        $this->serializeTemplate($items);

        return (object) $items[0];
    }


    /**
     * Serialize templates
     *
     * @param array $items  - array of template`s item
     * @void                - substitute values of templates on block`s output
     * @return array        - serialized array
     */
    private function serializeTemplate(&$items = array()){

        foreach($items as $key => &$item){

            /** @var string|bBlib $handler - instance of some block*/
            $handler = $item['handler'];

            if(strlen($handler)>0){
                $arguments = json_decode($item['template'],true);
                $output = $handler::create($arguments)->setParent(null)->output();
                $item['template'] = (is_array($output)?json_encode($output,256):$output);
            }
        }

        return $items;

    }


    /**
     * Get template by name and owner
     *
     * @param null|string $name - template name
     * @param string $owner     - template block - owner
     * @return stdClass - data-object {Template}
     */
    public function getTemplate($name = null, $owner = '%'){

        // Empty config object
        $prototype = (object)array('id'=>null, 'owner'=>null, 'name'=>null, 'handler'=>null, 'template'=>null);

        if($name == null)return $prototype;

        $query = $this->getDatabase()->prepare('SELECT * FROM `btemplate` AS `table` WHERE `table`.`name` LIKE  :name AND `table`.`owner` LIKE  :owner');
        $query->bindParam(':name', $name, PDO::PARAM_STR);
        $query->bindParam(':owner', $owner, PDO::PARAM_STR);

        $query->execute();

        if(!$result= $query->fetch(PDO::FETCH_ASSOC))return $prototype;

        $items = array($result);
        $this->serializeTemplate($items);

        return (object) $items[0];

    }

    /**
     * Get templates list from database
     *
     * @param array $list
     * @param string $owner     - template block - owner
     * @return null|object - data-array
     */
    public function getList(Array $list = null, $owner = null){

        $prototype = array();
        $ownStatement = '';

        if($list == null)return $prototype;


        $whereIn = implode(',', array_fill(0, count($list), '?'));

        if($owner !== null){
            $list[] = $owner;
            $ownStatement = ' AND `table`.`owner` LIKE  ? ';
        }

        $query = $this->getDatabase()->prepare('SELECT * FROM `btemplate` AS `table` WHERE `table`.`id` IN  ('.$whereIn.')'.$ownStatement);

        $query->execute($list);

        if(!$result= $query->fetchAll(PDO::FETCH_ASSOC))return $prototype;

        return $this->serializeTemplate($result);

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

            if(is_array($obj->template))$obj->template=json_encode($obj->template,true);

            if(isset($obj->id)){
                $query = $this->getDatabase()->prepare('UPDATE `btemplate` SET `owner` = :owner,  `name` = :name, `handler` = :handler, `template` = :template WHERE `id` = :id ;');
                $query->bindParam(':id', $obj->id, PDO::PARAM_INT);
                $query->bindParam(':owner', $obj->owner, PDO::PARAM_STR);
                $query->bindParam(':name', $obj->name, PDO::PARAM_STR);
                $query->bindParam(':handler', $obj->handler, PDO::PARAM_STR);
                $query->bindParam(':template', $obj->template, PDO::PARAM_STR);
                $query->execute();
            }else{
                $query = $this->getDatabase()->prepare('INSERT INTO `btemplate` (`owner`,`name`,`handler`,`template`) VALUES (:owner,:name,:handler,:template);');
                $query->bindParam(':owner', $obj->owner, PDO::PARAM_STR);
                $query->bindParam(':name', $obj->name, PDO::PARAM_STR);
                $query->bindParam(':handler', $obj->handler, PDO::PARAM_STR);
                $query->bindParam(':template', $obj->template, PDO::PARAM_STR);
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
            CREATE TABLE IF NOT EXISTS `btemplate` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Table for store json templates',
                `owner` varchar(45) DEFAULT NULL COMMENT 'block owner',
                `name` varchar(45) NOT NULL COMMENT 'templates name',
                `handler` varchar(45) DEFAULT NULL COMMENT 'the block that handle template',
                `template` text COMMENT 'json serialised template',
                PRIMARY KEY (`id`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


            INSERT INTO `btemplate` (`id`, `owner`, `name`, `handler`, `template`) VALUES
                (1, '', 'index', '', '{\"block\":\"bIndex\",\"mods\":{\"blib\":true},\"content\":[{\"elem\":\"header\",\"content\":[\"{1}\"]},{\"elem\":\"tools\",\"content\":[\"{2}\"]},{\"elem\":\"body\",\"content\":[\"{3}\",{\"elem\":\"clear\"}]},{\"elem\":\"footer\",\"content\":[\"{4}\"]}]}'),
                (2, NULL, 'logo', '', '{\"attrs\":{\"style\":\"float:right;\"},\"content\":[\"{1}\"]},{\"block\":\"bImageSprite\",\"mods\":{\"sprite\":\"blib\",\"type\":\"logo\"}}'),
                (3, NULL, 'menu', 'bMenu', '{\"menu\":1, \"mods\":{\"position\":\"horizontal\", \"default\":true}}'),
                (4, NULL, 'auth', 'bUser', '{\"mods\":{\"style\":\"default\"}}'),
                (5, NULL, 'footer', '', '{\"content\":[{\"content\":\"© Vitologi 2013-2014\", \"attrs\":{\"style\":\"padding:5px;color:#999999;\"}}, {\"block\":\"bTudaSuda\"}]}'),
                (6, 'bPanel', 'template', NULL, '{\"content\":[{\"block\":\"bPanel\",\"elem\":\"side\",\"content\":[{\"tag\":\"center\",\"content\":\"Блоки\"},\"{1}\"]},{\"block\":\"bPanel\",\"elem\":\"parent\",\"content\":[\"{2}\"]}, {\"block\":\"bPanel\",\"elem\":\"tools\",\"content\":[\"{3}\"]},{\"block\":\"bPanel\",\"elem\":\"content\",\"content\":[\"{4}\"]}]}')


        ");

        return $query->execute();
    }


    /**
     * Uninstall table in database
     *
     * @return bool
     */
    public function uninstall(){
        $query = $this->getDatabase()->prepare("DROP TABLE IF EXISTS `btemplate`;");
        return $query->execute();
    }

}