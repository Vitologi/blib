<?php
defined('_BLIB') or die;


/**
 * Class bMenu__bPanel - concrete controller for bMenu block
 * Included patterns:
 * 		MVC	- use input property like action and view for create code logic
 */
class bMenu__bPanel extends bController{

    /**
     * @var array
     */
    protected $_traits    = array(/* 'bRequest', */ 'bMenu__model', 'bMenu__view', 'bPanel__model', 'bMenu__validator');

    protected $_tableName = 'menuTable';
    protected $_formName  = 'menuForm';
    protected $_items = array();

    /**
     * Get tunnel data for business logic, set view template and configure it
     *
     * @param array $data   - provided data
     * @throws Exception
     */
    protected function configure($data = array()){

        /** @var bMenu__view $bMenu__view - template instance */
        $bMenu__view = $this->getInstance('bMenu__view');

        /** @var bPanel__model $bPanel__model */
        $bPanel__model = $this->getInstance('bPanel__model');


        $bMenu__view->set("tableName", $this->_tableName);
        $bMenu__view->set("formName", $this->_formName);

        // Save in menu view blocks list
        $blocks = $bPanel__model->getBlocks();
        $bMenu__view->set("blocks", $blocks);

        // Set template for menu view
        if ($template = $bPanel__model->getTemplate()) $bMenu__view->setTemplate($template);

    }


    public function indexAction(){

        /** @var  bMenu__model $bMenu__model */
        $bMenu__model  = $this->getInstance('bMenu__model');

        /** @var  bMenu__view $bMenu__view */
        $bMenu__view   = $this->getInstance('bMenu__view');

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

        $number = $tableData['page']['number'];
        $rows   = $tableData['page']['rows'];
        $list   = $bMenu__model->getList($number, $rows);
        $count  = $bMenu__model->getCount();

        $bMenu__view->set('list', $list);
        $bMenu__view->set('number', $number);
        $bMenu__view->set('rows', $rows);
        $bMenu__view->set('count', $count);

        switch($this->getView()){
            case "listJson":
                $bMenu__view->recordsJson();
                break;

            default:
                $bMenu__view->records();
                break;
        }

        return $bMenu__view->generate();
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