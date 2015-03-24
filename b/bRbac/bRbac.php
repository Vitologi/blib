<?php
defined('_BLIB') or die;

/**
 * Class bRbac  - for realization Role Based Access Control
 */
class bRbac extends bBlib{


    protected $_traits     = array('bSystem', 'bRbac__bDataMapper', 'bUser');
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
    protected function input(){

        /** @var bUser $bUser  - user instance */
        $bUser = $this->getInstance('bUser');

        /** @var bRbac__bDataMapper $bDataMapper    - rbac data mapper */
        $bDataMapper = $this->getInstance('bRbac__bDataMapper');

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


    /**
     * Method for use in children object
     *
     * @return bool|mixed   - access flag or result children rules
     * @throws Exception
     */
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