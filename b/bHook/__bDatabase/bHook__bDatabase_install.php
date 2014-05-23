<?php
defined('_BLIB') or die();
return array(
	'create'	=> array(
		'bHook'	=> array(
			'fields'	=> array(
				'id'		=> array('type'=> 'INT(10) UNSIGNED', 'null'=>'NOT NULL', 'extra'=> 'AUTO_INCREMENT', 'comment'=>'Table for hooks registration'),
				'blib'		=> array('type'=> 'VARCHAR(45)', 'null'=>'NOT NULL'),
				'name'		=> array('type'=> 'VARCHAR(45)', 'null'=>'NOT NULL'),
				'version'	=> array('type'=> 'VARCHAR(45)', 'null'=>'NOT NULL'),
				'enabled'	=> array('type'=> 'BOOLEAN ', 'null'=>'NOT NULL', 'default'=>'FALSE'),
				'json'		=> array('type'=> 'TEXT', 'null'=>'NULL')
			),
			'primary'	=> array('id')
		)
	)
);