<?php 

abstract class bBlib{
	
	/** GLOBAL DATA */
	protected static $global = array(
		'_version'	=>"0.0.2",
		'_request'	=>null
	);
	
	/** LOCAL DATA */
	protected $local = array(
		'parents' => array()
	);
	
	/** INTERCEPTION METHODS */
	final public function __construct(Array $data = array(), bBlib $caller = null){
		
		$this->inputSelf();
		
		foreach((array) $this->parents as $value){
			$parent = new $value($data, $this);
			$this->inputSystem((array)$parent->output());
		}

		$this->input($data, $caller);
	}
	
	function __get($property){
		return (substr($property,0,1)==="_")?self::$global[$property]:$this->local[$property];
	}
	
	function __set($property, $value){
		return(substr($property,0,1)==="_")?(self::$global[$property] or self::$global[$property] = $value):($this->local[$property] or $this->local[$property] = $value);
	}
	
	function __isset($property){
		return array_key_exists($property, (substr($property,0,1)==="_")?self::$global:$this->local);
	}
	
	function __call($method, $args){
		
		switch(substr($method, 0, 2)){
			case "__":
				$block = get_class($this);
				$element = sprintf('%s__%s', $block, substr($method, 2));
				
				if(class_exists($element, false)){
					return new $element($args, $this);
				}
				
				throw new Exeption("Called block's element is not defined.(".$element.")");
				
				break;
			
			default:
				
				foreach((array) $this->parents as $value){
					if (!method_exists($value, $method)) continue;
					return call_user_func_array(array($value, $method), array($args, $this));
				}
				
				break;
			
		}		
	} 
	
	private static function autoload(){
		
		function _autoload($class){
			$class = preg_replace('/\W/i', '', $class);
			$path = sprintf('%1$s/%2$s/%2$s.php',$class{0},$class);
			if(!file_exists($path)){throw new Exception('Called class is missing.');}
			require_once($path);
		}
		
		if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
			spl_autoload_register('_autoload');
		}else {
			function __autoload($class) {
				_autoload($class);
			}
		}
	}
	
	/** SETTERS */
	private static function inputGlobals(){
		self::$global['_request'] = (array)json_decode(file_get_contents("php://input"),true)+(array)$_POST +(array)$_GET;
	}
	
	abstract protected function inputSelf();
	
	final protected function inputSystem($data){
		$data = (is_array($data)?$data:array());
		foreach($data as $key => $value){
			if(isset($this->$key))continue;
			$this->$key = $value;
		}
	}
	
	protected function input($data, $caller){}
	
	/** GETTERS */
	public function output(){}

	/** INTERFACES */
	final public static function gate() {
		try{
			define("_BLIB", true);
			self::autoload();
			self::inputGlobals();
			
			if($blib = self::$global['_request']['blib']){
				$block = new $blib(self::$global['_request']);
				$block->output();
			}
		}catch(Exception $e){
			echo sprintf('(%1$s) [%2$s] - %3$s ', $e->getFile(), $e->getLine(), $e->getMessage());
		}
    }
}