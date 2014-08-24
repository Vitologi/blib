<?php
defined('_BLIB') or die();
return array(
	'create'	=> array(
		'bSlider'	=> array(
			'fields' => array(
				'id'	=> array('type'=> 'INT(10) UNSIGNED', 'null'=>'NOT NULL', 'comment'=>'Table for store sliders group'),
				'bTemplate_id'	=> array('type'=> 'INT(10) UNSIGNED', 'comment'=>'template number')
			),
			'foreign'	=> array(
				'bTemplate_id'	=> null
			)
		)
		
	),
	'insert' => array(
		'bSlider' => array(
			array('id', 'bTemplate_id'),
			array('1', '2'),
			array('1', '3'),
			array('1', '4'),
			array('1', '5')
		)
	)
);