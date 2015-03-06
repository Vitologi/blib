<?php
defined('_BLIB') or die;

/**
 * Class bMenu - for generate and work with menu
 */
class bMenu extends bBlib{

    protected $_traits   = array('bSystem', 'bDataMapper', 'bConfig', 'bDecorator');

    /** @var array $_template - base template for frontend */
    private   $_template = array(
        'block'   => __class__,
        'mods'    => array(),
        'content' => array(),
        'id'      => null
    );

    /** @var array  $_config - menu configuration */
    private $_config = array();


    /**
     * Redefine input template through decorator
     *
     * @param array $template   - user template
     */
    protected function input($template = array()){

        /** @var bDecorator__instance $bDecorator - decorator instance */
        $bDecorator = $this->getInstance('bDecorator');

        // Decor template
        $decorTemplate   = $bDecorator->getData($template);
		$this->_template = array_replace_recursive($this->_template, $decorTemplate);
	}

    /**
     * Correct template and output it
     *
     * @return array    - final template
     */
    public function output(){

        // get menu from base
        $menu = $this->getMenu($this->_template['id']);

        // set it into template
        $this->_template['content'] = $menu;

        // return template
        return $this->_template;
	}

    /**
     * Data provider through decorator
     *
     * @param $data     - data from block
     * @return mixed    - data from decorator
     */
    public function getData($data){
		return $data;
	}

    /**
     * Get menu from database
     *
     * @param null|int $id  - menu group id
     * @return array        - all menu items included in menu
     */
    protected function getMenu($id = null){

        if(!$id){return array();}

        /** @var bConfig $bConfig - config instance */
        $bConfig = $this->getInstance('bConfig');

        /** @var bMenu__bDataMapper $bDataMapper - data mapper instance */
        $bDataMapper = $this->getInstance('bDataMapper');

        // get menu item list
        $menuList = $bDataMapper->getMenu($id);

        // get config for menu
        $this->_config = $config = $bConfig->getConfig('bMenu.items');

        // serialize menu items by config
        foreach($menuList as &$item){
            $item['config'] = isset($config[$item['id']])?$config[$item['id']]:array();
        }

        return $menuList;
    }
}