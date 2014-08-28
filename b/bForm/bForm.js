(function(){
	
	//standart function for element
	var standartProto = {
			'prepare':function(data){
				if(!data.attrs)data.attrs={};
				this.name = data.name || data.attrs.name;
				if(!data.attrs)data.attrs = {};
				data.attrs.name = data.name;
				
				
				this.block.fields[data.name] =  this;
				this.template = data;
			},
			'prepareValue':function(){
				if(!this.template.attrs)this.template.attrs={};
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
			if(!data.attrs)data.attrs={};
			var config = {
				'ajax':data.ajax || data.attrs.ajax || false,
				'processor':data.processor || false,
				'action':data.action || data.attrs.action || '',
				'method':data.method || data.attrs.method || 'POST'
			}
			
			this.config = config;
			this.fields = {};
			this.template = data;
			
			if(!config.ajax){
				this.template.attrs.action = config.action;
				this.template.attrs.method = config.method;
			}
			
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
					
					if(temp instanceof HTMLInputElement && temp.files && temp.files.length){
						if(!result['_files'])result['_files'] = [];
						result['_files'].push(temp);
					}else if(temp !== undefined){
						result[i] = temp;
					}
				}
				
				return result;
			},
			'submit':function(handler){
				var config = this.config,
					ajax = config.ajax,
					processor = config.processor,
					action = config.action,
					method = config.method,
					request, files;
				
				if(ajax){					
					request = this.serialize();
					if(request['_files']){
						files = request['_files'];
						delete request['_files'];
					}
					
					if(processor)request.blib = processor;

					blib.ajax({
						'url':action,
						'type':method,
						'data':request,
						'dataType':'json',
						'files':files,
						'success':handler
					});

				}
				else{
					try{
						this.dom.constructor.prototype.submit.call(this.dom);
					}catch(e){Blib.exception("[bForm] Submit error.",e);}
				}

			}
		})
	);
	
	//MESSAGE
	blib.build.define(
		{'block':'bForm', 'elem':'message'},
		function(data){
			this.reset = data.reset || 5000;
			this.timer = null;
			this.template = data;
		},
		false,
		{
			'setText':function(text){
				var self = this,
					text = text;
				self.template.content = text;
				self.dom.innerHTML = text;

				if(self.reset){
					window.clearTimeout(self.timer);
					self.timer = window.setTimeout(function(){
						self.template.content = '';
						self.dom.innerHTML = '';
					},self.reset);
				}
			}
		}
	);
	
	//HIDDEN
	blib.build.define(
		{'block':'bForm', 'elem':'hidden'},
		function(data){		
			this.prepare(data);
			this.prepareValue();
		},
		{'tag':"input", 'attrs':{'type':"hidden"}},
		new standartFunction({})
	);
	
	//LABEL
	blib.build.define(
		{'block':'bForm', 'elem':'label'},
		function(data){		
			this.prepare(data);
		},
		{'tag':"label"},
		new standartFunction({})
	);
	
	//TEXT
	blib.build.define(
		{'block':'bForm', 'elem':'text'},
		function(data){		
			this.prepare(data);
			this.prepareValue();
		},
		{'tag':"input", 'attrs':{'type':"text"}},
		new standartFunction({})
	);
	
	//SUBMIT
	blib.build.define(
		{'block':'bForm', 'elem':'submit'},
		function(data){
			this.template = data;
			this.prepareValue();
		},
		{'tag':"input", 'attrs':{'type':"submit"}},
		new standartFunction({
			'onclick':function(e){
				var self = this,
					block = self.block,
					config = block.config,
					handler = function(data){
						if(data.reset)block.dom.reset();
						if(data.message)block.children.bForm__message[0].setText(data.message);
					};
					
				if(!config.ajax)return true;
				
				if(e.preventDefault){
					e.preventDefault();
				}else{
					e.returnValue = false;
				}
									
				block.submit(handler);
			}
		})
	);
	
	//RESET
	blib.build.define(
		{'block':'bForm', 'elem':'reset'},
		function(data){
			this.template = data;
			this.prepareValue();			
		},
		{'tag':"input", 'attrs':{'type':"reset"}},
		new standartFunction({})
	);
	
	//BUTTON
	blib.build.define(
		{'block':'bForm', 'elem':'button'},
		function(data){
			this.template = data;
			this.prepareValue();			
		},
		{'tag':"input", 'attrs':{'type':"button"}},
		new standartFunction({})
	);
	
	//IMAGE
	blib.build.define(
		{'block':'bForm', 'elem':'image'},
		function(data){
			this.template = data;
			this.prepareValue();
		},
		{'tag':"input", 'attrs':{'type':"image"}},
		new standartFunction({})
	);
	
	//PASSWORD
	blib.build.define(
		{'block':'bForm', 'elem':'password'},
		function(data){		
			this.prepare(data);
			this.prepareValue();
		},
		{'tag':"input", 'attrs':{'type':"password"}},
		new standartFunction({})
	);
	
	//CHECKBOX
	blib.build.define(
		{'block':'bForm', 'elem':'checkbox'},
		function(data){		
			this.prepare(data);
			this.prepareValue();
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
			
			this.prepareValue();
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
				return (this.dom.files.length?this.dom:undefined);
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
