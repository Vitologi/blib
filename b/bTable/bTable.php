<?php
defined('_BLIB') or die;

class bTable extends bBlib{
	
	private $meta = null;
	private $query = array();
	
	protected function inputSelf(){
		$this->version = '1.0.0';
		$this->parents = array('bSession');
	}
	
	protected function input($data, $caller){
		/** parent */
		if($caller){
			$this->caller = $caller;
			$this->table = $data['table'];
			$this->setQuery($data['query']);
			$this->setMeta($data['meta']);
			
			$this->blockTunnel = get_class($caller);
			
			$data['caller'] = $this->blockTunnel;
			$this->_setSession($data['table'], $data);
			
		/** ajax */
		}else if($data['table']){
			
			$this->table = $data['table'];
			$data = $this->_getSession($this->table);
			$this->caller = new $data['caller'](array());
			
			$this->setQuery($data['query']);
			$this->setMeta($data['meta']);
			$this->blockTunnel = $data['caller'];
			$this->ajax = true;
		}

	}
	
	public function output(){
		
		/** ajax */
		if($this->ajax){
			header('Content-Type: application/json; charset=UTF-8');
			$Q = $this->getQuery();
			$result = $this->caller->_query($Q);
			echo json_encode($result->fetchAll(PDO::FETCH_ASSOC));
			exit;
		/** parent */
		}else if($this->caller){
			return array('bTable'=>$this);
		}
		
	}
	
	private function getQuery(){
		$page = $this->meta['page'];
		$number = $page['number'];
		$rows = $page['rows'];
		
		$Q = $this->query;
		$Q['sql']= " LIMIT ".($number*$rows)." , ".$rows;
		return $Q;
	}
	
	public function setMeta($data){
		$tunnel = $this->getTunnel();
		$tunnelPage = ($tunnel['page'])?$tunnel['page']:array();
		$page = array_merge(array('number'=>0, 'rows'=>20, 'count'=>0, 'paginator'=>10), $data['page'], $tunnelPage);
		
		$Q = $this->query;
		
		if(!$page['count']){
			$result = $this->caller->_query($Q);
			$page['count']= $result->rowCount();
		}
		
		$this->meta = $data;
		$this->meta['page'] = $page;
		
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
		return array('block'=>__class__, 'tunnel'=>$this->blockTunnel, 'table'=>$this->table, 'meta'=>$this->getMeta(), 'content'=>$result->fetchAll(PDO::FETCH_ASSOC));
	}

	public function _getTable($data = array(), $caller = null){
		if($caller)return $caller->bTable->getTable($data);
		return $this->getTable();
	}
	
}
