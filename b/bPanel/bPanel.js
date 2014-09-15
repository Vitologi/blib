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
	
	blib.build.define(
		{'block':'bPanel', 'elem':'location'},
		function(data){
			var tunnel = blib.config('tunnel')||{};
			tunnel['bPanel'] = {'controller':data.controller};
			tunnel[data.controller] = {
				'layout':data.layout,
				'view':data.view
			};

			blib.tunnel(tunnel);
			blib.build({'block':'bLink'}).blib.setLocation();
			this.template = false;
		}
	);
	
	blib.build.define(
		{'block':'bPanel', 'elem':'button'},
		function(data){
			var tunnel = {'bPanel':{'controller':data.controller}},
				mods = blib.extend({'style':'button'}, data.mods),
				ajax = (data.ajax !== undefined)?data.ajax:true,
				uphold = data.uphold || [];
				
			tunnel[data.controller] = {
				'layout':data.layout,
				'view':data.view
			};
			
			
			
			this.template.content = [{
				'block':'bLink',
				'mods':mods,
				'ajax':ajax,
				'uphold':uphold,
				'tunnel':tunnel,
				'content':data.content,
				'before':[function(){
					this.data = {'template':blib('.bTemplate')[0].blib.template.template};
				}]
			}];
		}
	);

	
})(window);