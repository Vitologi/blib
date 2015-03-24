<?php
defined('_BLIB') or die;

/**
 * Class bRbac__bDataMapper - realisation of Data Mapper for role based access system block
 */
class bRbac__bDataMapper extends bDataMapper{

    /**
     * Get rbac by user id
     *
     * @param null $id  - user id
     * @return array    - array of allowed operations
     */
    public function getOperations($id = null){

        // Empty operation list
        $prototype = array();

        if($id === null)return $prototype;

        $query = $this->getDatabase()->prepare('
            SELECT  `brbac__roles`.`name` AS  `role` ,  `brbac__privileges`.`name` AS  `privilege` ,  `brbac__rules`.`name` AS  `rule`
			FROM  `brbac__privileges` ,  `brbac__roles` ,  `brbac__user_roles`, (`brbac`
			LEFT JOIN  `brbac__rules` ON  `brbac`.`brbac__rules_id` =  `brbac__rules`.`id` )
			WHERE (`brbac__user_roles`.`buser_id` =  :id)
			AND  `brbac__user_roles`.`brbac__roles_id` =  `brbac__roles`.`id`
			AND  `brbac`.`brbac__roles_id` =  `brbac__roles`.`id`
			AND  `brbac`.`brbac__privileges_id` =  `brbac__privileges`.`id`
        ');

        $query->bindParam(':id', $id, PDO::PARAM_INT);

        $query->execute();
        if(!$result = $query->fetchAll(PDO::FETCH_ASSOC))return $prototype;

        return $result;

    }


    /**
     * Get list of data (not completed) 0_0
     * @param array $params
     * @return array
     */
    public function getList($params = array()){
        return array();
    }

    /**
     * Handler for saving or update single Item
     *
     * @param stdClass $obj     - instance of data-object
     * @return $this            - for chaining
     */
    public function save(stdClass &$obj){
        // stub
        return $this;
    }


    /**
     * Install table in database
     *
     * @return bool
     */
    public function install(){

        // Create script
        $query = $this->getDatabase()->prepare("

            --
            -- Структура таблицы `brbac`
            --

            CREATE TABLE IF NOT EXISTS `brbac` (
                `brbac__roles_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'role id',
                `brbac__privileges_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'privilege id',
                `brbac__rules_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'rule id',
                PRIMARY KEY (`brbac__roles_id`,`brbac__privileges_id`,`brbac__rules_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;

            --
            -- Структура таблицы `brbac__privileges`
            --

            CREATE TABLE IF NOT EXISTS `brbac__privileges` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Table for store privileges',
                `name` varchar(45) NOT NULL COMMENT 'privilege name',
                PRIMARY KEY (`id`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;


            --
            -- Структура таблицы `brbac__roles`
            --

            CREATE TABLE IF NOT EXISTS `brbac__roles` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Table for store roles',
                `name` varchar(45) NOT NULL COMMENT 'role name',
                PRIMARY KEY (`id`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

            --
            -- Структура таблицы `brbac__rules`
            --

            CREATE TABLE IF NOT EXISTS `brbac__rules` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Table for store rules',
                `name` varchar(45) NOT NULL COMMENT 'rule name',
                PRIMARY KEY (`id`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

            --
            -- Структура таблицы `brbac__user_roles`
            --

            CREATE TABLE IF NOT EXISTS `brbac__user_roles` (
                `buser_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'user id',
                `brbac__roles_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'role id',
                PRIMARY KEY (`buser_id`,`brbac__roles_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;




            --
            -- Дамп данных таблицы `brbac`
            --

            INSERT INTO `brbac` (`brbac__roles_id`, `brbac__privileges_id`, `brbac__rules_id`) VALUES
            (1, 1, 0),
            (3, 2, 0),
            (3, 3, 1),
            (4, 3, 0),
            (6, 5, 0);

            --
            -- Дамп данных таблицы `brbac__privileges`
            --

            INSERT INTO `brbac__privileges` (`id`, `name`) VALUES
            (1, 'read'),
            (2, 'add'),
            (3, 'edit'),
            (4, 'delete'),
            (5, 'unlock');

            --
            -- Дамп данных таблицы `brbac__roles`
            --

            INSERT INTO `brbac__roles` (`id`, `name`) VALUES
            (1, 'public'),
            (2, 'user'),
            (3, 'author'),
            (4, 'editor'),
            (5, 'admin'),
            (6, 'superadmin');

            --
            -- Дамп данных таблицы `brbac__rules`
            --

            INSERT INTO `brbac__rules` (`id`, `name`) VALUES
            (1, 'editOwner');


            --
            -- Дамп данных таблицы `brbac__user_roles`
            --

            INSERT INTO `brbac__user_roles` (`buser_id`, `brbac__roles_id`) VALUES
            (1, 6);
        ");

        return $query->execute();
    }


    /**
     * Uninstall table in database
     *
     * @return bool
     */
    public function uninstall(){
        $query = $this->getDatabase()->prepare("DROP TABLE IF EXISTS `brbac__privileges`, `brbac__rules`, `brbac__roles`, `brbac__user_roles`, `brbac`;");
        return $query->execute();
    }

}