<?php
defined('_BLIB') or die;

/**
 * Created by PhpStorm.
 * User: morozov
 * Date: 25.03.2015
 * Time: 16:42
 *
 * Class bPanel__view - concrete view for bPanel block
 * Included patterns:
 * 		MVC	- localise view into one class
 */
class bPanel__view extends bView{

    /**
     * Format blocks array into blib structure. Can get blocks property from stored data.
     *
     * @param null|array $blocks    - blocks list
     * @return array                - blib structure as array
     */
    final public function buildBlocks($blocks = null){
        $blocks = is_array($blocks)?$blocks:$this->get("blocks", array());

        return array(
            'block'=>'bPanel',
            'elem'=>'blocks',
            'content'=>$blocks
        );

    }

    /**
     * Format error mesatge into blib structure.
     *
     * @param string $text  - text message
     * @return array        - blib structure as array
     */
    final public function buildError($text = "Module is not defined"){

        return array(
            'block'=>'bPanel',
            'elem'=>'error',
            'content'=>$text
        );

    }

    /**
     * Create button (some command object) from setted data
     *
     * @param string $content   - button name
     * @param array $uphold     - array of dependent element (if element status have error, than button throw exception)
     * @param array $tunnel     - some data what send if button is clicked
     * @param null $controller  - block-handler
     * @return array            - blib structure as array
     */
    final public function buildButton($content='', $uphold=array(), $tunnel=array(), $controller = null){

        return array(
            'block'      => 'bPanel',
            'elem'       => 'button',
            'controller' => $controller,
            'tunnel'     => $tunnel,
            'uphold'     => $uphold,
            'content'    => $content
        );

    }

    /**
     * Wrap elements into empty element
     *
     * @param array $collection - element array
     * @return array            - element wrapper
     */
    final public function buildCollection($collection = array()){

        return array(
            'content' => $collection
        );

    }

    /**
     * Wrapper for admin panel tools
     *
     * @param array $collection - element array
     * @return array            - element wrapper
     */
    final public function buildTools($collection = array()){

        return array(
            'block'   => 'bPanel',
            'elem'    => 'tools',
            'content' => $collection
        );

    }

    /**
     * Create element what change location in frontend
     *
     * @param null|string   $controller - redirect controller name
     * @param array $command            - command for that controller (like action=X view=Y)
     * @return array                    - blib structure as array
     */
    final public function buildLocation($controller = null, $command = array()){

        return array(
            'block'   => 'bLink',
            'elem'    => 'location',
            'visible' => true,
            'content' => array(
                'bPanel'=>array(
                    'controller'=>$controller
                ),
                $controller => $command
            )
        );

    }

    /**
     * Combiner for index view bPanel controller
     *
     * @throws Exception
     */
    public function indexPanel(){
        $this->setPosition('"{1}"', $this->buildBlocks());
        $this->setPosition('"{2}"', $this->buildError('Place for tools'));
        $this->setPosition('"{3}"', $this->buildError('Place for operation'));
        $this->setPosition('"{4}"', $this->buildError('Place for content'));
    }
}