(function(){
	
	//standart function for element
	var standartProto = {
			'prepare':function(data){
				this.name = data.name || data.attrs.name;
				if(!data.attrs)data.attrs = {};
				data.attrs.name = data.name;
				
				
				this.block.fields[data.name] =  this;
				this.template = data;
			},
			'prepareValue':function(){
				this.template.attrs.value = this.template.content;
				this.template.content = false;
			},
			'val':function(data){
				return (data)?this._attr('value',data):this.dom.value;
			}
		},
		standartFunction = function(obj){
			for(var key in obj)this[key]=obj[key];
		};
		
	standartFunction.prototype = standartProto;
	
	//FORM
	blib.build.define(
		{'block':'bForm'},
		function(data){
			var config = {
				'ajax':data.ajax || data.attrs.ajax || false,
				'processor':data.processor || false,
				'action':data.action || data.attrs.action || '',
				'method':data.method || data.attrs.method || 'POST'
			}
			
			this.config = config;
			this.fields = {};
			this.template = data;
		},
		{
			'tag':"form"
		},
		new standartFunction({
			'serialize':function(){
				var result = {},
					i, j, temp;
					
				for(i in this.fields){
					temp = undefined;
					if(blib.is(this.fields[i],"array")){
						
						for(j in this.fields[i]){
							if(this.fields[i][j].dom.checked){
								temp = this.fields[i][j].val();
								break;
							}
						}
						
					}else{
						temp = this.fields[i].val();
					}
					
					if(temp !== undefined)result[i] = temp;
				}
				
				return result;
			},
			'submit':function(handler){
				var config = this.config,
					ajax = config.ajax,
					processor = config.processor,
					action = config.action,
					method = config.method,
					request;
				
				if(ajax){					
					request = this.serialize();
					if(processor)request.blib = processor;
					
					blib.ajax({
						'url':action+'?blib='+processor,
						'type':method,
						'data':request,
						'success':handler
					});

				}else{
					this.dom.constructor.prototype.submit.call(this.dom);
				}
			}
		})
	);
	
	//MESSAGE
	blib.build.define(
		{'block':'bForm', 'elem':'message'},
		function(data){
			this.template = data;
		},
		false,
		{
			'setText':function(text){
				this.template.content = text;
				this.dom.innerHTML = text;
			}
		}
	);
	
	//HIDDEN
	blib.build.define(
		{'block':'bForm', 'elem':'hidden'},
		function(data){		
			this.prepare(data);
			this.prepareValue(data);
		},
		{'tag':"input", 'attrs':{'type':"hidden"}},
		new standartFunction({})
	);
	
	//TEXT
	blib.build.define(
		{'block':'bForm', 'elem':'text'},
		function(data){		
			this.prepare(data);
			this.prepareValue(data);
		},
		{'tag':"input", 'attrs':{'type':"text"}},
		new standartFunction({})
	);
	
	//SUBMIT
	blib.build.define(
		{'block':'bForm', 'elem':'submit'},
		function(data){
			this.template = data;
		},
		{'tag':"input", 'attrs':{'type':"submit"}},
		{
			'onclick':function(e){
				e.preventDefault();
				var self = this,
					block = self.block,
					handler = function(data){
						block.children.bForm__message[0]._append({"content":JSON.stringify(data)});
					};
					
				block.submit(handler);
			}
		}
	);
	
	//RESET
	blib.build.define(
		{'block':'bForm', 'elem':'reset'},
		function(data){
			this.template = data;			
		},
		{'tag':"input", 'attrs':{'type':"reset"}},
		{}
	);
	
	//BUTTON
	blib.build.define(
		{'block':'bForm', 'elem':'button'},
		function(data){
			this.template = data;			
		},
		{'tag':"input", 'attrs':{'type':"button"}},
		{}
	);
	
	//IMAGE
	blib.build.define(
		{'block':'bForm', 'elem':'image'},
		function(data){
			this.template = data;			
		},
		{'tag':"input", 'attrs':{'type':"image"}},
		{}
	);
	
	//PASSWORD
	blib.build.define(
		{'block':'bForm', 'elem':'password'},
		function(data){		
			this.prepare(data);
			this.prepareValue(data);
		},
		{'tag':"input", 'attrs':{'type':"password"}},
		new standartFunction({})
	);
	
	//CHECKBOX
	blib.build.define(
		{'block':'bForm', 'elem':'checkbox'},
		function(data){		
			this.prepare(data);
			this.prepareValue(data);
		},
		{'tag':"input", 'attrs':{'type':"checkbox"}},
		new standartFunction({
			'val':function(data){
				if(!data)return (this.dom.checked?this.dom.value:undefined);
				this.dom.checked=true;
			}
		})
	);
	
	//RADIO
	blib.build.define(
		{'block':'bForm', 'elem':'radio'},
		function(data){		
			this.name = data.name || data.attrs.name;
			if(!data.attrs)data.attrs = {};
			data.attrs.name = data.name;
			
			if(!this.block.fields[data.name])this.block.fields[data.name] = [];
			this.block.fields[data.name].push(this);
			
			this.template = data;
			
			this.prepareValue(data);
		},
		{'tag':"input", 'attrs':{'type':"radio"}},
		new standartFunction({
			'val':function(data){
				if(!data)return (this.dom.checked?this.dom.value:undefined);
				this.dom.checked=true;
			}
		})
	);
	
	//FILE 0_0
	blib.build.define(
		{'block':'bForm', 'elem':'file'},
		function(data){		
			this.prepare(data);
		},
		{'tag':"input", 'attrs':{'type':"file"}},
		new standartFunction({
			'val':function(data){
				return undefined;
			}
		})
	);
	
	//TEXTAREA
	blib.build.define(
		{'block':'bForm', 'elem':'textarea'},
		function(data){		
			this.prepare(data);
			
		},
		{'tag':"textarea"},
		new standartFunction({})
	);
	
	//SELECT
	blib.build.define(
		{'block':'bForm', 'elem':'select'},
		function(data){
			this.options = {};
			this.prepare(data);			
		},
		{'tag':"select"},
		new standartFunction({
			'val':function(data){
				for(var key in this.options){
					if(!this.options[key].dom.selected)continue;
					return this.options[key].val();
				};
			}
		})
	);
	
	blib.build.define(
		{'block':'bForm', 'elem':'option'},
		function(data){
			
			this.parent.options[data.value] = this;
			this.template = data;
			if(!this.template.attrs)this.template.attrs={};
			this.template.attrs.value = data.value;
		},
		{'tag':"option"},
		new standartFunction({})
	);
	
})();
