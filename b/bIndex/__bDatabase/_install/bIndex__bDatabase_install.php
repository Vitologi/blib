<?php
defined('_BLIB') or die();
return array(
	'create'	=> array(
		'bIndex'	=> array(
			'fields' => array(
				'id'		=> array('type'=> 'INT(10) UNSIGNED', 'null'=>'NOT NULL', 'extra'=> 'AUTO_INCREMENT', 'comment'=>'Table for store page properties'),
				'bConfig_id'	=> array('type'=> 'INT(10) UNSIGNED', 'comment'=>'configuration identifier'),
				'bTemplate_id'	=> array('type'=> 'INT(10) UNSIGNED', 'comment'=>'template identifier'),
				'bCategory_id'	=> array('type'=> 'INT(10) UNSIGNED', 'comment'=>'category identifier')				
			),
			'primary'	=> array('id'),
			'foreign'	=> array(
				'bConfig_id'	=> null,
				'bTemplate_id'	=> null,
				'bCategory_id'	=> null
			)
		)
	)
);