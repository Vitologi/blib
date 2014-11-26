<?php
defined('_BLIB') or die();
return array(
	'create'	=> array(
		'bannounces'	=> array(
			'fields' => array(
				'id'	=> array('type'=> 'INT(10) UNSIGNED', 'null'=>'NOT NULL', 'extra'=> 'AUTO_INCREMENT', 'comment'=>'Table for store Announces'),
				'date'	=> array('type'=> 'TIMESTAMP', 'null'=>'NOT NULL', 'default'=>'CURRENT_TIMESTAMP', 'comment'=>'item announce date'),
				'title'	=> array('type'=> 'TEXT', 'null'=>'NULL', 'comment'=>'announce title'),
				'content'	=> array('type'=> 'TEXT', 'null'=>'NULL', 'comment'=>'announce text'),
				'published'	=> array('type'=> 'BOOLEAN', 'default'=>'0', 'comment'=>'show flag')
			),
			'primary'	=> array('id')
		)
		
	)
);