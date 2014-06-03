<?php
defined('_BLIB') or die();
return array(
	'create'	=> array(
		'bIndex'	=> array(
			'fields' => array(
				'id'		=> array('type'=> 'INT(10) UNSIGNED', 'null'=>'NOT NULL', 'extra'=> 'AUTO_INCREMENT', 'comment'=>'Table for store page properties'),
				'meta'		=> array('type'=> 'TEXT', 'comment'=>'JSON serialized meta'),
				'bTemplate_id'	=> array('type'=> 'INT(10) UNSIGNED', 'comment'=>'template identifier'),
				'bCategory_id'	=> array('type'=> 'INT(10) UNSIGNED', 'comment'=>'category identifier')				
			),
			'primary'	=> array('id'),
			'foreign'	=> array(
				'bTemplate_id'	=> null,
				'bCategory_id'	=> null
			)
		)
	)
);