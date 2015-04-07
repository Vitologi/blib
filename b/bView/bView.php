<?php
defined('_BLIB') or die;

/**
 * Created by PhpStorm.
 * User: morozov
 * Date: 25.03.2015
 * Time: 15:05
 *
 * Class bView - prototype for blocks views
 * Included patterns:
 * 		builder	- store in it self template and elements
 *
 */

class bView  extends bBlib{

    /**
     * @var string $_template   - used main template
     */
    protected $_template  = '{"content":"{1}"}';

    /**
     * @var array $_positions   - multidimensional array of elements (key - template point, value - element)
     */
    protected $_positions = array();

    /**
     * @var array $_variables   - some predefined variables
     */
    protected $_variables = array();


    /**
     * Set variables in view
     *
     * @param string $name      - key for $this->_variables
     * @param mixed $value      - value for    $this->_variables
     * @return $this            - for chaining
     */
    final public function set($name = null, $value){
        if(is_string($name)){
            $this->_variables[$name] = $value;
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
    final public function get($name = null, $default = null){
        if(
            is_string($name)
            && isset($this->_variables[$name])
        ){
            return $this->_variables[$name];
        }

        return $default;
    }


    /**
     * Return self instance for parent block
     *
     * @return $this
     */
    final public function output(){
        if($this->_parent instanceof bBlib)return $this;
    }


    /**
     * Set main template
     *
     * @param array|string $template    - template like array or json string
     * @return $this                    - for chaining
     * @throws Exception
     */
    final public function setTemplate($template = null){

        if(!is_string($template) && !is_array($template))throw new Exception('Try set wrong template.');

        $this->_template = $template;

        return $this;
    }


    /**
     * Set element for view
     *
     * @param string $pos           - template point
     * @param string|array $content - element for include in template (array or json string)
     * @return $this                - for chaining
     * @throws Exception
     */
    final public function setPosition($pos = null, $content = '{}'){
        if(!is_string($pos))throw new Exception('Try set wrong position.');

        $this->_positions[$pos] = $content;

        return $this;
    }


    /**
     * Glued template and element for get complete template
     *
     * @param bool $asArray     - how return result (like array or like json string)
     * @return string|array     - complete template
     */
    final public function generate($asArray = false){

        $template = $this->_template;
        $blocks = $this->_positions;

        if(is_array($template))$template = json_encode($template, 256);

        foreach($blocks as $key =>$value){
            if(is_array($value))$blocks[$key] = json_encode($value, 256);
        }

        $combineTemplate = str_replace(array_keys($blocks), array_values($blocks), $template);

        return $asArray?json_decode($combineTemplate,true):$combineTemplate;
    }

}