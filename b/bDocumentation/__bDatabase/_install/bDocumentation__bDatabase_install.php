<?php
defined('_BLIB') or die();
return array(
	'create'	=> array(
		'bDocumentation__group'	=> array(
			'fields' => array(
				'id'	=> array('type'=> 'INT(10) UNSIGNED', 'null'=>'NOT NULL', 'extra'=> 'AUTO_INCREMENT', 'comment'=>'Table for store Documentation groups'),
				'name'	=> array('type'=> 'VARCHAR(45)', 'null'=>'NOT NULL', 'comment'=>'group name'),
				'bDocumentation__group_id'	=> array('type'=> 'INT(10) UNSIGNED', 'comment'=>'parent group')
			),
			'primary'	=> array('id'),
			'foreign'	=> array(
				'bDocumentation__group_id'	=> null
			)
		),
		'bDocumentation'	=> array(
			'fields' => array(
				'id'	=> array('type'=> 'INT(10) UNSIGNED', 'null'=>'NOT NULL', 'extra'=> 'AUTO_INCREMENT', 'comment'=>'Table for store Documentation'),
				'name'	=> array('type'=> 'VARCHAR(45)', 'null'=>'NOT NULL', 'comment'=>'item name'),
				'note'	=> array('type'=> 'TEXT', 'null'=>'NULL', 'comment'=>'small description'),
				'description'	=> array('type'=> 'TEXT', 'null'=>'NULL', 'comment'=>'full description'),
				'group'	=> array('type'=> 'TEXT', 'null'=>'NULL', 'comment'=>'all included descriptions'),
				'bDocumentation__group_id'	=> array('type'=> 'INT(10) UNSIGNED', 'comment'=>'group'),
				'bDocumentation_id'	=> array('type'=> 'INT(10) UNSIGNED', 'comment'=>'arrow to parent')
			),
			'primary'	=> array('id'),
			'foreign'	=> array(
				'bDocumentation_id'	=> null,
				'bDocumentation__group_id'	=> null
			)
		)
		
	),
	'insert' => array(
		'bDocumentation__group' => array(
			array('id', 'name','bDocumentation__group_id'),
			array('1', 'Фронтэнд'),
			array('2', 'Бэкэнд'),
			array('3', 'Глобальные объекты',1),
			array('4', 'Подключаемые блоки',1),
			array('5', 'Публичные свойства'),
			array('6', 'Функции'),
			array('7', 'Объекты')
		),
		'bDocumentation' => array(
			array('id', 'name', 'note', 'description', 'bDocumentation__group_id', 'bDocumentation_id','group'),
			array('1', 'blib', '{"content":"Основной обьект фронтэнда"}', '{"content":"Расширяет среду программирования на клиентской стороне"}', 3, null,'[2,3]'),
			array('2', 'build', '{"content":"Метод для построения blib-дерева"}', '{"content":"Строит bom"}', 5, 1),
			array('3', 'Object(bom)', '{"content":"Объект возвращаемый методом построения"}', '{"content":"Объект возвращаемый методом построения"}', 7, 2)
		)
	)
);