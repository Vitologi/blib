<?php
defined('_BLIB') or die();
return array(
	'create'	=> array(
		'bslider'	=> array(
			'fields' => array(
				'id'	=> array('type'=> 'INT(10) UNSIGNED', 'null'=>'NOT NULL', 'comment'=>'Table for store sliders group'),
				'btemplate_id'	=> array('type'=> 'INT(10) UNSIGNED', 'comment'=>'template number')
			),
			'foreign'	=> array(
				'btemplate_id'	=> null
			)
		)
		
	),
	'insert' => array(
		'bslider' => array(
			array('id', 'btemplate_id'),
			array('1', '2'),
			array('1', '3'),
			array('1', '4'),
			array('1', '5')
		)
	)
);