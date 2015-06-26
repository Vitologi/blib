<?php
defined('_BLIB') or die;

/**
 * Class bMenu__model - model for generate and work with menu
 * Included patterns:
 * 		MVC	- localise business logic into one class
 */
class bMenu__model extends bBlib{

    /** @var null|bConfig $_config - menu item configuration */
    private $_config = null;

    /** @var null|bMenu__bDataMapper $_db - save datamapper in property for quick access */
    private $_db = null;

    /**
     *  Set config and data mapper
     */
    protected function input() {
        $this->_config = $this->getInstance('config', 'bConfig');
        $this->_db     = $this->getInstance('db', 'bMenu__bDataMapper');
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
        $menuList = $this->_db->getMenu($id);

        // get config for menu
        $config = $this->_config->getConfig('bMenu.items');

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

        $item = $this->_db->getItem($id);

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

        $item = $this->_db->getItem();

        $item->menu = $data['menu'];
        $item->name = $data['name'];
        $item->link = $data['link'];
        $item->bconfig_id = $data['bconfig_id'];
        $item->bmenu_id = $data['bmenu_id'];

        $this->_db->save($item);

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

        $item = $this->_db->getItem($data['id']);

        $item->menu = $data['menu'];
        $item->name = $data['name'];
        $item->link = $data['link'];
        $item->bconfig_id = $data['bconfig_id'];
        $item->bmenu_id = $data['bmenu_id'];

        $this->_db->save($item);

        return ($item->id)?$item:null;

    }

    /**
     * Get all menu item without some property (only id, name, link)
     *
     * @return array
     * @throws Exception
     */
    public function getSmallList(){
        return $this->_db->getSmallList();
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
        return $this->_db->getList(array('from'=>$from,'count'=>$count));
    }

    /**
     * Delete menu items
     *
     * @param array $list   - array of menu items ids
     * @return null|object  - result of operation
     * @throws Exception
     */
    public function deleteItem(Array $list = array()){

        return $this->_db->destroy($list);
    }

    /**
     * Get count of menu items
     *
     * @return null|number  - menu item number
     */
    public function getCount(){
        return $this->_db->getCount();
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

}