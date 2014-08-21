blib.build.define(
	{'block':'bTemplate'},
	function(data){
			
		var singleton = this.singleton || false;
		
		if(!singleton || (singleton.template.template['0'] !== data.template['0'])){
			if(singleton)singleton._removeChildren();
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
			deep = deep || now[0];
			
			var counter = 0,
				key, prop;
			
			if(now[0] != old[0] || !now[0]){
				for (prop in old) delete old[prop];
				for (prop in now) old[prop] = now[prop];
				this.replace(deep);
				return;
			}

			for(key in now){
				
				if(!(key in old))old[key]=[];
				
				if(blib.is(now[key],["object","array"])){
					this.compare(old[key], now[key], deep+'.'+key);
					counter++;
					continue;
				}

			}
			
			if(!counter){
				old[key] = now[key];
				this.replace(deep);
			}
			
		},
		'replace':function(num){
			var childs = this.children.bTemplate__position
				key;
			for(key in childs){
				if(childs[key].template.template === num){
					childs[key]._replace(this.chunk[num]);
					break;
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