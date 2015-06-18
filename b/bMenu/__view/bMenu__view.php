<?php
defined('_BLIB') or die;

/**
 * Class bMenu__view - view collection for bMenu
 * Included patterns:
 * 		MVC	- localise view into one class
 */
class bMenu__view extends bView{

    protected function input(){
        $this->setInstance('converter', 'bConverter');
        $this->setInstance('helper', 'bPanel__view');
    }

    /**
     * Store bMenu default carcase
     *
     * @param array $list   - menu list
     * @param null $id      - root menu item
     * @return array        - blib structure (bom)
     */
    public function index($list = array(), $id = null){

        return array(
            'block'   => 'bMenu',
            'id'      => $id,
            'content' => $list
        );

    }

    /**
     * Store modified blib structure (for create horizontal menu)
     *
     * @param array $list   - menu list
     * @param null $id      - root menu item
     * @return array        - blib structure (bom)
     */
    public function horizontal($list = array(), $id = null){

        return array(
            'block'   => 'bMenu',
            'id'      => $id,
            'content' => $list,
            'mods'    => array(
                'position' => "horizontal",
                'default'  => true
            )
        );

    }

    /**
     * Output json serialised menu bom
     *
     * @param array $list   - menu list
     * @param null $id      - root menu item
     * @return mixed|null   - output json string and exit from application
     */
    public function indexJson($list = array(), $id = null){

        $temp = $this->index($list, $id);

        /** @var bConverter__instance $_converter */
        $_converter = $this->getInstance('converter');

        $_converter->setData($temp)->setFormat('array')->convertTo('json');
        return $_converter->output();
    }

    /**
     * View for add menu item (use bPanel view)
     *
     * @throws Exception
     */
    public function addPanel(){

        /** @var bPanel__view $_helper */
        $_helper = $this->getInstance('helper');

        $message = $this->get('message', "Добавление записи");
        $errors  = $this->get('errors', array());
        $item = $this->get('item',array());

        /** Buttons */
        $add    = $_helper->buildButton('Сохранить', array('menuForm'), array(
            'action' => 'add'
        ), 'bMenu__bPanel');
        $cancel = $_helper->buildButton('Отмена', array(), array(), 'bMenu__bPanel');

        /** Other interface elements */
        $blocks = $_helper->buildBlocks($this->get('blocks',array()));
        $message  = array($_helper->buildError($message));
        $tools = $_helper->buildTools(array($add, $cancel));

        foreach($errors as $key => $value){
            $message[] = $_helper->buildError($value);
        }

        $message = $_helper->buildCollection($message);

        $this->setPosition('"{1}"', $blocks);
        $this->setPosition('"{2}"', $message);
        $this->setPosition('"{3}"', $tools);
        $this->setPosition('"{4}"', $this->showForm($item));

    }

    /**
     * View for edit menu item (use bPanel view)
     *
     * @throws Exception
     */
    public function editPanel(){

        /** @var bPanel__view $_helper */
        $_helper = $this->getInstance('helper');

        $item = $this->get('item',array());
        $message = $this->get('message', "Редактирование записи");
        $errors  = $this->get('errors', array());

        /** Buttons */
        $edit   = $_helper->buildButton('Редактировать', array('menuForm'), array(
            'action' => 'edit'
        ), 'bMenu__bPanel');
        $cancel = $_helper->buildButton('Отмена', array(), array(), 'bMenu__bPanel');

        /** Other interface elements */
        $blocks = $_helper->buildBlocks($this->get('blocks',array()));
        $tools = $_helper->buildTools(array($edit, $cancel));

        $message  = array($_helper->buildError($message));
        foreach($errors as $key => $value){
            $message[] = $_helper->buildError($value);
        }
        $message = $_helper->buildCollection($message);

        $this->setPosition('"{1}"', $blocks);
        $this->setPosition('"{2}"', $message);
        $this->setPosition('"{3}"', $tools);
        $this->setPosition('"{4}"', $this->showForm($item));

    }

    /**
     * View for show menu list (use bPanel view)
     *
     * @throws Exception
     */
    public function records(){

        /** @var bPanel__view $_helper */
        $_helper = $this->getInstance('helper');

        $message = $this->get('message', "Панель редактирования пунктов меню");
        $errors  = $this->get('errors', array());


        /** Buttons */
        $add    = $_helper->buildButton('Добавить', array(), array(
            'action'=>'form',
            'view'   => 'add'
        ), 'bMenu__bPanel');
        $edit   = $_helper->buildButton('Редактировать', array('menuTable'), array(
            'action'=>'form',
            'view'   => 'edit'
        ), 'bMenu__bPanel');
        $delete = $_helper->buildButton('Удалить', array('menuTable'), array(
            'action' => 'delete',
            'view'   => 'index'
        ), 'bMenu__bPanel');

        /** Other interface elements */
        $blocks     = $_helper->buildBlocks($this->get('blocks',array()));
        $location   = $_helper->buildLocation('bMenu__bPanel');

        $message  = array($_helper->buildError($message));
        foreach($errors as $key => $value){
            $message[] = $_helper->buildError($value);
        }
        $message = $_helper->buildCollection($message);


        $collection = $_helper->buildCollection(array($message, $location));
        $tools      = $_helper->buildTools(array($add, $edit, $delete));

        $this->setPosition('"{1}"', $blocks);
        $this->setPosition('"{2}"', $collection);
        $this->setPosition('"{3}"', $tools);
        $this->setPosition('"{4}"', $this->showList());

    }

