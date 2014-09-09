<?php
defined('_BLIB') or die;

class bTable extends bBlib{
	
	private $meta = null;
	private $query = array();
	private $records = null;
	
	protected function inputSelf(){
		$this->version = '1.0.0';
	}
	
	protected function input($data, $caller){
		$this->query = $data['query'];
		$this->meta = $data['meta'];
		$this->caller = $caller;
		$this->blockTunnel = get_class($caller);
	}
	
	public function output(){
		if($this->caller)return array('bTable'=>$this);
	}
	
	private function getQuery(){
		//var_dump($this->getTunnel());
		return $this->query;
	}
	
	public function setMeta($data){
		$this->meta = $data;
	}
	
	public function getMeta(){
		return $this->meta;
	}
	
	public function setQuery($data){
		$this->query = $data;
	}
	
	public function getTable(){
		$Q = $this->getQuery();
		$result = $this->caller->_query($Q);
		return array('block'=>__class__, 'tunnel'=>$this->blockTunnel, 'meta'=>$this->getMeta(), 'content'=>$result->fetchAll(PDO::FETCH_ASSOC));
	}

	public function _getTable($data = array(), $caller = null){
		if($caller)return $caller->bTable->getTable($data);
		return $this->getTable();
	}
	
}
