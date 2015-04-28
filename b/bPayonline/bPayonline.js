(function(){

    // Локализация кодов ошибок
    blib.localize({
        'bPayonline':{
            'success':"Данные успешно получены, ожидайте перенаправления на сайт платежного агрегатора",
            'error':{
                'errorEmptyAmout':"Не указана сумма платежа",
                'errorFormat':"Недопустимый формат суммы платежа",
                'errorMinAmount':"Недопустимая (менее минимально допустимой) сумма платежа",
                'errorMaxAmount':"Недопустимая (превышающая максимально допустимую) сумма платежа",
                'errorCurrency':"Недопустимая валюта платежа",
                'errorOrderId':"Не указан код платежа"
            },
            '__submit':'Оплатить',
            '__amount':'Введите сумму платежа'
        }
    });
    
    // Основной блок взаимодействия с серверным апи
	blib.build.define(
		{'block':'bPayonline'},
		function(data){
            var _this       = this;
            
            _this.url       = false;    // адрес куда будет осуществлен переход для оплаты
            _this.paysystem = false;    // элемент, выбранной пользователем, платежной системы
            _this.detail    = false;    // элемент для отображения информации от выбранной системы
            _this.message   = false;    // элемент для отображения информации от сервера
            _this.amount    = false;    // поле ввода суммы платежа
            
            data.mods.init = true;
			_this.template  = data;
		},
        false,
        {   
            '_onSetMode':{
                'init':function(){
                    var _this = this;
                    _this.callback();
                }
            },
            'setUrl':function(url){
                var _this = this;
                _this.url = url;
            },
            'setDetail':function(data){
                var _this = this;
                if(_this.detail)return _this.detail.setDetail(data);
            },
            'setMessage':function(data){
                var _this = this;
                if(_this.message)return _this.message.setMessage(data);
                alert(data);
            },
            'getAmount':function(){
                var _this = this;
                return (_this.amount)?_this.amount.getAmount():0;
            },
            'getSecureData':function(){
                var _this = this,
                    paysystem = _this.paysystem,
                    amount = _this.getAmount();
                
                blib.ajax({
                    'dataType':'json',
                    'data':{'blib':'bPayonline', 'action':'getSecureData', 'amount':amount},
                    'success':function(data){
                        if(data.errors){
                            _this.setMessage(data.errors);
                        }else{
                            _this.setMessage('bPayonline.success');
                            _this.redirectToPayonline(data);
                        }
                    }
                });
            },
            'createInput':function(name, value){
                return {'tag':'input', 'attrs':{'type':'hidden', 'name':name, 'value':value}};
            },
            'redirectToPayonline':function(data){
                var _this = this,
                    url = _this.url || data.url,
                    body = document.getElementsByTagName('body')[0],
                    i, form, temp;
                
                form = {
                    'tag':'form',
                    'attrs':{
                        'method':'POST',
                        'action':url,
                        'target':'_blank'
                    },
                    'content':[]
                };
                
                for(i in data){
                    form.content.push(_this.createInput(i, data[i]));
                }
                
                temp = blib.build(form);

                body.appendChild(temp);
                temp.submit();

                window.setTimeout(function(){
                    body.removeChild(temp);
                },10000);

            },
            'callback':function(){}
        }
	);
	
    // Элемент отображения сообщений сервера
    blib.build.define(
		{'block':'bPayonline', 'elem':'message'},
		function(data){
            var _this = this;
            _this.block.message = _this;
			_this.template = {};
		},
        false,
        {
            'setMessage':function(data){
                var _this = this,
                    temp = [], i, len;

                if(blib.is(data, 'array')){
                    for(i=0,len = data.length; i<len;i++){
                        temp.push({'content':data[i]});
                    }
                }else{
                    temp = data;
                }

                _this._removeChildren();
                _this._append({'content':temp});
            }
        }
	);
    
    // Элемент отображения вспомогательной информации
    blib.build.define(
		{'block':'bPayonline', 'elem':'detail'},
		function(data){
            var _this = this;
            
            _this.block.detail = _this;
			_this.template = {};
		},
        false,
        {
            'setDetail':function(data){
                var _this = this;
                if(!blib.is(data, 'object'))data = {'content':data};
                _this._removeChildren();
                _this._append(data);
            },
            'getDetail':function(){
                var _this = this;
                return _this.dom.innerHTML;
            }
        }
	);
    
    // Поле ввода суммы
    blib.build.define(
		{'block':'bPayonline', 'elem':'amount'},
		function(data){
            var _this = this;
            _this.block.amount = _this;
			_this.template = {};
		},
        {'tag':'input', 'attrs':{'type':'text', 'placeholder':blib.localize('bPayonline.__amount')}},
        {
            'onkeyup':function(){
                var _this = this,
                    block = _this.block,
                    amount = _this.getAmount();
                
                // проверка на корректность введенных данных
            },
            'getAmount':function(){
                var _this = this;
                return _this.dom.value;
            }
        }
	);
    
    // Кнопка отправки
    blib.build.define(
		{'block':'bPayonline', 'elem':'submit'},
		function(data){
            var _this = this;
			_this.template = {};
		},
        {'tag':'input', 'attrs':{'type':'button', 'value':blib.localize('bPayonline.__submit')}},
        {
            'onclick':function(){
                var _this = this,
                    block = _this.block;
                
                block.getSecureData();
            }
        }
	);
    
    
})(window);(function(){
	
    // Конфигурации для различных систем оплаты
    var config = {
        'urlOk':'/b/bPayonline/__paysystem/_type/bPayonline__paysystemOk.png',
        'webMoney':{
            'url'       : 'https://secure.payonlinesystem.com/ru/payment/select/paymaster/',
            'message'   : 'bPayonline.__paysystem.message.webMoney',
            'image'     : '/b/bPayonline/__paysystem/_type/bPayonline__paysystem_type_webMoney.png',
            'detail'    : 'bPayonline__instruction_type_webMoney'
        },
        'yandexMoney':{
            'url'       : 'https://secure.payonlinesystem.com/ru/payment/select/yandexmoney/',
            'message'   : 'bPayonline.__paysystem.message.yandexMoney',
            'image'     : '/b/bPayonline/__paysystem/_type/bPayonline__paysystem_type_yandexMoney.png',
            'detail'    : 'bPayonline__instruction_type_yandexMoney'
        },
        'cards':{
            'url'       : 'https://secure.payonlinesystem.com/ru/payment/',
            'message'   : 'bPayonline.__paysystem.message.cards',
            'image'     : '/b/bPayonline/__paysystem/_type/bPayonline__paysystem_type_cards.png',
            'detail'    : 'bPayonline__instruction_type_cards'
        },
        'qiwi':{
            'url'       : 'https://secure.payonlinesystem.com/ru/payment/select/qiwi/',
            'message'   : 'bPayonline.__paysystem.message.qiwi',
            'image'     : '/b/bPayonline/__paysystem/_type/bPayonline__paysystem_type_qiwi.png',
            'detail'    : 'bPayonline__instruction_type_qiwi'
        },
        'mobileCard':{
            'url'       : 'https://secure.payonlinesystem.com/ru/payment/',
            'message'   : 'bPayonline.__paysystem.message.mobileCard',
            'image'     : '/b/bPayonline/__paysystem/_type/bPayonline__paysystem_type_mobileCard.png',
            'detail'    : 'bPayonline__instruction_type_mobileCard'
        }
    };
    
    //save config into global config and protect them
	Blib.config("bPayonline", config);
	Blib.config("_private.bPayonline", true);
    
    // Локализация названий систем оплат
    blib.localize({
        'bPayonline':{
            '__paysystem':{
                'message':{
                    'default':"Выбор системы оплаты",
                    'amtelCard':"Оплата скретч-картой Амтел",
                    'webMoney':"Оплата через систему «WebMoney»",
                    'yandexMoney':"Оплата через «Яндекс.Деньги»",
                    'cards':"Оплата банковской картой «VISA» или «MasterCard»",
                    'qiwi':"Оплата через «QIWI-Кошелек»",
                    'mobileCard':"Оплата через телефоны «BeeLine», «Megafon», «MTС»"
                }                
            }
        }
    });

        
    // Элемент системы оплаты
    blib.build.define(
		{'block':'bPayonline', 'elem':'paysystem'},
		function(data){
            var _this = this,
                block = _this.block,
                type = data.mods.type || 'default';
            
            _this.config = (type in _this.config)?_this.config[type]:_this.config['default'];
            
			this.template = {
                'content':[
                    {'elem':'paysystemOk', 'tag':'img', 'attrs':{'src':config.urlOk}},
                    {'elem':'paysystemImage', 'tag':'img', 'attrs':{'src':_this.config.image}}
                ]
            };
            
            block.callback = function(){
                block.children.bPayonline__paysystem[0].onclick();
            };
            
		},
        false,
        {
            'config':config || {},
            '_onSetMode':{
                'selected':function(){
                    var _this       = this,
                        block       = _this.block,
                        elements    = block.children.bPayonline__paysystem,
                        i;
                    
                    for(i in elements){
                        if(elements[i] == _this)continue;
                        elements[i]._setMode('selected', false);
                    }
                    
                    block.paysystem = this;
                    
                    blib.ajax({
                        'url':'/'+blib.path(_this.config.detail, 'html'),
                        'dataType':'html',
                        'success':function(data){
                            block.setDetail(data);
                        }
                    });
                    
                    block.setUrl(_this.config.url);
                }
            },
            'onmouseover':function(){
                var _this = this,
                    block = _this.block;
                block.setMessage(_this.config.message);
            },
            'onmouseout':function(){
                var _this = this,
                    block = _this.block;
                block.setMessage('');
            },
            'onclick':function(){
                var _this = this;                
                _this._setMode('selected', true);
            }
        }
	);
      
})(window);