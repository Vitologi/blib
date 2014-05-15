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
			do{
				$Q = array(
					'select' => array(
						'bConfig'=>array('value', 'bConfig_id')
					),
					'where' => array(
						'bConfig'=>array('group'=>'blib', 'name'=>$this->caller)
					)
				);
				
				if($default){
					$Q['where']['bConfig']=array('id'=>$default);
					$default = null;
				}
				
				if($result = $this->query($Q)){
					$row = $result->fetch();
					$config = (array)$config + (array)json_decode($row['value'],true);
					$default = $row['bConfig_id'];
				}
		
			}while($default);

			return $config;
		}

	}
	
}