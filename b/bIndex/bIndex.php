<?php
defined('_BLIB') or die;

/**
 * Class bIndex - main block for show site structure
 * if request have `ajax` variable than show json data without HTML, else HTML template with wrapped json data
 */
class bIndex extends bBlib{

    protected $_traits = array(/*'bRbac' */ 'bSystem', 'bRequest', 'bDataMapper', 'bConfig', 'bCssreset', 'bTemplate', 'bDecorator', 'bUser','bConverter');

    /**
     * @var array   $_config    - current page configuration
     */
    private   $_config = array();

    /**
     * Generate configuration for page
     */
    protected function input(){

        /** @var bRequest $bRequest - request instance */
        $bRequest = $this->getInstance('bRequest');

        // merge default page config with block`s config
        $this->_config = array_replace_recursive(array(
            'author'          => false,
            'ajax'            => false,
            'cache'           => 0,
            'defaultPage'     => 1,
            'skeleton'        => "bIndex__skeleton_default",
            "'{keywords}'"    => "",
            "'{description}'" => "",
            "'{title}'"       => "",
            'isLocked'        => false
        ), $this->_getConfig());

        // get page number from request or default
        $this->_config['pageNo'] = ($bRequest->get('pageNo')?$bRequest->get('pageNo'):$this->_config['defaultPage']);

        // get ajax request
        $this->_config['ajax'] = ($bRequest->get('ajax')?$bRequest->get('ajax'):$this->_config['ajax']);

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
    public function output(){

        /** @var bIndex__bDataMapper $bDataMapper  - page data mapper */
        $bDataMapper = $this->getInstance('bDataMapper');

        /** @var bConverter__instance $bConverter  - converter */
        $bConverter = $this->getInstance('bConverter');

        $pageNo = $this->_config['pageNo'];     // page number
        $isLocked = $this->_config['isLocked']; // flag of page protection

        // replace points (for template)
        $point = array(
            "'{keywords}'"    => $this->_config["'{keywords}'"],
            "'{description}'" => $this->_config["'{description}'"],
            "'{title}'"       => $this->_config["'{title}'"],
            "'{template}'"    => '{"container":"body","content":"not access"}'
        );


        // if page is not locked or user have permission for get it
        if(
            !$isLocked
            || $this->_decorate()->checkAccess($pageNo)
        ){
            // get page template tree
            $page = $bDataMapper->getItem($pageNo);

            // save it
            $this->_config['template'] = $page->tree;

            // create page from template
            $point["'{template}'"] = $this->_getTemplateDiff(false, $page->tree);
        }

        // output json page
        if($this->_config['ajax']){

            $temp = $bConverter->setData($point["'{template}'"])->setFormat('json')->convertTo('array');
            $temp['ajax'] = true;
            $bConverter->setData($temp)->setFormat('array')->convertTo('json');
            $bConverter->output();

        // if page gets in first time
        }else{
            $skeleton = file_get_contents(bBlib::path($this->_config['skeleton'],'tpl'));
            echo str_replace(array_keys($point), array_values($point), $skeleton);
        }

	}

    /**
     * Check permission to unlock page
     * @param null $pageNo  - page number
     * @return bool         - have user access or not
     */
    public function checkAccess($pageNo = null){

        // add role based access control system in traits
        $this->setTrait('bRbac');

        // if user have access
        if($this->_checkAccess('unlock',$pageNo))return true;

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