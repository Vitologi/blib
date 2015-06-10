<?php
defined('_BLIB') or die;


/**
 * Class bConfig__bPanel - concrete controller for bConfig block
 * Included patterns:
 * 		MVC	- use input property like action and view for create code logic
 */
class bConfig__bPanel extends bController{

    /**
     * @var array
     */
    protected $_traits    = array('bConfig__model', 'bConfig__view');

    protected $_block = 'bConfig';
    /**
     * Get tunnel data for business logic, set view template and configure it
     *
     * @param array $data   - provided data
     * @throws Exception
     */
    protected function configure($data = array()){

        /** @var bConfig__view $bConfig__view - template instance */
        $bConfig__view = $this->getInstance('bConfig__view');

        /** @var bConfig__model $bConfig__model */
        $bConfig__model = $this->getInstance('bConfig__model');

        // Save blocks (panel and config)
        $blocks = $bConfig__model->getBlocks('__bPanel');
        $confBlocks = $bConfig__model->getBlocks();

        $bConfig__view->set("blocks", $blocks);
        $bConfig__view->set("confBlocks", $confBlocks);

        // Set template for menu view
        if ($template = $bConfig__model->getTemplate()) $bConfig__view->setTemplate($template);

    }


    public function indexAction(){

        /** @var bConfig__view $bConfig__view - template instance */
        $bConfig__view = $this->getInstance('bConfig__view');

        /** @var bConfig__model $bConfig__model */
        $bConfig__model = $this->getInstance('bConfig__model');

        /** @var bConfig $bConfig configuration block */
        $bConfig = $this->getInstance('bConfig');

/*
        $block         = $this->get('block', $this->_block);
        $defaultConfig = $bConfig__model->getDefaultConfig($block);
        $currentConfig = $bConfig->getConfig($block);


        $bConfig__view->set('block', $block);
        $bConfig__view->set('defaultConfig', $defaultConfig);
        $bConfig__view->set('currentConfig', $currentConfig);
*/
        $bConfig__view->build();


        return $bConfig__view->generate();
    }


    public function formAction(){

        /** @var  bMenu__model $bMenu__model */
        $bMenu__model  = $this->getInstance('bMenu__model');

        /** @var  bMenu__view $bMenu__view */
        $bMenu__view   = $this->getInstance('bMenu__view');

        /** get item from table */
        $table = $this->get('bTable');
        $tableData = (isset($table[$this->_tableName]) ? $table[$this->_tableName]: array());
        $tableData = array_replace_recursive(array(
            'page'  => array(
                'number' => 0,
                'rows'   => 5,
                'count'  => 0
            ),
            'items' => array()
        ), $tableData);
        $tableItem = (isset($tableData['items'][0])?$tableData['items'][0]:array());

        /** get item from form */
        $form = $this->get('bForm');
        $formData = (isset($form[$this->_formName]) ? $form[$this->_formName]: array());
        $formItem = $formData;

        $item = array_replace_recursive($tableItem, $formItem);

        if(isset($item['id'])){
            $item = $bMenu__model->getItem($item['id']);
        }

        $bMenu__view->set('item', $item);

        $selectList = $bMenu__model->getSmallList();
        $bMenu__view->set('selectList', $selectList);

        switch($this->getView()){
            case "add":
                $bMenu__view->addPanel();
                break;

            case "edit":
                $bMenu__view->editPanel();
                break;
        }

        return $bMenu__view->generate();
    }

    public function addAction(){

        /** @var  bMenu__model $bMenu__model */
        $bMenu__model  = $this->getInstance('bMenu__model');

        /** @var  bMenu__view $bMenu__view */
        $bMenu__view   = $this->getInstance('bMenu__view');

        /** @var bMenu__validator $bMenu__validator */
        $bMenu__validator = $this->getInstance('bMenu__validator');

        /** get item from form */
        $form = $this->get('bForm');
        $formData = (isset($form[$this->_formName]) ? $form[$this->_formName]: array());
        $item  = $formData;

        // validate input data
        if($errors = $bMenu__validator->validateAddForm($item)){

            // change view
            $this->setView('add');

            // set view(template) variable
            $bMenu__view->set('message', "Данные не соответствуют формату.");
            $bMenu__view->set('errors', $errors);
            $bMenu__view->set('item', $item);

            return $this->formAction();

        }else{
            $result = $bMenu__model->addItem($item);

            $this->setView('index');

            $bMenu__view->set('message', "Запись успешно добавлена.");

            return $this->indexAction();
        }


    }


    public function editAction(){

        /** @var  bMenu__model $bMenu__model */
        $bMenu__model  = $this->getInstance('bMenu__model');

        /** @var  bMenu__view $bMenu__view */
        $bMenu__view   = $this->getInstance('bMenu__view');

        /** @var bMenu__validator $bMenu__validator */
        $bMenu__validator = $this->getInstance('bMenu__validator');


        $form = $this->get('bForm');
        $formData = (isset($form[$this->_formName]) ? $form[$this->_formName]: array());
        $item = $formData;

        if($errors = $bMenu__validator->validateEditForm($item)){
            $bMenu__view->set('message', "Данные не соответствуют формату.");
            $bMenu__view->set('errors', $errors);
            $bMenu__view->set('item', $item);
        }else{
            $result = $bMenu__model->editItem($item);
            $bMenu__view->set('message', "Запись успешно отредактирована.");
            $bMenu__view->set('item', $result);
        }

        $this->setView('edit');

        return $this->formAction();
    }

    public function deleteAction(){

        /** @var  bMenu__model $bMenu__model */
        $bMenu__model  = $this->getInstance('bMenu__model');

        /** @var  bMenu__view $bMenu__view */
        $bMenu__view   = $this->getInstance('bMenu__view');

        /** @var bMenu__validator $bMenu__validator */
        $bMenu__validator = $this->getInstance('bMenu__validator');

        $table = $this->get('bTable');
        $tableData = (isset($table[$this->_tableName]) ? $table[$this->_tableName]: array());
        $tableData = array_replace_recursive(array(
            'page'  => array(
                'number' => 0,
                'rows'   => 5,
                'count'  => 0
            ),
            'items' => array()
        ), $tableData);

        $itemNums = $bMenu__model->serializeItemNumbers($tableData['items']);

        if($errors = $bMenu__validator->validateDelete($itemNums)){
            $bMenu__view->set('message', "Данные не соответствуют формату.");
            $bMenu__view->set('errors', $errors);
        }else{
            $bMenu__model->deleteItem($itemNums);
            $bMenu__view->set('message', "Строки успешно удалены.");
        }

        $this->setView('index');

        return $this->indexAction();
    }

}