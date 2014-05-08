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
			//'install' => sprintf('CREATE TABLE IF NOT EXISTS `%1$s` (`%1$s__id` INT)', $this->callerName),
			'uninstall'	=> sprintf('DROP TABLE IF EXISTS `%s`', $this->callerName),
			'install'	=> array(
				'create'	=> array(
					$block	=> array(
						'fields'	=> array(
							$block.'_id'	=> array('type'=> 'INT(10) UNSIGNED', 'null'=>'NOT NULL', 'extra'=> 'AUTO_INCREMENT', 'default'=>'null'),
							'description'	=> array('type'=> 'VARCHAR(45)', 'null'=>'NULL')
						),
						'primary'	=> array($block.'_id'),
						'foreign'	=> array(
							'bConfig_id'	=> array('references'=>'bConfig'),
							'bTest_id'		=> array('references'=>'bTest', 'ondelete'=>'cascade', 'onupdate'=>'cascade'),
						),
						'charset'	=> 'utf8',
						'engine'	=> 'InnoDB',
						'collate'	=> 'utf8_general_ci'
					)
				),
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
			)
		);
	}

	/*
		CREATE TABLE IF NOT EXISTS `wf`.`foto` (
		  `fot_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'таблица связующая информацию по фирме',
		  `fot_name` VARCHAR(45) NOT NULL,
		  `fot_parent` INT(10) UNSIGNED NULL DEFAULT NULL,
		  `fot_os` INT(10) UNSIGNED NULL DEFAULT NULL,
		  PRIMARY KEY (`fot_id`),
		  INDEX `fk_fot_parent_idx` (`fot_parent` ASC),
		  INDEX `fk_fot_os_idx` (`fot_os` ASC),
		  CONSTRAINT `fk_fot_parent`
			FOREIGN KEY (`fot_parent`)	REFERENCES `wf`.`foto` (`fot_id`) ON DELETE CASCADE	ON UPDATE CASCADE,
		  CONSTRAINT `fk_fot_os`
			FOREIGN KEY (`fot_os`)	REFERENCES `wf`.`organisation_settings` (`os_id`) ON DELETE SET NULL ON UPDATE CASCADE)
		ENGINE = InnoDB
		DEFAULT CHARACTER SET = utf8
		COLLATE = utf8_general_ci;		
		
		
		
		
		'fields'	=> array(
			$block.'_id'	=> array('INT(10)', 'UNSIGNED', 'NOT NULL', 'AUTO_INCREMENT'),
			'description'	=> array('VARCHAR(45)', 'NULL')
		),
		'primary'	=> array($block.'_id'),
		'foreign'	=> array(
			'bConfig_id'	=> 'bConfig',
			'bTest_id'		=> array('references'=>'bTest', 'ondelete'=>'cascade', 'onupdate'=>'cascade')),
		)
		FOREIGN KEY (`fot_parent`)	REFERENCES `wf`.`foto` (`fot_id`) ON DELETE CASCADE	ON UPDATE CASCADE,
		
	*/
		
	/** ------------- */
	
	private function parseFields($data){
		$temp = '';
		
		foreach($data as $key => $value){
			$temp .= sprintf(' `%1$s` %2$s,', $key, implode(' ', $value));
		}
		return substr($temp, 0, -1);
	}
	
	private function parsePrimary(Array $data){
		return sprintf(' , PRIMARY KEY (`%1$s`) ', implode('`,`', $data));
	}
	
	private function getForeignFromParent(String $name, String $parent){
		
		if(isset($this->$parent)){
			
		}
		
		$block = new $parent();
		//$block->bDatabase
	}
	
	private function parseForeign($foreing, &$fields){
		
		foreach($foreing as $key =>$value){
			if(is_srting($value)){}
		}
		return substr($temp, 0, -1);
	}
	
	public function query($Q){
		$temp = '';
		
		//for native sql queries
		if(is_string($Q)){
			return $this->pdo->query($Q);
		}
		
		//for serialise queries
		if(is_array($Q)){
			
			if(array_key_exists('create', $Q)){
				$create = $Q['create'];

				foreach($create as $key => $value){
					
					$tableName = $key;
					$foreing = is_array($value['foreing'])?$this->parseForeing($value['foreing'], $value['fields']):'';
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
			
			var_dump($temp);
			return;
			//return $this->pdo->query($Q);			
		}
		
		throw new Exeption('Try execute wrong sql query.');

	}
	
	
	//methods for child blocks
	public function install($data, $caller){
		$caller->bDatabase->query($caller->install);
	}
	
	public function uninstall($data, $caller){
		$caller->bDatabase->query($caller->uninstall);
	}

}

class bDatabase__object{
	
	public function __construct($data, $bDatabase){
		//var_dump($data, $bDatabase);
		//var_dump('select * from '.$data['table'], $bDatabase->query('select * from '.$data['table']));
	}

}