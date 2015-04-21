<?php
defined('_BLIB') or die;

/**
 * Class bPayonline__model - model for generate and work with menu
 * Included patterns:
 * 		MVC	- localise business logic into one class
 */
class bPayonline__model extends bBlib{

    protected $_url                 = null;     // Переход пользователя с сайта торгово-сервисного предприятия на сервер PayOnline System может быть выполнен на один из следующих адресов: Форма выбора платежного инструмента: https://secure.payonlinesystem.com/ru/payment/select/
    protected $_merchantId          = null;     // Идентификатор, который вы получаете при активации. Обязательный параметр. Целое число (Integer).
    protected $_privateSecurityKey  = null;     // Приватный секретный ключ
    protected $_minAmount           = null;     // Минимальная сумма
    protected $_maxAmount           = null;     // Максимальная сумма
    protected $_fmtAmount           = null;     // Формат суммы
    protected $_availableCurrencies = null;     // Формат суммы
    protected $_currency            = null;     // валюта
    protected $_validUntil          = null;     // дата просрочки
    protected $_returnUrl           = null;     // адрес куда перейдет при удачном платеже
    protected $_failUrl             = null;     // адрес куда перейдет при провале
    protected $_orderId             = null;     // идентификатор запроса на оплату
    protected $_securityKey         = null;     // генерация публичного ключа
    protected $_amount              = null;     // сумма
    protected $_clientId            = null;     // код клиента


    /** @var false|array $_error    - detected errors list or false  */
    protected $_error = false;

    /** @var null|bPayonline__bDataMapper  $_bDataMapper - save datamapper in property for quick access */
    private   $_bDataMapper = null;

    /**
     * @var array   - included traits
     */
    protected $_traits      = array('bPayonline__bDataMapper', 'bUser', 'bRbac');


    /**
     *  Set config and data mapper
     */
    protected function input(){

        /** @var bPayonline__bDataMapper $bDataMapper - data mapper instance */
        $this->_bDataMapper = $this->getInstance('bPayonline__bDataMapper');
    }

    /**
     * Return them self to parent
     *
     * @return $this
     */
    public function output(){
        return ($this->_parent instanceof bBlib)?$this:null;
    }


    public function configure(){

        /** @var bUser $bUser */
        $bUser = $this->getInstance('bUser');

        $this->_url                 = $this->getVars('url', "https://secure.payonlinesystem.com/ru/payment/select/");
        $this->_merchantId          = $this->getVars('merchantId', null);
        $this->_privateSecurityKey  = $this->getVars('privateSecurityKey', null);
        $this->_minAmount           = $this->getVars('minAmount', 100);
        $this->_maxAmount           = $this->getVars('maxAmount', 50000);
        $this->_fmtAmount           = $this->getVars('fmtAmount', "/^\d+(\.\d+)?$/");
        $this->_availableCurrencies = $this->getVars('availableCurrencies', array("RUB"));
        $this->_currency            = $this->getVars('currency', "RUB");
        $this->_returnUrl           = $this->getVars('returnUrl', null);
        $this->_failUrl             = $this->getVars('failUrl', null);

        $this->_amount              = $this->getVars('amount', 0);
        $this->_clientId            = $this->getVars('clientId', $bUser->getId());
        $this->_validUntil          = $this->getVars('validUntil', gmdate("Y-m-d H:i:s", (time() + (30 * 60))));

        $this->_orderId             = $this->getVars('orderId', null);
        $this->_securityKey         = $this->getVars('securityKey', $this->getSecurityKey());

        $this->checkParameters();

    }

    /**
     * Параметр «SecurityKey» вычисляется хеш-функцией md5 от строки
     * !Порядок следования параметров и регистр символов важен!
     * Вместо выражений в фигурных скобках подставляются значения параметров.
     * Значение параметров MerchantId и PrivateSecurityKey вы получаете при активации.
     *
     * @example
     * MerchantId={MerchantId}&OrderId={OrderId}&Amount={Amount}&Currency={Currency}&ValidUntil={ValidUntil}&PrivateSecurityKey={PrivateSecurityKey} – в случае, если параметр ValidUntil указан или
     * MerchantId={MerchantId}&OrderId={OrderId}&Amount={Amount}&Currency={Currency}&PrivateSecurityKey={PrivateSecurityKey} – в случае, если параметр ValidUntil не указан.
     * @return string - string of security key
     */
    public function getSecurityKey() {
        return strtolower(md5(sprintf("MerchantId=%s&OrderId=%s&Amount=%s&Currency=%s&ValidUntil=%s&PrivateSecurityKey=%s",
            $this->_merchantId, $this->_orderId, $this->convertAmount($this->_amount), $this->_currency, $this->_validUntil, $this->_privateSecurityKey)));
    }


    /**
     * Округление суммы платежа
     *
     * @param null|int $amount  - payment amount
     * @return string
     */
    public function convertAmount($amount = null) {
        if($amount === null)$amount = $this->_amount;
        return number_format(round(floatval(str_replace(",", ".", $amount)), 2), 2, '.', '');
    }

    public function getError(){
        return $this->_error;
    }


    public function getSecureData() {

        $this->configure();

        if( $this->_error )return array();

        return array(
            "url"           => $this->_url,
            "merchantId"	=> $this->_merchantId,
            "orderId"		=> $this->_orderId,
            "amount"		=> $this->convertAmount($this->_amount),
            "currency"		=> $this->_currency,
            "validUntil"	=> $this->_validUntil,
            "securityKey"	=> $this->_securityKey,
            "returnUrl"		=> $this->_returnUrl,
            "failUrl"		=> $this->_failUrl,
            "clientId"		=> $this->_clientId
        );

    }

    public function checkParameters(){

        $error = array();
        $hasError = 0;

        if (!preg_match($this->_fmtAmount, $this->_amount)) {
            $hasError++;
            $error[] = "bPayonline.error.errorFormat";
        }else{

            if ($this->_amount < 0) {
                $hasError++;
                $error[] = "bPayonline.error.errorEmptyAmout";
            }

            if (floatval($this->_amount) < $this->_minAmount) {
                $hasError++;
                $error[] = "bPayonline.error.errorMinAmount";
            }

            if (floatval($this->_amount) > $this->_maxAmount) {
                $hasError++;
                $error[] = "bPayonline.error.errorMaxAmount";
            }
        }

        if ($this->_orderId === null){
            $hasError++;
            $error[] = "bPayonline.error.errorOrderId";
        }

        if (!in_array($this->_currency, $this->_availableCurrencies)) {
            $hasError++;
            $error[] = "bPayonline.error.errorCurrency";
        }

        return $this->_error = ($hasError>0?$error:false);

    }



}