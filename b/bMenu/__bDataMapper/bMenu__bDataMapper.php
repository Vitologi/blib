<?php
defined('_BLIB') or die;

/**
 * Class bMenu__bDataMapper - realisation of Data Mapper for menu block
 */
class bMenu__bDataMapper extends bDataMapper{

    /**
     * Menu object
     *
     * @typedef array \Menu {
     * @type int $id                - menu item id
     * @type int $menu              - group id (used item id for grouping)
     * @type string $name           - menu item name
     * @type string $link           - menu item link (attribute href in <a> tag)
     * @type int $bmenu_id          - parent id (used item id for nesting)
     * }
     */

    /**
     * Get menu item by name from table
     *
     * @return stdClass      - data-object {Menu}
     */
    public function getItem(){

        // Empty object
        $prototype = (object)array('id'=>null, 'menu'=>null, 'name'=>null, 'link'=>null, 'bmenu_id'=>null);

        if(func_num_args()===0)return $prototype;

        $id = func_get_arg(0);

        $query = $this->getDatabase()->prepare('SELECT * FROM `bmenu` AS `table` WHERE `table`.`id` LIKE  :id');
        $query->bindParam(':id', $id, PDO::PARAM_INT);

        $query->execute();
        if(!$result = $query->fetch(PDO::FETCH_ASSOC))return $prototype;

        return (object) $result;
    }

    /**
     * Get menu group (list items)
     *
     * @param null|int $group   - group id
     * @return array            - menu list
     */
    public function getMenu($group = null){

        // Empty list
        $prototype = array();

        if(func_num_args()===0)return $prototype;

        $menu = func_get_arg(0);

        $query = $this->getDatabase()->prepare('SELECT * FROM `bmenu` AS `table` WHERE `table`.`id` LIKE  :menu OR `table`.`menu` LIKE  :menu  ORDER BY `id` ASC');
        $query->bindParam(':menu', $menu, PDO::PARAM_INT);

        $query->execute();
        if(!$result = $query->fetchAll(PDO::FETCH_ASSOC))return $prototype;

        return $result;
    }

    /**
     * Get count of rows in table
     *
     * @return null|number   - row count
     */
    public function getCount(){
        $query = $this->getDatabase()->prepare('SELECT COUNT(*) AS `length` FROM `bmenu`');
        $query->execute();

        if(!$result = $query->fetch(PDO::FETCH_ASSOC))return null;
        return $result['length'];
    }

    /**
     * Get list of data
     *
     * @return array - data-array
     * @throws Exception
     */
    public function getSmallList(){

        $query = $this->getDatabase()->prepare('SELECT `id`, `name`, `link` FROM `bmenu` AS `table` ORDER BY `id`');
        $query->execute();

        if(!$result= $query->fetchAll(PDO::FETCH_ASSOC))throw new Exception("Can`t get small menu list from db.");

        return $result;
    }

    /**
     * Get list of data
     *
     * @param array $params
     * @return null|object - data-array
     * @throws Exception
     */
    public function getList($params = array()){
        $params = array_replace_recursive(
            array('from'=>0,'count'=>5),
            $params
        );

        $from = (int)$params['from'];
        $count = (int)$params['count'];

        $query = $this->getDatabase()->prepare('SELECT * FROM `bmenu` AS `table` ORDER BY `id` DESC LIMIT :from, :count');
        $query->bindValue(':from', $from, PDO::PARAM_INT);
        $query->bindValue(':count', $count, PDO::PARAM_INT);

        $query->execute();

        if($result= $query->fetchAll(PDO::FETCH_ASSOC))return $result;

        throw new Exception("Can`t get menu list");

    }

    /**
     * Delete list from database
     *
     * @param array $list
     * @return null|object - data-array
     * @throws Exception
     */
    public function destroy(Array $list = null){

        if(!is_array($list))throw new Exception("Send wrong item list to delete");

        $whereIn = implode(',', array_fill(0, count($list), '?'));
        $query = $this->getDatabase()->prepare('DELETE FROM `bmenu` WHERE `id` IN  ('.$whereIn.')');
        if(!$query->execute($list))throw new Exception("Can`t delete menu items");
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
            CREATE TABLE IF NOT EXISTS `bmenu` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Table for store menu',
                `menu` int(10) unsigned DEFAULT NULL COMMENT 'menu group',
                `name` varchar(45) NOT NULL COMMENT 'item name',
                `link` text COMMENT 'link view',
                `bmenu_id` int(10) unsigned DEFAULT NULL COMMENT 'arrow to parent',
            PRIMARY KEY (`id`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

            --
            -- Дамп данных таблицы `bmenu`
            --

            INSERT INTO `bmenu` (`id`, `menu`, `name`, `link`, `bmenu_id`) VALUES
                (1, 1, 'Главное меню', '', 0),
                (2, 1, 'Главная', '/', 1),
                (3, 1, 'Документация', '', 1),
                (4, 1, 'Ядро', '/documentation/base/', 3),
                (5, 1, 'API', '', 3),
                (6, 1, 'Бэкенд', '/documentation/api/backend/', 5),
                (7, 1, 'Фронтенд', '/documentation/api/frontend/', 5),
                (8, 1, 'Вопросы', '/documentation/faq/', 3),
                (9, 1, 'Загрузки', '/downloads/', 1),
                (10, 1, 'Аналитика', '', 1),
                (11, 1, 'Графики', '/analitics/grafics/', 10),
                (12, 1, 'Примеры', '/analitics/examples/', 10),
                (13, 1, 'Панель администратора', '/adminpanel/', 1);
        ");

        return $query->execute();
    }


    /**
     * Uninstall table in database
     *
     * @return bool
     */
    public function uninstall(){
        $query = $this->getDatabase()->prepare("DROP TABLE IF EXISTS `bmenu`;");
        return $query->execute();
    }

}