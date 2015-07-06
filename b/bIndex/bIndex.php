<?php
defined('_BLIB') or die;

/**
 * Class bIndex - main block for show site structure
 * if request have `ajax` variable than show json data without HTML, else HTML template with wrapped json data
 */
class bIndex extends bController{

    /**
     * @var array   $_config    - current page configuration
     */
    private   $_config = array(
        'author'          => false,
        'ajax'            => false,
        'cache'           => 0,
        'defaultPage'     => 1,
        'skeleton'        => "bIndex__skeleton_default",
        "'{keywords}'"    => "",
        "'{description}'" => "",
        "'{title}'"       => "",
        'access'        => false
    );

    /**
     * Generate configuration for page
     */
    public function initialize($data = null){

        $this->setInstance('request', 'bRequest');
        $this->setInstance('db', 'bIndex__bDataMapper');
        $this->setInstance('template', 'bTemplate');
        $this->setInstance('this', 'bDecorator');
        $this->setInstance('user', 'bUser');
        $this->setInstance('converter', 'bConverter');
        $this->setInstance('rbac', 'bRbac');

        $_config = $this->getInstance('config', 'bConfig');

        // merge default page config with block`s config
        $this->_config = array_replace_recursive($this->_config, $_config->getConfig(__CLASS__));

        // get page number from request or default
        $this->_config['pageNo'] = $this->get('pageNo', $this->_config['defaultPage']);

        // get ajax request
        $this->_config['ajax'] = $this->get('ajax', $this->_config['ajax']);

        // get config for current page
        $pageConfig = $_config->getConfig(__CLASS__.'.'.$this->_config['pageNo']);

        // extend configuration with page config
        if(is_array($pageConfig))$this->_config = array_replace_recursive($this->_config, $pageConfig);

	}


    /**
     * Generate page and show it
     *
     * @throws Exception
     */
    public function indexAction(){

        /** @var bIndex__bDataMapper $_db  - page data mapper */
        $_db = $this->getInstance('db');

        /** @var bConverter__instance $_converter  - converter */
        $_converter = $this->getInstance('converter');

        /** @var self $_this */
        $_this = $this->getInstance('this');

        /** @var bTemplate $_template */
        $_template = $this->getInstance('template');

        $pageNo = $this->_config['pageNo'];     // page number
        $access = $this->_config['access']; // flag of page protection

        // replace points (for template)
        $point = array(
            "'{keywords}'"    => $this->_config["'{keywords}'"],
            "'{description}'" => $this->_config["'{description}'"],
            "'{title}'"       => $this->_config["'{title}'"],
            "'{template}'"    => '{"block":"bServerMessage", "content":"accesserror"}'
        );


        // if page is not locked or user have permission for get it
        if(
            !is_string($access)
            || $_this->checkAccess($access, $pageNo)
        ){
            // get page template tree
            $page = $_db->getItem($pageNo);

            // save it
            $this->_config['template'] = $page->tree;

            // create page from template
            $point["'{template}'"] = $_template->getTemplateDiff(false, $page->tree);
        }


        switch($this->getView()){
            // output json page
            case "json":

                $temp = $_converter->setData($point["'{template}'"])->setFormat('json')->convertTo('array');
                $_converter->setData($temp)->setFormat('array')->convertTo('json');
                $_converter->output();

                break;

            // if page gets in first time
            default:

                $skeleton = file_get_contents(bBlib::path($this->_config['skeleton'],'tpl'));
                echo str_replace(array_keys($point), array_values($point), $skeleton);

                break;

        }

	}

    /**
     * Check permission to unlock page
     * @param null|string $privilege    - page access privilege
     * @param null $pageNo              - page number
     * @return bool                     - have user access or not
     */
    public function checkAccess($privilege = null, $pageNo = null){

        /** @var bRbac $_rbac */
        $_rbac = $this->getInstance('rbac');

        // if user have access
        if($_rbac->checkAccess($privilege, $pageNo))return true;

        // or not
        return false;
	}

    /**
     * Check user is author
     *
     * @return bool
     */
    public function isOwner(){

        /** @var bUser $_user - user instance */
        $_user = $this->getInstance('user');

        $userId     = $_user->getId();
        $pageAuthor = $this->_config['author'];

        return $userId == $pageAuthor;
    }
	
}