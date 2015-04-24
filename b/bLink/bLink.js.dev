(function(){
	
	var config = {
			'curloc':window.location.pathname,
			'dontShow':{
				'view':true,
				'bTemplate':true
			}
		},
		storage = {};
	
	Blib.config("bLink", config);
	//Blib.config("_private.bLink", true); //0_0 open until bMenu is not based on bLink
	
	blib.build.define(
		{'block':'bLink'},
		function(data){
			this.link = data.link||this.getCurloc();
			this.data = data.data || {};
			this.disabled = (data.mods)?data.mods.disabled:false;
			this.uphold = data.uphold || [];
			this.before = data.before || [];
			this.after = data.after || [];
			this.visible = data.visible || false;
			this.go = data.go || false;
			
			this.template = {};
			this.template.attrs = data.attrs || {};
			this.template.attrs.href = this.link;
			this.template.mods = data.mods || {};
			this.template.content = [
				{'elem':'error'},
				{'tag':'span', 'content':data.content}				
			];
			
			if(this.go)this.rendered();
			
		},
		{
			'tag':'a'
		},
		{	
			'rendered':function(){
				var _this = this;
				blib.build.ready(function(){_this.onclick.call(_this, {});}, true);
			},
			'_getStatus':function(){
				var _this = this,
					uphold = _this.uphold,
					uLen = uphold.length,
					i=0, temp = [], status, stack;
				
				for(;i<uLen;i++){
					temp.push(storage[uphold[i]]);
				}
				
				return _this._getStatusList(temp);
				
			},
			'setUphold':function(name, obj){
				storage[name] = obj;
			},
			'dropUphold':function(name, obj){
				if(storage[name] === obj)delete storage[name];
			},
			'getCurloc':function(){return config.curloc;},
			'setCurloc':function(data){config.curloc = data;},
			
			'onclick':function(e){
				e.preventDefault ? e.preventDefault() : e.returnValue = false;
				
				var _this = this,
					before = _this.before,
					after = _this.after,
					disabled = _this.disabled,
					link = _this.link,
					requestData = _this.data,
					allow, key;
				
				
				if(disabled)return false;
				allow = _this._getStatus();
				if(allow.error)return _this.showError(allow);
				_this.clearError();
				
				for(key in before){
					before[key].call(_this);
				}

				if(requestData.view == 'json') {

					_this.setCurloc(link);

					blib.ajax({
						'url': link,
						'data': requestData,
						'dataType': 'json',
						'success': function (data) {

							_this.setLocation(requestData);

							blib('body').html(blib.build(data));

							for (key in after) {
								after[key].call(_this, [data]);
							}
						}
					});

				}else{
					window.location.href = link+"?"+blib.object2url(requestData);
				}

			},
			'setLocation':function(data, params){
				var _this = this,
					visible = _this.visible,
					dontShow = config.dontShow,
					key, temp, loc;

				data = data || _this.data;
				
				if((params && params.visible) || visible){
					temp = blib.clone(data);
					for(key in dontShow)delete temp[key];
					loc = "?"+blib.object2url(temp, {'length':20});
				}
				
				if(history.pushState){
					history.pushState({}, location.host , _this.getCurloc());
					history.pushState({}, location.search , loc);
				}else{
					window.chHashFlag = true;
					location.hash = _this.getCurloc();
				}
			},
			'showError':function(error){
				var elem = (this.children && this.children.bLink__error)?this.children.bLink__error[0]:false;
				if(elem)elem.show(error);
			},
			'clearError':function(){
				var elem = (this.children && this.children.bLink__error)?this.children.bLink__error[0]:false;
				if(elem)elem.clear();
			}
		}
	);
	
	blib.build.define(
		{'block':'bLink', 'elem':'location'},
		function(data){
			var parent = this._static('bLink'),
				content = data.content;
			
			this.visible = data.visible || false;
			
			if(data.link)parent.setCurloc(data.link);

			parent.setLocation(content, {'visible':this.visible});
			this.template = false;
		}
	);
	
	blib.build.define(
		{'block':'bLink', 'elem':'error'},
		function(data){
			this.template = data;
		},
		{'tag':'span'},
		{	
			'show':function(error){
				var message = error.message;
				if(!blib.is(message,"object"))message = {'tag':'span', 'content':message};
				this._removeChildren();
				this._append(message);
				this._setMode('active',true);
			},
			'clear':function(){
				this._removeChildren();
				this._setMode('active',false);
			}
		}
	);

})(window);