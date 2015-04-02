<?php
defined('_BLIB') or die;

/**
 * Class bMenu - for generate and work with menu
 * Included patterns:
 * 		MVC	- use input property like action and view for create code logic
 */
class bMenu extends bBlib{

    protected $_traits = array('bSystem', 'bRequest','bMenu__view', 'bMenu__model');
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

        /** @var bRequest $bRequest - request instance */
        $bRequest   = $this->getInstance('bRequest');

        $tunnel     = $bRequest->getTunnel(__CLASS__);

        // Glue request params
        $this->_mvc     = array_replace($this->_mvc, $tunnel, $data);

	}

    /**
     * Correct template and output it
     *
     * @return array    - final template
     */
    public function output(){

        /** @var  bMenu__model $model */
        $model  = $this->getInstance('bMenu__model');

        /** @var  bMenu__view $view */
        $view   = $this->getInstance('bMenu__view');

        $mvc    = $this->_mvc;
        $id     = $mvc['id'];

        switch($mvc['action']){
            case 'index':
            default:
                $temp = $model->getMenu($id);
                break;
        }


        switch($mvc['view']){
            case "horizontal":
                return $view->horizontal($temp, $id);
                break;

            case "indexJson":
                return $view->indexJson($temp, $id);
                break;


            case 'index':
            default:
                return $view->index($temp, $id);
                break;
        }

	}

}