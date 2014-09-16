<?php
defined('_BLIB') or die;

class bMenu__bPanel extends bBlib{	
	
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
		
		switch($pannel->getLayout()){
			
			case "add":
				$message = ($block->_addItem($item)?"Запись добавлена":"Ошибка добавления записи");
				$pannel->setModule('"{2}"', array('content'=>array($pannel->showError($message),array('block'=>'bPanel', 'elem'=>'location', 'controller'=>'bMenu', 'layout'=>'show', 'view'=>'list'))));
				break;
			
			case "edit":
				$message = ($block->_editItem($item)?"Запись отредактирована":"Ошибка редактирования записи");
				$pannel->setModule('"{2}"', array('content'=>array($pannel->showError($message),array('block'=>'bPanel', 'elem'=>'location', 'controller'=>'bMenu', 'layout'=>'show', 'view'=>'list'))));
				break;
				
			case "delete":
				$message = ($block->_delItem($items)?"Строки удалены":"Ошибка удаления записей");
				$pannel->setModule('"{2}"', array('content'=>array($pannel->showError($message),array('block'=>'bPanel', 'elem'=>'location', 'controller'=>'bMenu', 'layout'=>'show', 'view'=>'list'))));
				break;
			
			case "show":
			default:
				$pannel->setModule('"{2}"', $pannel->showError('Панель редактирования пунктов меню'));
				break;
		}
		
		
		
		
		switch($pannel->getView()){
			case "add":
				$tools = array('content'=>array(
					array('block'=>'bPanel','elem'=>'button', 'layout'=>'add', 'view'=>'list', 'controller'=>'bMenu', 'content'=>'Сохранить'),
					array('block'=>'bPanel','elem'=>'button', 'layout'=>'show', 'view'=>'list', 'controller'=>'bMenu', 'content'=>'Отмена')
				));
				
				
				$pannel->setModule('"{2}"', $pannel->showError('Добавление записи'));
				$pannel->setModule('"{3}"', $tools);
				$pannel->setModule('"{4}"', $block->_showItem());
				
				break;
			
			case "edit":
				$tools = array('content'=>array(
					array('block'=>'bPanel','elem'=>'button', 'layout'=>'edit', 'view'=>'list', 'controller'=>'bMenu', 'content'=>'Сохранить'),
					array('block'=>'bPanel','elem'=>'button', 'layout'=>'show', 'view'=>'list', 'controller'=>'bMenu', 'content'=>'Отмена')
				));
				
				$pannel->setModule('"{2}"', $pannel->showError('Редактирование записи'));
				$pannel->setModule('"{3}"', $tools);
				$pannel->setModule('"{4}"', $block->_showItem($item));
				
				break;
			
			case "list":
			default:
			
				$tools = array('content'=>array(
					array('block'=>'bPanel','elem'=>'button', 'layout'=>'show', 'view'=>'add', 'controller'=>'bMenu', 'content'=>'Добавить'),
					array('block'=>'bPanel','elem'=>'button', 'uphold'=>array('menuTable'), 'layout'=>'show', 'view'=>'edit', 'controller'=>'bMenu', 'content'=>'Редактировать'),
					array('block'=>'bPanel','elem'=>'button', 'uphold'=>array('menuTable'), 'layout'=>'delete', 'view'=>'list', 'controller'=>'bMenu', 'content'=>'Удалить')
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
			'name'	=> 'menuForm',
			'meta'	=> array(
				'processor'	=> false,
				'method' => "POST",
				'action' => "/",
				'ajax' =>true,
				'position'=>array('id', 'menu', 'name', 'link', 'bconfig_id', 'bmenu_id'),
				'fields'	=> array(
					'id'	=> array('title'=>'Ключевое поле', 'note'=>'Подле для хранения ключа таблицы', 'type'=>'hidden'),
					'menu'	=> array('title'=>'Номер меню', 'note'=>'К какому меню принадлежит', 'type'=>'text', 'mods'=>array('align'=>'center')),
					'name'	=> array('title'=>'Название', 'note'=>'Название пункта меню', 'type'=>'text'),
					'link'	=> array('title'=>'Ссылка', 'note'=>'На что ссылается пункт (если пусто, значит меню-контейнер)', 'type'=>'text'),
					'bconfig_id'	=> array('title'=>'Настройки', 'note'=>'Номер конфигурационных настроек', 'type'=>'text'),
					'bmenu_id'	=> array('title'=>'Родитель', 'note'=>'Корневой пункт меню (куда будет вложен)', 'type'=>'text', 'mods'=>array('align'=>'center'))
				)
			)
		);
		
		if($data['id'])$config['query'] = array('select'=>array('bMenu'=>array('id', 'menu', 'name', 'link', 'bconfig_id', 'bmenu_id')),'where'=>array('bMenu'=>array('id'=>$data['id'])));
		
		$caller->setParent('bForm', $config);
		$form = $caller->_getForm();
		$form['mods']=array('style'=>'default');
		
		return $form;
	}
	
	public function _showList($data = array(), $caller = null){
		if($caller == null){return;}
		
		
		$caller->setParent('bTable', array(
			'name'	=> 'menuTable',
			'query'	=> array('select'=>array('bMenu'=>array('id', 'menu', 'name', 'link', 'bconfig_id', 'bmenu_id'))),
			'meta'	=> array(
				'processor'	=> false,
				'position'=>array('id', 'menu', 'name', 'link', 'bconfig_id', 'bmenu_id'),
				'keys'	=> array('id','bmenu_id'),
				'page'	=> array('rows'=>5),
				'fields'	=> array(
					'id'	=> array('title'=>'Ключевое поле', 'note'=>'Подле для хранения ключа таблицы', 'type'=>'hidden'),
					'menu'	=> array('title'=>'Номер меню', 'note'=>'К какому меню принадлежит', 'type'=>'text', 'mods'=>array('align'=>'center')),
					'name'	=> array('title'=>'Название', 'note'=>'Название пункта меню', 'type'=>'text'),
					'link'	=> array('title'=>'Ссылка', 'note'=>'На что ссылается пункт (если пусто, значит меню-контейнер)', 'type'=>'text'),
					'bconfig_id'	=> array('title'=>'Настройки', 'note'=>'Номер конфигурационных настроек', 'type'=>'text'),
					'bmenu_id'	=> array('title'=>'Родитель', 'note'=>'Корневой пункт меню (куда будет вложен)', 'type'=>'text', 'mods'=>array('align'=>'center'))
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
				'menu'=>'1',
				'name'=>'noname',
				'link'=>'/',
				'bconfig_id'=>NULL,
				'bmenu_id'=>NULL
			),
			(array) $data[0]
		);
		
		$Q = array(
			'insert'=>array(
				'bMenu'=>array(
					'menu'=>$data['menu'],
					'name'=>$data['name'],
					'link'=>$data['link'],
					'bconfig_id'=>$data['bconfig_id'],
					'bmenu_id'=>$data['bmenu_id']
				)
			)
		);
		
		return $caller->_query($Q);
	}
	
	public function _editItem($data = array(), $caller = null){
		if($caller == null){return;}
		$data = $data[0];
		
		$update = array();
		if($data['menu'])$update['menu'] = $data['menu'];
		if($data['name'])$update['name'] = $data['name'];
		if($data['link'])$update['link'] = $data['link'];
		if($data['bconfig_id'])$update['bconfig_id'] = $data['bconfig_id'];
		if($data['bmenu_id'])$update['bmenu_id'] = $data['bmenu_id'];
		
		$Q = array(
			'update'=>array(
				'bMenu'=>$update
			),
			'where'=>array(
				'bMenu'=>array('id'=>$data['id'])
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
	public function _delItem($data = array(), $caller = null){
		if($caller == null){return;}
		
		$where = array();
		foreach($data[0] as $key => $value){
			$where[] = array('id',$value['id'],false,true);
		}
		
		return $caller->_query(array(
			'delete'=>array('bMenu'),
			'where'=>array('bMenu'=>$where)
		));
	}
}