blib.build.define(
	{'block':'bIndex'},
	function(data){
		
		if(data.ajax){
			this.setTemplate(data, true);
		}else{
			this.setTemplate(data);
			
			setTimeout(function(){
				blib.ajax({
				dataType: 'json',
				data:{'ajax':true},
				success: function(data){
					blib('body').html(blib.build(data));
				}
			});
			},10000);
			
		}
	}
);
