<?php
defined('_BLIB') or die;

/**
 * Class bDocumentation - Show documentation with navigation tree
 */
class bDocumentation extends bBlib{

    /** @var bDocumentation__bDataMapper $_db */
    protected $_db = null;
    /** @var bDecorator $_decorator */
    protected $_decorator = null;
    /** @var bRequest $_request */
    protected $_request = null;

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

        $_this          = $this->_decorator = $this->getInstance('decorator', 'bDecorator');
        $this->_request = $this->getInstance('request', 'bRequest');
        $this->_db      = $this->getInstance('db', 'bDocumentation__bDataMapper');


        // Decor template
        $decorTemplate = $_this->getData($template);
        $requestTemplate =($this->_request->get('blib')===__CLASS__)?array('id'=>$this->_request->get('id'), 'ajax'=>$this->_request->get('ajax')):array();
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
            $navigation = $this->getNavigation($chapterId); // 0_0
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

        // get menu item list
        $item = $this->_db->getItem($id);
        $item->content = ($item->group)?$this->_db->getList($item->group):array();
        return  $item;
    }

    /**
     * Get item list for create navigation tree
     *
     * @return null|array
     */
    protected function getNavigation(){
        return $this->_db->getList();
    }
}