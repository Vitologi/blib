<?php
defined('_BLIB') or die;

class bSlider extends bBlib{

	protected $_traits = array('bTemplate');
	protected $_length = 0;
	protected $_delay = 10000;
	protected $_list = array();
	protected $_mods = array();

	protected function input($template = array()){

        $this->setInstance('template', 'bTemplate');

		if(isset($template['mods']))$this->_mods = $template['mods'];
		if(isset($template['length']))$this->_length = $template['length'];
		if(isset($template['delay']))$this->_delay = $template['delay'];
		if(isset($template['content']))$this->_list = $template['content'];
	}
	
	public function output(){
		
		/** @var bTemplate $_template */
		$_template = $this->getInstance('template');

		$sliders = $_template->getOwnTemplate($this->_list, __CLASS__);

		foreach($sliders as $key => $value){
			$sliders[$key]=json_decode($value);
		}

		return $this->indexView($sliders);

	}

	public function indexView(Array $list = array()){
		$content = array();

		foreach($list as $key => $elem){
			$content[] = array(
				'elem'    => 'slide',
				'content' => array($elem)
			);
		}

		return array(
			'block'   => __class__,
			'mods'    => $this->_mods,
			'delay'   => $this->_delay,
			'content' => $content
		);
	}
	
}