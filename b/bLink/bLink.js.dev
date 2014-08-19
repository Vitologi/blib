(function(){
	
	blib.build.define(
		{'block':'bLink'},
		function(data){
			this.ajax = data.ajax;
			this.link = (data.link)?data.link:data.content;
			
			this.template = data;
			if(!this.template.attrs)this.template.attrs = {};
			this.template.attrs.href = this.link;
		},
		{
			'tag':'a'
		},
		{
			'onclick':function(e){
				var self = this,
					template;
					
				if(!self.link || blib.is(self.link,"null")){
					e.preventDefault ? e.preventDefault() : e.returnValue = false;
					return false;
				}else if(self.ajax){
					template = blib('.bTemplate')[0].blib.template.template;
					
					blib.ajax({
						url:self.link,
						data:{'ajax':true, 'template':template},
						dataType:'json',
						'success':function(data){
							blib('body').html(blib.build(data));
							self.setLocation();
						}
					});
					
					
					e.preventDefault ? e.preventDefault() : e.returnValue = false;
					return false;
				}
			},
			'setLocation':function(){
				var self = this;
				
				if(history.pushState){
					history.pushState({}, location.host , self.link);
				}else{
					window.chHashFlag = true;
					if(location.pathname){location.href = location.host+"#"+self.link;}else{
					location.hash = self.link;}
				}
			}
		}
	);

})(window);