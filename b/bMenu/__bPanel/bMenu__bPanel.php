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
		$pannel->setPanelTemplate($template);
		
		switch($pannel->getLayout()){
			case "show":
			default:
				
				switch($pannel->getView()){
					case "error":
					default:
						$pannel->setPanelModule('"{1}"', $pannel->showBlocks());
						$pannel->setPanelModule('"{2}"', $pannel->showError());
						$pannel->setPanelModule('"{3}"', $pannel->showError());
						$pannel->setPanelModule('"{4}"', $block->_showList());
						break;
				}
				break;
		}
	}
	

	public function _showList($data = array(), $caller = null){
		if($caller == null){return;}
		
		$result = $caller->_query(array('select'=>array('bMenu'=>array('id'))));
		return array("block"=>get_class($caller), "elem"=>"all", "content"=>$result->fetchAll(PDO::FETCH_ASSOC));
	}
	
	
}