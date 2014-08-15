blib.build.define(
	{'block':'bTemplate'},
	function(data){
			
		var singleton = this.singleton || false;
		
		if(!singleton || (singleton.template.template['0'] !== data.template['0'])){
			if(singleton)singleton._removeCildren();
			this.template = data;
			this.constructor.prototype.singleton = this;			
		}else{
			singleton.chunk = {};
			singleton.setChunk(data);
			singleton.compare(singleton.template.template, data.template);
			this.template = false;
		}
		
	},
	false,
	{
		'chunk':{},
		'setChunk':function(obj){
			var content = obj['content'],
				key;			
			
			if(obj.block == 'bTemplate' && obj.elem == 'position'){
				this.chunk[obj.template] = obj;
			}
			
			if(blib.is(content, "array")){
				for(key in content){
					this.setChunk(content[key]);
				}
			}
			
		},
		'compare':function(old, now, deep){
			if(!now[0])return;	
			
			var key, prop, j, temp, 
				childs = this.children.bTemplate__position;
			
			deep = deep || now[0];

			for(key in now){
				
				if(blib.is(now[key],["object","array"]) && blib.is(old[key],["object","array"])){
					this.compare(old[key], now[key], deep+'.'+key);
					continue;
				}
				
				if(key !== '0'){
					old[key] = now[key];
					temp = deep+'.'+key;
				}else if(old[key] === now[key]){
					continue;
				}else{
					for (prop in old) delete old[prop];
					for (prop in now) old[prop] = now[prop];
					temp = deep;
				}
				
				for(j in childs){
					if(childs[j].template.template === temp){
						childs[j]._replace(this.chunk[temp]);
						break;
					}
				}

			}
			
		}
	}
);

blib.build.define(
	{'block':'bTemplate', 'elem':'position'},
	function(data){
		this.template = data;
	}
);