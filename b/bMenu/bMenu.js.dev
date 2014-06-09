blib.build.define(
	{'block':'bMenu'},
	function(data){
		var self = this,
			content = {},
			item,
			glueMenuItem = function(elem){
				var itemContent = content[elem.id],
					temp;
					
				if(!itemContent)return elem;
				
				elem.content = [];
				
				for(key in itemContent){
					elem.content.push(glueMenuItem(itemContent[key]));
				};
				
				return elem;
			};
			
		for(key in data.content){
			item = data.content[key];
			item.elem = 'item';
			if(+item.parent === 0){combine = item; continue;}
			if(!content[item.parent]){content[item.parent] = [];}
			content[item.parent].push(item);
		}
		
		temp = glueMenuItem(combine);
		this.template = blib.clone(this.template);
		this.template.mods = data.mods;
		this.template.content = temp.content;
	},
	{
		'tag':'ul'
	},
	//actions
	{
		'onSetMode':{
			'position':{
				'horizontal':function(){
					console.log('position -> horizontal');
				}
			}
		},
		'onclick':function(){
			this.blib.setMode('position','vertical');
		}
	}
);

blib.build.define(
	{'block':'bMenu', 'elem':'item'},
	function(data){
		if(!data.content){data.content=[];}
		[].unshift.call(data.content, {'tag':'a', 'attrs':{'href':data.link}, 'content':data.name});
		this.template = blib.clone(this.template);
		this.template.content = data.content;
	},
	//template
	{
		'tag':'li'
	},
	//actions
	{
		'onclick':function(){
			alert('elem click');
		}
	}
);