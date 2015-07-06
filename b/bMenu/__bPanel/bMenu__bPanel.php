<?php
defined('_BLIB') or die;


/**
 * Class bMenu__bPanel - concrete controller for bMenu block
 * Included patterns:
 * 		MVC	- use input property like action and view for create code logic
 */
class bMenu__bPanel extends bController{

    protected $_tableName = 'menuTable';
    protected $_formName  = 'menuForm';
    protected $_items = array();

    /**
     * Get tunnel data for business logic, set view template and configure it
     *
     * @param array $data   - provided data
     * @throws Exception
     */
    public function initialize($data = array()){

        $this->setInstance('model', 'bMenu__model');
        $this->setInstance('validator', 'bMenu__validator');
        /** @var bPanel__model $_helper */
        $_helper = $this->getInstance('helper', 'bPanel__model');
        /** @var bMenu__view $_view */
        $_view = $this->getInstance('view', 'bMenu__view');

        $_view->set("tableName", $this->_tableName);
        $_view->set("formName", $this->_formName);

        // Save in menu view blocks list
        $blocks = $_helper->getBlocks('__bPanel');
        $_view->set("blocks", $blocks);

        // Set template for menu view
        if ($template = $_helper->getTemplate()) $_view->setTemplate($template);

    }


    public function indexAction(){

        $_model = $this->getInstance('model');
        $_view  = $this->getInstance('view');

        $table     = $this->get('bTable');
        $tableData = (isset($table[$this->_tableName]) ? $table[$this->_tableName] : array());
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
        $list   = $_model->getList($number, $rows);
        $count  = $_model->getCount();

        $_view->set('list', $list);
        $_view->set('number', $number);
        $_view->set('rows', $rows);
        $_view->set('count', $count);

        switch($this->getView()){
            case "listJson":
                $_view->recordsJson();
                break;

            default:
                $_view->records();
                break;
        }

        return $_view->generate();
    }


    public function formAction(){

        $_model = $this->getInstance('model');
        $_view  = $this->getInstance('view');

        /** get item from table */
        $table     = $this->get('bTable');
        $tableData = (isset($table[$this->_tableName]) ? $table[$this->_tableName] : array());
        $tableData = array_replace_recursive(array(
            'page'  => array(
                'number' => 0,
                'rows'   => 5,
                'count'  => 0
            ),
            'items' => array()
        ), $tableData);
        $tableItem = (isset($tableData['items'][0]) ? $tableData['items'][0] : array());

        /** get item from form */
        $form     = $this->get('bForm');
        $formData = (isset($form[$this->_formName]) ? $form[$this->_formName] : array());
        $formItem = $formData;

        $item = array_replace_recursive($tableItem, $formItem);

        if (isset($item['id'])) {
            $item = $_model->getItem($item['id']);
        }

        $_view->set('item', $item);

        $selectList = $_model->getSmallList();
        $_view->set('selectList', $selectList);

        switch ($this->getView()) {
            case "add":
                $_view->addPanel();
                break;

            case "edit":
                $_view->editPanel();
                break;
        }

        return $_view->generate();
    }

    public function addAction(){

        $_model     = $this->getInstance('model');
        $_view      = $this->getInstance('view');
        $_validator = $this->getInstance('validator');

        /** get item from form */
        $form     = $this->get('bForm');
        $formData = (isset($form[$this->_formName]) ? $form[$this->_formName] : array());
        $item     = $formData;

        // validate input data
        if ($errors = $_validator->validateAddForm($item)) {

            // change view
            $this->setView('add');

            // set view(template) variable
            $_view->set('message', "Данные не соответствуют формату.");
            $_view->set('errors', $errors);
            $_view->set('item', $item);

            return $this->formAction();

        } else {
            $result = $_model->addItem($item);

            $this->setView('index');

            $_view->set('message', "Запись успешно добавлена.");

            return $this->indexAction();
        }

    }


    public function editAction(){

        $_model     = $this->getInstance('model');
        $_view      = $this->getInstance('view');
        $_validator = $this->getInstance('validator');

        $form     = $this->get('bForm');
        $formData = (isset($form[$this->_formName]) ? $form[$this->_formName] : array());
        $item     = $formData;

        if ($errors = $_validator->validateEditForm($item)) {
            $_view->set('message', "Данные не соответствуют формату.");
            $_view->set('errors', $errors);
            $_view->set('item', $item);
        } else {
            $result = $_model->editItem($item);
            $_view->set('message', "Запись успешно отредактирована.");
            $_view->set('item', $result);
        }

        $this->setView('edit');

        return $this->formAction();
    }

    public function deleteAction(){

        $_model     = $this->getInstance('model');
        $_view      = $this->getInstance('view');
        $_validator = $this->getInstance('validator');

        $table     = $this->get('bTable');
        $tableData = (isset($table[$this->_tableName]) ? $table[$this->_tableName] : array());
        $tableData = array_replace_recursive(array(
            'page'  => array(
                'number' => 0,
                'rows'   => 5,
                'count'  => 0
            ),
            'items' => array()
        ), $tableData);

        $itemNums = $_model->serializeItemNumbers($tableData['items']);

        if ($errors = $_validator->validateDelete($itemNums)) {
            $_view->set('message', "Данные не соответствуют формату.");
            $_view->set('errors', $errors);
        } else {
            $_model->deleteItem($itemNums);
            $_view->set('message', "Строки успешно удалены.");
        }

        $this->setView('index');

        return $this->indexAction();
    }

}