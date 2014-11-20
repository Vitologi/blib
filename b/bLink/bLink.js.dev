(function(){
	
	var config = {
			'curloc':window.location.pathname,
			'dontShow':{
				'bTemplate':true
			}
		},
		storage = {};
	
	Blib.config("bLink", config);
	//Blib.config("_private.bLink", true); //0_0 open until bMenu is not based on bLink
	
	blib.build.define(
		{'block':'bLink'},
		function(data){
			this.ajax = data.ajax;
			this.link = data.link||this.getCurloc();
			this.tunnel = data.tunnel;
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
				var self = this,
					uphold = self.uphold,
					uLen = uphold.length,
					i=0, temp = [], status, stack;
				
				for(;i<uLen;i++){
					temp.push(storage[uphold[i]]);
				}
				
				return self._getStatusList(temp);
				
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
				
				var self = this,
					before = self.before,
					after = self.after,
					allow, key;
				
				
				if(self.disabled)return false;
				allow = self._getStatus();
				if(allow.error)return self.showError(allow);
				self.clearError();
				
				for(key in before){
					before[key].call(self);
				}
				
				if(!self.ajax){
					blib.tunnel(self.tunnel);
					window.location.href = self.link+"?"+blib.object2url({'_tunnel':blib.config('tunnel')});
					return false;
				}

				self.data.ajax = true;
				
				blib.tunnel(self.tunnel);
				
				self.setCurloc(self.link);
				self.setLocation();
				
				blib.ajax({
					url:self.link,
					data:self.data,
					dataType:'json',
					'success':function(data){
						
						blib('body').html(blib.build(data));
						
						for(key in after){
							after[key].call(self, [data]);
						}
					}
				});

			},
			'dontShow':{
				'bTemplate':true
			},
			'setLocation':function(params){
				var self = this,
					dontShow = config.dontShow,
					key, temp, tunnel;
				
				if((params && params.visible) || self.visible){
					temp = blib.config('tunnel');
					for(key in dontShow)delete temp[key];
					tunnel = "?"+blib.object2url({'_tunnel':temp}, {'length':20});
				}
				
				if(history.pushState){
					history.pushState({}, location.host , self.getCurloc());
					history.pushState({}, location.search , tunnel);
				}else{
					window.chHashFlag = true;
					location.hash = self.getCurloc();
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
				tunnel = blib.config('tunnel')||{},
				content = data.content,
				key, item;
			
			this.visible = data.visible || false;
			
			if(data.link)parent.setCurloc(data.link);
			
			for(key in content){
				item = content[key];
				tunnel[key] = item;
			}

			blib.tunnel(tunnel);
			
			parent.setLocation({'visible':this.visible});
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
				this._append(message,true);
				this._setMode('active',true);
			},
			'clear':function(){
				this._removeChildren();
				this._setMode('active',false);
			}
		}
	);

})(window);