<?php
defined('_BLIB') or die();
return array(
	'create'	=> array(
		'bindex_rewrite' => array(
			'fields'	=> array(
				'id'		=> array('type'=> 'INT(10) UNSIGNED', 'null'=>'NOT NULL', 'extra'=> 'AUTO_INCREMENT', 'comment'=>'Rewrite compliance'),
				'url'	=> array('type'=> 'text', 'null'=>'NOT NULL', 'comment'=>'rewrite url'),
				'data'	=> array('type'=> 'text', 'null'=>'NOT NULL', 'comment'=>'rewrite data')
			),
			'primary'	=> array('id')
		)
	)
);