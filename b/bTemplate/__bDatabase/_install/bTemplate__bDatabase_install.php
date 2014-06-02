<?php
defined('_BLIB') or die();
return array(
	'create'	=> array(
		'bTemplate'	=> array(
			'fields' => array(
				'id'		=> array('type'=> 'INT(10) UNSIGNED', 'null'=>'NOT NULL', 'extra'=> 'AUTO_INCREMENT', 'comment'=>'Table for store json templates'),
				'name'		=> array('type'=> 'VARCHAR(45)', 'null'=>'NOT NULL', 'comment'=>'templates name'),
				'involved'		=> array('type'=> 'TEXT', 'null'=>'NULL', 'comment'=>'ids used templates like 1,2,5,8,26,45'),
				'template'	=> array('type'=> 'TEXT', 'comment'=>'json serialised template'),
			),
			'primary'	=> array('id')
		)
	)
);