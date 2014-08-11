<?php
defined('_BLIB') or die();
return array(
	'create'	=> array(
		'bDocumentation'	=> array(
			'fields' => array(
				'id'	=> array('type'=> 'INT(10) UNSIGNED', 'null'=>'NOT NULL', 'extra'=> 'AUTO_INCREMENT', 'comment'=>'Table for store Documentation'),
				'name'	=> array('type'=> 'VARCHAR(45)', 'null'=>'NOT NULL', 'comment'=>'item name'),
				'note'	=> array('type'=> 'TEXT', 'null'=>'NULL', 'comment'=>'small description'),
				'description'	=> array('type'=> 'TEXT', 'null'=>'NULL', 'comment'=>'full description'),
				'group'	=> array('type'=> 'TEXT', 'null'=>'NULL', 'comment'=>'all included objects'),
				'bDocumentation_id'	=> array('type'=> 'INT(10) UNSIGNED', 'comment'=>'arrow to parent')
			),
			'primary'	=> array('id'),
			'foreign'	=> array(
				'bDocumentation_id'	=> null
			)
		)
		
	),
	'insert' => array(
		'bDocumentation' => array(
			array('id', 'name', 'bDocumentation_id', 'note', 'description', 'group'),
			array('1', 'Фронтэнд'),
			array('2', 'Бэкэнд'),
			array('3', 'Глобальные объекты',1),
			array('4', 'Подключаемые блоки',1),
			array('5', 'Публичные свойства'),
			array('6', 'Функции'),
			array('7', 'Объекты'),
			array('8', 'blib', 3,'{"content":"Основной обьект фронтэнда"}', '{"content":"Расширяет среду программирования на клиентской стороне"}', '[9,10]'),
			array('9', 'build', 4,'{"content":"Метод для построения blib-дерева"}', '{"content":"Строит bom"}'),
			array('10', 'Object(bom)', 7,'{"content":"Объект возвращаемый методом построения"}', '{"content":"Объект возвращаемый методом построения"}'),
			array('11', 'include', 4,'{"content":"Метод для подключения блоков"}', '{"content":"Подключает блоки"}')
		)
	)
);