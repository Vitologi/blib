<?php
defined('_BLIB') or die();
return array(
	'create'	=> array(
		'brewrite' => array(
			'fields'	=> array(
				'id'	=> array('type'=> 'INT(10) UNSIGNED', 'null'=>'NOT NULL', 'comment'=>'Rewrite compliance', 'extra'=> 'AUTO_INCREMENT'),
				'url'	=> array('type'=> 'text',             'null'=>'NOT NULL', 'comment'=>'rewrite url'),
				'bindex_id'=> array('type'=> 'INT(10) UNSIGNED', 'null'=>'NOT NULL', 'comment'=>'Page number'),
				'data'	=> array('type'=> 'text',             'null'=>'NOT NULL', 'comment'=>'rewrite data')
			),
			'primary'	=> array('id'),
			'foreign'	=> array(
				'bindex_id'	=> null
			)
		)
	)
);