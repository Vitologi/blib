<?php
defined('_BLIB') or die;

/**
 * Class bExtension
 * Compile dev. files into product version (block.js.dev + elem1.js.dev + elem2.js.dev + mode1.js.dev = block.js)
 * Included patterns:
 * 		singleton	- one extension object
 */
class bExtension extends bBlib{

	/** @var null|static $_instance - Singleton instance */
	private static $_instance = null;

	/**
	 * Overload object factory for Singleton
	 *
	 * @return null|static
     */
	static public function create() {
        if (self::$_instance === null)self::$_instance = parent::create(func_get_args());
        return self::$_instance;
    }

	/**
	 * Return current object without parent block
	 *
	 * @return $this
     */
	public function output(){
        $this->_parent = null;
        return $this;
	}

	/**
	 * Glues files abstraction in working files
	 * Attribute $list can gets from bDecorator::$_list
	 * It looks like ["bBlock"=>["bBlock_mode1"],"bBlock2"=>["bBlock2_mode1","bBlock2_mode2"]]
	 *
	 * @param array $list		- two-dimensional array (names of dev-files)
	 * @param bBlib $caller		- block-initiator
	 */
	public static function _concat($list = array(), bBlib $caller){

		/** @var bExtension $bExtension	- instance */
		$bExtension = $caller->getInstance(__class__);

        foreach($list as $key => $value){
			$bExtension->concat($key, $value);
        }
    }

	/**
	 * Directly glues one block and his modifiers
	 *
	 * @param string $block		- block`s name
	 * @param string[] $list	- modifiers list
	 * @return $this			- for chaining
     */
	private function concat($block = '', $list = array()){

        $files = $this->concatCode($block, array());
        
		foreach($list as $key => $value){
			$files = $this->concatCode($value, $files);
		}
        
		foreach($files as $key => $value){
			file_put_contents(bBlib::path($block, $key), $value);
		}
		return $this;

	}


	/**
	 * Collects all code from the dev-files and sorts it by file extension
	 *
	 * @param string $name		- block`s name
	 * @param array $stack		- empty array or result previous iteration
	 * @return array			- sorted glues code (like ["js"=>"code1 \n code2", "css"=>"css rule1 \n css rule2"])
     */
	private function concatCode($name = '', $stack = array()){
		$path 	= bBlib::path($name);
		$folder = opendir($path);

		while($file = readdir($folder)){

			// glue only .dev files
			if(preg_match('/\w*.(\w+).dev$/', $file, $matches)){
				if(!isset($stack[$matches[1]]))$stack[$matches[1]]='';
				$stack[$matches[1]] .= file_get_contents($path.$file);
				continue;
			}

			// climb down if the folder named like element '__...'
            if(is_dir($path.$file) && substr($file, 0,2) === '__'){
				$stack = $this->concatCode($name.$file, $stack);
			};
		}
		
		return $stack;
	}

}