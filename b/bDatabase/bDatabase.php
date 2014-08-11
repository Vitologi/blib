<?php
defined('_BLIB') or die;

class bDatabase extends bBlib{	
	
	private $pdo;
	
	protected function inputSelf(){
		$this->version = '1.0.0';
		$db = array(
			'host'=>'localhost',
			'user'=>'root',
			'password'=>'',
			'database'=>'test'
		);

		if(bBlib::$global['_bDatabase__pdo']){
			$this->pdo = bBlib::$global['_bDatabase__pdo'];
		}else{
			$dsn = sprintf('mysql:host=%1$s;dbname=%2$s', $db['host'], $db['database']);
			$this->pdo = new PDO($dsn, $db['user'], $db['password'], array(PDO::ATTR_PERSISTENT => true));
			$this->pdo->query("SET NAMES utf8");
			bBlib::$global['_bDatabase__pdo'] = $this->pdo;
		}

	}

	protected function input($data, bBlib $caller){
		
		$block = get_class($caller);
		$path = bBlib::path($block.'__'.__class__.'_install','php');
		$instal = (file_exists($path))?require_once($path):null;

		if($instal !== null){
			$this->local['install'] = $instal;
			$this->local['uninstall'] = require_once(bBlib::path($block.'__'.__class__.'_uninstall','php'));
			$this->local['update'] = require_once(bBlib::path($block.'__'.__class__.'_update','php'));
		}else{
			$this->local['install'] = array(				
				'create' => array(
					$block => array(
						'fields' => array(
							'id' => array('type'=> 'INT(10) UNSIGNED', 'null'=>'NOT NULL', 'extra'=> 'AUTO_INCREMENT')
						),
						'primary' => array('id')
					)
				)
			);
			$this->local['uninstall'] = array('drop' => array($block));
			$this->local['update'] = array('1.0.0' => null);
		}
		
	}
	
	
	public function output(){
		return array('bDatabase' => $this);
	}

	
	private function parseFields($data){
		$temp = '';
		
		foreach($data as $key => $value){
			
			$type = is_string($value['type'])?$value['type']:'INT(10)';
			$null = is_string($value['null'])?$value['null']:'NULL';
			$extra = is_string($value['extra'])?$value['extra']:'';
			$comment = is_string($value['comment'])?$value['comment']:'';
			
			
			if(is_string($value['default'])){
				$upper = mb_strtoupper($value['default']);
				
				if($upper === 'NULL' or $upper === 'CURRENT_TIMESTAMP' or $upper === 'FALSE'){
					$default = sprintf(' DEFAULT %1$s', $upper);
				}else{
					$default = sprintf(' DEFAULT %1$s', $this->pdo->quote($value['default']));
				}
			}else{
				$default ='';
			}
			
			$temp .= sprintf(
				' `%1$s` %2$s %3$s %4$s %5$s COMMENT "%6$s",',
				$key,		//1
				$type,		//2
				$null,		//3
				$extra,		//4
				$default,	//5
				$comment	//6
			);
			
		}
		return substr($temp, 0, -1);
	}
	
	private function parsePrimary(Array $data){
		return sprintf(' , PRIMARY KEY (`%1$s`) ', implode('`,`', $data));
	}
	
	private function parseForeign($foreign, &$fields){
		$temp = '';
		
		foreach($foreign as $key =>$value){
		
			if(!$fields[$key]){throw new Exception("Foreign key does not found in the table");}
			
			preg_match('/^(\w+)_(\w+)$/', $key, $namingRule);
			$table = is_string($value['table'])?$value['table']:$namingRule[1];
			$column = is_string($value['column'])?$value['column']:$namingRule[2];
			$ondelete = is_string($value['ondelete'])?$value['ondelete']:'RESTRICT';
			$onupdate = is_string($value['onupdate'])?$value['onupdate']:'CASCADE';
			
			$temp .= sprintf(
				', FOREIGN KEY (`%1$s`)	REFERENCES `%2$s` (`%3$s`) ON DELETE %4$s ON UPDATE %5$s',
				$key,		//1
				$table,		//2
				$column,	//3
				$ondelete,	//4
				$onupdate	//5
			);
			
			
		}
		return $temp;
	}
	
