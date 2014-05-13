<?php
defined('_BLIB') or die();
return array(
	'create'	=> array(
		'bExampleTest1'	=> array(
			'fields'	=> array(
				'id'			=> array('type'=> 'INT(10) UNSIGNED', 'null'=>'NOT NULL', 'extra'=> 'AUTO_INCREMENT', 'comment'=>'index column'),
				'description'	=> array('type'=> 'VARCHAR(45)', 'null'=>'NULL')
			),
			'primary'	=> array('id'),
			'engine'	=> 'InnoDB'
		),
		'bExampleTest2'	=> array(
			'fields'	=> array(
				'id'			=> array('type'=> 'INT(10) UNSIGNED', 'null'=>'NOT NULL', 'extra'=> 'AUTO_INCREMENT', 'comment'=>'index column'),
				'description'	=> array('type'=> 'VARCHAR(45)', 'null'=>'NULL')
			),
			'primary'	=> array('id'),
			'engine'	=> 'InnoDB'
		),
		'bExampleTest3'	=> array(
			'fields'	=> array(
				'id'			=> array('type'=> 'INT(10) UNSIGNED', 'null'=>'NOT NULL', 'extra'=> 'AUTO_INCREMENT', 'comment'=>'index column'),
				'description'	=> array('type'=> 'VARCHAR(45)', 'null'=>'NULL')
			),
			'primary'	=> array('id'),
			'engine'	=> 'InnoDB'
		),
		'bExampleTest4'	=> array(
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
				'bExampleTest1_id'	=> array('type'=> 'INT(10) UNSIGNED', 'null'=>'NOT NULL'),
				'bExampleTest2_id'		=> array('type'=> 'INT(10) UNSIGNED', 'null'=>'NOT NULL')
			),
			'primary'	=> array('id'),
			'foreign'	=> array(
				'bExampleTest1_id'	=> null,
				'bExampleTest2_id'		=> array('table'=>'bExampleTest2',  'column' =>'id', 'ondelete'=>'cascade', 'onupdate'=>'cascade'),
			),
			'charset'	=> 'utf8',
			'engine'	=> 'InnoDB',
			'collate'	=> 'utf8_general_ci'
		)
	)
);