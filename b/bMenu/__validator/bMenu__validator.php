<?php
defined('_BLIB') or die;

/**
 * Class bMenu__validator - validate methods
 */
class bMenu__validator extends bBlib{

    /**
     * @return $this    - return them self for parent
     */
    public function output(){
        return $this;
    }

    /**
     * Validate data for create menu item
     *
     * @param array $data   - menu item data
     * @return array|bool   - list of error or false(if all is fine)
     * @throws \SimpleValidator\SimpleValidatorException    - include outer validator ( 0_0 change in native )
     */
    public function validateAddForm($data = array()){
        $result = false;

        $rules = array(
            'menu'       => array(
                'required',
                'integer'
            ),
            'name'       => array(
                'required'
            ),
            'link'       => array(),
            'bconfig_id' => array(),
            'bmenu_id'   => array(
                'integer'
            )
        );

        $validation_result = SimpleValidator\Validator::validate($data, $rules);

        if ($validation_result->isSuccess() !== true) {
            $result = $validation_result->getErrors();
        }

        return $result;
    }

    /**
     * Validate data for edit menu item
     *
     * @param array $data   - menu item data
     * @return array|bool   - list of error or false(if all is fine)
     * @throws \SimpleValidator\SimpleValidatorException   - include outer validator ( 0_0 change in native )
     */
    public function validateEditForm($data = array()){
        $result = false;

        $rules = array(
            'id'         => array(
                'required',
                'integer'
            ),
            'menu'       => array(
                'required',
                'integer'
            ),
            'name'       => array(
                'required'
            ),
            'link'       => array(),
            'bconfig_id' => array(),
            'bmenu_id'   => array(
                'integer'
            )
        );

        $validation_result = SimpleValidator\Validator::validate($data, $rules);

        if ($validation_result->isSuccess() !== true) {
            $result = $validation_result->getErrors();
        }

        return $result;
    }

    /**
     * Validate data for delete menu items
     *
     * @param array $list   - list of menu item ids
     * @return array|bool   - list of error or false(if all is fine)
     */
    public function validateDelete(Array $list = array()){
        $result = false;

        foreach($list as $key => $value){
            if(!is_numeric($value)){
                $result = array("Delete item list is broken.");
                break;
            }
        }

        return $result;
    }

}