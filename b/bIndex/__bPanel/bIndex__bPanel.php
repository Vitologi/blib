<?php
defined('_BLIB') or die;

class bIndex__bPanel extends bBlib{	
	
	protected function inputSelf(){
		$this->version = '1.0.0';
	}
	
	public static function _controller($data = array(), $caller = null){
		$block = $caller;
		$pannel = $caller->bPanel;
		$tunnel = $block->getTunnel();
		$items = bBlib::extend($tunnel, 'items', array());
		$item = bBlib::extend($items, '0');
		
		
		$pannel->setModule('"{1}"', $pannel->showBlocks());
		$defaultLocation = array(
			'block'=>'bLink',
			'elem'=>'location',
			'content'=> array(
				'bPanel' => array('controller'=>'bIndex'),
				'bIndex' => array('layout'=>'show', 'view'=>'list')
			)
		);
		
		switch($pannel->getLayout()){
			case "add":
				$message = ($block->_addItem($item)?"Запись добавлена":"Ошибка добавления записи");
				break;
			
			case "edit":
				$message = ($block->_editItem($item)?"Запись отредактирована":"Ошибка редактирования записи");
				break;
				
			case "delete":
				$message = ($block->_delItem($items)?"Строки удалены":"Ошибка удаления записей");
				break;
			
			case "show":
			default:
				$message = 'Панель редактирования страниц';
				break;
		}
		
		$pannel->setModule('"{2}"', array('content'=>array($pannel->showError($message), $defaultLocation)));
		
		
		switch($pannel->getView()){
			case "add":
				$tools = array('content'=>array(
					array('block'=>'bPanel','elem'=>'button', 'uphold'=>array('indexForm'), 'layout'=>'add', 'view'=>'list', 'controller'=>'bIndex', 'content'=>'Сохранить'),
					array('block'=>'bPanel','elem'=>'button', 'layout'=>'show', 'view'=>'list', 'controller'=>'bIndex', 'content'=>'Отмена')
				));
				
				
				$pannel->setModule('"{2}"', $pannel->showError('Добавление записи'));
				$pannel->setModule('"{3}"', $tools);
				$pannel->setModule('"{4}"', $block->_showItem());
				
				break;
			
			case "edit":
				$tools = array('content'=>array(
					array('block'=>'bPanel','elem'=>'button', 'uphold'=>array('indexForm'), 'layout'=>'edit', 'view'=>'list', 'controller'=>'bIndex', 'content'=>'Сохранить'),
					array('block'=>'bPanel','elem'=>'button', 'layout'=>'show', 'view'=>'list', 'controller'=>'bIndex', 'content'=>'Отмена')
				));
				
				$pannel->setModule('"{2}"', $pannel->showError('Редактирование записи'));
				$pannel->setModule('"{3}"', $tools);
				$pannel->setModule('"{4}"', $block->_showItem($item));
				
				break;
			
			case "list":
			default:
			
				$tools = array('content'=>array(
					array('block'=>'bPanel','elem'=>'button', 'layout'=>'show', 'view'=>'add', 'controller'=>'bIndex', 'content'=>'Добавить'),
					array('block'=>'bPanel','elem'=>'button', 'uphold'=>array('indexTable'), 'layout'=>'show', 'view'=>'edit', 'controller'=>'bIndex', 'content'=>'Редактировать'),
					array('block'=>'bPanel','elem'=>'button', 'uphold'=>array('indexTable'), 'layout'=>'delete', 'view'=>'list', 'controller'=>'bIndex', 'content'=>'Удалить')
				));

				$pannel->setModule('"{3}"', $tools);
				$pannel->setModule('"{4}"', $block->_showList());
				break;
		}
	}
	
	
	
	public static function _showItem($data = array(), $caller = null){
		if($caller == null){return;}
		$data = bBlib::extend($data,'0');
	
		$config = array(
			'name'	=> 'indexForm',
			'mods'=>array('style'=>'default'), //0_0 need frontend side
			'meta'	=> array(
				'processor'	=> false,
				'method' => "POST",
				'action' => "/",
				'ajax' =>true,
				'select' => array()
			),
			'content'	=> array(

				array('elem'=>'hidden', 'name'=>'id'),
				
				array('elem'=>'label', 'content'=>'Категория', 'attrs'=>array('title'=>'Название категории страницы')),
				array('elem'=>'selectplus', 'name'=>'bcategory_id'),
				
				array('tag'=>'center', 'content'=>'Шаблон', 'attrs'=>array('title'=>'Компановка шаблонов в страницу')),
				array('elem'=>'textarea', 'name'=>'template')
			)
		);

		if($data['id'])$config['meta']['query'] = array('select'=>array('bIndex'=>array('id', 'template', 'bcategory_id')),'where'=>array('bIndex'=>array('id'=>$data['id'])));

		$caller->setParent('bForm', $config);
		$form = $caller->_getForm();
		
		return $form;
	}
	
	public static function _showList($data = array(), $caller = null){
		if($caller == null){return;}
		
		
		$caller->setParent('bTable', array(
			'name'	=> 'indexTable',
			'query'	=> array('select'=>array('bIndex'=>array('id', 'template', 'bcategory_id'))),
			'meta'	=> array(
				'processor'	=> false,
				'position'=>array('id', 'template', 'bcategory_id'),
				'keys'	=> array('id'),
				'page'	=> array('rows'=>5),
				'fields'	=> array(
					'id'	=> array('title'=>'Ключевое поле', 'note'=>'Подле для хранения ключа таблицы', 'type'=>'text', 'mods'=>array('align'=>'center')),
					'template'	=> array('title'=>'Шаблон', 'note'=>'Компановка шаблонов в страницу', 'type'=>'text'),
					'bcategory_id'	=> array('title'=>'Категория', 'note'=>'Название категории к которой принадлежит страница', 'type'=>'text', 'mods'=>array('align'=>'center'))
				)
			)
		));
		
		$table = $caller->_getTable();
		$table['mods']=array('style'=>'default');
		
		return $table;
	}
	
	public static function _addItem($data, $caller = null){
		if($caller == null){return;}
		bBlib::extend($data, '0', array());
		
		$data = array_merge(
			array(
				'template'=>'{}',
				'bcategory_id'=>NULL
			),
			(array) $data[0]
		);
		
		$Q = array(
			'insert'=>array(
				'bIndex'=>array(
					'template'=>$data['template'],
					'bcategory_id'=>$data['bcategory_id']
				)
			)
		);
		
		return $caller->_query($Q);
	}
	
	public static function _editItem($data = array(), $caller = null){
		if($caller == null){return;}
		
		$update = array_merge(
			array(
				'template'=>NULL,
				'bcategory_id'=>NULL
			),
			(array) $data[0]
		);
		$id = $update['id'];
		unset($update['id']);
		
		$Q = array(
			'update'=>array(
				'bIndex'=>$update
			),
			'where'=>array(
				'bIndex'=>array('id'=>$id)
			)
		);
		return $caller->_query($Q);
	}
	
	/**
	 * Method for delete selected menu item
	 * 
	 * @param {number}[] $data[0] 	- id
	 * @return {boolean}			- request status
	 */
	public static function _delItem($data = array(), $caller = null){
		if($caller == null){return;}
		
		$where = array();
		foreach($data[0] as $key => $value){
			$where[] = array('id',$value['id'],false,true);
		}
		
		return $caller->_query(array(
			'delete'=>array('bIndex'),
			'where'=>array('bIndex'=>$where)
		));
	}
}