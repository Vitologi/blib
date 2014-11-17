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
			$this->name = ($data['name']?$data['name']:$this->generateName());
			$this->caller = $caller;
			$this->setQuery($data['query']);
			$this->setMeta($data['meta']);
			
			$this->blockTunnel = get_class($caller);
			
			$data['caller'] = $this->blockTunnel;
			$this->_setSession($this->name, $data);
			
		/** ajax */
		}else if(isset($data['name'])){
			
			$this->name = $data['name'];
			$data = $this->_getSession($this->name);
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
		$limit = " LIMIT ".($number*$rows)." , ".$rows;
		
		$Q = $this->query;
		if(is_string($Q)){
			$Q .= $limit;
		}else{
			$Q['sql'] = $limit;
		}
		return $Q;
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
		$tunnel = $this->getTunnel();
		bBlib::extend($tunnel, 'page', array());
		$tunnelPage = $tunnel['page'];
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
		return array('block'=>__class__, 'tunnel'=>$this->blockTunnel, 'name'=>$this->name, 'meta'=>$this->getMeta(), 'content'=>$result->fetchAll(PDO::FETCH_ASSOC));
	}

	public static function _getTable($data = array(), $caller = null){
		if($caller == null)return;
		return $caller->bTable->getTable($data);
	}
	
}
