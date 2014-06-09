<?php
defined('_BLIB') or die();
return array(
	'create'	=> array(
		'bTemplate'	=> array(
			'fields' => array(
				'id'		=> array('type'=> 'INT(10) UNSIGNED', 'null'=>'NOT NULL', 'extra'=> 'AUTO_INCREMENT', 'comment'=>'Table for store json templates'),
				'name'		=> array('type'=> 'VARCHAR(45)', 'null'=>'NOT NULL', 'comment'=>'templates name'),
				'blib'		=> array('type'=> 'VARCHAR(45)', 'null'=>'NULL', 'comment'=>'dynamic block name'),
				'template'	=> array('type'=> 'TEXT', 'comment'=>'json serialised template'),
			),
			'primary'	=> array('id')
		)
	),
	'insert'	=> array(
		'bTemplate' => array(
			array('id', 'name', 'blib', 'template'),
			array(1, 'index', null,  '{"block":"bIndex","content":[{"elem":"header","content":["{1}"]},{"elem":"body","content":[{"elem":"helper","content":["{2}"]},{"elem":"content","content":"content"}]},{"elem":"footer","content":["{3}"]}]}'),
			array(2, 'logo', null, '{"block":"bImageSprite","mods":{"sprite":"blib","type":"logo"}}'),
			array(3, 'menu', "bMenu", '{"menu":1, "position":"gorisontal"}'),
			array(4, 'test', null, '{"block":"bTest","mods":{"sprite":"blib","type":"logo"}, "content":["{1}", {"block":"bTest2", "content":"test"}]}')
		)
	)
);