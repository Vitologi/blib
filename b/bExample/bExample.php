<?php
defined('_BLIB') or die;

class bExample extends bBlib{	
	
	protected function inputSelf(){
		$this->version = '1.112.1';
		$this->parents = array('bSystemAlias', 'bConfig', 'bDatabase');
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
			
			/* testing */
			
			echo '<br />DROP<br />';
			var_dump($this->query($this->uninstall()));
			
			
			echo '<br />CREATE<br />';
			var_dump($this->query($this->install()));
			
			echo '<br />INSERT';
			echo '<br />[10]';
			$q = array(
				'insert'=>array(
					'bExampleTest1'=>array('id'=>'null', 'description'=>'some description'),
					'bExampleTest2'=>array('id'=>'null', 'description'=>'some description')
				)
			);
			for($i=0; $i<9; $i++){
				$this->query($q);
			}
			var_dump($this->query($q));
			echo '<br />[10]';
			for($i=1; $i<=10; $i++){
				$q = array(
					'insert'=>array(
						'bExample'=>array('id'=>'null', 'description'=>'some description', 'bExampleTest1_id'=>$i, 'bExampleTest2_id'=>$i)
					)
				);
				if($i==10)continue;
				$this->query($q);
			}
			var_dump($this->query($q));
			
			echo '<br />UPDATE';
			echo '<br />[5]';
			for($i=6; $i<=10; $i++){
				$q = array(
					'update'=>array(
						'bExample'=>array('description'=>'CHANGE description'),
						'bExampleTest1'=>array()
					),
					'where'=>array(
						'bExampleTest1'=>array('id'=>'='.$i)
					)
				);
				if($i==10)continue;
				$this->query($q);
			}
			var_dump($this->query($q));
			
			echo '<br />SELECT';
			echo '<br />[5]';
			for($i=6; $i<=10; $i++){
				$q = array(
					'select'=>array(
						'bExample'=>array('id', 'description'),
						'bExampleTest1'=>array('description')
					),
					'where'=>array(
						'bExampleTest1'=>array('id'=>'='.$i)
					)
				);
				echo '<br />['.($i-5).']';
				var_dump($this->query($q));
			}
			
			
			
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