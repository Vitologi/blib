<?php
defined('_BLIB') or die;

class bPayonline extends bBlib{
	
	//коды ошибок
	private $_error = array(
		'ok'         => array('code'=>0, 'message'=>"Payment data obtained"),
		'amountEmpty'=> array('code'=>1, 'message'=>"Undisclosed payment"),
		'amountFmt'  => array('code'=>2, 'message'=>"Invalid format of the payment amount"),
		'amountSmall'=> array('code'=>3, 'message'=>"Invalid (less than the minimum allowed) payment"),
		'amountBig'  => array('code'=>4, 'message'=>"Invalid (exceeds the maximum) amount. of payment"),
		'currency'   => array('code'=>5, 'message'=>"Invalid currency of payment"),
		'orderid'    => array('code'=>6, 'message'=>"Unknown payment code")
	);
	
	protected function inputSelf(){
		$this->version = '1.0.0';
		$this->parents = array('bUser');
	}
	
    /**
     * Прием пользовательских параметров
     * 
     * @param {array}   data           - массив данных
     * @param {number}      data.amount    - сумма платежа
     * @param {bBlib}   caller         - обьект, вызвавший метод
     */
	protected function input($data, $caller){
		$this->caller = $caller;
        $this->action = isset($data['action'])?$data['action']:null;    //действия для контроллера
        $this->amount = isset($data['amount'])?$data['amount']:0;       //указанная пользователем сумма платежа
	}
	
    // контроллер
	public function output(){
        $_this = $this;
		if($_this->caller)return;
        
        $_this->clientId    = $_this->bUser->id;    // идентификатор пользователя
        $answer             = array();              // пустой массив ответа
        
		switch($this->action){
            case "getSecureData":

                //делаем через хук для возможности переопределения настроек
                $_this->hook('initializeData', array());
                
                $answer = $_this->getSecureData();
                
                break;
        }
        
		header('Content-Type: application/json; charset=UTF-8');
		echo json_encode($answer);
		exit;
	}
	
    /**
     * Инициализация и определение переменных (все переменные должны присутствовать)
     */
	protected function initializeData(){
        $_this = $this;
        
		//базовые настройки
		$_this->url                 = "https://secure.payonlinesystem.com/ru/payment/select/";      // Переход пользователя с сайта торгово-сервисного предприятия на сервер PayOnline System может быть выполнен на один из следующих адресов: Форма выбора платежного инструмента: https://secure.payonlinesystem.com/ru/payment/select/
		$_this->merchantId          = null;                                                         // Идентификатор, который вы получаете при активации. Обязательный параметр. Целое число (Integer).
		$_this->privateSecurityKey  = null;                                                         // Приватный секретный ключ
		$_this->minAmount           = 100;                                                          // Минимальная сумма
		$_this->maxAmount           = 50000;                                                        // Максимальная сумма
		$_this->fmtAmount           = "/^\d+(\.\d+)?$/";                                            // Формат суммы
        $_this->availableCurrencies = array("RUB");                                                 // Формат суммы
        
        
        //генерируемые настройки
        $_this->currency            = "RUB";                                                        // валюта
        $_this->validUntil          = gmdate("Y-m-d H:i:s", (time() + (30 * 60)));                  // дата просрочки
        $_this->returnUrl           = "https://login.amtelcom.ru/?option=payonline_success";        // адрес куда перейдет при удачном платеже
        $_this->failUrl             = "https://login.amtelcom.ru/?option=payonline_fail";           // адрес куда перейдет при провале
        $_this->orderId             = null;                                                         // идентификатор запроса на оплату 
        $_this->securityKey         = $_this->getSecurityKey();                                     // генерация публичного ключа
        
        //$_this->local['amount']     = $_this->getAmount();                                          // сериализация указанной суммы
    }
	
	/**
     * Округление суммы платежа
     */
	public function getAmount($amount = null) {
		$amount = ($amount === null)?$this->amount:$amount;
        return number_format(round(floatval(str_replace(",", ".", $amount)), 2), 2, '.', '');
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
     * @return {string} - string of security key
     */
	public function getSecurityKey() {
        return strtolower(md5(sprintf("MerchantId=%s&OrderId=%s&Amount=%s&Currency=%s&ValidUntil=%s&PrivateSecurityKey=%s", 
            $this->merchantId, $this->orderId, $this->getAmount(), $this->currency, $this->validUntil, $this->privateSecurityKey)));
	}
	
    
    /**
     * Проверка на правильность заполнения данных
     *
     * @return {number} - код ошибки (если без ошибок то код 0)
     */
	public function checkParameters() {
        if( !preg_match($this->fmtAmount, $this->amount) )               return $this->_error['amountFmt'];      // не соответствует формату
        if( $this->amount <= 0 )                                        return $this->_error['amountEmpty'];    // пустая сумма платежа
        if( empty($this->orderId) )                                     return $this->_error['orderid'];        // пустой номер заказа
        if( !in_array($this->currency, $this->availableCurrencies) )    return $this->_error['currency'];       // валюты нет среди дефолтных        
        if( floatval($this->getAmount()) < $this->minAmount )           return $this->_error['amountSmall'];    // меньше минимальной
        if( floatval($this->getAmount()) > $this->maxAmount )           return $this->_error['amountBig'];      // больше максимальной
        return $this->_error['ok'];
	}
	
    
	public function getSecureData() {
        $status = $this->checkParameters();
		if( $status['code'] !== $this->_error['ok']['code'] )return array('status'=>$status);
		
        return array(
            "status"        => $status,
            "url"           => $this->url,
            "merchantId"	=> $this->merchantId,
            "orderId"		=> $this->orderId,
            "amount"		=> $this->getAmount(),
            "currency"		=> $this->currency,
            "validUntil"	=> $this->validUntil,
            "securityKey"	=> $this->securityKey,
            "returnUrl"		=> $this->returnUrl,
            "failUrl"		=> $this->failUrl,
            "clientId"		=> $this->clientId
        );
        
   	}
	
}