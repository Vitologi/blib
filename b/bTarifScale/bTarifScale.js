(function(){
	
	var header = ["Услуги", "Тарифы", "Абонентск. плата руб.", "Состав тарифа"];
	
	blib.build.define(
		{'block':'bTarifScale'},
		function(data){

			var content = data.content,
				groups = {},
				temp;
			
			for(key in content){
				temp = content[key]['group'];
				if(!(temp in groups)){
					groups[temp]=[];
				}
				groups[temp].push(content[key]);
			}
			
			temp = [{'elem':'header', 'content':header}];
			
			for(key in groups){
				
				for(i in groups[key]){
					var item = {'elem':'item', 'content':groups[key][i]};
					if(i == '0'){item.first = groups[key].length+1;}
					temp.push(item);
				}
				
				temp.push({
					'elem':'groupDescription',
					'tag':'tr',
					'content':[
						{'tag':'td', 'attrs':{'colspan':header.length}, 'content':groups[key][0].groupDescription}
					]
				});
			}
			
			temp.push({
				'tag':'tr',
				'content':[
					{'tag':'td', 'attrs':{'colspan':header.length}, 'content':[
						{'elem':'regdata', 'content':'regdata'},
						{'elem':'submit', 'content':[{'tag':'span','content':'Подать заявку на подключение услуг'}, {'elem':'price', 'content':'0 руб.'}]}						
					]}
				]
			});
			
			this.template = blib.clone(this.template);
			this.template.mods = data.mods;
			this.template.header = header;
			this.template.content = temp;
		},
		{
			'tag':'table'
		},
		{
			'_onSetMode':{
				'position':{
					'horizontal':function(){
						console.log('position -> horizontal');
					},
					'vertical':function(){
						console.log('position -> vertical');
					}
				}
			}
		}
	);
	
	blib.build.define(
		{'block':'bTarifScale', 'elem':'header'},
		function(data){
			var temp = [];
			
			for(key in data.content){
				temp.push({'tag':'td', 'content':data.content[key]});
			}
			
			this.template = blib.clone(this.template);
			this.template.content = temp;
		},
		{
			'tag':'tr'
		}
	);
	
	blib.build.define(
		{'block':'bTarifScale', 'elem':'item'},
		function(data){
			var temp = [];
			
			if(data.first){temp.push({'elem':'groupName', 'tag':'td', 'attrs':{'rowspan':data.first}, 'content':data.content.group});}
			temp.push({'elem': 'tarName', 'tag':'td', 'content':[{'elem':'checker', 'content':data.content},{'elem':'tarText','tag':'span', 'content':data.content.name}]});
			temp.push({'tag':'td', 'content':data.content.cost});
			temp.push({'elem': 'description', 'tag':'td', 'content':data.content.description});
			
			this.template = blib.clone(this.template);
			this.template.content = temp;
		},
		//template
		{
			'tag':'tr'
		}
	);
	
	blib.build.define(
		{'block':'bTarifScale', 'elem':'checker'},
		function(data){
			this.id = data.content.id;
			this.chousen = false;
			this.template = blib.clone(this.template);
			this.price = data.content.cost;
		},
		//template
		{
			'tag':'input',
			'attrs':{'type':'checkbox'}
		},
		//actions
		{	
		
			'reset':function(){
				if(!this.chousen)return;
				
				this.dom.checked = false;
				this.chousen = false;
				this.block.children.bTarifScale__price[0].setPrice(-this.price);
				
			},
			
			'onclick':function(e){
				var self = this,
					block = self.block;
				self.chousen = !self.chousen;
				
				if(self.chousen){
					block.children.bTarifScale__regdata[0]._setMode('closed', false);
					block.children.bTarifScale__price[0].setPrice(self.price);
				}else{
					block.children.bTarifScale__price[0].setPrice(-self.price);
				}
			}
			
			
		}
	);
	
	blib.build.define(
		{'block':'bTarifScale', 'elem':'regdata'},
		function(data){
			this.template = blib.clone(this.template);
			this.template.mods = {'closed':true};
			this.template.content = [
				{'elem':'regHeader', 'content':"Для оформления заявки укажите персональные данные, чтобы мы могли связаться с Вами."},
				{'elem':'regField', 'tag':'input', 'attrs':{'type':'text', 'name':'name', 'placeholder':"Имя", 'value':"Имя"}},
				{'elem':'regField', 'tag':'input', 'attrs':{'type':'text', 'name':'email', 'placeholder':"Электронная почта", 'value':"Электронная почта"}},
				{'elem':'regField', 'tag':'input', 'attrs':{'type':'text', 'name':'phone', 'placeholder':"Телефон", 'value':"Телефон"}},
				{'elem':'regField', 'tag':'input', 'attrs':{'type':'text', 'name':'passport', 'placeholder':"Паспорт", 'value':"Паспорт"}},
				{'elem':'regField', 'tag':'textarea', 'attrs':{'name':'passport_issued', 'placeholder':"Дата выдачи паспорта"}, 'content':"Дата выдачи паспорта"},
				{'elem':'regField', 'tag':'textarea', 'attrs':{'name':'address', 'placeholder':"Адрес"}, 'content':"Адрес"}
			];
		}
	);
	
	blib.build.define(
		{'block':'bTarifScale', 'elem':'regField'},
		function(data){
			this.template = blib.clone(this.template);
			this.template.mods = data.mods;
			this.template.tag = data.tag;
			this.template.content = data.content;
			this.template.attrs = data.attrs;
			
			this.virgin = true;
		},
		false,
		//actions
		{
			'onfocus':function(e){
				console.log(e, this);
				var self = this;
				if(self.virgin){
					self.dom.value = '';
					self.virgin = false;
				}
			}
		}
	);
	
	blib.build.define(
		{'block':'bTarifScale', 'elem':'price'},
		function(data){
			this.template = blib.clone(this.template);
			this.template.content = data.content;
			this.price = 0;
		},
		false,
		{
			'setPrice':function(price){
				this.price += +price;
				this.dom.innerHTML = this.price+' руб.'
			}
		}
	);
	
	blib.build.define(
		{'block':'bTarifScale', 'elem':'submit'},
		function(data){
			this.template.content = data.content;
		},
		false,
		//actions
		{
			'onclick':function(e){
				var self = this,
					tarifs = self.block.children.bTarifScale__checker,
					regFields = self.block.children.bTarifScale__regField,
					request = {
						'options':[]
					},
					temp;
					
				for(key in tarifs){
					if(tarifs[key].chousen)request.options.push(tarifs[key].id);
				}
				
				if(!request.options.length)return;
				
				for(key in regFields){
					temp = regFields[key].dom;
					request[temp.name] = temp.value;
				}
				
				for(key in tarifs){
					tarifs[key].reset();
				}
				
				request.blib = 'bTarifScale';
				
				blib.ajax({
					'data':request,
					'dataType':'json',
					'success':function(data){
						console.log(data);
					}
				});			
			}		
		}
	);

})(window);
