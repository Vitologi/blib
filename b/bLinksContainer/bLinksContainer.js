(function(){
	
	blib.build.define(
		{'block':'bLinksContainer'},
		function(data){
			var temp = [],
				content = data.content,
				item;
			
			for(key in content){
				item = {'block':'bLink', 'mods':{'style':'default'}, 'link':content[key][0], 'content':content[key][1]};
				if(content[key][2])item.ajax = true;
				temp.push({'elem':'li', 'tag':'li', 'content':[item]});
			}
			
			this.template = {
				"mods":data.mods,
				"content":temp
			};

		},
		{
			'tag':'ul'
		}
	);

})(window);