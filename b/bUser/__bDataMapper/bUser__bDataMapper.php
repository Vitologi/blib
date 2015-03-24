<?php
defined('_BLIB') or die;

/**
 * Class bUser__bDataMapper - realisation of Data Mapper for configuration block
 */
class bUser__bDataMapper extends bDataMapper{

    /**
     * User object
     *
     * @typedef array \User {
     * @type int $id        - user id
     * @type string $login  - user login
     * }
     */

    /**
     * Get configuration by name from table
     *
     * @param string $login     - user login
     * @param string $password  - user password
     * @return stdClass         - data-object {User}
     */
    public function getItem($login = '', $password = ''){

        // Empty user object
        $prototype = (object)array('id'=>null, 'login'=>null, 'password'=>null);

        if(!$login || !$password)return $prototype;
        $password = md5($password);

        $query = $this->getDatabase()->prepare('SELECT `id`, `login` FROM `buser` AS `table` WHERE `table`.`login` = :login AND  `table`.`password` = :password');
        $query->bindParam(':login', $login, PDO::PARAM_STR);
        $query->bindParam(':password', $password, PDO::PARAM_STR);

        $query->execute();
        if(!$result = $query->fetch(PDO::FETCH_ASSOC))return $prototype;

        return (object) $result;
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
     * @throws Exception
     */
    public function save(stdClass &$obj){

        try{

            $db = $this->getDatabase();

            // Update user
            if(isset($obj->id)){

                $query = $db->prepare('SELECT `login`,`password` FROM `buser` AS `table` WHERE `table`.`id` = :id');
                $query->bindParam(':id', $obj->id, PDO::PARAM_INT);
                $query->execute();
                $oldUser = $query->fetch(PDO::FETCH_ASSOC);

                if($oldUser['password'] !== $obj->password){
                    $obj->password =  md5($obj->password);
                }

                $query = $db->prepare('UPDATE `buser` SET `login` = :login, `password` = :password WHERE `id` = :id ;');
                $query->bindParam(':id', $obj->id, PDO::PARAM_INT);
                $query->bindParam(':login', $obj->login, PDO::PARAM_STR);
                $query->bindParam(':password', $obj->password, PDO::PARAM_STR);
                $query->execute();

            // Insert user
            }else{

                $obj->password =  md5($obj->password);

                $query = $db->prepare('INSERT INTO `buser` (`login`,`password`) VALUES (:login, :password);');
                $query->bindParam(':login', $obj->login, PDO::PARAM_STR);
                $query->bindParam(':password', $obj->password, PDO::PARAM_STR);
                $query->execute();
                $obj->id = $db->lastInsertId();
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

        // Create script
        $query = $this->getDatabase()->prepare("
            CREATE TABLE IF NOT EXISTS `buser` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Table for store users',
                `login` varchar(45) NOT NULL COMMENT 'user login',
                `password` varchar(45) NOT NULL COMMENT 'user password',
                PRIMARY KEY (`id`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
        ");

        // Insert first user (admin:admin)
        $query->prepare("
            INSERT INTO `buser` (`id`, `login`, `password`) VALUES
            (1, 'admin', '21232f297a57a5a743894a0e4a801fc3');
        ");

        return $query->execute();
    }


    /**
     * Uninstall table in database
     *
     * @return bool
     */
    public function uninstall(){
        $query = $this->getDatabase()->prepare("DROP TABLE IF EXISTS `buser`;");
        return $query->execute();
    }

}