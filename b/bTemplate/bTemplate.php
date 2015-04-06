<?php
defined('_BLIB') or die;

/**
 * Class bTemplate  - block for work with page json-templates
 */
class bTemplate extends bBlib{

    protected $_traits    = array('bSystem', 'bTemplate__bDataMapper', 'bRequest');

    /**
     * @var array $_stack   - template storage (key = template number, value = json-template)
     */
    private   $_stack     = array();


	public function output(){
        if($this->_parent)return $this;
	}


    /**
     * Parse multidimensional array to list of templates (without double numbers)
     *
     * @param array $tree       - multidimensional array of template numbers
     * @param array $result     - temp variable
     * @param int $deep         - temp variable
     * @return array            - templates list like array(1,3,5,6,8,9)
     */
    private function parseTemplateTree($tree = array(), $result = array(), $deep = 0){
		foreach ($tree as $branch) {
			if(is_array($branch)) {
				$result = $this->parseTemplateTree($branch, $result, $deep+1);
			}else{ 
				$result[$branch] = true;
			} 
		} 
		return ($deep?$result:array_keys($result));
	}

    /**
     * Get templates from database and save it in private property
     *
     * @param array $list   - list of template numbers
     * @return array        - serialized array (also store in private property)
     */
    private function saveTemplates($list = array()){

        /** @var bTemplate__bDataMapper $bDataMapper  - data mapper instance */
        $bDataMapper = $this->getInstance('bTemplate__bDataMapper');

        $templates = $bDataMapper->getList($list);

        foreach ($templates as $template) {
            $this->_stack[$template['id']] = $template['template'];
        }

        return $this->_stack;
    }


    /**
     * Get only difference between old and new template tree.
     * set NULL value if new template haven`t old value
     *
     * @param $old          - old template tree
     * @param $new          - new template tree
     * @param bool $deep    - temp variable
     * @return array        - difference template tree
     */
    public function getTreeDiff($old, $new, $deep = false) {

		$oldKey = (isset($old[0]) && !isset($new['d']))?(string)$old[0]:null;
		$newKey = isset($new[0])?(string)$new[0]:null;
		$difference = array($newKey);
		
		if($oldKey != $newKey || $newKey === null)$old = array();
		
		
		foreach($new as $key => $value) {
			if( is_array($value)  && $key != 0) {
				if(!isset($old[$key]))$old[$key] = null;
				$temp = $this->getTreeDiff($old[$key], $value, true);
				if(count($temp))$difference[$key] = $temp;
			}
			unset($old[$key]);
		}

		
		foreach($old as $key => $value) {
			$difference[$key] = array(null);
		}
		
		return ($oldKey !== $newKey || count($difference)!=1 || !$deep)?$difference:array();
	}

    /**
     * Glue template tree in one template
     * use already saved template from $this->_stack property
     * also can wrap templates by position markers (for frontend navigation)
     *
     * @param array $templateTree   - template tree
     * @param bool $isWrap          - wrap flag
     * @param bool $deep            - temp variable
     * @return mixed|string         - glued template
     */
    private function glueTemplate(Array $templateTree = array(), $isWrap = false, $deep = false){
        $isFirstIteration = false;

		if(!$deep){
			$deep = $templateTree[0];
            $isFirstIteration = true;
		}
		
		$template = (isset($this->_stack[$templateTree[0]])?$this->_stack[$templateTree[0]]:'');

		$levelTemplate = array();
		
		foreach($templateTree as $key => $value){
			if((int)$key === 0 || (int)$value === 0){
				continue;
			}elseif(is_array($value)){
				$levelTemplate['"{'.$key.'}"'] = $this->glueTemplate($value, $isWrap, $deep.'.'.$key);
			}
		}

        if($isWrap){

            if($isFirstIteration){
                $template = '{"block":"bTemplate", "content":['.$template.'] ,"template":'.json_encode($templateTree,JSON_FORCE_OBJECT).' }';
            }else{
                $template = '{"block":"bTemplate", "elem":"position", "content":['.$template.'] ,"template":"'.$deep.'" }';
            }

        }


        // replace template point
        $template = str_replace(array_keys($levelTemplate), array_values($levelTemplate), $template);

        // clear not used point
        $template = preg_replace('/"{(\d+)}"/', '{"block":"bTemplate", "elem":"position", "content":[] ,"template":"'.$deep.'.$1" }', $template);

		return $template;
	}


    /**
     * Get template (use template tree)
     *
     * @param array $templateTree       - multidimensional array of template numbers
     * @param bool $isWrap              - wrap template by position markers (for frontend navigation)
     * @return mixed                    - glued template
     */
    public function getTemplate($templateTree = array(), $isWrap = false){
        if(!is_array($templateTree)){$templateTree = array($templateTree);}

        // get nums of needed template
        $allTemplatesNum = $this->parseTemplateTree($templateTree);

        // get these template from database
        $this->saveTemplates($allTemplatesNum);

        // get glued template from all saved templates
        $gluedTemplate = $this->glueTemplate($templateTree, $isWrap);

		return $gluedTemplate;
	}

    /**
     * Get template (use template name)
     *
     * @param string $name - template name(or list)
     * @param string $owner - template owner
     * @return mixed - glued template
     */
    public function getOwnTemplate($name = '', $owner = ''){

        /** @var bTemplate__bDataMapper $bDataMapper - data mapper instance */
        $bDataMapper = $this->getInstance('bTemplate__bDataMapper');

        if(is_array($name)){
            $temp = array();
            $list = $bDataMapper->getList($name, $owner);

            foreach($list as $key => $value)$temp[]= $value['template'];

            return $temp;
        }

        $dataObject = $bDataMapper->getTemplate($name, $owner);

        return $dataObject->template;
    }

    /**
     * Get actual template(difference between old and new)
     *
     * @param null|array $oldTree       - old template tree (multidimensional array of template numbers)
     * @param null|array $newTree       - new template tree (multidimensional array of template numbers)
     * @return mixed                        - glued template
     */
    public function getTemplateDiff($oldTree = null, $newTree = null){

        // if old tree is not provided than try get it from request tunnel
        if(!is_array($oldTree)){
            $tunnel =  (array) $this->_getTunnel();
            $oldTree = isset($tunnel['template'])?$tunnel['template']:array();
        }

        // if get template number than create array from it
        if(!is_array($newTree)){
            $newTree = array($newTree);
        }

        // get difference between old and new template tree
        $diff = $this->getTreeDiff($oldTree, $newTree);

        return $this->getTemplate($diff, true);
    }

    /**
     * Get template from child block
     *
     * @param array $templateTree   - template tree for get template
     * @param bBlib $caller         - block-initiator
     * @return mixed                - glued template
     */
    public static function _getTemplate($templateTree = array(), bBlib $caller){

        /** @var bTemplate $bTemplate - template instance */
        $bTemplate = $caller->getInstance(__CLASS__);

		return $bTemplate->getTemplate($templateTree);
	}

    /**
     * Get actual template(difference between old and new) from child block
     *
     * @param null|array $oldTemplate       - old template tree (multidimensional array of template numbers)
     * @param null|array $newTemplate       - new template tree (multidimensional array of template numbers)
     * @param bBlib $caller                 - block-initiator
     * @return mixed                        - glued template
     */
    public static function _getTemplateDiff($oldTemplate = null, $newTemplate = null, bBlib $caller){

        /** @var bTemplate $bTemplate - template instance */
        $bTemplate = $caller->getInstance(__CLASS__);

        return $bTemplate->getTemplateDiff($oldTemplate, $newTemplate);

	}
	
}