	private function parseRelation($query){
	
		$structure = $this->local['install']['create'];
		$temp = '';
		if(!is_array($structure) || !($tables = array_intersect_key($structure, $query))){ return $temp; }
		
		foreach($tables as $selfTable => $table){
			$foreings = $table['foreign'];
			if(!is_array($foreings)){continue;}
			
			foreach($foreings as $selfColumn => $column){
				if($column === null){
					preg_match('/^(\w+)_(\w+)$/', $selfColumn, $namingRule);
					$foreignTable = $namingRule[1];
					$foreignColumn = $namingRule[2];
				}
				if($column['table']){$foreignTable = $column['table'];}
				if($column['column']){$foreignColumn = $column['column'];}
				
				if(array_key_exists($foreignTable, $query) && $selfTable!=$foreignTable){
					$temp .= sprintf(' `%1$s`.`%2$s` = `%3$s`.`%4$s` AND', $selfTable, $selfColumn, $foreignTable, $foreignColumn);
				}
			}
		}
		return ($temp!='')?substr($temp, 0, -3):$temp;		
	}
	

	public function _query($Q, $caller = null){
		
		
		//protect call from block
		if($caller !== null){
			return $caller->bDatabase->_query($Q);
		}
		$debug = $Q[1];
		$Q = $Q[0];
		
		//for native sql queries
		if(is_string($Q)){
			return $this->pdo->query($Q);
		}
		
		//if isn't serialise queries
		if(!is_array($Q)){ throw new Exception('Trying execute wrong sql query.');}
		
		$temp = '';
		
		/** DROP TABLE */
		if(array_key_exists('drop', $Q) && count($Q['drop'])){
			
			$tables = '';
			
			foreach($Q['drop'] as $table){
				$tables .= sprintf(' `%s`,', $table);
			}
			
			$temp .= sprintf(' DROP TABLE IF EXISTS %s; ', substr($tables, 0, -1));
		}
		
		/** CREATE TABLE */
		if(array_key_exists('create', $Q) && count($Q['create'])){
			
			foreach($Q['create'] as $key => $value){
				
				$tableName = $key;
				$foreing = (is_array($value['foreign']) && (strtolower($value['engine'])=='innodb'))?$this->parseForeign($value['foreign'], $value['fields']):'';
				$fields = $this->parseFields($value['fields']);
				$primary = is_array($value['primary'])?$this->parsePrimary($value['primary']):'';
				$engine = sprintf(' ENGINE = %1$s ', is_string($value['engine'])?$value['engine']:'MyISAM');
				$charset = sprintf(' DEFAULT CHARACTER SET = %1$s ', is_string($value['charset'])?$value['charset']:'utf8');
				$collate = sprintf(' COLLATE = %1$s', is_string($value['collate'])?$value['collate']:'utf8_general_ci');
				
				$temp .= sprintf(
					'CREATE TABLE IF NOT EXISTS `%1$s` (%2$s %3$s %4$s) %5$s %6$s %7$s ;',
					$tableName,	//1
					$fields,	//2
					$primary,	//3
					$foreing,	//4
					$engine,	//5
					$charset,	//6
					$collate	//7
				);
			}

		}
		
		/** INSERT */
		
		if(array_key_exists('insert', $Q) && count($Q['insert'])){
			$query = $Q['insert'];

			foreach($query as $table => $columns){
				
				$into = '';
				$values = '';
				
				if(is_array($columns[0])){
					$into = $columns[0];
					$intoLen = count($into);
					$len = count($columns);
					
					for($i=1; $i<$len; $i++){
						$full = array_pad($columns[$i], $intoLen, null);
						
						for($j=0; $j<$intoLen; $j++){
							$full[$j] = ($full[$j] !== null)?$this->pdo->quote($full[$j]):'null';
						}
						
						$values .= sprintf(' (%1$s) ,', implode(',',$full));
					}
					
					$into = sprintf('(`%1$s`)', implode('`, `',$into));
					$values = substr($values, 0, -1);
				}else{

					foreach($columns as $columnName => $columnValue){
						$into .= sprintf(' `%1$s` ,', $columnName);
						$values .= sprintf(' %1$s ,', $this->pdo->quote($columnValue));
					}
					
					$into = sprintf('(%1$s)', substr($into, 0, -1));
					$values = sprintf('(%1$s)', substr($values, 0, -1));
				}
				
				$temp .= sprintf(
					'INSERT INTO `%1$s` %2$s VALUES %3$s; ',
					$table,	//1
					$into,	//2
					$values	//3
				);
			}
		}
		
		
		
		/** WHERE STATEMENT */
		if(array_key_exists('where', $Q) && count($Q['where'])){
			$where = '';
			foreach($Q['where'] as $table => $columns){
				if(is_array($columns[0])){
					foreach($columns as $value){
						$value[2] = ($value[2])?$value[2]:'=';
						$value[3] = ($value[3])?' OR ':' AND';
						$where .= sprintf(' `%1$s`.`%2$s` %3$s %4$s %5$s', $table, $value[0], $value[2], $this->pdo->quote($value[1]), $value[3]);
					}
				}else{			
					foreach($columns as $column => $value){
						$where .= sprintf(' `%1$s`.`%2$s` = %3$s AND', $table, $column, $this->pdo->quote($value));
					}
				}
			}
			$where = ' ( '.substr($where, 0, -3).' ) ';
		}
		
		/** DELETE */
		if(array_key_exists('delete', $Q) && count($Q['delete'])){
			
			$query = $Q['delete'];
			$delete = ' DELETE FROM ';
			foreach($query as $table => $columns){
				$delete .= sprintf(' `%1$s`,', $table);
			}
			
			$relation = $this->parseRelation($query);
			
			if($where && $relation){
				$concatWhere = sprintf(' WHERE %1$s AND %2$s ', $where, $relation);
			}elseif($where){
				$concatWhere = sprintf(' WHERE %1$s ', $where);
			}elseif($relation){
				$concatWhere = sprintf(' WHERE %1$s ', $relation);
			}else{
				$concatWhere = '';
			}
			
			$delete = substr($delete, 0, -1);
			$temp .= $delete.$concatWhere.'; ';
		}
		
		/** UPDATE */
		if(array_key_exists('update', $Q) && count($Q['update'])){
			$query = $Q['update'];
			$update = ' UPDATE ';
			$set = ' SET ';
			
			foreach($query as $table => $columns){
				
				foreach($columns as $columnName => $columnValue){
					$set .= sprintf(' `%1$s`.`%2$s` = %3$s,', $table, $columnName, $this->pdo->quote($columnValue));
				}
				
				$update .= sprintf(' `%1$s`,', $table);
			}
			
			$relation = $this->parseRelation($query);
			
			if($where && $relation){
				$concatWhere = sprintf(' WHERE %1$s AND %2$s ', $where, $relation);
			}elseif($where){
				$concatWhere = sprintf(' WHERE %1$s ', $where);
			}elseif($relation){
				$concatWhere = sprintf(' WHERE %1$s ', $relation);
			}else{
				$concatWhere = '';
			}

			$update = substr($update, 0, -1);
			$set = substr($set, 0, -1);
			$temp .= $update.$set.$concatWhere.'; ';
	
		}
		
		/** SELECT */
		if(array_key_exists('select', $Q) && count($Q['select'])){
			
			if($temp!=''){
				if(!$this->pdo->query($temp)){throw new Exception('Wrong sql syntax.'.$temp);}
				$temp='';
			}
			
			$query = $Q['select'];
			$select = ' SELECT ';
			$from = ' FROM ';
			foreach($query as $table => $columns){
				foreach($columns as $alias => $column){
					$select .= (is_numeric($alias)?sprintf(' `%1$s`.`%2$s`,', $table, $column):sprintf(' `%1$s`.`%2$s` AS `%3$s`,', $table, $column, $alias));
				}
				$from .= sprintf(' `%1$s`,', $table);
			}
			
			$relation = $this->parseRelation($query);
			
			if($where && $relation){
				$concatWhere = sprintf(' WHERE %1$s AND %2$s ', $where, $relation);
			}elseif($where){
				$concatWhere = sprintf(' WHERE %1$s ', $where);
			}elseif($relation){
				$concatWhere = sprintf(' WHERE %1$s ', $relation);
			}else{
				$concatWhere = '';
			}
			
			$select = substr($select, 0, -1);
			$from = substr($from, 0, -1);
			$sql = (array_key_exists('sql', $Q)?$Q['sql']:'');  // random sql statement
			
			$temp .= $select.$from.$concatWhere.$sql.'; ';
		}
		
		
		
		return ($debug)?$temp:$this->pdo->query($temp);			

	}
	
	//methods for child blocks
	public function _install($data, $caller = null){
		return ($caller === null)?$this->install:$caller->bDatabase->_install($data);
	}
	
	public function _uninstall($data, $caller = null){
		return ($caller === null)?$this->uninstall:$caller->bDatabase->_uninstall($data);
	}
	
	public function _update($data, $caller = null){
		
		if($caller != null){
			return $caller->bDatabase->_update($data);
		}
		
		$temp = $this->update;
		uksort($temp, 'version_compare');
		return $temp;
	}
		
}