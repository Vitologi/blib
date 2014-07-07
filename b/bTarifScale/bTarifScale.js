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
					if(i == '0'){item.first = groups[key].length;}
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
						{'elem':'submit', 'content':'отправить'}
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
			'onSetMode':{
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
			temp.push({'tag':'td', 'content':[{'tag':'span', 'content':data.content.name},{'elem':'checker', 'content':data.content}]});
			temp.push({'tag':'td', 'content':data.content.cost});
			temp.push({'tag':'td', 'content':data.content.description});
			
			this.template = blib.clone(this.template);
			this.template.content = temp;
		},
		//template
		{
			'tag':'tr'
		},
		//actions
		{
				
		}
	);
	
	blib.build.define(
		{'block':'bTarifScale', 'elem':'checker'},
		function(data){
			this.chousen = false;
			this.template = blib.clone(this.template);
		},
		//template
		{
			'tag':'input',
			'attrs':{'type':'checkbox'}
		},
		//actions
		{
			'onclick':function(e){
				var self = e.blib;
				self.chousen = !self.chousen;
			}		
		}
	);
	
	blib.build.define(
		{'block':'bTarifScale', 'elem':'regdata'},
		function(data){
			this.template = blib.clone(this.template);
			this.template.content = [
				{'elem':'regHeader', 'content':"Для оформления заявки укажите персональные данные, чтобы мы могли связаться с Вами."},
				{'elem':'regField', 'tag':'input', 'attrs':{'type':'text', 'name':'name', 'placeholder':"Имя", 'value':"Имя"}},
				{'elem':'regField', 'tag':'input', 'attrs':{'type':'text', 'name':'email', 'placeholder':"Электронная почта", 'value':"Электронная почта"}},
				{'elem':'regField', 'tag':'input', 'attrs':{'type':'text', 'name':'phone', 'placeholder':"Телефон", 'value':"Телефон"}},
				{'elem':'regField', 'tag':'input', 'attrs':{'type':'text', 'name':'passport', 'placeholder':"Паспорт", 'value':"Паспорт"}},
				{'elem':'regField', 'tag':'textarea', 'attrs':{'name':'passport_issued', 'placeholder':"Дата выдачи паспорта"}, 'content':"Дата выдачи паспорта"},
				{'elem':'regField', 'tag':'textarea', 'attrs':{'name':'address', 'placeholder':"Адрес"}, 'content':"Адрес"}
			];
		},
		false,
		//actions
		{
			'onSetMode':{
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
		{'block':'bTarifScale', 'elem':'submit'},
		function(data){
			this.template.content = data.content;
		},
		false,
		//actions
		{
			'onclick':function(e){
				console.log('отправляем нахрен', e.blib.block.children.bTarifScale__checker);
			}		
		}
	);

})(window);
