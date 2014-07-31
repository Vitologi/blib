(function(){
	
	blib.build.define(
		{'block':'bLinksContainer'},
		function(data){
			var temp = [],
				content = data.content,
				item;
				
			this.template = blib.clone(this.template);
			this.template.mods = data.mods;
			
			for(key in content){
				item = {'block':'bLink', 'mods':{'style':'default'}, 'link':content[key][0], 'content':content[key][1]};
				if(content[key][2])item.ajax = true;
				temp.push({'elem':'li', 'tag':'li', 'content':[item]});
			}
			this.template.content = temp;

		},
		{
			'tag':'ul'
		}
	);

})(window);