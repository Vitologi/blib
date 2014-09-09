(function(){
	
	blib.config('bLink',{'curloc':window.location.pathname});
	
	blib.build.define(
		{'block':'bLink'},
		function(data){
			this.ajax = data.ajax;
			this.link = data.link||this.getCurentLocation();
			this.tunnel = data.tunnel;
			this.data = data.data;
			this.disabled = (data.mods)?data.mods.disabled:false;
			this.before = data.before || [];
			this.after = data.after || [];
			
			this.template = data;
			if(!this.template.attrs)this.template.attrs = {};
			this.template.attrs.href = this.link;
		},
		{
			'tag':'a'
		},
		{
			'handlers':[],
			'onclick':function(e){
				var self = this,
					before = self.before,
					after = self.after,
					key;
				
				for(key in before){
					before[key].call(self);
				}
				
				if(self.disabled){
					e.preventDefault ? e.preventDefault() : e.returnValue = false;
					return false;
				}else if(self.ajax){
					
					self.data.ajax = true;
					
					blib
					.tunnel(self.tunnel)
					.ajax({
						url:self.link,
						data:self.data,
						dataType:'json',
						'success':function(data){
							self.setLocation();
							
							for(key in after){
								after[key].call(self);
							}
							
							blib('body').html(blib.build(data));
							
							
							
						}
					});
					
					e.preventDefault ? e.preventDefault() : e.returnValue = false;
					return false;
				}
			},
			'getCurentLocation':function(){
				return blib.config('bLink.curloc');
			},
			'setLocation':function(){
				var self = this,
					tunnel;
				blib.config('bLink.curloc', self.link);
				blib.config('bLink.tunnel', self.tunnel);
				tunnel = "?"+blib.object2url({'_tunnel':blib.config('bLink.tunnel')});
				
				if(history.pushState){
					history.pushState({}, location.host , self.link);
					history.pushState({}, location.search , tunnel);
				}else{
					window.chHashFlag = true;
					location.hash = self.link;
				}
			}
		}
	);

})(window);