<?php
defined('_BLIB') or die();
return array(
	'create'	=> array(
		'bConfig'	=> array(
			'fields'	=> array(
				'id'			=> array('type'=> 'INT(10) UNSIGNED', 'null'=>'NOT NULL', 'extra'=> 'AUTO_INCREMENT', 'comment'=>'index column'),
				'description'	=> array('type'=> 'VARCHAR(45)', 'null'=>'NULL')
			),
			'primary'	=> array('id'),
			'engine'	=> 'InnoDB'
		),
		'bTest'	=> array(
			'fields'	=> array(
				'id'			=> array('type'=> 'INT(10) UNSIGNED', 'null'=>'NOT NULL', 'extra'=> 'AUTO_INCREMENT', 'comment'=>'index column'),
				'description'	=> array('type'=> 'VARCHAR(45)', 'null'=>'NULL')
			),
			'primary'	=> array('id'),
			'engine'	=> 'InnoDB'
		),
		'bExample'	=> array(
			'fields'	=> array(
				'id'			=> array('type'=> 'INT(10) UNSIGNED', 'null'=>'NOT NULL', 'extra'=> 'AUTO_INCREMENT', 'comment'=>'index column'),
				'description'	=> array('type'=> 'VARCHAR(45)', 'null'=>'NULL'),
				'bConfig_id'	=> array('type'=> 'INT(10) UNSIGNED', 'null'=>'NOT NULL'),
				'bTest_id'		=> array('type'=> 'INT(10) UNSIGNED', 'null'=>'NOT NULL')
			),
			'primary'	=> array('id'),
			'foreign'	=> array(
				'bConfig_id'	=> null,
				'bTest_id'		=> array('table'=>'bTest',  'column' =>'id', 'ondelete'=>'cascade', 'onupdate'=>'cascade'),
			),
			'charset'	=> 'utf8',
			'engine'	=> 'InnoDB',
			'collate'	=> 'utf8_general_ci'
		)
	)
);