(function(){
	
	var config = {
		'curloc':window.location.pathname
	};
	
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
			this.before = data.before || [];
			this.after = data.after || [];
			this.invisible = data.invisible || false;
			
			
			this.template.attrs.href = this.link;
			this.template.mods = data.mods || {};
			this.template.content = data.content;
		},
		{
			'tag':'a'
		},
		{
			'getCurloc':function(){return config.curloc;},
			'setCurloc':function(data){config.curloc = data;},
			
			'onclick':function(e){
				e.preventDefault ? e.preventDefault() : e.returnValue = false;
				
				var self = this,
					before = self.before,
					after = self.after,
					key;
				
				if(self.disabled)return false;
				
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
			}
		}
	);

})(window);