<?php
defined('_BLIB') or die;

class bConfig extends bBlib{	
	
	
	protected function inputSelf(){
		$this->version = '1.0.0';
		$this->parents = array('bSystem', 'bDatabase');
		
	}
	
	protected function input($data, $caller){
		$this->caller = get_class($caller);
	}
	
	
	public function output(){
		if($this->caller){
			$config = $this->getConfig($this->caller, array('group'=>'blib'));
			$config['bConfig']=$this;
			return $config;
		}
	}
	
	/** 
	* Private method for get configuration
	*
	* @param {string} $name - name of configuration
	* @param {mixed}[] $param - other parameters
	*   {string} group - change config group (default 'blib')
	*   {bollean} deep - get config concat with parents value (default true)
	* @return {array} - associative array with configuration
	*/
	private function getConfig($name, $param){
		$param = (array) $param + array('group'=>'blib', 'deep'=>true);
		$used = array();
		do{
			$Q = array(
				'select' => array(
					'bconfig'=>array('id', 'value', 'bconfig_id')
				),
				'where' => array(
					'bconfig'=>array('group'=>$param['group'], 'name'=>$name)
				)
			);
			
			if($default){
				$Q['where']['bconfig']=array('id'=>$default);
				$default = null;
			}
			
			if($result = $this->_query($Q)){
				$row = $result->fetch();
				$config = (array)$config + (array)json_decode($row['value'],true);
				if($param['deep'] && !in_array($row['bconfig_id'], $used)){
					$used[] = $default = $row['bconfig_id'];
				}
			}
	
		}while($default);
		return $config;
	}
	
	/** 
	* Private method for set configuration
	*
	* @param {string} $name - name of configuration
	* @param {array} $value - configuration array
	* @param {mixed}[] $param - other parameters
	*   {string} group - change config group (default 'blib')
	*   {bollean} correct - set on old configuration values (default true)
	*   {number} parent - change parent config
	* @return {number} - id updated or new item
	*/
	private function setConfig($name, Array $value, $param){
		$param = (array) $param + array('group'=>'blib', 'correct'=>false);

		$value = is_array($value)?$value:array();
		
		$Q = array(
			'select' => array('bconfig'=>array('id', 'value', 'bconfig_id')),
			'where' => array('bconfig'=>array('group'=>$param['group'], 'name'=>$name))
		);
		
		$result = $this->_query($Q);
				
		if($result->rowCount()){
			$row = $result->fetch();
			
			if($param['correct']){
				$value = $value + (array) json_decode($row['value'], true);
			}
			
			$value = json_encode($value);
				
			$Q = array(
				'update' => array('bconfig'=>array('value'=>$value)),
				'where' => array('bconfig'=>array('id'=>$row['id']))
			);
			
			if(isset($param['parent'])){$Q['update']['bconfig']['bconfig_id'] = $param['parent'];}
			if(!$this->_query($Q)){	throw new Exception('Can`t rewrite config');}
			return $row['id'];
			
			
		}else{
			
			$value = json_encode($value);
				
			$Q = array(
				'insert' => array(
					'bconfig'=>array(
						'group'=>$param['group'],
						'name'=>$name,
						'value'=>$value
					)
				)
			);
			
			if(isset($param['parent'])){$Q['insert']['bconfig']['bconfig_id'] = $param['parent'];}
			if(!$this->_query($Q)){throw new Exception('Can`t rewrite config');}
			return $this->_lastInsertId();
		}
		
		
	}
	
	/** 
	* Method for get default config value
	* @param {array} $data - arguments
	*   {string} 0 - name of config item/block/other
	*   {bool} 1 - reduced or not
	* @return {array} - configuration
	*/
	public function _getDefaultConfig($data, $caller = null){
		if($caller === null){return;}
		$name = $data[0]?$data[0]:'block';
		$reduced = $data[1]?$data[1]:true;
		$path = bBlib::path(get_class($caller).'__'.__class__.'_'.$name,'php');
		if(!file_exists($path)){return array();}
		
		$config = (array) require($path);
		
		if($reduced){
			$temp = array();
			foreach($config as  $item){
				if(!(isset($item['name'])&&isset($item['name'])))continue;
				$temp[$item['name']]=$item['value'];
			}
			$config = $temp;
			
		}
		
		return $config;

	}
	
	/** 
	* Method for extend other class
	* @param {array} $data - arguments
	*   {string} 0 - config name
	*   {array} 1 - additional parameters (group, deep)
	* @param {bBlib} $caller - method initiator
	* @return {array} - configuration
	*/
	public function _getConfig($data, $caller = null){
		if($caller !== null){
			if(!$data[1]['group']){$data[1]['group'] = get_class($caller);}
			return $caller->local['bConfig']->_getConfig($data);
		}
		return $this->getConfig($data[0], $data[1]);
	}
	
	/** 
	* Method for extend other class
	* @param {array} $data - arguments
	*   {string} 0 - config name
	*   {array} 1 - config value
	*   {array} 2 - additional parameters (group, correct, parent)
	* @param {bBlib} $caller - method initiator
	* @return {string} - id changed/new configuration
	*/
	public function _setConfig($data, $caller = null){
		$data[1] = (array)$data[1];
		if($caller !== null){
			if(!$data[2]['group']){$data[2]['group'] = get_class($caller);}
			return $caller->local['bConfig']->_setConfig($data);
		}
		return $this->setConfig($data[0], $data[1], $data[2]);

	}
	
	
}