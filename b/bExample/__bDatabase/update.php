<?php
defined('_BLIB') or die();
return array(
	
	'1.112.1' => array(
		'create'	=> array(
			'bExampleTest4'	=> array(
				'fields'	=> array(
					'id'			=> array('type'=> 'INT(10) UNSIGNED', 'null'=>'NOT NULL', 'extra'=> 'AUTO_INCREMENT', 'comment'=>'index column'),
					'description'	=> array('type'=> 'VARCHAR(45)', 'null'=>'NULL')
				),
				'primary'	=> array('id'),
				'engine'	=> 'InnoDB'
			)
		)
	),
	
	'1.2.1' => array(
		'create'	=> array(
			'bExampleTest3'	=> array(
				'fields'	=> array(
					'id'			=> array('type'=> 'INT(10) UNSIGNED', 'null'=>'NOT NULL', 'extra'=> 'AUTO_INCREMENT', 'comment'=>'index column'),
					'description'	=> array('type'=> 'VARCHAR(45)', 'null'=>'NULL')
				),
				'primary'	=> array('id'),
				'engine'	=> 'InnoDB'
			)
		)
	),
);