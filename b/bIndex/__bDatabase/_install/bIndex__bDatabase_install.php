<?php
defined('_BLIB') or die();
return array(
	'create'	=> array(
		'bIndex'	=> array(
			'fields' => array(
				'id'		=> array('type'=> 'INT(10) UNSIGNED', 'null'=>'NOT NULL', 'extra'=> 'AUTO_INCREMENT', 'comment'=>'Table for store page properties'),
				'template'		=> array('type'=> 'TEXT', 'null'=>'NULL', 'comment'=>'used templates like {0:1, 1:7, 2:6, 3:{0:4, 1:6}}'),
				'bConfig_id'	=> array('type'=> 'INT(10) UNSIGNED', 'comment'=>'configuration identifier'),								
				'bCategory_id'	=> array('type'=> 'INT(10) UNSIGNED', 'comment'=>'category identifier')				
			),
			'primary'	=> array('id'),
			'foreign'	=> array(
				'bConfig_id'	=> null,
				'bCategory_id'	=> null
			)
		)
	),
	'insert' => array(
		'bIndex' => array(
			array('template', 'bConfig_id'),
			array('{"0":1, "1":2, "2":3, "3":4, "4":{"0":5}}', 1),
		)
	)
);