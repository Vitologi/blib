<?php
defined('_BLIB') or die;

/**
 * Class bSession__database    - strategy for store session in database
 * Included patterns:
 *        Data Mapper - interface for interaction to database
 *        singleton    - one work object
 */
class bSession__database extends bBlib
{

    /** @var null|static $_instance - Singleton instance */
    private static $_instance = null;
    private        $_id       = null;
    private        $_expire   = 0;                              // Cookie lifetime
    private        $_data     = array();                        // Local session storage
    protected      $_traits   = array('bSystem', 'bDataMapper');

    /**
     * Overload object factory for Singleton
     *
     * @return bConfig|null|static
     */
    static public function create()
    {
        if (self::$_instance === null) self::$_instance = parent::create(func_get_args());
        return self::$_instance;
    }

    /**
     * Configure php session
     *
     * @throws Exception
     */
    protected function input()
    {

        $this->_path        = '/';
        $this->_domen       = '';
        $this->_secure      = false;
        $this->_httponly    = false;

        $this->updateSession($this->_expire);
    }

    public function output()
    {
        return $this;
    }

    /**
     * Get session from inner data
     *
     * @param string $selector - session selector
     * @return mixed[]            - local session
     */
    public function getSession($selector = null)
    {
        return $this->_navigate($this->_data, $selector);
    }

    /**
     * Save configurations to database
     *
     * @param string $selector - config selector
     * @param mixed $value - config value
     * @void                    - save configurations to database
     */
    public function setSession($selector = null, $value = null)
    {
        $this->_data = $this->_navigate($this->_data, $selector, $value);

        /** @var bDataMapper__instance $bDataMapper - session Data Mapper */
        $bDataMapper = $this->getInstance('bDataMapper');
        $empty = $bDataMapper->getItem();

        $empty->id = $this->_id;
        $empty->value = $this->_data;

        $bDataMapper->save($empty);
    }

    public function clearSession()
    {
        $this->_id = null;
        $this->_data = array();
        setcookie('bSession', $this->_id, time() - 3600, $this->_path, $this->_domen, $this->_secure, $this->_httponly);
        unset($_COOKIE['bSession']);
    }


    public function updateSession($expire = null)
    {

        /** @var bDataMapper__instance $bDataMapper - session Data Mapper */
        $bDataMapper = $this->getInstance('bDataMapper');



        if (isset($_COOKIE) && isset($_COOKIE['bSession'])) {

            $session = $bDataMapper->getItem($_COOKIE['bSession'], $expire);

            if ($session->id) {
                $this->_id = $session->id;
                $this->_data = $session->value;
            }
        }

        if (!$this->_id) {

            $empty = $bDataMapper->getItem();
            $empty->value = $this->_data;

            $bDataMapper->save($empty);

            $this->_id = $empty->id;

        }


        $cookieExpire = ($expire) ? time() + $expire : $expire;

        if (
            ($expire !== null)
            && (setcookie('bSession', $this->_id, $cookieExpire, $this->_path, $this->_domen, $this->_secure, $this->_httponly) === false)
            && ($this->_expire = $expire)
        ) {
            throw new Exception('Can`t set php session cookie lifetime');
        }


    }


}