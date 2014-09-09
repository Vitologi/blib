<?php
defined('_BLIB') or die;

class bMenu__bPanel extends bBlib{	
	
	protected function inputSelf(){
		$this->version = '1.0.0';
	}
	
	public function _controller($data = array(), $caller = null){
		$block = $caller;
		$pannel = $caller->bPanel;
		
		$block->setParent('bTemplate',array());
		$template = $block->_getTemplateByName('template');
		$pannel->setTemplate($template);
		
		switch($pannel->getLayout()){
			case "show":
			default:
				
				switch($pannel->getView()){
					
					case "list":
						$pannel->setModule('"{4}"', $block->_showList());
						break;
					case "error":
					default:
						$pannel->setModule('"{1}"', $pannel->showBlocks());
						$pannel->setModule('"{2}"', $pannel->showError());
						$pannel->setModule('"{3}"', $pannel->showError());
						$pannel->setModule('"{4}"', $block->_showList());
						break;
				}
				break;
		}
	}
	

	public function _showList($data = array(), $caller = null){
		if($caller == null){return;}
		
		
		$caller->setParent('bTable', array(
			'query'	=> array('select'=>array('bMenu'=>array('id', 'menu', 'name', 'link', 'bconfig_id', 'bmenu_id'))),
			'meta'	=> array(
				'processor'	=> false,
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
		));
		
		$table = $caller->_getTable();
		$table['mods']=array('style'=>'default');
		
		return $table;
	}
	
	
}