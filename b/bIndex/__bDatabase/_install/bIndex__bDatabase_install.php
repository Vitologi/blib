<?php
defined('_BLIB') or die();
return array(
	'create'	=> array(
		'bIndex'	=> array(
			'fields' => array(
				'id'		=> array('type'=> 'INT(10) UNSIGNED', 'null'=>'NOT NULL', 'extra'=> 'AUTO_INCREMENT', 'comment'=>'Table for store page properties'),
				'template'	=> array('type'=> 'VARCHAR(45)', 'null'=>'NOT NULL', 'comment'=>'template name = address'),
				'meta'		=> array('type'=> 'TEXT', 'comment'=>'JSON serialized meta')
			),
			'primary'	=> array('id')
		)
	)
);