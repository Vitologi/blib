<?php
defined('_BLIB') or die;

class bDocumentation extends bBlib{

    protected $_traits   = array('bSystem', 'bConfig', 'bDataMapper', 'bDecorator', 'bRequest');

    /** @var array $_template - base template for frontend */
    private $_template = array(
        'block'      => __class__,
        'mods'       => array(),
        'content'    => array(),
        'id'         => null,
        'item'       => null,
        'chapter'    => null,
        'navigation' => array()
    );


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

	public function output(){

        $itemId = $this->_template['id'];
        $chapterId = $this->_template['chapter'];


        if($itemId !== null){
            $item = $this->getItem($itemId);
            $this->_template['item'] = $item;
        }

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
        $bDataMapper = $this->getInstance('bDataMapper');

        // get menu item list
        $item = $bDataMapper->getItem($id);

        $item->content = ($item->group)?$bDataMapper->getList($item->group):array();

        return  $item;

    }

    protected function getNavigation(){

        /** @var bDocumentation__bDataMapper $bDataMapper - data mapper instance */
        $bDataMapper = $this->getInstance('bDataMapper');

        // get menu item list
        $navigation = $bDataMapper->getList();

        return $navigation;

    }
}