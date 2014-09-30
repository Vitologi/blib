<?php
defined('_BLIB') or die;

class bForm extends bBlib{
	
	private $meta = null;
	private $query = null;
	private $select = null;
	private $radio = null;
	private $content = null;
	private $mods = array();
	
	private function getMeta(){return $this->meta;}
	private function getMods(){return $this->mods;}
	private function setMods($data){$this->mods = $data;}
	private function getContent(){return $this->content;}
	private function setContent($data){$this->content = $data;}
	
	protected function inputSelf(){
		$this->version = '1.0.0';
	}
	
	protected function input($data, $caller){
		if(!$caller)return;
		$this->name = ($data['name']?$data['name']:$this->generateName());
		$this->caller = $caller;

		if($data['mods'])$this->setMods($data['mods']);
		if($data['content'])$this->setContent($data['content']);
		$this->setMeta($data['meta']);
		
		
	}
	
	public function output(){
		if($this->caller)return array('bForm'=>$this);
	}

	private function generateName($length = 8){
		$chars = 'abdefhiknrstyzABDEFGHKNQRSTYZ23456789';
		$numChars = strlen ($chars);
		$string = '';
		for ($i = 0; $i < $length; $i++) {
			$string .= substr ($chars, rand (1, $numChars) - 1, 1);
		}
		return $string;
	}
	
	public function setMeta($data){
		
		if($data['query']){
			$result = $this->caller->_query($data['query']);
			$data['query'] = $result->fetchAll(PDO::FETCH_ASSOC);
		}
		
	
		$select = (isset($data['select'])?$data['select']:array());
		$temp = array();
		foreach($select as $key => $value){
			$result = $this->caller->_query($value);
			$temp[$key] = $result->fetchAll(PDO::FETCH_ASSOC);
		}
		$data['select'] = $temp;
		
		
		$data['tunnel'] = get_class($this->caller);
		
		$this->meta = $data;
		
	}
	
	public function getForm(){
		return array('block'=>__class__, 'mods'=>$this->mods, 'name'=>$this->name, 'meta'=>$this->getMeta(), 'content'=>$this->getContent());
	}

	public static function _getForm($data = array(), $caller = null){
		if($caller == null)return false;
		return $caller->bForm->getForm($data);
	}
	
}