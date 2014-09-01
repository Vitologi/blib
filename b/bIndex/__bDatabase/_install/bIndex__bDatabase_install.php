<?php
defined('_BLIB') or die();
return array(
	'create'	=> array(
		'bindex'	=> array(
			'fields' => array(
				'id'		=> array('type'=> 'INT(10) UNSIGNED', 'null'=>'NOT NULL', 'extra'=> 'AUTO_INCREMENT', 'comment'=>'Table for store page properties'),
				'template'		=> array('type'=> 'TEXT', 'null'=>'NULL', 'comment'=>'used templates like {0:1, 1:7, 2:6, 3:{0:4, 1:6}}'),
				'bconfig_id'	=> array('type'=> 'INT(10) UNSIGNED', 'comment'=>'configuration identifier'),								
				'bcategory_id'	=> array('type'=> 'INT(10) UNSIGNED', 'comment'=>'category identifier')				
			),
			'primary'	=> array('id'),
			'foreign'	=> array(
				'bconfig_id'	=> null,
				'bcategory_id'	=> null
			)
		)
	),
	'insert' => array(
		'bindex' => array(
			array('template', 'bconfig_id'),
			array('{"0":1, "1":{"0":2}, "2":{"0":3}, "3":{"0":4}, "4":{"0":5}}', 1),
		)
	)
);