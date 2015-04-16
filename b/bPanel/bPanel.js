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
				tunnel, controller, key;
			
			for(key in data.content){
                controller = data.content[key];
				tunnel = {'bPanel':{'controller':controller},'view':'json'};

				content.push({
					'block':'bLink',
					'mods':{'style':'button', 'position':'vertical'},
					'data':tunnel,
					'visible':true,
					'content':key
				});
			}
			
			this.template.content = content;

		}
	);

	blib.build.define(
		{'block':'bPanel', 'elem':'button'},
		function(data){
			var tunnel = {'bPanel':{'controller':data.controller},'view':'json'},
				mods = blib.extend({'style':'button'}, data.mods),
				uphold = data.uphold || [];
				
			tunnel[data.controller] = data.tunnel;

			this.template.content = [{
				'block':'bLink',
				'mods':mods,
				'uphold':uphold,
				'data':tunnel,
				'visible':true,
				'content':data.content
			}];
		}
	);

	
})(window);