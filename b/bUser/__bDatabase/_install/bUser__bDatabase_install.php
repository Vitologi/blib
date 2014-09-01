<?php
defined('_BLIB') or die();
return array(
	'create'	=> array(
		'buser'	=> array(
			'fields' => array(
				'id'		=> array('type'=> 'INT(10) UNSIGNED', 'null'=>'NOT NULL', 'extra'=> 'AUTO_INCREMENT', 'comment'=>'Table for store users'),
				'login'		=> array('type'=> 'VARCHAR(45)', 'null'=>'NOT NULL', 'comment'=>'user login'),
				'password'	=> array('type'=> 'VARCHAR(45)', 'null'=>'NOT NULL', 'comment'=>'user password'),
				'bconfig_id' => array('type'=> 'INT(10) UNSIGNED', 'comment'=>'config')
			),
			'primary'	=> array('id'),
			'foreign'	=> array(
				'bconfig_id'	=> null
			)
		)
	),
	'insert' => array(
		'buser' => array(
			array('login', 'password'),
			array('admin', '21232f297a57a5a743894a0e4a801fc3')
		)
	)
);