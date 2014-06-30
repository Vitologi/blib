blib.build.define(
	{'block':'bTemplate'},
	(function(){
		
		var template = {
			'stack':{},
			
			'parse':function(obj){
				var content = obj['content'];
				
				if(obj['block']=='bTemplate' && obj['elem']=='position'){
					this.stack[obj['template']] = obj;
					
				}
				
				if(blib.is(content, "array")){
					for(key in content){
						this.parse(content[key]);
					}
				}
				
			}
			
		};
			
		
		return function(data){
			var oldDom = blib('.bTemplate')[0],
				oldObj = (oldDom)?oldDom.blib:false,
				oldTemp = (oldObj)?oldObj.template.template:false,
				newTemp = data.template;
			
			if(!newTemp[0]){
				this.template = false;
			}else if(oldTemp[0] === newTemp[0]){
				template.parse(data);
				console.log(template, oldObj.children);
			}else{
				this.template = data;
			}
		};		
	})()
);

blib.build.define(
	{'block':'bTemplate', 'elem':'position'},
	function(data){
		this.template = data;
	}
);