<?php
defined('_BLIB') or die();

return array(
	'create'	=> array(
		'brbac__privileges'=> array(
			'fields' => array(
				'id'		=> array('type'=> 'INT(10) UNSIGNED', 'null'=>'NOT NULL', 'extra'=> 'AUTO_INCREMENT', 'comment'=>'Table for store privileges'),
				'name'	=> array('type'=> 'VARCHAR(45)', 'null'=>'NOT NULL', 'comment'=>'privilege name')	
			),
			'primary'	=> array('id')
		),
		'brbac__rules'=> array(
			'fields' => array(
				'id'		=> array('type'=> 'INT(10) UNSIGNED', 'null'=>'NOT NULL', 'extra'=> 'AUTO_INCREMENT', 'comment'=>'Table for store rules'),
				'name'	=> array('type'=> 'VARCHAR(45)', 'null'=>'NOT NULL', 'comment'=>'rule name')	
			),
			'primary'	=> array('id')
		),
		'brbac__roles'=> array(
			'fields' => array(
				'id'		=> array('type'=> 'INT(10) UNSIGNED', 'null'=>'NOT NULL', 'extra'=> 'AUTO_INCREMENT', 'comment'=>'Table for store roles'),
				'name'	=> array('type'=> 'VARCHAR(45)', 'null'=>'NOT NULL', 'comment'=>'role name')	
			),
			'primary'	=> array('id')
		),
		
		'brbac__user_roles'	=> array(
			'fields' => array(
				'buser_id' => array('type'=> 'INT(10) UNSIGNED', 'comment'=>'user id'),
				'brbac__roles_id'	=> array('type'=> 'INT(10) UNSIGNED', 'comment'=>'role id')				
			),
			'primary'	=> array('buser_id', 'brbac__roles_id'),
			'foreign'	=> array(
				'buser_id'	=> null,
				'brbac__roles_id'	=> null
			)
		),
		'brbac'	=> array(
			'fields' => array(
				'brbac__roles_id'	=> array('type'=> 'INT(10) UNSIGNED', 'comment'=>'role id'),
				'brbac__privileges_id'	=> array('type'=> 'INT(10) UNSIGNED', 'comment'=>'privilege id'),
				'brbac__rules_id'	=> array('type'=> 'INT(10) UNSIGNED', 'comment'=>'rule id')
			),
			'primary'	=> array('brbac__roles_id', 'brbac__privileges_id','brbac__rules_id'),
			'foreign'	=> array(
				'brbac__roles_id'	=> null,
				'brbac__privileges_id'	=> null,
				'brbac__rules_id'	=> null
			)
		)
	),
	'insert' => array(
		'brbac__privileges'=> array(
			array('id','name'),
			array('1','read'),
			array('2','add'),
			array('3','edit'),
			array('4','delete'),
			array('5','unlock')
		),
		'brbac__rules'=> array(
			array('id','name'),
			array('1','editOwner')
		),
		'brbac__roles'=> array(
			array('id','name'),
			array('1','public'),
			array('2','user'),
			array('3','author'),
			array('4','editor'),
			array('5','admin'),
			array('6','superadmin')
		),
		'brbac__user_roles'	=> array(
			array('buser_id','brbac__roles_id'),
			array('1','6')
		),
		'brbac'	=> array(
			array('brbac__roles_id','brbac__privileges_id','brbac__rules_id'),
			array('1','1',''),
			array('3','2',''),
			array('3','3','1'),
			array('4','3',''),
			array('6','5','')
		)
	)
);