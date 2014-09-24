<?php
defined('_BLIB') or die;

class bInclude__bPanel extends bBlib{	
	
	protected function inputSelf(){
		$this->version = '1.0.0';
	}
	
	public function _controller($data = array(), $caller = null){
		$block = $caller;
		$pannel = $caller->bPanel;
		$tunnel = $block->getTunnel();		
		
		$pannel->setModule('"{1}"', $pannel->showBlocks());
		$defaultLocation = array(
			'block'=>'bLink',
			'elem'=>'location',
			'content'=> array(
				'bPanel' => array('controller'=>'bInclude'),
				'bInclude' => array('layout'=>'show', 'view'=>'list')
			)
		);
		
		switch($pannel->getLayout()){
			case "disableCache":
				$message = ($block->_disableCache()?"Кэширование отключено":"Ошибка отключения кеширования");
				break;
			
			case "enableCache":
				$message = ($block->_enableCache()?"Кэширование включено":"Ошибка включения кеширования");
				break;
			
			default:
				$message = 'Панель настройки блока кеширования файлов';
				break;
		}
		
		$pannel->setModule('"{2}"', array('content'=>array($pannel->showError($message), $defaultLocation)));
		
		
		switch($pannel->getView()){
			case "list":
			default:
				$config = $block->_getConfig(get_class($block), array('group'=>'blib'));
				$button = array('block'=>'bPanel','elem'=>'button', 'layout'=>'disableCache', 'view'=>'list', 'controller'=>'bInclude', 'content'=>'Отключить кеширование');
				
				if($config['disableCache']){
					$button['layout']='enableCache';
					$button['content']='Включить кеширование';
				}
				
				$pannel->setModule('"{3}"', $button);
				$pannel->setModule('"{4}"', array());
				break;
		}
	}
	
	
	
	public function _disableCache($data = array(), $caller = null){
		if($caller == null){return;}
		return $caller->_setConfig(get_class($caller), array('disableCache'=>true), array('group'=>'blib', 'correct'=>true));
	}
	
	public function _enableCache($data = array(), $caller = null){
		if($caller == null){return;}
		return $caller->_setConfig(get_class($caller), array('disableCache'=>false), array('group'=>'blib', 'correct'=>true));
	}
	
}