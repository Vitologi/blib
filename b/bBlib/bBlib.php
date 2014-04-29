<?php 

abstract class bBlib{
	
	/** GLOBAL PROPERTY */
	protected static $global = array(
		'REQUEST'	=>null,
		'DB'		=>null
	);
	
	/** LOCAL PROPERTY */
	protected $local = array(
		'block'		=>"bBlib",
		'parent'	=>array()
	);
	
	
	/** INTERCEPTION METHODS */
	final public function __construct($data){
		$this->setLocals($data);
		$this->input($data);
	}
	
	function __get($prorerty){
		return $this->local[$prorerty];
	}
	
	function __isset($prorerty){
		return array_key_exists($prorerty, $this->local);
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
	private static function setGlobals(){
		self::$global['REQUEST'] = (array)json_decode(file_get_contents("php://input"),true)+(array)$_POST +(array)$_GET;
		self::$global['DB'] = '0_0 db connect';
	}
	
	final protected function setLocals($data){
		$class = $this->local['block'] = get_class($this);
		$this->local['path'] = sprintf('%1$s/%2$s',$class{0},$class);
	}
	

	/** INTERFACES */
	public static function gate() {
		define("_BLIB", true);
		self::setGlobals();
		self::autoload();
		
		if($blib = self::$global['REQUEST']['blib']){
			$block = new $blib(self::$global['REQUEST']);
			
			var_dump($block);
			
			
			$block->output();
		}
    }
	
	abstract public function output();
	abstract protected function input($data);
	
}