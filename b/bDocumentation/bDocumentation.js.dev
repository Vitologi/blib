(function(){
	
	blib.build.define(
		{'block':'bDocumentation'},
		function(data){
			var group = data.group;
			delete data.group;
			
			console.log(data);
			this.id = data.id;
			this.template = blib.clone(this.template);
			this.template.content = [
				{'elem':'sidebar', 'content':[{'elem':'navigation', 'content':group}]},
				{'elem':'content', 'content':[
					{
						'elem':'description',
						'content': data
					}
				]}
			];
		}
	);
	
	blib.build.define(
		{'block':'bDocumentation', 'elem':'navigation'},
		function(data){
		console.log(data);
			this.template = blib.clone(this.template);	
			this.id = data.content.start;
			
			var temp=[];
			for(key in data.content.navigation){
				
			}
			
			
			this.template.content=[];
		}
	);

})(window)