<?php
defined('_BLIB') or die;

/**
 * Class bConfig__database 	- strategy for store configuration in database
 * Included patterns:
 * 		Data Mapper - interface for interaction to data base
 */
class bSession__database extends bBlib{

	protected $_traits  = array('bSystem','bDataMapper');

	/** @var mixed[]	- local config storage */
	private $_config = array();

	protected function input(){
		if(isset($_COOKIE) && isset($_COOKIE['bSession'])){
			$Q = array(
				'select'=>array(
					'bsession'=>array('value')
				),
				'where'=>array(
					'bsession'=>array('id'=>$_COOKIE['bSession'])
				)
			);
			if(!$result = $this->_query($Q)){ throw new Exception('Can`t get session information.'); }
			if($result->rowCount()){
				$row = $result->fetch();
				$this->id = $_COOKIE['bSession'];
				bSession::$data = (array)json_decode($row['value'], true);
				bSession::$singleton = $this;
			}
		}

		if(!$this->id){
			$this->id = md5(microtime(true).$_SERVER['REMOTE_ADDR']);
			$Q = array(
				'insert'=>array(
					'bsession'=>array('id'=>$this->id, 'value'=>json_encode(bSession::$data))
				)
			);
			if(!$this->_query($Q)){
				throw new Exception('Can`t insert session information.');
			}
			bSession::$singleton = $this;
			$expire = ($this->expire)?(time()+$this->expire):0;
			setcookie('bSession', $this->id, $expire, $this->path, $this->domen, $this->secure, $this->httponly);
		}
	}

	public function output(){
		return $this;
	}

	/**
	 * Get config from block`s file named like bBlock__bConfig.php
	 *
	 * @param string $selector	- config selector
	 * @return mixed[]			- local configs
	 */
	public function getConfig($selector = ''){

		// Return stored configuration if it already exists
		if($temp = $this->_navigate($this->_config, $selector))return $temp;

		/** @var bConfig__database__bDataMapper $dataMapper	- config data mapper */
		$dataMapper = $this->_getDataMapper();


		/** Recursive(string based) grab configuration from database
		 * For example:
		 * bBlock.item.subItem
		 *  - means that cycle get configuration for
		 * bBlock , bBlock.item , bBlock.item.subItem
		 *  - store it in local configuration array $_config
		 *  - and return bBlock.item.subItem config
		 */
		$path = explode('.', $selector);
		$currentPath ='';
		for($i=0; $i<count($path); $i++){
			$currentPath .= $path[$i];

			if(!$this->_navigate($this->_config, $currentPath)) {

				// Merge configurations with parents lines
				$config = $dataMapper->mergeItem($dataMapper->getItem($currentPath));

				// Concat with local config
				$this->_config = $this->_navigate($this->_config, $currentPath, $config->value);

			}

			$currentPath .= '.';
		}

		return $this->_navigate($this->_config, $selector);
	}

	/**
	 * Save configurations to database
	 *
	 * @param string $selector	- config selector
	 * @param mixed $value 		- config value
	 * @void 					- save configurations to database
	 */
	public function setConfig($selector = '', $value = null){

		/** @var bDataMapper__instance $dataMapper	- config data mapper */
		$dataMapper = $this->_getDataMapper();

		$config = $dataMapper->getItem($selector);

		if(is_array($value) and is_array($config->value)){
			$config->value = array_replace_recursive($config->value,$value);
		}else{
			$config->value = $value;
		}

		$this->_config = $this->_navigate($this->_config, $selector, $config->value);

		$config->name = $selector;
		$dataMapper->save($config);
	}

	public static function _clearSession(bBlib $caller){

		if(!($caller instanceof bBlib))throw new Exception('Not correct arguments given.');

		return $caller->getInstance(__CLASS__)->clearSession(get_class($caller));

		$bSession = $caller->local['bSession'];
		bSession::$data = null;
		bSession::$singleton = null;

		switch($bSession->sessionType){
			case "database":
				unset($_COOKIE[__CLASS__]);
				break;

			default:
				unset($_COOKIE[session_name()]);
				session_destroy();
				session_start();
				break;

		}


	}

	public static function _updateSession($expire, bBlib $caller = null){

		$bSession = $caller->local['bSession'];
		bBlib::extend($data, '0', array());
		$bSession->local = bBlib::extend($bSession->local, $data[0]);

		switch($bSession->sessionType){
			case "database":

				$expire = ($bSession->expire)?(time()+$bSession->expire):0;
				setcookie('bSession', $bSession->id, $expire, $bSession->path, $bSession->domen, $bSession->secure, $bSession->httponly);
				break;

			default:

				$session = $_SESSION;
				unset($_COOKIE[session_name()]);
				session_destroy();


				if(!ini_set('session.save_path', $bSession->storePath)){
					throw new Exception('Canot set php session save path');
				};

				ini_set ('session.cookie_lifetime', $bSession->expire);

				if(!session_start()){throw new Exception('Cannot use php session.');};

				$_SESSION = $session;
				bSession::$data = $_SESSION[__class__];
				bSession::$singleton = $bSession;
				break;

		}


	}

	private function setSession($name ='', $value = null){

		$strategy = $this->getInstance($this->_strategy);

		$strategy->setSession($name, $value);

		switch($this->sessionType){
			case "database":
				$Q = array(
					'update'=>array(
						'bsession'=>array('value'=>json_encode(bSession::$data))
					),
					'where' => array(
						'bsession'=>array('id'=>$this->id)
					)
				);

				if(!$this->_query($Q)){
					throw new Exception('Can`t insert session information.');
				}

				break;

			default:
				if ( !isset($_SESSION) && !session_id() ) {
					if(!session_start()){throw new Exception('Cannot use php session.');};
				}

				$_SESSION[__class__] = bSession::$data;
				break;
		}
	}
}