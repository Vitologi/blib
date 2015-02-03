<?php
defined('_BLIB') or die;

class bConfig__database extends bBlib{

	private   $_config  = array();
	protected $_traits = array('bSystem', 'bDatabase');

	public function output(){
		return $this;
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
	public function getConfig($name){
		if($name === 'bDatabase')return array();

		return array();
		/*
		$param = (array) $param + array('group'=>'blib', 'deep'=>true);
		$used = array();
		$config = array();
		$default = null;

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

		*/
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
	public function setConfig($name, Array $value, $param){

		return array();
		/*
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

		*/

	}

}
