<?php
defined('_BLIB') or die;

class bRbac extends bBlib{	
	
	private static $singleton = null;
	private $user;
	private $operations;
	private $roles;
	private $privilages;

	
	
	protected function inputSelf(){
		$this->version = '1.0.0';
		$this->parents = array('bSystem', 'bDatabase', 'bConfig', 'bSession', 'bUser');
	}
	
	protected function input($data, $caller){
		$this->user = $this->local['bUser'];
	}
	
	public function output(){
		
		$oldId = $this->_getSession('userId');
		$newId = $this->user->getId();

		if($oldId != null && ($oldId == $newId) && bRbac::$singleton == null){
			$this->operations = $this->_getSession('operations');
			$this->parseOperation($this->operations);
			bRbac::$singleton = $this;
		}elseif(!$oldId || ($oldId !== $newId) || bRbac::$singleton == null){
			$this->operations = $this->getOperation($newId);
			$this->_setSession('userId', $newId);
			$this->_setSession('operations', $this->operations);
			$this->parseOperation($this->operations);
			bRbac::$singleton = $this;
		}
		
		return array('bRbac'=>bRbac::$singleton);
		
	}

	
	private function getOperation($id){
		if(!$id)return array();

		$Q = "
			SELECT  `brbac__roles`.`name` AS  `role` ,  `brbac__privileges`.`name` AS  `privilage` ,  `brbac__rules`.`name` AS  `rule` 
			FROM  `brbac__privileges` ,  `brbac__roles` ,  `brbac__user_roles`, (`brbac` 
			LEFT JOIN  `brbac__rules` ON  `brbac`.`brbac__rules_id` =  `brbac__rules`.`id` )
			WHERE (`brbac__user_roles`.`buser_id` =  '".$id."')
			AND  `brbac__user_roles`.`brbac__roles_id` =  `brbac__roles`.`id` 
			AND  `brbac`.`brbac__roles_id` =  `brbac__roles`.`id` 
			AND  `brbac`.`brbac__privileges_id` =  `brbac__privileges`.`id`
		";
		$result = $this->_query($Q);

		return $result->fetchAll(PDO::FETCH_ASSOC);
	}
	
	private function parseOperation($operations){
		$roles = array();
		$privilages = array();
		
		foreach($operations as $key => $value){
			$roles[$value['role']] = true;
			if(!array_key_exists($value['privilage'], $privilages) || $privilages[$value['privilage']] !== null)$privilages[$value['privilage']] = $value['rule'];
		} 
		
		$this->roles = $roles;
		$this->privilages = $privilages;
	}
	
	
	protected function checkAccess($operation, $data, $caller){
		$privilages = $this->privilages;
		if(!array_key_exists($operation, $privilages))return false;
		
		$value = $privilages[$operation];
		if(is_string($value))return $caller->call($value, array($data));
		return true;
	}
	
	
	protected static function _checkAccess($data, $caller = null){
		if($caller == null)throw new Exception("Try call access check for not defined object.");
		return bRbac::$singleton->checkAccess($data[0], $data[1], $caller);
	}
	
}