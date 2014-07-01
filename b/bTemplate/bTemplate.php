<?php
defined('_BLIB') or die;

class bTemplate extends bBlib{	
	
	protected function inputSelf(){
		$this->version = '1.0.0';
		$this->parents = array('bSystem', 'bDatabase', 'bConfig');
	}
	
	protected function input($data, $caller){
		$this->caller = $caller;
		$this->type = 'array';
		$this->stack = array();
	}
	
	public function output(){
		return array(
			'bTemplate'=>$this
		);
	}
	
	/** Get all template nums */
	private function usedTemplate($array, $result = array(), $deep = 0){
		foreach ($array as $value) { 
			if(is_array($value)) {
				$result = $this->usedTemplate($value, $result, $deep+1);
			}else{ 
				$result[$value]=true; 
			} 
		} 
		return ($deep?$result:array_keys($result));
	}
	
	/** Get all template from database */
	private function addTempStack(Array $list){
		
		$where = array();
		$all = $this->usedTemplate($list);
		foreach($all as $id){
			$where[] = array('id', $id, '=', true);
		}
		
		$Q = array(
			'select'	=> array(
				'bTemplate' => array('id', 'name', 'blib', 'template')
			),
			'where'		=> array(
				'bTemplate' => $where
			)
		);
		
		if(!$result = $this->_query($Q)){throw new Exception('Can`t get template from database.');}

		while($row = $result->fetch()){
			
			if($row['blib']){
				$block = new $row['blib'](json_decode($row['template'],true));
				$return = $block->output();
				$row['template'] = is_array($return)?json_encode($return):$return;
			}

			$this->local['stack'][$row['id']] = $row['template'];
		}
		
	}
	
	private function glueTempStack(Array $list, $deep = false){
		
		if(!$deep){
			$deep = $list[0];
			$template = '{"block":"bTemplate", "content":['.$this->stack[$list[0]].'] ,"template":'.json_encode($list).' }';
		}else{
			$template = $this->stack[$list[0]];
		}
		
		
		$template = preg_replace_callback(
			'/"{(\d+)}"/',
			create_function(
				'$matches',
				'return \'{"block":"bTemplate", "elem":"position", "content":[\'.$matches[0].\'] ,"template":"'.$deep.'.\'.$matches[1].\'" }\';'
			),
			$template
		);
		
		$levelTemplate = array();
		
		foreach($list as $key => $value){
			if((int)$key === 0){
				continue;
			}elseif(is_array($value)){
				$levelTemplate['"{'.$key.'}"'] = $this->glueTempStack($value, $deep.'.'.$key);
			}else{
				$levelTemplate['"{'.$key.'}"'] = $this->stack[$value];
			}
		}
		
		return str_replace(array_keys($levelTemplate), array_values($levelTemplate), $template);
	}
	
	public function _install($data = array(), $caller = null){
		if($caller !== null){return $caller->local['bDatabase']->install;}
		$this->_setConfig('bTemplate', $this->_getDefaultConfig('block'), array('group'=>'blib', 'correct'=>false));
		return $this->local['bDatabase']->install;
	}
	
	public function _getTemplate($data, $caller = null){
		if($caller !== null){return $caller->local['bTemplate']->_getTemplate($data[0]);};
		if(!is_array($data)){$data = array($data);}
		$this->addTempStack($data);
		return $this->glueTempStack($data);
	}
}