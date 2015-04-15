(function( Blib ){

	//standard function for element
	var ajaxSetup = Blib.ajaxSetup,
		extend = Blib.extend,
		config = {
			'request':{
				'bForm':{}
			},
			'files':false
		},
		standardProto = {
			'prepare':function(data){
				if(!data.attrs)data.attrs={};
				this.name = data.name || data.attrs.name;
				data.attrs.name = data.name;


				this.block.fields[data.name] =  this;
				this.template = data;
			},
			'prepareValue':function(){
				var items = this.block.meta.items[0];
				if(!this.template.content && items)this.template.content = items[this.name];
				this.template.attrs.value = this.template.content;
				this.template.content = false;
			},
			'prepareContent':function(){
				var items = this.block.meta.items[0];
				if(!this.template.content && items)this.template.content = items[this.name];
			},
			'val':function(data){
				return (data)?this._attr('value',data):this.dom.value;
			}
		},
		standardFunction = function(obj){

			for(var key in obj)this[key]=obj[key];
		};

	standardFunction.prototype = standardProto;

	//save config into global config and protect them
	Blib.config("bForm", config);
	Blib.config("_private.bForm", true);

	ajaxSetup({
		'beforeSend':function(){
			this.data = extend(true, {}, config.request, this.data);
			if(config.files)this.files = [].concat(config.files, this.files);
		}
	});

	//FORM
	blib.build.define(
		{'block':'bForm'},
		function(data){

			var _this = this,
				meta = extend(true, {
					'name':false,
					'tunnel':{},
					'ajax':true,
					'action':'',
					'method':'POST',
					'select':{},
					'items':{}
				}, data.meta);

			if(!data.attrs)data.attrs={};
			if(data.attrs.action)meta.action = data.attrs.action;
			if(data.attrs.method)meta.method = data.attrs.method;

			_this.meta = meta;

			_this.fields = {};
			_this.name = meta.name;
			_this.template.attrs = meta.attrs;
			_this.template.method = meta.method;
			_this.template.mods = data.mods;
			_this.template.content = data.content;

			if(_this.name)this._static('bLink').setUphold(_this.name, this);

		},
		{
			'tag':"form"
		},
		new standardFunction({
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

					}else if(blib.is(this.fields[i].val(),"null")){
						continue;
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
					name = meta.name,
					ajax = meta.ajax,
					tunnel = meta.tunnel,
					action = meta.action,
					method = meta.method,
					request, files, temp;

				if(ajax){
					request = this.serialize();
					if(request['_files']){
						files = request['_files'];
						delete request['_files'];
					}

					if(name){
						temp = {'bForm':{}};
						temp.bForm[name] = request;
						request = temp;
					}

					request = extend(true, tunnel, request);

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
				var _this = this,
					meta = _this.meta,
					name = meta.name,
					status = _this._getStatusList(_this.fields),
					tunnel = meta.tunnel,
					request, temp;

				if(!status.error){
					request = _this.serialize();

					if('_files'in request){
						config.files = request['_files'];
						request['_files'] = null;
					}

					if(name){
						temp = {'bForm':{}};
						temp.bForm[name] = request;
						request = temp;
					}

					config.request = extend(true, config.request, tunnel, request);
				}

				return status;

			},
			'_onRemove':[
				function(){
					this._static('bLink').dropUphold(this.name, this);
					config.request = {
						'bForm':{}
					};
					config.files = false;
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
				var self = this;
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
		new standardFunction({})
	);

	//LABEL
	blib.build.define(
		{'block':'bForm', 'elem':'label'},
		function(data){
			this.prepare(data);
		},
		{'tag':"label"},
		new standardFunction({})
	);

	//TEXT
	blib.build.define(
		{'block':'bForm', 'elem':'text'},
		function(data){
			this.prepare(data);
			this.prepareValue();
		},
		{'tag':"input", 'attrs':{'type':"text"}},
		new standardFunction({})
	);

	//SUBMIT
	blib.build.define(
		{'block':'bForm', 'elem':'submit'},
		function(data){
			this.template.content = data.content;
			this.prepareValue();
		},
		{'tag':"input", 'attrs':{'type':"submit"}},
		new standardFunction({
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
		new standardFunction({})
	);

	//BUTTON
	blib.build.define(
		{'block':'bForm', 'elem':'button'},
		function(data){
			this.template.content = data.content;
			this.prepareValue();
		},
		{'tag':"input", 'attrs':{'type':"button"}},
		new standardFunction({})
	);

	//IMAGE
	blib.build.define(
		{'block':'bForm', 'elem':'image'},
		function(data){
			this.template.content = data.content;
			this.prepareValue();
		},
		{'tag':"input", 'attrs':{'type':"image"}},
		new standardFunction({})
	);

	//PASSWORD
	blib.build.define(
		{'block':'bForm', 'elem':'password'},
		function(data){
			this.prepare(data);
			this.prepareValue();
		},
		{'tag':"input", 'attrs':{'type':"password"}},
		new standardFunction({})
	);

	//CHECKBOX
	blib.build.define(
		{'block':'bForm', 'elem':'checkbox'},
		function(data){
			this.prepare(data);
			this.prepareValue();
		},
		{'tag':"input", 'attrs':{'type':"checkbox"}},
		new standardFunction({
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
		new standardFunction({
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
		new standardFunction({
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
			this.prepareContent();
		},
		{'tag':"textarea"},
		new standardFunction({})
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
					'selected':(data.selected)?'selected':false
				}
			}
		},
		{'tag':"option"},
		new standardFunction({
			'deselect':function(){
				this._attr('selected',false);
			},
			'select':function(){
				this._attr('selected','selected');
			}
		})
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
				defValue = (meta.items && meta.items[0])?meta.items[0][this.name]:null,
				optionValue, optionText, optionSelected, setDefault, i;

			this.defValue = defValue;
			if(!this.template.content)this.template.content = [];

			for(i in options){
				optionText = '';
				optionValue = options[i][this.key];
				for(j in this.show){
					optionText += options[i][this.show[j]]+' ';
				}

				if(this.defValue == optionValue){
					setDefault = optionSelected = true;
				}else{
					optionSelected = false;
				}

				this.template.content.push({'block':'bForm', 'elem':'option', 'selected':optionSelected, 'value':optionValue, 'content':optionText});
			}

		},
		{'tag':"select"},
		new standardFunction({
			'val':function(data){
				for(var key in this.options){
					if(!this.options[key].dom.selected)continue;
					return this.options[key].val();
				}
			},
			'addOption':function(value){
				this.deselect();
				this._append({'block':'bForm', 'elem':'option', 'selected':true, 'value':value, 'content':value});
			},
			'deselect':function(){
				var options = this.options,
					key;
				for(key in options){
					options[key].deselect();
				}
			}
		})
	);

	//SELECTPLUS
	blib.build.define(
		{'block':'bForm', 'elem':'selectplus'},
		function(data){
			data.elem = 'select';
			this.template.content = [
				{'block':'bForm', 'elem':'selectadd'},
				{'block':'bForm', 'elem':'selectinput'},
				data
			];
		},
		{'tag':"span"},
		{
			'getSelectinput':function(){
				return this.children.bForm__selectinput[0];
			},
			'getSelect':function(){
				return this.children.bForm__select[0];
			}
		}
	);

	//SELECTADD
	blib.build.define(
		{'block':'bForm', 'elem':'selectadd'},
		function(data){
			this.template.content = '+';
		},
		{'tag':"span"},
		{
			'html':function(text){
				this.template.content = text;
				this.dom.innerHTML = text;
			},
			'onclick':function(){
				var parent = this.parent,
					input = parent.getSelectinput(),
					select = parent.getSelect(),
					status = input._getMode('open');

				if(status){
					select.addOption(input.val());
					input.close();
					this.html('+');
				}else{
					input.open();
					this.html('ok');
				}
			}
		}
	);

	//SELECTINPUT
	blib.build.define(
		{'block':'bForm', 'elem':'selectinput'},
		function(data){

		},
		{'tag':"input",'attrs':{'type':'text'}},
		{
			'open':function(){
				this._setMode('open',true);
			},
			'close':function(){
				this._setMode('open',false);
			},
			'val':function(){
				return this.dom.value;
			}
		}
	);

	//TEXTAREAPLUS
	blib.build.define(
		{'block':'bForm', 'elem':'textareaplus'},
		function(data){

			var self = this,
				setData = function(data){
					self.setData(data);
				};

			this.editor = data.editor || 'bEditor';
			data.elem = 'textarea';
			this.template.mods = data.mods;

			this.template.content = [
				{'block':this.editor, 'output':setData},
				data
			];
		},
		false,
		{
			'setData':function(data){
				this.children.bForm__textarea[0].dom.innerHTML = data;
			},
			'getTextarea':function(){
				return this.children.bForm__textarea[0];
			},
			'getEditor':function(){
				return this.children[this.editor][0];
			}
		}
	);

})( window.blib );
