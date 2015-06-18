<?php
defined('_BLIB') or die;

/**
 * Class bMenu - for generate and work with menu
 * Included patterns:
 * 		MVC	- use input property like action and view for create code logic
 */
class bMenu extends bBlib{

    private   $_mvc    = array(
        'action' => 'index',
        'view'   => 'index',
        'id'    => 1
    );

    /**
     * Redefine input template through decorator
     *
     * @param array $data   - user template
     */
    protected function input($data = array()){

        $this->setInstance('model', 'bMenu__model');
        $this->setInstance('view', 'bMenu__view');

        /** @var bRequest $_request - request instance */
        $_request   = $this->getInstance('request', 'bRequest');

        $tunnel     = (array) $_request->get(__CLASS__);

        // Glue request params
        $this->_mvc     = array_replace($this->_mvc, $tunnel, $data);

	}

    /**
     * Correct template and output it
     *
     * @return array    - final template
     */
    public function output(){

        /** @var  bMenu__model $_model */
        $_model  = $this->getInstance('model');

        /** @var  bMenu__view $view */
        $_view   = $this->getInstance('view');

        $mvc    = $this->_mvc;
        $id     = $mvc['id'];

        switch($mvc['action']){
            case 'index':
            default:
                $temp = $_model->getMenu($id);
                break;
        }


        switch($mvc['view']){
            case "horizontal":
                return $_view->horizontal($temp, $id);
                break;

            case "indexJson":
                return $_view->indexJson($temp, $id);
                break;


            case 'index':
            default:
                return $_view->index($temp, $id);
                break;
        }

	}

}