<?php
defined('_BLIB') or die;

/**
 * Class bFeedBack__bDataMapper - realisation of Data Mapper for feedback block
 */
class bFeedBack__bDataMapper extends bDataMapper{


    /**
     * Theme object
     *
     * @typedef array \Theme {
     * @type int $id        - theme id
     * @type string $name   - theme name
     * }
     */

    /**
     * Get theme
     *
     * @param null $id  - theme id
     * @return stdClass - data-object {Theme}
     */
    public function getTheme($id = null){

        // Empty theme object
        $prototype = (object)array('id'=>null, 'name'=>null);

        if($id === null)return $prototype;

        $query = $this->getDatabase()->prepare('SELECT * FROM `bfeedback__theme` AS `table` WHERE `table`.`id` =  :id');
        $query->bindParam(':id', $id, PDO::PARAM_INT);

        $query->execute();
        if(!$result = $query->fetch(PDO::FETCH_ASSOC))return $prototype;

        return (object) $result;
    }

    /**
     * Handler for saving or update theme
     *
     * @param stdClass $obj     - instance of data-object {Theme}
     * @return $this            - for chaining
     * @throws Exception
     */
    public function saveTheme(stdClass &$obj){

        try{

            if(isset($obj->id)){
                $query = $this->getDatabase()->prepare('UPDATE `bfeedback__theme` SET `name` = :name WHERE `id` = :id ;');
                $query->bindParam(':id', $obj->id, PDO::PARAM_INT);
                $query->bindParam(':name', $obj->name, PDO::PARAM_STR);
                $query->execute();
            }else{
                $query = $this->getDatabase()->prepare('INSERT INTO `bfeedback__theme` (`name`) VALUES (:name);');
                $query->bindParam(':name', $obj->note, PDO::PARAM_STR);
                $query->execute();
                $obj->id = $this->getDatabase()->lastInsertId();
            }

        }catch (PDOException $e){
            throw new Exception('Database error('.$e->getCode().') '.$e->errorInfo);
        }

        return $this;
    }


    /**
     * Thread object
     *
     * @typedef array \Thread {
     * @type int $id            - thread id
     * @type int $user          - user owner id
     * @type string $time       - thread time
     * @type int $theme         - theme id
     * @type string $content    - message content
     * @type int $status        - message status (-1 negative, +1 positive, 0 empty/not complete)
     * }
     */


