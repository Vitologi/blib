<?php
defined('_BLIB') or die;

/**
 * Class bRbac  - for realization Role Based Access Control
 */
class bRbac extends bBlib{

    /**
     * @var null|array $_operations - list of roles + privileges + rules witch get from database
     */
    private   $_operations = null;
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

        /** @var bUser $_user - user instance */
        $_user = $this->getInstance('user', 'bUser');

        /** @var bRbac__bDataMapper $_db - rbac data mapper */
        $_db = $this->getInstance('db', 'bRbac__bDataMapper');

        $userId = $_user->getId();

        $globalOperations = isset(bBlib::$_VARS[__CLASS__][$userId]) ? bBlib::$_VARS[__CLASS__][$userId] : null;

        if ($globalOperations != null) {
            $this->_operations = $globalOperations;
        } else {
            $this->_operations                = $_db->getOperations($userId);
            bBlib::$_VARS[__CLASS__][$userId] = $this->_operations;
        }

        $this->parseOperation($this->_operations);
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

}