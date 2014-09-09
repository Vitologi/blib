(function(){
	
	blib.build.define(
		{'block':'bPanel'},
		function(data){
			
			this.template = data;

		}
	);
	
	blib.build.define(
		{'block':'bPanel', 'elem':'blocks'},
		function(data){
			var content = [],
				tunnel;
			
			for(key in data.content){
				tunnel = {'bPanel':{'controller':key}};
				tunnel[key] = {'layout':"show"};
				
				content.push({
					'block':'bLink',
					'mods':{'style':'button', 'position':'vertical'},
					'ajax':true,
					'tunnel':tunnel,
					'content':key,
					'before':[function(){
						this.data = {'template':blib('.bTemplate')[0].blib.template.template};
					}]
				});
			}
			
			this.template.content = content;

		}
	);

	
})(window);