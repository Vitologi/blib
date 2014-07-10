<?php
defined('_BLIB') or die;

class bTarifScale extends bBlib{	
	
	protected function inputSelf(){
		$this->version = '1.0.0';
		$this->parents = array('bSystem', 'bConfig');
	}
	
	protected function input($data, $caller){
		$this->caller = $caller;
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
		
		
		if($this->data['blib'] === 'bTarifScale'){
			$data = $this->data;
			
			foreach($data as $key => $value){
				$data[$key] = $this->pdo->quote($data[$key]);
			}

			$Q = sprintf('INSERT INTO `amtelsky_reg_tickets`(`id`,`name`,`email`,`phone`,`passport`,`passport_issued`,`address`,`options`) VALUES(NULL, %1$s, %2$s, %3$s, %4$s, %5$s, %6$s, %7$s);',$data['name'],$data['email'],$data['phone'],$data['passport'],$data['passport_issued'],$data['address'],$data['options']);
			
			
			if($result = $this->pdo->query($Q)){
				echo '{"status":true, "message":"Ваша заявка принята в обработку."}';
			}else{
				echo '{"status":false, "message":"Ваша заявка отвергнута."}';
			};
			exit;
			
		}else{
		
			$in = ($this->tarif?' AND `o`.`id` IN('.implode(',',$this->tarif).')':'');
			$result = $this->pdo->query('SELECT `o`.*, `g`.`name` AS `group`, `g`.`description` AS `groupDescription` FROM `amtelsky_reg_option_groups` `g`, `amtelsky_reg_options` `o` WHERE `o`.`group`=`g`.`id`'.$in);
			$content = $result->fetchAll(PDO::FETCH_ASSOC);

			return array('block'=>__class__, 'mods'=>$this->data['mods'], 'content'=>$content);
		}
	}
	
	protected function getData($data){
		return $data;
	}
	
}