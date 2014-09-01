<?php
defined('_BLIB') or die();
return array(
	'create'	=> array(
		'bsession'	=> array(
			'fields' => array(
				'id'	=> array('type'=> 'CHAR(32)', 'null'=>'NOT NULL', 'comment'=>'Table for store session data in JSON format, as key use md5'),
				'date'	=> array('type'=> 'TIMESTAMP', 'null'=>'NOT NULL', 'default'=>'CURRENT_TIMESTAMP', 'extra'=>'on update CURRENT_TIMESTAMP', 'comment'=>'Session date'),
				'value'	=> array('type'=> 'TEXT', 'comment'=>'JSON serialized session data')
			),
			'primary'	=> array('id')
		)
	)
);