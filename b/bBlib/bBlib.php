<?php 

abstract class bBlib{
	
	/** GLOBAL DATA */
	protected static $global = array(
		'_version'	=>"0.0.2"
	);
	
	/** LOCAL DATA */
	protected $local = array(
		'parents' => array()
	);
	
	/** INTERCEPTION METHODS */
	final public function __construct(Array $data = array()){
		
		$this->inputSelf();
		
		foreach((array) $this->parents as $value){
			$this->inputParents(new $value(array('caller'=>$this, 'data' => $data)));
		}

		$this->inputUser($data);
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
	
	private static function autoload(){
		
		function _autoload($class){
			$class = preg_replace('/\W/i', '', $class);
			require_once(sprintf('%1$s/%2$s/%2$s.php',$class{0},$class));
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
	
	final protected function inputParents(bBlib $block){
		
		$data = (array) $block->outputParents();
		
		foreach($data as $key => $value){
			$this->$key = $value;
		}
	}
	
	protected function inputUser( Array $data){
		
	}
	
	/** GETTERS */
	
	public function outputParents(){
		
	}
	
	public function outputUser(){
		
	}

	/** INTERFACES */
	public static function gate() {
		define("_BLIB", true);
		self::autoload();
		self::inputGlobals();
		
		if($blib = self::$global['_request']['blib']){
			$block = new $blib(self::$global['_request']);
			$block->outputUser();
		}
    }
	
}