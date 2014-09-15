(function(){
	
	var config = {
			'curloc':window.location.pathname
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
			this.data = data.data;
			this.disabled = (data.mods)?data.mods.disabled:false;
			this.uphold = data.uphold || [];
			this.before = data.before || [];
			this.after = data.after || [];
			this.invisible = data.invisible || false;
			
			
			this.template.attrs.href = this.link;
			this.template.mods = data.mods || {};
			this.template.content = [
				{'elem':'error'},
				{'tag':'span', 'content':data.content}				
			];
		},
		{
			'tag':'a'
		},
		{	
			'getUpholdStatus':function(){
				var self = this,
					result = {'error':0, 'code':0, 'message':'', 'stack':[]},
					uphold = self.uphold,
					uLen = uphold.length,
					i=0, j, temp, status, stack;
				
				for(;i<uLen;i++){
					temp = storage[uphold[i]];
					if(!temp || !('getStatus' in  temp))continue;
					
					status = temp.getStatus();
					stack = result.stack;
					if(status.error)result = blib.extend({'error':0, 'code':0, 'message':'', 'stack':[]}, status);
					stack.push(status);
					result.stack = stack;
				}
				
				return result;
				
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
				allow = self.getUpholdStatus();
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
				
				blib
				.tunnel(self.tunnel)
				.ajax({
					url:self.link,
					data:self.data,
					dataType:'json',
					'success':function(data){
						
						if(!self.invisible){
							self.setCurloc(self.link);
							blib.tunnel(self.tunnel);
						}
						
						blib('body').html(blib.build(data));
						
						self.setLocation();
						
						for(key in after){
							after[key].call(self, [data]);
						}
					}
				});

			},
			'setLocation':function(){
				var self = this,
					tunnel;
				
				tunnel = "?"+blib.object2url({'_tunnel':blib.config('tunnel')});
				
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