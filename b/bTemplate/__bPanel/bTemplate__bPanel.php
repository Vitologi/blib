<?php
defined('_BLIB') or die;

class bTemplate__bPanel extends bBlib{	
	
	protected function inputSelf(){
		$this->version = '1.0.0';
	}
	
	public function _controller($data = array(), $caller = null){
		$block = $caller;
		$pannel = $caller->bPanel;
		$tunnel = $block->getTunnel();
		$items = $tunnel['items'];
		$item = $items[0];
		
		
		$pannel->setModule('"{1}"', $pannel->showBlocks());
		$defaultLocation = array(
			'block'=>'bLink',
			'elem'=>'location',
			'content'=> array(
				'bPanel' => array('controller'=>'bTemplate'),
				'bTemplate' => array('layout'=>'show', 'view'=>'list')
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
				$message = 'Панель редактирования пунктов меню';
				break;
		}
		
		$pannel->setModule('"{2}"', array('content'=>array($pannel->showError($message), $defaultLocation)));
		
		
		switch($pannel->getView()){
			case "add":
				$tools = array('content'=>array(
					array('block'=>'bPanel','elem'=>'button', 'uphold'=>array('templateForm'), 'layout'=>'add', 'view'=>'list', 'controller'=>'bTemplate', 'content'=>'Сохранить'),
					array('block'=>'bPanel','elem'=>'button', 'layout'=>'show', 'view'=>'list', 'controller'=>'bTemplate', 'content'=>'Отмена')
				));
				
				
				$pannel->setModule('"{2}"', $pannel->showError('Добавление записи'));
				$pannel->setModule('"{3}"', $tools);
				$pannel->setModule('"{4}"', $block->_showItem());
				
				break;
			
			case "edit":
				$tools = array('content'=>array(
					array('block'=>'bPanel','elem'=>'button', 'uphold'=>array('templateForm'), 'layout'=>'edit', 'view'=>'list', 'controller'=>'bTemplate', 'content'=>'Сохранить'),
					array('block'=>'bPanel','elem'=>'button', 'layout'=>'show', 'view'=>'list', 'controller'=>'bTemplate', 'content'=>'Отмена')
				));
				
				$pannel->setModule('"{2}"', $pannel->showError('Редактирование записи'));
				$pannel->setModule('"{3}"', $tools);
				$pannel->setModule('"{4}"', $block->_showItem($item));
				
				break;
			
			case "list":
			default:
			
				$tools = array('content'=>array(
					array('block'=>'bPanel','elem'=>'button', 'layout'=>'show', 'view'=>'add', 'controller'=>'bTemplate', 'content'=>'Добавить'),
					array('block'=>'bPanel','elem'=>'button', 'uphold'=>array('templateTable'), 'layout'=>'show', 'view'=>'edit', 'controller'=>'bTemplate', 'content'=>'Редактировать'),
					array('block'=>'bPanel','elem'=>'button', 'uphold'=>array('templateTable'), 'layout'=>'delete', 'view'=>'list', 'controller'=>'bTemplate', 'content'=>'Удалить')
				));

				$pannel->setModule('"{3}"', $tools);
				$pannel->setModule('"{4}"', $block->_showList());
				break;
		}
	}
	
	
	
	public function _showItem($data = array(), $caller = null){
		if($caller == null){return;}
		$data = $data[0];
	
		$config = array(
			'name'	=> 'templateForm',
			'mods'=>array('style'=>'default'), //0_0 need frontend side
			'meta'	=> array(
				'processor'	=> false,
				'method' => "POST",
				'action' => "/",
				'ajax' =>true
			),
			'content'	=> array(

				array('elem'=>'hidden', 'name'=>'id'),
				
				array('elem'=>'label', 'content'=>'Название', 'attrs'=>array('title'=>'Произвольное название шаблона (для упрощения поиска)')),
				array('elem'=>'text', 'name'=>'name'),
				
				array('elem'=>'label', 'content'=>'Блок владелец', 'attrs'=>array('title'=>'Блок за которым закреплен шаблон')),
				array('elem'=>'text', 'name'=>'owner'),				
				
				array('elem'=>'label', 'content'=>'Блок', 'attrs'=>array('title'=>'Блок который обрабатывает/дорабатывает шаблон')),
				array('elem'=>'text', 'name'=>'block'),
				
				array('elem'=>'textarea', 'mods'=>array('full'=>true), 'name'=>'template')
			)
		);

		if($data['id'])$config['meta']['query'] = array('select'=>array('bTemplate'=>array('id', 'name', 'owner', 'block'=>'blib', 'template')),'where'=>array('bTemplate'=>array('id'=>$data['id'])));

		$caller->setParent('bForm', $config);
		$form = $caller->_getForm();
		
		return $form;
	}
	
	public function _showList($data = array(), $caller = null){
		if($caller == null){return;}
		
		
		$caller->setParent('bTable', array(
			'name'	=> 'templateTable',
			'query'	=> array('select'=>array('bTemplate'=>array('id', 'name', 'owner', 'blib', 'template'))),
			'meta'	=> array(
				'processor'	=> false,
				'position'=>array('id', 'name', 'owner', 'blib', 'template'),
				'keys'	=> array('id'),
				'page'	=> array('rows'=>10),
				'fields'	=> array(
					'id'	=> array('title'=>'Ключевое поле', 'note'=>'Подле для хранения ключа таблицы', 'type'=>'text', 'mods'=>array('align'=>'center')),
					'name'	=> array('title'=>'Название', 'note'=>'Название шаблона', 'type'=>'text', 'mods'=>array('align'=>'center')),
					'owner'	=> array('title'=>'Блок владелец', 'note'=>'За каким блоком закреплен шаблон', 'type'=>'text'),
					'blib'	=> array('title'=>'Блок', 'note'=>'Блок который обрабатывает/дорабатывает шаблон)', 'type'=>'text'),
					'template'	=> array('title'=>'Шаблон', 'type'=>'text')
				)
			)
		));
		
		$table = $caller->_getTable();
		$table['mods']=array('style'=>'default');
		
		return $table;
	}
	
	public function _addItem($data = array(), $caller = null){
		if($caller == null){return;}
		$data = array_merge(
			array(
				'name'=>'noname',
				'owner'=>NULL,
				'blib'=>NULL,
				'template'=>NULL
			),
			(array) $data[0]
		);
		
		$Q = array(
			'insert'=>array(
				'bTemplate'=>array(
					'name'=>$data['name'],
					'owner'=>$data['owner'],
					'blib'=>$data['blib'],
					'template'=>$data['template']
				)
			)
		);
		
		return $caller->_query($Q);
	}
	
	public function _editItem($data = array(), $caller = null){
		if($caller == null){return;}
		
		$update = array_merge(
			array(
				'name'=>'noname',
				'owner'=>NULL,
				'block'=>NULL,
				'template'=>NULL
			),
			(array) $data[0]
		);
		$id = $update['id'];
		$update['blib'] = $update['block'];
		unset($update['id']);
		unset($update['block']);
		
		
		$Q = array(
			'update'=>array(
				'bTemplate'=>$update
			),
			'where'=>array(
				'bTemplate'=>array('id'=>$id)
			)
		);
		//var_dump($Q, $caller->_query($Q,true));
		return $caller->_query($Q);
	}
	
	/**
	 * Method for delete selected menu item
	 * 
	 * @param {number}[] $data[0] 	- id
	 * @return {boolean}			- request status
	 */
	public function _delItem($data = array(), $caller = null){
		if($caller == null){return;}
		
		$where = array();
		foreach($data[0] as $key => $value){
			$where[] = array('id',$value['id'],false,true);
		}
		
		return $caller->_query(array(
			'delete'=>array('bTemplate'),
			'where'=>array('bTemplate'=>$where)
		));
	}
}