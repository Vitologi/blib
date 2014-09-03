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
			
			this.template = data;

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
				var self = this;
				
				blib
				.tunnel({'bPanel':{"action":"show","view":"block","name":self.link}})
				.ajax({
					'url':window.location,
					'data':{'ajax':true},
					'dataType':'json',
					'success':function(data){
						blib.build(data);
					}
				});
				
			}
		}
	);

})(window);