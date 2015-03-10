<?php
defined('_BLIB') or die;

/**
 * Class bDocumentation__bDataMapper - realisation of Data Mapper for documentation block
 */
class bDocumentation__bDataMapper extends bDataMapper__instance{

    /**
     * Documentation object
     *
     * @typedef array \Documentation {
     * @type int $id                - item id
     * @type string $name           - item name
     * @type string $note           - small description
     * @type string $description    - full description (json format)
     * @type string $group          - included items
     * @type int $bdocumentation_id - parent id (used item id for nesting)
     * }
     */


    /**
     * Get menu item by name from table
     *
     * @param null|int  $id - item id
     * @return stdClass     - data-object {Documentation}
     */
    public function getItem($id = null){

        // Empty object
        $prototype = (object)array('id'=>null, 'name'=>null, 'note'=>null, 'description'=>null ,'group'=>null,'bdocumentation_id'=>null);

        if($id === null)return $prototype;

        $query = $this->getDatabase()->prepare('SELECT * FROM `bdocumentation` AS `table` WHERE `table`.`id` LIKE  :id');
        $query->bindParam(':id', $id, PDO::PARAM_INT);

        $query->execute();
        if(!$result = $query->fetch(PDO::FETCH_ASSOC))return $prototype;

        $result['description'] = ($result['description']!== null)?json_decode($result['description'],true):null;
        $result['group'] = ($result['group']!== null)?json_decode($result['group'],true):array();

        return (object) $result;
    }


    /**
     * Get list of data
     *
     * @param array $list   -
     * @return null|object - data-array
     */
    public function getList(Array $list = null){

        $prototype = array();

        if($list == null){
            $query = $this->getDatabase()->prepare('SELECT `id`, `name`, `bdocumentation_id` FROM `bdocumentation` AS `table`');
            $query->execute();
        }else{
            $whereIn = implode(',', array_fill(0, count($list), '?'));
            $query = $this->getDatabase()->prepare('SELECT `id`, `name`, `note`, `bdocumentation_id` FROM `bdocumentation` AS `table` WHERE `table`.`id` IN  ('.$whereIn.')');
            $query->execute($list);
        }

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
                $query = $this->getDatabase()->prepare('
                    UPDATE `bmenu`
                    SET `menu` = :menu, `name` = :name, `link` = :link, `bmenu_id` = :bmenu_id
                    WHERE `id` = :id ;
                ');
                $query->bindParam(':id', $obj->id, PDO::PARAM_INT);
                $query->bindParam(':menu', $obj->menu, PDO::PARAM_INT);
                $query->bindParam(':name', $obj->name, PDO::PARAM_STR);
                $query->bindParam(':link', $obj->link, PDO::PARAM_STR);
                $query->bindParam(':bmenu_id', $obj->bmenu_id, PDO::PARAM_INT);
                $query->execute();
            }else{
                $query = $this->getDatabase()->prepare('INSERT INTO `bmenu` (`menu`,`name`,`link`,`bmenu_id`) VALUES (:menu,:name,:link,:bmenu_id);');
                $query->bindParam(':menu', $obj->menu, PDO::PARAM_INT);
                $query->bindParam(':name', $obj->name, PDO::PARAM_STR);
                $query->bindParam(':link', $obj->link, PDO::PARAM_STR);
                $query->bindParam(':bmenu_id', $obj->bmenu_id, PDO::PARAM_INT);
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
            CREATE TABLE IF NOT EXISTS `bdocumentation` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Table for store Documentation',
                `name` varchar(45) NOT NULL COMMENT 'item name',
                `note` text COMMENT 'small description',
                `description` text COMMENT 'full description (json format)',
                `group` text COMMENT 'all included objects',
                `bdocumentation_id` int(10) unsigned DEFAULT NULL COMMENT 'arrow to parent',
            PRIMARY KEY (`id`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

            --
            -- Дамп данных таблицы `bdocumentation`
            --

            INSERT INTO `bdocumentation` (`id`, `name`, `note`, `description`, `group`, `bdocumentation_id`) VALUES
                (1, 'Фронтэнд', NULL, NULL, NULL, NULL),
                (2, 'Бэкэнд', NULL, NULL, NULL, NULL),
                (3, 'Глобальные объекты', NULL, NULL, NULL, 1),
                (4, 'Подключаемые блоки', NULL, NULL, NULL, 1),
                (5, 'Публичные свойства', NULL, NULL, NULL, NULL),
                (6, 'Функции', NULL, NULL, NULL, NULL),
                (7, 'Объекты', NULL, NULL, NULL, NULL),
                (8, 'blib', 'Основной обьект фронтэнда', '{\"content\":\"Расширяет среду программирования на клиентской стороне\"}', '[9,10,11]', 3),
                (9, 'build', 'Метод для построения blib-дерева', '{\"content\":\"Строит bom\"}', '[10]', 4),
                (10, 'Object(bom)', 'Объект возвращаемый методом построения', '{\"content\":\"Объект возвращаемый методом построения\"}', NULL, 7),
                (11, 'include', 'Метод для подключения блоков', '{\"content\":\"Подключает блоки\"}', NULL, 4);
        ");

        return $query->execute();
    }


    /**
     * Uninstall table in database
     *
     * @return bool
     */
    public function uninstall(){
        $query = $this->getDatabase()->prepare("DROP TABLE IF EXISTS `bdocumentation`;");
        return $query->execute();
    }

}