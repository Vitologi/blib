<?php
defined('_BLIB') or die;

/**
 * Class bConfig__model - model for generate and work with config components
 * Included patterns:
 * 		MVC	- localise business logic into one class
 */
class bConfig__model extends bBlib{

    /** @var null|bConfig $_bConfig                 menu item configuration */
    private $_bConfig       = null;

    /** @var null|bPanel__model $_bPanel__model     for get some method */
    private $_bPanel__model = null;

    /** @var null|bTemplate $_bTemplate             template instance */
    private $_bTemplate     = null;

    /**
     * @var array   - included traits
     */
    protected $_traits      = array('bConfig', 'bPanel__model', 'bTemplate');


    /**
     *  Set config and data mapper
     */
    protected function input(){
        /** @var bConfig $bConfig               config instance */
        $this->_bConfig = $this->getInstance('bConfig');

        /** @var bPanel__model  $_bPanel__model for get some method */
        $this->_bPanel__model = $this->getInstance('bPanel__model');

        /** @var bTemplate $_bTemplate          template instance */
        $this->_bTemplate = $this->getInstance('bTemplate');
    }

    /**
     * Return them self to parent
     *
     * @return $this
     */
    public function output(){
        return ($this->_parent instanceof bBlib)?$this:null;
    }

    /**
     * Get menu from database
     *
     * @param null|int $id  - menu group id
     * @return array        - all menu items included in menu
     */
    public function getMenu($id = null){

        if(!$id){return array();}

        // get menu item list
        $menuList = $this->_bDataMapper->getMenu($id);

        // get config for menu
        $config = $this->_bConfig->getConfig('bMenu.items');

        // serialize menu items by config
        foreach($menuList as &$item){
            $item['config'] = isset($config[$item['id']])?$config[$item['id']]:array();
        }

        return $menuList;
    }

    /**
     * Get single menu item
     *
     * @param int $id   - menu item id
     * @return array    - item data
     * @throws Exception
     */
    public function getItem($id = null){

        if(!is_numeric($id))return array();

        $item = $this->_bDataMapper->getItem($id);

        if ($item->id)return(array)$item;

        throw new Exception("Ошибка добавления записи");
    }

    /**
     * Add menu item
     *
     * @param array $data   - menu item data
     * @return array        - added item
     * @throws Exception
     */
    public function addItem(Array $data = array()){

        $data = array_replace(array(
            'menu'=>null,
            'name'=>null,
            'link'=>null,
            'bconfig_id'=>null,
            'bmenu_id'=>null
        ),$data);

        $item = $this->_bDataMapper->getItem();

        $item->menu = $data['menu'];
        $item->name = $data['name'];
        $item->link = $data['link'];
        $item->bconfig_id = $data['bconfig_id'];
        $item->bmenu_id = $data['bmenu_id'];

        $this->_bDataMapper->save($item);

        if ($item->id)return(array)$item;

        throw new Exception("Ошибка добавления записи");
    }


    /**
     * Edit menu item
     *
     * @param array $data   - menu item data
     * @return null|stdClass    - menu object or null
     * @throws Exception
     */
    public function editItem($data = array()){

        $data = array_replace(array(
            'id'=>null,
            'menu'=>null,
            'name'=>null,
            'link'=>null,
            'bconfig_id'=>null,
            'bmenu_id'=>null
        ),$data);

        $item = $this->_bDataMapper->getItem($data['id']);

        $item->menu = $data['menu'];
        $item->name = $data['name'];
        $item->link = $data['link'];
        $item->bconfig_id = $data['bconfig_id'];
        $item->bmenu_id = $data['bmenu_id'];

        $this->_bDataMapper->save($item);

        return ($item->id)?$item:null;

    }

    /**
     * Get all menu item without some property (only id, name, link)
     *
     * @return array
     * @throws Exception
     */
    public function getSmallList(){
        return $this->_bDataMapper->getSmallList();
    }

    /**
     * Get menu item list
     *
     * @param int $pageNo   - page number
     * @param int $count    - rows on page
     * @return array        - array of menu item
     * @throws Exception
     */
    public function getList($pageNo = 0, $count = 5){
        $from = $pageNo*$count;
        return $this->_bDataMapper->getList(array('from'=>$from,'count'=>$count));
    }

    /**
     * Delete menu items
     *
     * @param array $list   - array of menu items ids
     * @return null|object  - result of operation
     * @throws Exception
     */
    public function deleteItem(Array $list = array()){

        return $this->_bDataMapper->destroy($list);
    }

    /**
     * Get count of menu items
     *
     * @return null|number  - menu item number
     */
    public function getCount(){
        return $this->_bDataMapper->getCount();
    }

    /**
     * Create list of menu item ids
     *
     * @param array $list   - non serialized array
     * @return array        - serialized array
     */
    public function serializeItemNumbers($list = array()){
        $temp = array();
        foreach($list as $key=>$value){
            if(!isset($value['id']))continue;
            $temp[] = $value['id'];
        }
        return $temp;
    }

    /**
     * Get template from template blocks
     *
     * @return mixed    - string template
     */
    public function getTemplate(){
        return $this->_bTemplate->getOwnTemplate('template', 'bConfig');
    }


    /**
     * Get blocks list
     *
     * @param null|string $block    block name
     * @return array                blocks array (associative)
     */
    public function getBlocks($block = null){
        return $this->_bPanel__model->getBlocks($block);
    }

}