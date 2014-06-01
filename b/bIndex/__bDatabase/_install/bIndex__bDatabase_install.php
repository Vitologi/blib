<?php
defined('_BLIB') or die();
return array(
	'create'	=> array(
		'bIndex'	=> array(
			'fields' => array(
				'id'		=> array('type'=> 'INT(10) UNSIGNED', 'null'=>'NOT NULL', 'extra'=> 'AUTO_INCREMENT', 'comment'=>'Table for store page properties'),
				'group'		=> array('type'=> 'VARCHAR(45)', 'comment'=>'settings owner'),
				'name'		=> array('type'=> 'VARCHAR(45)', 'comment'=>'owner identificator'),
				'value'	=> array('type'=> 'TEXT', 'comment'=>'JSON serialized configurations'),
				'bConfig_id'=> array('type'=> 'INT(10) UNSIGNED', 'comment'=>'id for default configurations'),
			),
			'primary'	=> array('id'),
			'foreign'	=> array(
				'bConfig_id'	=> null
			)
		)
	)
);