(function(){
	
	blib.build.define(
		{'block':'bTable'},
		function(data){
			var temp = [];
				
			this.tunnel = data.tunnel || false;
			this.meta = data.meta || {'position':{},'fields':{}};
			this.content = data.content;
			
			
			temp.push(this.getHeader());
			temp.push(this.getBody());
			
			this.template.mods = data.mods;
			this.template.content = temp;

		},
		{'tag':'table'},
		{
			'getHeader':function(){
				return {'block':'bTable', 'elem':'head'};
			},
			'getBody':function(){
				return {'block':'bTable', 'elem':'body', 'content':this.content};
			}
		}
	);
	
	blib.build.define(
		{'block':'bTable', 'elem':'head'},
		function(data){
			var block = this.block,
				position = block.meta.position,
				fields = block.meta.fields,
				temp = [],
				key, name;
			
			for(key in position){
				name = position[key];
				if(fields[name].type == 'hidden')continue;
				temp.push({'block':'bTable', 'elem':'th', 'note':fields[name].note, 'content':fields[name].title});
			}
			
			this.template.content = temp;

		},
		{'tag':'tr'}
	);
	
	
	
	blib.build.define(
		{'block':'bTable', 'elem':'th'},
		function(data){
			this.template = data;
		},
		{'tag':'th'}
	);
	
	blib.build.define(
		{'block':'bTable', 'elem':'body'},
		function(data){
			var content = data.content,
				temp = [], key;
			
			for(key in content){
				temp.push({'block':'bTable', 'elem':'tr', 'content':content[key]});
			}

			this.template.content = temp;

		},
		{'tag':'tbody'}
	);
	
	blib.build.define(
		{'block':'bTable', 'elem':'tr'},
		function(data){
			var block = this.block,
				position = block.meta.position,
				fields = block.meta.fields,
				temp = [],
				key, name,
				content = this.content = data.content;
			
			for(key in position){
				name = position[key];
				if(fields[name].type == 'hidden')continue;
				temp.push({'block':'bTable', 'elem':'td', 'name':name, 'content':content[name]});
			}
			
			this.template.content = temp;

		},
		{'tag':'tr'}
	);
	
	blib.build.define(
		{'block':'bTable', 'elem':'td'},
		function(data){
			var block = this.block,
				name = this.name = data.name,
				meta = block.meta.fields[name],
				content = this.content = data.content;
			
			this.template.mods = meta.mods;
			this.template.content = content;

		},
		{'tag':'td'}
	);


})(window);