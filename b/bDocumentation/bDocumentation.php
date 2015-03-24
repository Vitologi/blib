<?php
defined('_BLIB') or die;

/**
 * Class bDocumentation - Show documentation with navigation tree
 */
class bDocumentation extends bBlib{

    protected $_traits   = array('bSystem', 'bConfig', 'bDocumentation__bDataMapper', 'bDecorator', 'bRequest');

    /** @var array $_template - base template for frontend */
    private $_template = array(
        'block'      => __class__,      // frontend block
        'mods'       => array(),        // modificators
        'content'    => array(),        // default property
        'id'         => null,           // item id it will be show in main window
        'item'       => null,           // item data
        'chapter'    => null,           // root navigation chapter
        'navigation' => array()         // all navigation item
    );


    /**
     * Change base template from decorator and request data
     *
     * @param array $template - template provided from create function
     */
    protected function input($template = array()){

         /** @var bDecorator__instance $bDecorator - decorator instance */
         $bDecorator = $this->getInstance('bDecorator');

         /** @var bRequest $bRequest - request instance */
         $bRequest = $this->getInstance('bRequest');


         // Decor template
         $decorTemplate = $bDecorator->getData($template);
         $requestTemplate =($bRequest->get('blib')===__CLASS__)?array('id'=>$bRequest->get('id'), 'ajax'=>$bRequest->get('ajax')):array();
         $this->_template = array_replace_recursive($this->_template, $decorTemplate, $requestTemplate);
    }

    /**
     * Return documentation data
     *
     * @return array    - multidimensional array for frontend
     */
    public function output(){

        // item id, root chapter id
        $itemId = $this->_template['id'];
        $chapterId = $this->_template['chapter'];


        // return item data only if it isn't null
        if($itemId !== null){
            $item = $this->getItem($itemId);
            $this->_template['item'] = $item;
        }

        // return navigation only if it isn't null
        if($chapterId !== null){
            $navigation = $this->getNavigation($chapterId);
            $this->_template['navigation']  = $navigation;
        }


		if(isset($this->_template['ajax'])){
			echo json_encode($this->_template, 256);
		}else{
			return $this->_template;
		}
		
	}

    /**
     * Provide data for decorator
     *
     * @param $data
     * @return mixed
     */
    protected function getData($data){
		return $data;
	}

    /**
     * Get documentation from database
     *
     * @param null|int $id  - item id
     * @return array        - all menu items included in menu
     */
    protected function getItem($id = null){

        if(!$id){return array();}

        /** @var bDocumentation__bDataMapper $bDataMapper - data mapper instance */
        $bDataMapper = $this->getInstance('bDocumentation__bDataMapper');

        // get menu item list
        $item = $bDataMapper->getItem($id);

        $item->content = ($item->group)?$bDataMapper->getList($item->group):array();

        return  $item;

    }

    /**
     * Get item list for create navigation tree
     *
     * @return null|array
     */
    protected function getNavigation(){

        /** @var bDocumentation__bDataMapper $bDataMapper - data mapper instance */
        $bDataMapper = $this->getInstance('bDocumentation__bDataMapper');

        // get menu item list
        $navigation = $bDataMapper->getList();

        return $navigation;

    }
}