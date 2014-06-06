<?php
defined('_BLIB') or die();
return array(
	'create'	=> array(
		'bTemplate'	=> array(
			'fields' => array(
				'id'		=> array('type'=> 'INT(10) UNSIGNED', 'null'=>'NOT NULL', 'extra'=> 'AUTO_INCREMENT', 'comment'=>'Table for store json templates'),
				'name'		=> array('type'=> 'VARCHAR(45)', 'null'=>'NOT NULL', 'comment'=>'templates name'),
				'blib'		=> array('type'=> 'VARCHAR(45)', 'null'=>'NULL', 'comment'=>'dynamic block name'),				
				'involved'		=> array('type'=> 'TEXT', 'null'=>'NULL', 'comment'=>'ids used templates like 1,2,5,8,26,45'),
				'template'	=> array('type'=> 'TEXT', 'comment'=>'json serialised template'),
			),
			'primary'	=> array('id')
		)
	),
	'insert'	=> array(
		'bTemplate' => array(
			array('id', 'name', 'blib', 'involved', 'template'),
			array(1, 'index', null, '2', '{"block":"bIndex","content":[{"elem":"header","content":["{2}"]},{"elem":"body","content":[{"elem":"helper","content":"helper"},{"elem":"content","content":"content"}]},{"elem":"footer","content":"footer"}]}'),
			array(2, 'logo', null, null, '{"block":"bImageSprite","mods":{"sprite":"blib","type":"logo"}}')
		)
	)
);