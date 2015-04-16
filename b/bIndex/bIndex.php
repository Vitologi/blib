<?php
defined('_BLIB') or die;

/**
 * Class bIndex - main block for show site structure
 * if request have `ajax` variable than show json data without HTML, else HTML template with wrapped json data
 */
class bIndex extends bController{

    protected $_traits = array(/*'bRbac' */ 'bRequest', 'bIndex__bDataMapper', 'bConfig', 'bCssreset', 'bTemplate', 'bDecorator', 'bUser','bConverter');

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
    protected function configure($data = null){

        // merge default page config with block`s config
        $this->_config = array_replace_recursive($this->_config, $this->_getConfig());

        // get page number from request or default
        $this->_config['pageNo'] = $this->get('pageNo', $this->_config['defaultPage']);

        // get ajax request
        $this->_config['ajax'] = $this->get('ajax', $this->_config['ajax']);

        // get config for current page
        $pageConfig = $this->_getConfig($this->_config['pageNo']);

        // extend configuration with page config
        if(is_array($pageConfig))$this->_config = array_replace_recursive($this->_config, $pageConfig);

	}


    /**
     * Generate page and show it
     *
     * @throws Exception
     */
    public function indexAction(){

        /** @var bIndex__bDataMapper $bDataMapper  - page data mapper */
        $bDataMapper = $this->getInstance('bIndex__bDataMapper');

        /** @var bConverter__instance $bConverter  - converter */
        $bConverter = $this->getInstance('bConverter');

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
            || $this->_decorate()->checkAccess($access, $pageNo)
        ){
            // get page template tree
            $page = $bDataMapper->getItem($pageNo);

            // save it
            $this->_config['template'] = $page->tree;

            // create page from template
            $point["'{template}'"] = $this->_getTemplateDiff(false, $page->tree);
        }


        switch($this->getView()){
            // output json page
            case "json":

                $temp = $bConverter->setData($point["'{template}'"])->setFormat('json')->convertTo('array');
                //$temp['ajax'] = true;
                $bConverter->setData($temp)->setFormat('array')->convertTo('json');
                $bConverter->output();

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

        // add role based access control system in traits
        $this->setTrait('bRbac');

        // if user have access
        if($this->_checkAccess($privilege, $pageNo))return true;

        // or not
        return false;
	}

    /**
     * Check user is author
     *
     * @return bool
     */
    public function isOwner(){

        /** @var bUser $bUser - user instance */
        $bUser = $this->getInstance('bUser');

        $userId     = $bUser->getId();
        $pageAuthor = $this->_config['author'];

        return $userId == $pageAuthor;
    }
	
}