<?php
defined('_BLIB') or die();
return array(
	'create'	=> array(
		'bSession'	=> array(
			'fields' => array(
				'id'	=> array('type'=> 'CHAR(32)', 'null'=>'NOT NULL', 'comment'=>'Table for store session data in JSON format, as key use md5'),
				'value'	=> array('type'=> 'TEXT', 'comment'=>'JSON serialized session data')
			),
			'primary'	=> array('id')
		)
	)
);