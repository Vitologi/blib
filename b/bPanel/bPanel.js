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

})(window);