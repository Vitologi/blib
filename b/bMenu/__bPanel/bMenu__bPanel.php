<?php
defined('_BLIB') or die;


/**
 * Class bMenu__bPanel - concrete controller for bMenu block
 * Included patterns:
 * 		MVC	- use input property like action and view for create code logic
 */
class bMenu__bPanel extends bBlib{

    /**
     * @var array
     */
    protected $_traits    = array('bSystem', 'bRequest', 'bMenu__model', 'bMenu__view', 'bPanel__model', 'bMenu__validator');

    /**
     * @var array $_mvc     - default request data
     */
    protected $_mvc       = array(
        "action"     => "index",
        "view"       => "index",
        "items"      => array() // item array (need for CRUD operation)
    );


    /**
     * @var array $_tableData   - default table data (for paginator)
     */
    protected $_tableData = array(
        'page' => array(
            'number' => 0,  // curent page No
            'rows'   => 5,  // rows on page
            'count'  => 0   // rows count
        )
    );

    /**
     * Get tunnel data for business logic, set view template and configure it
     *
     * @param array $data   - provided data
     * @throws Exception
     */
    protected function input($data = array()){

        /** @var bRequest $bRequest - request instance */
        $bRequest = $this->getInstance('bRequest');

        /** @var bMenu__view $bMenu__view - template instance */
        $bMenu__view = $this->getInstance('bMenu__view');

        /** @var bPanel__model $bPanel__model */
        $bPanel__model = $this->getInstance('bPanel__model');

        // Set request params
        $tunnel = $bRequest->getTunnel(__CLASS__);
        $this->_mvc = array_replace($this->_mvc, $tunnel, $data);

        // Set table data
        $tableName = 'menuTable';
        $tunnel = $bRequest->getTunnel('bTable');
        if (isset($tunnel[$tableName])) {
            $this->_tableData = array_replace_recursive($this->_tableData, $tunnel[$tableName]);
        }
        $bMenu__view->set("tableName", $tableName);

        // Save in menu view blocks list
        $blocks = $bPanel__model->getBlocks();
        $bMenu__view->set("blocks", $blocks);

        // Set template for menu view
        if ($template = $bPanel__model->getTemplate()) $bMenu__view->setTemplate($template);

    }

    /**
     * Provide controller function
     *
     * @return array|string
     * @throws Exception
     */
    public function output(){

        /** @var  bMenu__model $bMenu__model */
        $bMenu__model  = $this->getInstance('bMenu__model');

        /** @var  bMenu__view $bMenu__view */
        $bMenu__view   = $this->getInstance('bMenu__view');

        /** @var bMenu__validator $bMenu__validator */
        $bMenu__validator = $this->getInstance('bMenu__validator');

        $mvc        = $this->_mvc;
        $items      = $mvc["items"];
        $item       = isset($items[0])?$items[0]:null;

        switch($mvc['action']){

            case "index":
            case "list":
                break;

            // add menu item
            case "add":

                // validate input data
                if($errors = $bMenu__validator->validateAddForm($item)){

                    // change view
                    $mvc['view'] = "add";

                    // set view(template) variable
                    $bMenu__view->set('message', "Данные не соответствуют формату.");
                    $bMenu__view->set('errors', $errors);
                    $bMenu__view->set('item', $item);
                }else{
                    $result = $bMenu__model->addItem($item);
                    $mvc['view'] = "index";
                    $bMenu__view->set('message', "Запись успешно добавлена.");
                    $bMenu__view->set('item', $result);
                }

                break;

            case "edit":

                if($errors = $bMenu__validator->validateEditForm($item)){
                    $mvc['view'] = "edit";
                    $bMenu__view->set('message', "Данные не соответствуют формату.");
                    $bMenu__view->set('errors', $errors);
                    $bMenu__view->set('item', $item);
                }else{
                    $mvc['view'] = "edit";
                    $result = $bMenu__model->editItem($item);
                    $bMenu__view->set('message', "Запись успешно отредактирована.");
                    $bMenu__view->set('item', $result);
                }

                break;

                case "delete":

                    $itemNums = $bMenu__model->serializeItemNumbers($items);

                    if($errors = $bMenu__validator->validateDelete($itemNums)){
                        $bMenu__view->set('message', "Данные не соответствуют формату.");
                        $bMenu__view->set('errors', $errors);
                    }else{
                        $bMenu__model->deleteItem($itemNums);
                        $bMenu__view->set('message', "Строки успешно удалены.");
                    }

                    $mvc['view'] = "listTable";

                    break;


            default:
                break;
        }


        switch($mvc['view']){

            case "index":
            case "listTable":
                // detect how much item need, configure view
                $page = $this->_tableData['page'];
                $number = $page['number'];
                $rows = $page['rows'];

                // get rows
                $list = $bMenu__model->getList($number, $rows);
                $bMenu__view->set('list', $list);
                $bMenu__view->set('number', $number);
                $bMenu__view->set('rows', $rows);

                $count = $bMenu__model->getCount();
                $bMenu__view->set('count', $count);
                $bMenu__view->set('message', "Панель редактирования пунктов меню.");

                // configure indexPanel view
                $bMenu__view->indexPanel();
                break;

            // get purify table data for ajax
            case "listJson":
                $page = $this->_tableData['page'];
                $number = $page['number'];
                $rows = $page['rows'];

                $list = $bMenu__model->getList($number, $rows);
                $bMenu__view->set('list', $list);
                $bMenu__view->set('number', $number);
                $bMenu__view->set('rows', $rows);

                $bMenu__view->listJson();
                break;

            // add form
            case "add":
                $selectList = $bMenu__model->getSmallList();
                $bMenu__view->set('selectList', $selectList);
                $bMenu__view->addPanel();
                break;

            // edit form
            case "edit":
                $selectList = $bMenu__model->getSmallList();

                if($item['id']){
                    $item = $bMenu__model->getItem($item['id']);
                    $bMenu__view->set('item', $item);
                }

                $bMenu__view->set('selectList', $selectList);
                $bMenu__view->editPanel();
                break;

            default:
                break;

        }

        // build final view
        return $bMenu__view->generate();

    }

}