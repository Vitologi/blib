<?php
defined('_BLIB') or die;

class bTemplate extends bBlib{	
	
	private $block;
	
	protected function inputSelf(){
		$this->version = '1.0.0';
		$this->parents = array('bSystem', 'bDatabase');
	}
	
	protected function input($data, $caller){
		$this->local['stack'] = array();
		$this->block = get_class($caller);
	}
	
	public function output(){
		return array(
			'bTemplate'=>$this,
			'bTemplate__dynamic'=>false
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
				'btemplate' => array('id', 'owner', 'name', 'blib', 'template')
			),
			'where'		=> array(
				'btemplate' => $where
			)
		);
		
		if(!$result = $this->_query($Q)){throw new Exception('Can`t get template from database.');}
			
		while($row = $result->fetch()){
			
			if($row['owner'] && $this->block !== $row['owner'])continue;
			
			if($row['blib']){ 
				$this->local['block'][$row['id']] = new $row['blib'](json_decode($row['template'],true));
			}else{
				$this->local['stack'][$row['id']] = $row['template'];
			}
		}
		
	}
	
	private function templateDiff($old, $new, $deep = false) {
		
		$oldKey = isset($old[0])?(string)$old[0]:null;
		$newKey = isset($new[0])?(string)$new[0]:null;
		$difference = array($newKey);
		
		if($oldKey != $newKey)$old = array();
		
		
		foreach($new as $key => $value) {
			if( is_array($value)  && $key != 0) {
				if(!isset($old[$key]))$old[$key] = null;
				$temp = $this->templateDiff($old[$key], $value, true);
				if(count($temp))$difference[$key] = $temp;
			}
			unset($old[$key]);
		}
		
		$block = isset($this->local['block'][$newKey])?$this->local['block'][$newKey]:null;
		$isDynamic = ($block && isset($block->local['bTemplate__dynamic']))?$block->local['bTemplate__dynamic']:false;
		
		foreach($old as $key => $value) {
			$difference[$key] = array(null);
		}
		
		if($oldKey !== $newKey){
			
			if($block && !array_key_exists($newKey,$this->local['stack'])){
				$this->local['stack'][$newKey] = json_encode($block->output());
			}
			
			return $difference;
		}
		
		if($block){
			if(!$isDynamic)return array();
			if(!array_key_exists($newKey,$this->local['stack'])){
				$this->local['stack'][$newKey] = json_encode($block->output());
			}
			return $difference;
		}
		
		
		
		return (count($difference)!=1 || !$deep)?$difference:array();
	}
	
	private function glueTempStack(Array $list, $deep = false){
		
		if(!$deep){
			$deep = $list[0];
		}
		
		$template = $this->stack[$list[0]];

		$levelTemplate = array();
		
		foreach($list as $key => $value){
			if((int)$key === 0 || (int)$value === 0){
				continue;
			}elseif(is_array($value)){
				$levelTemplate['"{'.$key.'}"'] = $this->glueTempStack($value, $deep.'.'.$key);
			}
		}
		
		return str_replace(array_keys($levelTemplate), array_values($levelTemplate), $template);
	}
	
	private function glueTempDiff(Array $list, $deep = false){
		
		if(!$deep){
			$deep = $list[0];
			$template = '{"block":"bTemplate", "content":['.$this->stack[$list[0]].'] ,"template":'.json_encode($list,JSON_FORCE_OBJECT).' }';
		}else{
			$template = isset($this->stack[$list[0]])?$this->stack[$list[0]]:'';
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
			if((int)$key === 0 || (int)$value === 0){
				continue;
			}elseif(is_array($value)){
				$levelTemplate['"{'.$key.'}"'] = $this->glueTempDiff($value, $deep.'.'.$key);
			}
		}
		
		return str_replace(array_keys($levelTemplate), array_values($levelTemplate), $template);
	}
	
	
	
	public function _install($data = array(), $caller = null){
		if($caller !== null){return bDatabase::_install($data, $caller);};
		$this->_setConfig('bTemplate', $this->_getDefaultConfig('block'), array('group'=>'blib', 'correct'=>false));
		return bDatabase::_install($data, $this);;
	}
	
	private function getTemplate($data){
		if(!is_array($data)){$data = array($data);}
		$this->addTempStack($data);
		return $this->glueTempStack($data);
	}
	
	public static function _getTemplate($data, $caller = null){
		if($caller == null)return array();
		return $caller->local['bTemplate']->getTemplate($data[0]);
	}
	
	public static function _getTemplateDiff($data, $caller = null){
		if($caller === null)return false;
		$self = $caller->local['bTemplate'];
		
		if(!is_array($data[0])){$data[0] = array($data[0]);}
		if(!is_array($data[1])){$data[1] = array($data[1]);}
		$self->addTempStack($data[1]);
		$diff = $self->templateDiff($data[0],$data[1]);
		return $self->glueTempDiff($diff);
	}
	
	public static function _getTemplateByName($data, $caller = null){ //0_0
		if($caller == null)return false;
		
		$Q = array(
			'select'	=> array(
				'btemplate' => array('blib', 'template')
			),
			'where'		=> array(
				'btemplate' => array('owner'=>get_class($caller), 'name'=>$data[0])
			)
		);
		
		if(!$result = $caller->local['bTemplate']->_query($Q)){throw new Exception('Can`t get template from database.');}
			
		$row = $result->fetch();
		
		if($row['blib']){ 
			return new $row['blib'](json_decode($row['template'],true));
		}else{
			return $row['template'];
		}
		
	}
	
}