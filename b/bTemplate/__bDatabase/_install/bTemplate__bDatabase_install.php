<?php
defined('_BLIB') or die();
return array(
	'create'	=> array(
		'btemplate'	=> array(
			'fields' => array(
				'id'		=> array('type'=> 'INT(10) UNSIGNED', 'null'=>'NOT NULL', 'extra'=> 'AUTO_INCREMENT', 'comment'=>'Table for store json templates'),
				'owner'		=> array('type'=> 'VARCHAR(45)', 'null'=>'NULL', 'comment'=>'owner block name'),
				'name'		=> array('type'=> 'VARCHAR(45)', 'null'=>'NOT NULL', 'comment'=>'templates name'),
				'blib'		=> array('type'=> 'VARCHAR(45)', 'null'=>'NULL', 'comment'=>'dynamic block name'),
				'template'	=> array('type'=> 'TEXT', 'comment'=>'json serialised template'),
			),
			'primary'	=> array('id')
		)
	),
	'insert'	=> array(
		'btemplate' => array(
			array('id', 'name', 'blib', 'template'),
			array(1, 'index', null,  '{"block":"bIndex","content":[{"elem":"header","content":["{1}"]},{"elem":"tools","content":["{2}"]},{"elem":"body","content":[{"elem":"helper","content":["{3}"]},{"elem":"content","content":"content"},{"elem":"clear"}]},{"elem":"footer","content":["{4}"]}]}'),
			array(2, 'logo', null, '{"block":"bImageSprite","mods":{"sprite":"blib","type":"logo"}}'),
			array(3, 'menu', "bMenu", '{"menu":1, "mods":{"position":"horizontal", "default":true}}'),
			array(4, 'menu', "bMenu", '{"menu":1, "mods":{"position":"vertical", "default":true}}'),
			array(5, 'footer', null, '{"block":"bStrip", "mods":{"position":"center"}, "content":"footer"}')
		)
	)
);