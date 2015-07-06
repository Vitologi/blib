<?php
defined('_BLIB') or die;

/**
 * Class bRbac  - for realization Role Based Access Control
 */
class bRbac extends bBlib{

    /**
     * @var array $_operations - list of roles + privileges + rules witch get from database
     */
    private static  $_operations = array();

    /**
     * @var null|int $_id          current user id
     */
    private   $_id         = null;

    /**
     * @var null|array $_roles      - serialized roles array like array('admin'=>true, 'manager'=> true)
     */
    private   $_roles      = null;
    /**
     * @var null|array $_privileges - serialized privileses+rules array like array('show'=>null, 'add'=>null, 'edit'=>'editOwner', 'delete'=>'deleteOwner')
     */
    private   $_privileges = null;


    /**
     * Get and store rbac data:
     *  - from global variable if it exists
     *  - or from database
     */
    protected function input() {
        $this->setInstance('user', 'bUser');
        $this->setInstance('db', 'bRbac__bDataMapper');
        $_this = $this->getInstance('this', 'bDecorator');

        $_this->initialize();
	}

    public function initialize(){
        /** @var bUser $_user - user instance */
        $_user = $this->getInstance('user');

        /** @var bRbac__bDataMapper $_db - rbac data mapper */
        $_db = $this->getInstance('db');

        $this->_id = $_user->getId();
        if(!isset(self::$_operations[$this->_id]))self::$_operations[$this->_id] = $_db->getOperations($this->_id);
        $this->parseOperation(self::$_operations[$this->_id]);
    }

    /**
     * Save self instance in child block
     *
     * @return $this
     */
    public function output(){
		return $this;
	}

    /**
     * Parse rbac data
     *
     * @param $operations   - list of privilege,role,rules
     * @void                - parse privilege,role,rules how $this propertys
     */
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


    /**
     * Check current user access to operation
     *
     * @param $operation    - operation`s name
     * @param null $data    - provided data
     * @return bool|mixed   - access flag or result children rules
     */
    public function checkAccess($operation, $data = null){
		$privileges = $this->_privileges;
		if(!array_key_exists($operation, $privileges))return false;
		
		$value = $privileges[$operation];
		if(method_exists($this->_parent, $value))return $this->_parent->$value($data);
		return true;
	}

    public function hasRole($roleName = null){
        return array_key_exists($roleName, $this->_roles);
    }

}