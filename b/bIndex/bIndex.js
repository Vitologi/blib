blib.build.define(
	{'block':'bIndex'},
	function(data){
		if(data.ajax){
			this._setTemplate(data, true);
		}else{
			this._setTemplate(data);
		}
	}
);
