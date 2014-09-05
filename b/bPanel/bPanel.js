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
			var content = [];
			
			for(key in data.content){
				content.push({'block':'bPanel', 'elem':'blockLink', 'content':key});
			}
			this.template.content = content;

		}
	);
	
	blib.build.define(
		{'block':'bPanel', 'elem':'blockLink'},
		function(data){
			this.link = data.content;
			this.template = data;

		},
		{"tag":"a"},
		{
			'onclick':function(){
				var self = this,
					tunnel = {'bPanel':{'controller':self.link}},
					template;
					
				tunnel[self.link] = {'layout':"show"};
				template = blib('.bTemplate')[0].blib.template.template;
				
				blib
				.tunnel(tunnel)
				.ajax({
					'url':window.location,
					'data':{'ajax':true, 'template':template},
					'dataType':'json',
					'success':function(data){
						blib('body').html(blib.build(data)); //blib.build(data);
					}
				});
				
			}
		}
	);

})(window);