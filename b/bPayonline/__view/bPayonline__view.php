<?php
defined('_BLIB') or die;

/**
 * Class bPayonline__view - view collection for bPayonline
 * Included patterns:
 * 		MVC	- localise view into one class
 */
class bPayonline__view extends bView{

    /**
     * @var array $_traits - included traits (get bPanel view)
     */
    protected $_traits = array('bConverter');

    /**
     * Store bPayonline default carcase
     *
     * @param array $list   - menu list
     * @param null $id      - root menu item
     * @return array        - blib structure (bom)
     */
    public function json(){

        /** @var bConverter__instance $bConverter */
        $bConverter = $this->getInstance('bConverter');

        $return = $this->getVars('error', false);

        if($return === false){
            $return = $this->getVars('data', array());
        }else{
            $return = array('errors'=>$return);
        }

        $bConverter->setData($return)->setFormat('array')->convertTo('json');

        $bConverter->output();

    }

}