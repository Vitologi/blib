<?php
defined('_BLIB') or die;

class bRbac extends bBlib{


    protected $_traits     = array('bSystem', 'bDataMapper', 'bUser');
    private   $_operations = null;
    private   $_roles      = null;
    private   $_privileges = null;

	
	
	protected function input(){

        /** @var bUser $bUser  - user instance */
        $bUser = $this->getInstance('bUser');

        /** @var bRbac__bDataMapper $bDataMapper    - rbac data mapper */
        $bDataMapper = $this->getInstance('bDataMapper');

        $userId = $bUser->getId();

        $globalOperations = isset(bBlib::$_VARS[__CLASS__][$userId])?bBlib::$_VARS[__CLASS__][$userId]:null;

        if($globalOperations != null){
            $this->_operations = $globalOperations;
        }else{
            $this->_operations = $bDataMapper->getOperations($userId);
            bBlib::$_VARS[__CLASS__][$userId] = $this->_operations;
        }

        $this->parseOperation($this->_operations);
	}
	
	public function output(){
		return $this;
	}

	private function parseOperation($operations){
        $roles      = array();
        $privileges = array();
		
		foreach($operations as $key => $value){
			$roles[$value['role']] = true;
			if(!array_key_exists($value['privilege'], $privileges) || $privileges[$value['privilege']] !== null)$privileges[$value['privilege']] = $value['rule'];
		} 
		
		$this->_roles       = $roles;
		$this->_privileges  = $privileges;
	}
	
	
	public function checkAccess($operation, $data = null){
		$privileges = $this->_privileges;
		if(!array_key_exists($operation, $privileges))return false;
		
		$value = $privileges[$operation];
		if(method_exists($this->_parent, $value))return $this->_parent->$value($data);
		return true;
	}
	
	
	protected static function _checkAccess(){

        if(func_num_args()===3){

            /**
             * @var string $operationName 	- name of checked operation
             * @var mixed $data 		    - some data
             * @var bBlib $caller		    - block-initiator
             */
            list($operationName, $data, $caller) = func_get_args();

        }else if(func_num_args()===2){
            list($operationName, $caller) = func_get_args();
            $data = array();
        }else{
            throw new Exception('Not correct arguments given.');
        }

        if(!($caller instanceof bBlib))throw new Exception('Not correct arguments given.');

        /** @var bRbac $bRbac  - rbac instance */
        $bRbac = $caller->getInstance(__CLASS__);

		return  $bRbac->checkAccess($operationName, $data);
	}
	
}