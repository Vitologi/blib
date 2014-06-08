<?php
defined('_BLIB') or die();
return array(
	'create'	=> array(
		'bMenu'	=> array(
			'fields' => array(
				'id'		=> array('type'=> 'INT(10) UNSIGNED', 'null'=>'NOT NULL', 'extra'=> 'AUTO_INCREMENT', 'comment'=>'Table for store menu'),
				'menu' => array('type'=> 'INT(10) UNSIGNED', 'comment'=>'menu group'),
				'name'	=> array('type'=> 'VARCHAR(45)', 'null'=>'NOT NULL', 'comment'=>'item name'),
				'link'	=> array('type'=> 'TEXT', 'null'=>'NULL', 'comment'=>'link view'),
				'bConfig_id' => array('type'=> 'INT(10) UNSIGNED', 'comment'=>'config'),
				'bMenu_id'	=> array('type'=> 'INT(10) UNSIGNED', 'comment'=>'arrow to parent')				
			),
			'primary'	=> array('id'),
			'foreign'	=> array(
				'bMenu_id'	=> null,
				'bConfig_id'	=> null
				
			)
		)
	)
);