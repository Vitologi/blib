<?php
defined('_BLIB') or die;

class bDatabase extends bBlib{	
	
	private $db = array(
		'host'=>'127.0.0.1',
		'user'=>'root',
		'password'=>'',
		'database'=>'wf'
	);
	
	protected function inputSelf(){
		$this->version = '1.0.0';
	}

	protected function input($data, $caller){
		$this->caller = $caller;
		$this->callerName = get_class($this->caller);
		
		if($this->_bDatabase){
			$this->pdo = $this->_bDatabase;
		}else{
			$dsn = sprintf('mysql:host=%1$s;dbname=%2$s', $this->db['host'], $this->db['database']);
			$this->pdo = new PDO($dsn, $this->db['user'], $this->db['password'], array(PDO::ATTR_PERSISTENT => true));
			$this->pdo->query("SET NAMES utf8");
			$this->_bDatabase = $this->pdo;
		}		
	}
	
	
	public function output(){
		$block = $this->callerName;

		return array(
			'install' => array(				
				'create' => array(
					$block => array(
						'fields' => array(
							'id' => array('type'=> 'INT(10) UNSIGNED', 'null'=>'NOT NULL', 'extra'=> 'AUTO_INCREMENT')
						),
						'primary' => array('id')
					)
				)
			),
			'uninstall'	=> array('drop' => array($block)),
			'update'	=> array('1.0.0' => null)		
		);
	}

	
	private function parseFields($data){
		$temp = '';
		
		foreach($data as $key => $value){
			
			$type = is_string($value['type'])?$value['type']:'INT(10)';
			$null = is_string($value['null'])?$value['null']:'NULL';
			$extra = is_string($value['extra'])?$value['extra']:'';
			$default = is_string($value['default'])?' DEFAULT "'.$value['default'].'" ':'';
			$comment = is_string($value['comment'])?$value['comment']:'';
			
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
			
			$namingRule = explode('_', $key);
			$table = is_string($value['table'])?$value['table']:$namingRule[0];
			$column = is_string($value['column'])?$value['column']:$namingRule[1];
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
	
	
	/*
	

		'select'=>array(
			'bExample'=>array('bExample_id', 'description'),
			'bTest'=>array('bTest_id', 'name', 'description')
		),
		'update'=>array(
			'bExample'=>array('bExample_id'=>'6', 'description'=>'some description'),
			'bTest'=>array('name'=>"Paris")
		),
		'where'=>array(
			'bExample'=>array('bExample_id'=>'=3'),
			'bTest'=>array('name'=>'LIKE "Moscow"')
		)
	
	*/
	
	
	
	public function query($Q){
		$temp = '';
		
		//for native sql queries
		if(is_string($Q)){
			return $this->pdo->query($Q);
		}
		
		//for serialise queries
		if(is_array($Q)){
			
			if(array_key_exists('drop', $Q)){
				
				foreach($Q['drop'] as $key => $value){
					$temp .= sprintf(
						' DROP TABLE IF EXISTS `%s`; ',
						$value	//1
					);
				}
			}
			
			if(array_key_exists('create', $Q)){
				$create = $Q['create'];
				
				foreach($create as $key => $value){
					
					$tableName = $key;
					$foreing = is_array($value['foreign'])?$this->parseForeign($value['foreign'], $value['fields']):'';
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

			return;
			//return $this->pdo->query($Q);			
		}
		
		throw new Exeption('Trying execute wrong sql query.');

	}
	
	//methods for child blocks
	public function getDatabaseMinion($name, $caller){
		$localInstall = $caller->getBlockPath().'/__bDatabase/'.$name[0].'.php';

		if(file_exists($localInstall)){
			return require($localInstall);
		}elseif($caller->$name[0]){
			return $caller->$name[0];
		}else{
			return null;
		}
	}
	
	public function install($data, $caller){
		return $caller->getDatabaseMinion('install');
	}
	
	public function uninstall($data, $caller){
		return $caller->getDatabaseMinion('uninstall');
	}
	
	public function update($data, $caller){
		return $caller->getDatabaseMinion('update');
	}

}