    /**
     *  View for send data (menu item list) like json
     */
    public function recordsJson(){
        /** @var bConverter__instance $bConverter */
        $_converter = $this->getInstance('converter');

        $list = $this->get('list', array());

        $_converter->setData($list)->setFormat('array')->convertTo('json');
        $_converter->output();
        exit;
    }

    /**
     * View for menu item form
     *
     * @param array $item   - menu item data
     * @return array        - blib structure (bom)
     */
    public function showForm($item = array()){


        return array(
            'block' => 'bForm',
            'mods'  => array('style' => 'default'),
            'meta'  => array(
                'name'   => 'menuForm',
                'tunnel' => array(),
                'method' => "POST",
                'action' => "/",
                'ajax'   => true,
                'select' => array(
                    'list' => $this->get('selectList', array())
                ),
                'items'  => array($item)
            ),
            'content'	=> array(

                array('elem'=>'hidden', 'name'=>'id'),

                array('elem'=>'label', 'content'=>'Группируем с пунктом', 'attrs'=>array('title'=>'К какой группе меню принадлежит')),
                array('elem'=>'select', 'name'=>'menu', 'select'=>'list', 'key'=>'id', 'show'=>array('id', 'name')),

                array('elem'=>'label', 'content'=>'Название', 'attrs'=>array('title'=>'Название пункта меню')),
                array('elem'=>'text', 'name'=>'name'),

                array('elem'=>'label', 'content'=>'Ссылка', 'attrs'=>array('title'=>'На что ссылается пункт (если пусто, значит меню-контейнер)')),
                array('elem'=>'text', 'name'=>'link'),

                array('elem'=>'label', 'content'=>'Настройки', 'attrs'=>array('title'=>'Номер конфигурационных настроек')),
                array('elem'=>'select', 'name'=>'bconfig_id'),

                array('elem'=>'label', 'content'=>'В какой пункт вложен', 'attrs'=>array('title'=>'Корневой пункт меню (куда будет вложен)')),
                array('elem'=>'selectplus', 'name'=>'bmenu_id', 'select'=>'list', 'key'=>'id', 'show'=>array('name', 'link'))
            )
        );

    }

    /**
     * View for menu item list (like table)
     *
     * @return array    - blib structure (bom)
     */
    protected function showList(){

        $tableName = $this->get('tableName', 'menuTable');
        $list = $this->get('list', array());
        $number =  $this->get('number', 0);
        $rows =  $this->get('rows', 0);
        $count = $this->get('count', 0);

        return array(
            'block'   => 'bTable',
            'mods'    => array('style' => 'default'),
            'meta'    => array(
                'name'    => $tableName,
                'tunnel' => array(
                    'bMenu__bPanel' => array(
                        'action' => 'index',
                        'view'   => 'listJson'
                    )

                ),
                'position'  => array('id', 'menu', 'name', 'link', 'bconfig_id', 'bmenu_id'),
                'keys'      => array('id', 'bmenu_id'),
                'page'      => array(
                    'handler'   => 'bMenu__bPanel',
                    'number'    => $number,
                    'rows'      => $rows,
                    'count'     => $count,
                    'paginator' => 10,
                ),
                'fields'    => array(
                    'id'         => array('type' => 'hidden',   'title' => 'Ключевое поле',   'note' => 'Подле для хранения ключа таблицы'),
                    'menu'       => array('type' => 'text',     'title' => 'Номер меню',      'note' => 'К какому меню принадлежит', 'mods' => array('align' => 'center')),
                    'name'       => array('type' => 'text',     'title' => 'Название',        'note' => 'Название пункта меню'),
                    'link'       => array('type' => 'text',     'title' => 'Ссылка',          'note' => 'На что ссылается пункт (если пусто, значит меню-контейнер)'),
                    'bconfig_id' => array('type' => 'text',     'title' => 'Настройки',       'note' => 'Номер конфигурационных настроек'),
                    'bmenu_id'   => array('type' => 'text',     'title' => 'Родитель',        'note' => 'Корневой пункт меню (куда будет вложен)', 'mods' => array('align' => 'center'))
                )
            ),
            'content' => $list
        );

    }

}