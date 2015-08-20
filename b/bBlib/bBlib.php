<?php


/**
 * Class bBlib  - base abstract class
 */
abstract class bBlib{

    const VERSION           = "0.8.42";  // Engine version

    /** @var null|bBlib $_parent - Block - creator  */
    protected $_parent      = null;
    protected $_instances   = array();  // Implemented objects
    protected $_vars        = array();  // Local variables
    protected static $_VARS = array();  // Global variables
    
    /**
     * Create all bBlib concrete instance factory method
     *
     * @return static       - concrete instance
     */
    static public function create() {
        $data = (func_num_args()===1)?func_get_arg(0):func_get_args();
        return new static($data);
    }


    /** BASE INPUT/OUTPUT METHODS */
    protected function input(){}
    public function output(){return $this;}


    /**
     * Set variables in view
     *
     * @param string $name      - key for $this->_variables
     * @param mixed $value      - value for    $this->_variables
     * @return $this            - for chaining
     */
    final public function setVars($name = null, $value){
        if(is_string($name)){
            $this->_vars[$name] = $value;
        }
        return $this;
    }


    /**
     * Get variables in view
     *
     * @param string $name      - key for $this->_variables
     * @param mixed $default    - default value
     * @return mixed            - variable value or default
     */
    final public function getVars($name = null, $default = null){
        if(
            is_string($name)
            && isset($this->_vars[$name])
            && $this->_vars[$name] !== null
        ){
            return $this->_vars[$name];
        }

        return $default;
    }
    
    /** INTERFACES */
    /**
     * Get included instance
     *
     * @param string $name                  instance`s name
     * @param null|object|string $default   default instance
     * @return object|null                  instance
     * @throws Exception
     */
    final protected function getInstance($name = '', $default = null){
        $instance = isset($this->_instances[$name]) ? $this->_instances[$name] : null;
        $isObject = is_object($instance);

        if ($isObject)return $instance;

        $isString = is_string($instance);
        $isDefString = is_string($default);
        $constructor = ($isString ? $instance : ($isDefString? $default : 'stdClass'));

        try {

            if (class_exists($constructor)) {

                if(method_exists($constructor,'create') && method_exists($constructor, 'output')){
                    $instance = $constructor::create()->setParent($this)->output();
                }else{
                    $instance = new $constructor();
                }

            }

        } catch (Exception $e) {
            throw new Exception('Can`t create instance of ' . $default . ' class.');
        }

        $this->setInstance($name, $instance);

        return $this->_instances[$name];
    }


    /**
     * Set instance
     *
     * @param string $name                  instance`s name
     * @param null|object|string $default   default instance
     * @return $this                        for chaining
     * @throws Exception
     */
    final protected function setInstance($name = '', $default = null){
        if(is_object($default) || is_string($default))$this->_instances[$name] = $default;
        return $this;
    }

    /**
     * Set caller block
     *
     * @param bBlib $block      - block which initiated creation
     * @return $this            - for chaining
     */
    final protected function setParent(bBlib $block = null){
        $this->_parent = $block;
        return $this;
    }


    /**
     * Generate path to block in BEM notation
     * For example:
     * $path = bBlib::path('bBlock__elem_modifier','php');
     * $path = 'b/bBlock/__elem/_modifier/bBlock__elem_modifier.php';
     *
     * @param string $name      - block`s name
     * @param string $ext       - extension of file
     * @return string           - path to block folder or to file (if have $ext)
     * @throws Exception
     */
    final public static function path($name = '', $ext = ''){
        if($name === '')throw new Exception('Given incorrect argument') ;
        if($ext != ''){$ext = $name.'.'.$ext;}
        return $name{0}.'/'.preg_replace('/(_+)/i', '/${1}', $name).'/'.$ext;
    }


    /**
     * Start application point
     *
     * @param string $block     - block`s name
     */
    final public static function init($block = '') {
        try{
            define("_BLIB", true);
            self::autoload();
            if(!is_string($block) or $block === '')return;
            $block::create()->output();
        }catch(Exception $e){
            echo sprintf('(%1$s) [%2$s] - %3$s ', $e->getFile(), $e->getLine(), $e->getMessage());
        }
    }

    
    /** INTERCEPTION METHODS */
    final private function __clone(){}  // Protect from cloning
    final private function __sleep(){}  // Protect from serialize
    final private function __wakeup(){} // Protect from unserialize
    final private function __construct(){
        $data = (func_num_args()===1)?func_get_arg(0):func_get_args();
        
        // base input handler
        $this->input($data);
    }


    /**
     * Call method from trait blocks
     *
     * @param string $method    - method name
     * @param array $args       - provided arguments
     * @return mixed            - some result of traits method
     */
    function __call($method = '', $args = array()){
        
        foreach($this->_instances as $value){
            if (!method_exists($value, $method)) continue;
            $args[] = $this;
            return call_user_func_array(array($value, $method), $args);
        }

        return null;
    } 

    /**
     * Autoload of class
     *
     * @void    - set autoloader
     */
    private static function autoload(){

        /**
         * Dynamically created autoloader
         *
         * @param string $class - class name
         * @throws Exception
         */
        function _autoload($class = ''){
            $path = bBlib::path($class, 'php');
            if(!file_exists($path)){throw new Exception('Called class '.$class.' is missing.');}
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
    
}
