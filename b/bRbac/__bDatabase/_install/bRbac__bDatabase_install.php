<?php
defined('_BLIB') or die();

return array(
	'create'	=> array(
		'bRbac__privileges'=> array(
			'fields' => array(
				'id'		=> array('type'=> 'INT(10) UNSIGNED', 'null'=>'NOT NULL', 'extra'=> 'AUTO_INCREMENT', 'comment'=>'Table for store privileges'),
				'name'	=> array('type'=> 'VARCHAR(45)', 'null'=>'NOT NULL', 'comment'=>'privilege name')	
			),
			'primary'	=> array('id')
		),
		'bRbac__rules'=> array(
			'fields' => array(
				'id'		=> array('type'=> 'INT(10) UNSIGNED', 'null'=>'NOT NULL', 'extra'=> 'AUTO_INCREMENT', 'comment'=>'Table for store rules'),
				'name'	=> array('type'=> 'VARCHAR(45)', 'null'=>'NOT NULL', 'comment'=>'rule name')	
			),
			'primary'	=> array('id')
		),
		'bRbac__roles'=> array(
			'fields' => array(
				'id'		=> array('type'=> 'INT(10) UNSIGNED', 'null'=>'NOT NULL', 'extra'=> 'AUTO_INCREMENT', 'comment'=>'Table for store roles'),
				'name'	=> array('type'=> 'VARCHAR(45)', 'null'=>'NOT NULL', 'comment'=>'role name')	
			),
			'primary'	=> array('id')
		),
		
		'bRbac__user_roles'	=> array(
			'fields' => array(
				'bUser_id' => array('type'=> 'INT(10) UNSIGNED', 'comment'=>'user id'),
				'bRbac__roles_id'	=> array('type'=> 'INT(10) UNSIGNED', 'comment'=>'role id')				
			),
			'primary'	=> array('bUser_id', 'bRbac__roles_id'),
			'foreign'	=> array(
				'bUser_id'	=> null,
				'bRbac__roles_id'	=> null
			)
		),
		'bRbac'	=> array(
			'fields' => array(
				'bRbac__roles_id'	=> array('type'=> 'INT(10) UNSIGNED', 'comment'=>'role id'),
				'bRbac__privileges_id'	=> array('type'=> 'INT(10) UNSIGNED', 'comment'=>'privilege id'),
				'bRbac__rules_id'	=> array('type'=> 'INT(10) UNSIGNED', 'comment'=>'rule id')
			),
			'primary'	=> array('bRbac__roles_id', 'bRbac__privileges_id','bRbac__rules_id'),
			'foreign'	=> array(
				'bRbac__roles_id'	=> null,
				'bRbac__privileges_id'	=> null,
				'bRbac__rules_id'	=> null
			)
		)
	),
	'insert' => array(
		'bRbac__privileges'=> array(
			array('id','name'),
			array('1','read'),
			array('2','add'),
			array('3','edit'),
			array('4','delete')
		),
		'bRbac__rules'=> array(
			array('id','name'),
			array('1','editOwner')
		),
		'bRbac__roles'=> array(
			array('id','name'),
			array('1','public'),
			array('2','user'),
			array('3','author'),
			array('4','editor'),
			array('5','admin'),
			array('6','superadmin')
		),
		'bRbac__user_roles'	=> array(
			array('bUser_id','bRbac__roles_id'),
			array('1','6')
		),
		'bRbac'	=> array(
			array('bRbac__roles_id','bRbac__privileges_id','bRbac__rules_id'),
			array('1','1',''),
			array('3','2',''),
			array('3','3','1'),
			array('4','3',''),
			array('5','4','')
		)
	)
);