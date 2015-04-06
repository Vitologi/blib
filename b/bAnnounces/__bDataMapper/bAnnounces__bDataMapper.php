<?php
defined('_BLIB') or die;

/**
 * Class bAnnounces__bDataMapper - realisation of Data Mapper for announces block
 */
class bAnnounces__bDataMapper extends bDataMapper{

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

        $query = $this->getDatabase()->prepare('SELECT `id`, `date`, `title`, `content` FROM `bannounces` AS `table` WHERE `published` = 1 ORDER BY `id` DESC LIMIT :from, :count');
        $query->bindValue(':from', $from, PDO::PARAM_INT);
        $query->bindValue(':count', $count, PDO::PARAM_INT);

        $query->execute();

        return ($result= $query->fetchAll(PDO::FETCH_ASSOC))?$result:array();
    }


    /**
     * Install empty table in database
     *
     * @return bool
     */
    public function install(){
        $query = $this->getDatabase()->prepare("
            CREATE TABLE IF NOT EXISTS `bannounces` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Table for store Announces',
            `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'item announce date',
            `title` text COMMENT 'announce title',
            `content` text COMMENT 'announce text',
            `published` tinyint(1) DEFAULT '0' COMMENT 'show flag',
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
        $query = $this->getDatabase()->prepare("DROP TABLE IF EXISTS `bannounces`;");
        return $query->execute();
    }

}