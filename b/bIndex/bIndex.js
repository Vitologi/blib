blib.build.define(
	{'block':'bIndex'},
	function(data){
		if(data.ajax){
			this.setTemplate(data, true);
		}else{
			this.setTemplate(data);
		}
	}
);
