<?php 

abstract class bBlib{
	
	/** GLOBAL DATA */
	protected static $global = array(
		'_version'	=>"0.0.2",
		'_request'	=>null
	);
	
	/** LOCAL DATA */
	protected $local = array(
		
	);
	
	/** INTERCEPTION METHODS */
	final public function __construct(Array $data = array(), bBlib $caller = null){
		
		$this->inputSelf();
		
		$parents = is_array($this->local['parents'])?$this->local['parents']:array();
		foreach($parents as $value){
			$parent = new $value($data, $this);
			$this->inputSystem((array)$parent->output());
		}

		$this->input($data, $caller);
	}
	
	//increases the access time (local by 11 times)(global by 5 times) (1kk iteration test) need use $this->local['name'] or bBlib::$global['name'] for ignore it
	function __get($property){
		return ($property{0}==="_")?self::$global[$property]:$this->local[$property];
	}
	
	function __set($property, $value){
		return($property{0}==="_")?(isset(self::$global[$property]) or self::$global[$property] = $value):(isset($this->local[$property]) or $this->local[$property] = $value);
	}
	
	function __isset($property){
		return ($property{0}==="_")?isset(self::$global[$property]):isset($this->local[$property]);
	}
	
	//increases the access time by 6 times(1kk iteration test) need overload methods in child class for ignore it
	function __call($method, $args){
		foreach($this->local['parents'] as $value){
			if (!method_exists($value, $method)) continue;
			return call_user_func_array(array($value, $method), array($args, $this));
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
		foreach($data as $key => $value){
			if(isset($this->local[$key]))continue;
			$this->local[$key] = $value;
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