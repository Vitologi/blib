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
	),
	'insert' => array(
		'bMenu' => array(
			array('id', 'menu', 'name', 'link', 'bMenu_id'),
			array('1', '1', 'main'),
			array('2', '1', 'Home', '/', '1'),
			array('3', '1', 'Documentation', null, '1'),
			array('4', '1', 'Base', '/documentation/base/', '3'),
			array('5', '1', 'API', null, '3'),
			array('6', '1', 'Backend', '/documentation/api/backend/', '5'),
			array('7', '1', 'Frontend', '/documentation/api/frontend/', '5'),
			array('8', '1', 'FAQ', '/documentation/faq/', '3'),
			array('9', '1', 'Downloads', '/downloads/', '1'),
			array('10', '1', 'Analitics', null, '1'),
			array('11', '1', 'Grafics', '/analitics/grafics/', '10'),
			array('12', '1', 'Examples', '/analitics/examples/', '10')
		)
	)
);