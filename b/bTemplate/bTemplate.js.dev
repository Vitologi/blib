blib.build.define(
	{'block':'bTemplate'},
	(function(){
		
		var template = {
			'position':{},
			'chunk':{},
			
			'setChunk':function(obj){
				var content = obj['content'];
				
				if(obj['block']=='bTemplate' && obj['elem']=='position'){
					this.chunk[obj['template']] = obj;
					
				}
				
				if(blib.is(content, "array")){
					for(key in content){
						this.setChunk(content[key]);
					}
				}
				
			},
			
			'setPosition':function(obj){
				var childs = obj.children.bTemplate__position,
					temp;
				for(key in childs){
					temp = childs[key]['template']['template'];
					this.position[temp] = childs[key];
				}
			},
			
			'compare':function(old, now, deep){
				
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
				template.setChunk(data);
				template.setPosition(oldObj);
				
				/*
				for(i in template.chunk){
					
					for(j in oldObj.children.bTemplate__position){
						if(oldObj.children.bTemplate__position[j].template.template === i){
							console.log(i, oldObj.children.bTemplate__position[j].template.template);
						}
					}
					
				}
				*/
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