<?php
defined('_BLIB') or die;

class bExample extends bBlib{	
	
	protected function inputSelf(){
		$this->version = '1.112.1';
		$this->parents = array('bSystemAlias', 'bConfig', 'bDatabase');
		
		
		//install testing
		$insert = array(
			'bExampleTest1'=>array(array('id', 'description')),
			'bExampleTest2'=>array(array('id', 'description')),
			'bExample'=>array(array('id', 'description', 'bExampleTest1_id', 'bExampleTest2_id'))
		);
		for($i=1; $i<=10; $i++){
			$insert['bExampleTest1'][]=array('NULL', 'some description');
			$insert['bExampleTest2'][]=array('NULL', 'some description');
			$insert['bExample'][]=array('NULL', 'some description', $i, $i);
		}
		
		$this->install = array(
			'drop' => array(
				'bExample',
				'bExampleTest1',
				'bExampleTest2',
				'bExampleTest3',
				'bExampleTest4'
			),
			'create'	=> array(
				'bExampleTest1'	=> array(
					'fields'	=> array(
						'id'			=> array('type'=> 'INT(10) UNSIGNED', 'null'=>'NOT NULL', 'extra'=> 'AUTO_INCREMENT', 'comment'=>'index column'),
						'description'	=> array('type'=> 'VARCHAR(45)', 'null'=>'NULL')
					),
					'primary'	=> array('id')
				),
				'bExampleTest2'	=> array(
					'fields'	=> array(
						'id'			=> array('type'=> 'INT(10) UNSIGNED', 'null'=>'NOT NULL', 'extra'=> 'AUTO_INCREMENT', 'comment'=>'index column'),
						'description'	=> array('type'=> 'VARCHAR(45)', 'null'=>'NULL')
					),
					'primary'	=> array('id')
				),
				'bExampleTest3'	=> array(
					'fields'	=> array(
						'id'			=> array('type'=> 'INT(10) UNSIGNED', 'null'=>'NOT NULL', 'extra'=> 'AUTO_INCREMENT', 'comment'=>'index column'),
						'description'	=> array('type'=> 'VARCHAR(45)', 'null'=>'NULL')
					),
					'primary'	=> array('id')
				),
				'bExampleTest4'	=> array(
					'fields'	=> array(
						'id'			=> array('type'=> 'INT(10) UNSIGNED', 'null'=>'NOT NULL', 'extra'=> 'AUTO_INCREMENT', 'comment'=>'index column'),
						'description'	=> array('type'=> 'VARCHAR(45)', 'null'=>'NULL')
					),
					'primary'	=> array('id')
				),
				'bExample'	=> array(
					'fields'	=> array(
						'id'			=> array('type'=> 'INT(10) UNSIGNED', 'null'=>'NOT NULL', 'extra'=> 'AUTO_INCREMENT', 'comment'=>'index column'),
						'description'	=> array('type'=> 'VARCHAR(45)', 'null'=>'NULL'),
						'bExampleTest1_id'	=> array('type'=> 'INT(10) UNSIGNED', 'null'=>'NOT NULL'),
						'bExampleTest2_id'		=> array('type'=> 'INT(10) UNSIGNED', 'null'=>'NOT NULL')
					),
					'primary'	=> array('id'),
					'foreign'	=> array(
						'bExampleTest1_id'	=> null,
						'bExampleTest2_id'		=> array('table'=>'bExampleTest2',  'column' =>'id', 'ondelete'=>'cascade', 'onupdate'=>'cascade'),
					),
					'charset'	=> 'utf8',
					'collate'	=> 'utf8_general_ci'
				)
			),
			'insert'=>$insert,
			'update'=>array(
				'bExample'=>array('description'=>'CHANGE description'),
				'bExampleTest1'=>array()
			),
			'select'=>array(
				'bExample'=>array('id', 'description'),
				'bExampleTest1'=>array('description')
			),
			'where'=>array(
				'bExampleTest1'=>array(array('id',0,'>'),array('id',5,'<='))
			)				
		);
		
	}
	
	protected function input($data, $caller){
		$this->caller = $caller;
	}
	

	
	public function output(){
		
		if($this->caller){
			$veryImportantData = array(
				'local' => $this->version,
				'global' => $this->_version
			);
			
			$forMyChild = 'do this';

			return array(
				'do'	=>	$forMyChild,
				'data'	=>	$veryImportantData
			);

		}else{
						
			var_dump($this);
			
			//var_dump($this->query($q));
			
			/** dumps */
			//var_dump($this->install());
			//var_dump($this->uninstall());
			//var_dump($this->update());
			//var_dump($this);
			
			//header('Content-Type: text/html; charset=utf-8');
			//echo '<div>Hallo win.</div>';
			//exit;
		}
	}
	
	
	/** overload for parent block bDatabase */
	/*
	public function install() {
		return false;
    }
	
	public function uninstall() {
		return false;
    }
	*/
}

class bExample__install{
	
	public function __construct($data, $caller){
		
	}

}