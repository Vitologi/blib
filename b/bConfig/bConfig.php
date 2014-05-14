<?php
defined('_BLIB') or die;

class bConfig extends bBlib{	
	
	protected function inputSelf(){
		$this->version = '1.0.0';
		$this->parents = array('bSystemAlias', 'bDatabase');
		
	}
	
	protected function input($data, $caller){
		$this->caller = get_class($caller);
	}
	
	
	public function output(){
		do{
			$q = array(
				'select' => array(
					'bConfig'=>array('config', 'bConfig_id')
				),
				'where' => array(
					'bConfig'=>array('name'=>'blib', 'value'=>$this->caller)
				)
			);
			
			if($default){$q['where']['bConfig']=array('id'=>$default);}
			$result = $this->query($q)->fetch();
			
			$config = (array)$config + json_decode($result['config'],true);
			$default = $result['bConfig_id'];
	
		}while($default);
		
		return $config;

	}
	
}