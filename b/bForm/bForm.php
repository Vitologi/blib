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
	private function getQuery(){return $this->query;}
	public function setQuery($data){$this->query = $data;}
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
		
		if($data['query'])$this->setQuery($data['query']);
		if($data['mods'])$this->setMods($data['mods']);
		if($data['content'])$this->setContent($data['content']);
		$this->setMeta($data['meta']);
		
		$this->blockTunnel = get_class($caller);
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
		$select = ($data['select']?$data['select']:array());
		$temp = array();
		foreach($select as $key => $value){
			$result = $this->caller->_query($value);
			
			$temp[$key] = $result->fetchAll(PDO::FETCH_ASSOC);
		}
		$data['select'] = $temp;	
		
		$this->meta = $data;
		
	}
	
	public function getForm(){
		return array('block'=>__class__, 'mods'=>$this->mods, 'tunnel'=>$this->blockTunnel, 'name'=>$this->name, 'meta'=>$this->getMeta(), 'content'=>$this->getContent());
	}

	public function _getForm($data = array(), $caller = null){
		if($caller)return $caller->bForm->getForm($data);
		return $this->getForm();
	}
	
}