    /**
     * Get thread
     *
     * @param null $id  - thread id
     * @return stdClass - data-object {Thread}
     */
    public function getThread($id = null){

        // Empty thread object
        $prototype = (object)array('id'=>null, 'user'=>null, 'time'=>null, 'theme'=>0, 'content'=>null, 'status'=>0);

        if($id === null)return $prototype;

        $query = $this->getDatabase()->prepare('
            SELECT
                `id`,
                `buser_id` AS `user`,
                `time`,
                `bfeedback__theme_id` AS `theme`,
                `content`,
                `status`
            FROM `bfeedback__thread`
            WHERE `id` =  :id
        ');
        $query->bindParam(':id', $id, PDO::PARAM_INT);

        $query->execute();
        if(!$result = $query->fetch(PDO::FETCH_ASSOC))return $prototype;

        foreach($result as $key => $value){
            if(is_numeric($value))$result[$key] = (int)$value;
        }

        return (object) $result;
    }

    /**
     * Handler for saving or update thread
     *
     * @param stdClass $obj     - instance of data-object {Thread}
     * @return $this            - for chaining
     * @throws Exception
     */
    public function saveThread(stdClass &$obj){

        try{

            if(isset($obj->id)){
                $query = $this->getDatabase()->prepare('
                    UPDATE
                        `bfeedback__thread`
                    SET
                        `buser_id` = :user,
                        `bfeedback__theme_id` = :theme,
                        `content` = :content,
                        `status` = :status
                    WHERE `id` = :id ;
                ');
                $query->bindParam(':id', $obj->id, PDO::PARAM_INT);
                $query->bindParam(':user', $obj->user, PDO::PARAM_INT);
                $query->bindParam(':theme', $obj->theme, PDO::PARAM_INT);
                $query->bindParam(':content', $obj->content, PDO::PARAM_STR);
                $query->bindParam(':status', $obj->status, PDO::PARAM_INT);
                $query->execute();
            }else{
                $query = $this->getDatabase()->prepare('
                    INSERT INTO
                      `bfeedback__thread` (`buser_id`, `bfeedback__theme_id`, `content`, `status`)
                    VALUES (:user, :theme, :content, :status);
                ');
                $query->bindParam(':user', $obj->user, PDO::PARAM_INT);
                $query->bindParam(':theme', $obj->theme, PDO::PARAM_INT);
                $query->bindParam(':content', $obj->content, PDO::PARAM_STR);
                $query->bindParam(':status', $obj->status, PDO::PARAM_INT);
                $query->execute();
                $obj->id = $this->getDatabase()->lastInsertId();
            }

        }catch (PDOException $e){
            throw new Exception('Database error('.$e->getCode().') '.$e->errorInfo);
        }

        return $this;
    }



    /**
     * Reply object
     *
     * @typedef array \Reply {
     * @type int $id            - reply id
     * @type string $thread     - thread owner id
     * @type int $user          - user owner id
     * @type string $time       - reply time
     * @type string $content    - message content
     * }
     */

    /**
     * Get reply
     *
     * @param null $id  - reply id
     * @return stdClass - data-object {Reply}
     */
    public function getReply($id = null){

        // Empty reply object
        $prototype = (object)array('id'=>null, 'thread'=>null, 'user'=>null, 'time'=>null, 'content'=>null);

        if($id === null)return $prototype;

        $query = $this->getDatabase()->prepare('
            SELECT
                `id`,
                `bfeedback__thread_id` AS `thread`,
                `buser_id` AS `user`,
                `time`,
                `content`
            FROM `bfeedback__reply`
            WHERE `id` =  :id
        ');
        $query->bindParam(':id', $id, PDO::PARAM_INT);

        $query->execute();
        if(!$result = $query->fetch(PDO::FETCH_ASSOC))return $prototype;

        return (object) $result;
    }


    /**
     * Handler for saving or update reply
     *
     * @param stdClass $obj     - instance of data-object {Reply}
     * @return $this            - for chaining
     * @throws Exception
     */
    public function saveReply(stdClass &$obj){

        try{

            if(isset($obj->id)){
                $query = $this->getDatabase()->prepare('
                    UPDATE
                        `bfeedback__reply`
                    SET
                        `bfeedback__thread_id` = :thread,
                        `buser_id` = :user,
                        `content` = :content
                    WHERE `id` = :id ;
                ');
                $query->bindParam(':id', $obj->id, PDO::PARAM_INT);
                $query->bindParam(':thread', $obj->thread, PDO::PARAM_INT);
                $query->bindParam(':user', $obj->user, PDO::PARAM_INT);
                $query->bindParam(':content', $obj->content, PDO::PARAM_STR);
                $query->execute();
            }else{
                $query = $this->getDatabase()->prepare('
                    INSERT INTO
                      `bfeedback__reply` (`bfeedback__thread_id`, `buser_id`, `content`)
                    VALUES (:thread, :user, :content);
                ');
                $query->bindParam(':thread', $obj->thread, PDO::PARAM_INT);
                $query->bindParam(':user', $obj->user, PDO::PARAM_INT);
                $query->bindParam(':content', $obj->content, PDO::PARAM_STR);
                $query->execute();
                $obj->id = $this->getDatabase()->lastInsertId();
            }

        }catch (PDOException $e){
            throw new Exception('Database error('.$e->getCode().') '.$e->errorInfo);
        }

        return $this;
    }

    /**
     * Get all theme list
     *
     * @return array - theme list (id, name)
     */
    public function getThemeList(){

        $query = $this->getDatabase()->prepare('SELECT `id`,`name` FROM `bfeedback__theme` AS `table`');
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Get threads list by user id
     *
     * @param int|null $user    - user id
     * @return array            - threads array {Thread}
     */
    public function getThreadsByUser($user = null){

        if($user == null)return array();

        $query = $this->getDatabase()->prepare('
            SELECT
                `id`,
                `buser_id` AS `user`,
                `time`,
                `bfeedback__theme_id` AS `theme`,
                `content`,
                `status`
            FROM
                `bfeedback__thread`
            WHERE
                `buser_id` =  :user
            ORDER BY `time` DESC
        ');

        $query->bindParam(':user', $user, PDO::PARAM_INT);
        $query->execute();

        return ($result= $query->fetchAll(PDO::FETCH_ASSOC))?$result:array();
    }


     /**
     * Get replies list by thread id
     *
     * @param int|null $thread  - thread id
     * @return array            - replies array {Reply}
     */
    public function getRepliesByThread($thread = null){

        if($thread == null)return array();

        $query = $this->getDatabase()->prepare('
            SELECT
                `id`,
                `buser_id` AS `user`,
                `time`,
                `content`
            FROM
                `bfeedback__reply`
            WHERE
                `bfeedback__thread_id` = :thread
            ORDER BY `time` ASC
        ');

        $query->bindParam(':thread', $thread, PDO::PARAM_INT);
        $query->execute();

        return ($result= $query->fetchAll(PDO::FETCH_ASSOC))?$result:array();
    }


    /**
     * Install empty table in database
     *
     * @return bool
     */
    public function install(){

        // Create script
        $query = $this->getDatabase()->prepare("

            CREATE TABLE IF NOT EXISTS `bfeedback__theme` (
              `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT COMMENT 'theme identifier',
              `name` varchar(45) NOT NULL COMMENT 'theme name',
              PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='For store feedback themes';

            CREATE TABLE IF NOT EXISTS `bfeedback__thread` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'feedback thread id',
              `buser_id` int(10) unsigned NOT NULL COMMENT 'user id',
              `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'thread time',
              `bfeedback__theme_id` int(10) unsigned NOT NULL COMMENT 'theme id',
              `content` text COMMENT 'feedback thread text',
              `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'thread status (-1 negative +1 positive 0 open)',
              PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Feedbacks thread collection';

            CREATE TABLE IF NOT EXISTS `bfeedback__reply` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'reply id',
              `bfeedback__thread_id` int(10) unsigned NOT NULL COMMENT 'threat id',
              `buser_id` int(10) unsigned NOT NULL COMMENT 'user id',
              `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'reply time',
              `content` text COMMENT 'reply text',
              PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Feedback threads replays';

        ");

        return $query->execute();
    }


    /**
     * Uninstall table in database
     *
     * @return bool
     */
    public function uninstall(){
        $query = $this->getDatabase()->prepare("DROP TABLE IF EXISTS `bfeedback__reply`, `bfeedback__thread`, `bfeedback__theme`;");
        return $query->execute();
    }

}