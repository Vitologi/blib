<?php
defined('_BLIB') or die;

class bTarifScale extends bBlib{	
	
	protected function inputSelf(){
		$this->version = '1.0.0';
		$this->parents = array('bSystem', 'bConfig');
	}
	
	protected function input($data, $caller){
		$this->data = $this->hook('getData', array($data));
	}
	
	public function output(){
		
		//db connection
		$db = array(
			'host'=>'192.168.2.20',//90.111',
			'user'=>'billing',
			'password'=>'billing',
			'database'=>'agent46' //56
		);

		if(bBlib::$global['_bTarifScale__pdo']){
			$this->pdo = bBlib::$global['_bTarifScale__pdo'];
		}else{
			$dsn = sprintf('mysql:host=%1$s;dbname=%2$s', $db['host'], $db['database']);
			$this->pdo = new PDO($dsn, $db['user'], $db['password'], array(PDO::ATTR_PERSISTENT => true));
			$this->pdo->query("SET NAMES utf8");
			bBlib::$global['_bTarifScale__pdo'] = $this->pdo;
		}
		
		$result = $this->pdo->query('SELECT `o`.*, `g`.`name` AS `group`, `g`.`description` AS `groupDescription` FROM `amtelsky_reg_option_groups` `g`, `amtelsky_reg_options` `o` WHERE `o`.`group`=`g`.`id` AND `o`.`id` IN('.implode(',',$this->tarif).')');
		
		$content = $result->fetchAll(PDO::FETCH_ASSOC);

		return array('block'=>__class__, 'mods'=>$this->data['mods'], 'content'=>$content);
	}
	
	protected function getData($data){
		return $data;
	}
	
}