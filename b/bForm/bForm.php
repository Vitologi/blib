<?php
defined('_BLIB') or die;

class bForm extends bBlib{
	
	private $meta = null;
	private $query = array();
	
	protected function inputSelf(){
		$this->version = '1.0.0';
	}
	
	protected function input($data, $caller){
		if(!$caller)return;
		
		$this->name = ($data['name']?$data['name']:$this->generateName());
		$this->caller = $caller;
		
		$this->setQuery($data['query']);
		$this->setMeta($data['meta']);
		
		$this->blockTunnel = get_class($caller);
		
		$data['caller'] = $this->blockTunnel;
		$this->_setSession($this->name, $data);
	}
	
	public function output(){
		if($this->caller)return array('bForm'=>$this);
	}
	
	private function getQuery(){
	
		$Q = $this->query;
		return $Q;
	}
	
	private function generateName($length = 8){
		$chars = 'abdefhiknrstyzABDEFGHKNQRSTYZ23456789';
		$numChars = strlen ($chars);
		$string = '';
		for ($i = 0; $i < $length; $i++) {
			$string .= substr ($chars, rand (1, $numChars) - 1, 1);
		}
		return $string;
	}
	
	public function setMeta($data){
		$tunnel = $this->getTunnel();
		
		$Q = $this->query;
		
		if(!$page['count']){
			$result = $this->caller->_query($Q);
			$page['count']= $result->rowCount();
		}
		
		$this->meta = $data;
		$this->meta['page'] = $page;
		
	}
	
	public function getMeta(){
		return $this->meta;
	}
	
	public function setQuery($data){
		$this->query = $data;
	}
	
	public function getForm(){
		$Q = $this->getQuery();
		$result = $this->caller->_query($Q);
		return array('block'=>__class__, 'tunnel'=>$this->blockTunnel, 'name'=>$this->name, 'meta'=>$this->getMeta(), 'content'=>$result->fetchAll(PDO::FETCH_ASSOC));
	}

	public function _getForm($data = array(), $caller = null){
		if($caller)return $caller->bForm->getForm($data);
		return $this->getForm();
	}
	
}



/*

{
    "block": "bForm",
    "mods": {
        "style": "default"
    },
    "ajax": true,
    "processor": "bLK",
    "tag": "form",
    "attrs": {
        "method": "POST",
        "action": "/downloads/"
    },
    "content": [
        {
            "elem": "message"
        },
        {
            "elem": "label",
            "name": "label",
            "content": "label"
        },
        {
            "elem": "text",
            "name": "text[]",
            "content": "example"
        },
        {
            "elem": "label",
            "name": "label",
            "content": "label"
        },
        {
            "elem": "password",
            "name": "password",
            "content": "example"
        },
        {
            "elem": "label",
            "name": "label",
            "content": "label"
        },
        {
            "elem": "checkbox",
            "name": "checkbox"
        },
        {
            "elem": "label",
            "name": "label",
            "content": "label"
        },
        {
            "elem": "radio",
            "name": "radio",
            "content": "radio1"
        },
        {
            "elem": "label",
            "name": "label",
            "content": "label"
        },
        {
            "elem": "radio",
            "name": "radio",
            "content": "radio2"
        },
        {
            "elem": "label",
            "name": "label",
            "content": "label"
        },
        {
            "elem": "radio",
            "name": "radio"
        },
        {
            "elem": "label",
            "name": "label",
            "content": "label"
        },
        {
            "elem": "file",
            "name": "file",
            "content": "example"
        },
        {
            "elem": "hidden",
            "name": "hidden",
            "content": "example"
        },
        {
            "elem": "textarea",
            "name": "textarea",
            "content": "example"
        },
        {
            "elem": "select",
            "name": "select",
            "content": [
                {
                    "elem": "option",
                    "value": 1,
                    "content": "example"
                },
                {
                    "elem": "option",
                    "value": 2,
                    "content": "example"
                }
            ]
        },
        {
            "elem": "submit",
            "name": "submit",
            "content": "example"
        },
        {
            "elem": "reset",
            "name": "reset",
            "content": "example"
        },
        {
            "elem": "button",
            "name": "button",
            "content": "example"
        },
        {
            "elem": "image",
            "name": "image",
            "content": "example"
        }
    ]
}


*/