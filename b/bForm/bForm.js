(function(){
	
	//standart function for element
	var standartProto = {
			'prepare':function(data){
				if(!data.attrs)data.attrs={};
				this.name = data.name || data.attrs.name;
				data.attrs.name = data.name;
				
				
				this.block.fields[data.name] =  this;
				this.template = data;
			},
			'prepareValue':function(){
				var query = this.block.meta.query[0];
				if(!this.template.content && query)this.template.content = query[this.name];
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
			var meta = data.meta || {'processor':false,	'tunnel':false,	'ajax':true, 'action':'', 'method':'POST', 'select':{}, 'query':{}};
			this.meta = {
				'processor':meta.processor || false,
				'tunnel':meta.tunnel || false,
				'ajax':('ajax' in meta)?meta.ajax:true,
				'action':meta.action || data.attrs.action || '',
				'method':meta.method || data.attrs.method || 'POST',
				'select':meta.select || {},
				'query':meta.query || {}
			}

			this.fields = {};
			this.name = data.name;
			this.template.mods = data.mods;
			this.template.attrs = data.attrs;
			this.template.content = data.content;
			
			if(!meta.ajax){
				this.template.attrs.action = meta.action;
				this.template.attrs.method = meta.method;
			}
			
			//console.log(this.name, this);
			this._static('bLink').setUphold(this.name, this);
			
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
				var meta = this.meta,
					ajax = meta.ajax,
					processor = meta.processor,
					action = meta.action,
					method = meta.method,
					request, files, temp;
				
				if(ajax){					
					request = this.serialize();
					if(request['_files']){
						files = request['_files'];
						delete request['_files'];
					}
					
					if(meta.tunnel){
						temp = {};
						temp[meta.tunnel] = request;
						if(files)temp._files = files;
						blib.tunnel(temp);
						request = {};
						files = undefined;
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

			},
			'_getStatus':function(){				
				var status = this._getStatusList(this.fields),
					tunnel = this.meta.tunnel,
					serialized, temp = {};
				
				if(!status.error){
					serialized = this.serialize();
					
					if('_files'in serialized){
						blib.tunnel({'_files':serialized['_files']});
						serialized['_files'] = null;
					}
					
					temp[tunnel] = {'items':[serialized]};
					blib.tunnel(temp);					
				}
				
				return status;
				
			},
			'_onRemove':[
				function(){
					this._static('bLink').dropUphold(this.name, this);
				}
			]
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
			this.template.content = data.content;
			this.prepareValue();
		},
		{'tag':"input", 'attrs':{'type':"submit"}},
		new standartFunction({
			'onclick':function(e){
				var self = this,
					block = self.block,
					meta = block.meta,
					handler = function(data){
						if(data.reset)block.dom.reset();
						if(data.message)block.children.bForm__message[0].setText(data.message);
					};
					
				if(!meta.ajax)return true;
				
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
			this.template.content = data.content;
			this.prepareValue();			
		},
		{'tag':"input", 'attrs':{'type':"reset"}},
		new standartFunction({})
	);
	
	//BUTTON
	blib.build.define(
		{'block':'bForm', 'elem':'button'},
		function(data){
			this.template.content = data.content;
			this.prepareValue();			
		},
		{'tag':"input", 'attrs':{'type':"button"}},
		new standartFunction({})
	);
	
	//IMAGE
	blib.build.define(
		{'block':'bForm', 'elem':'image'},
		function(data){
			this.template.content = data.content;
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
			this.prepare(data);
			this.options = {};	
			this.select = data.select;
			this.show = data.show;
			this.key = data.key;
			
			var meta = this.block.meta,
				options = meta.select[data.select],
				defValue = (meta.query && meta.query[0])?meta.query[0][this.name]:null,
				optionText;
			
			this.defValue = defValue;
			if(!this.template.content)this.template.content = [];
			
			for(i in options){
				optionText = '';
				for(j in this.show){
					optionText += options[i][this.show[j]]+' . ';
				}
				this.template.content.push({'block':'bForm', 'elem':'option', 'value':options[i][this.key], 'content':optionText});
			}
			
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
			this.value = data.value;
			this.parent.options[data.value] = this;
			this.template = {
				'content':data.content,
				'attrs':{
					'value':data.value,
					'selected':(this.parent.defValue == this.value ?'selected':false)
				}
			}
		},
		{'tag':"option"},
		new standartFunction({})
	);
	
})();
