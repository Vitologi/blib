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
			'query'	=> array('select'=>array('bMenu'=>array('id'))),
			'meta'	=> array('processor'=>false)
		));
		
		$table = $caller->_getTable();
		
		return array("block"=>get_class($caller), "elem"=>"all", "content"=>array($table));
	}
	
	
}