blib.build.define(
	{'block':'bMenu'},
	function(data){
		var content = {},
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
		temp.content.push({'elem':'clear'});
		this.template.content = temp.content;
	},
	{
		'tag':'ul'
	},
	//actions
	{
		'_onSetMode':{
			'position':{
				'horizontal':function(){
					console.log('position -> horizontal');
				},
				'vertical':function(){
					console.log('position -> vertical');
				}
			}
		}
	}
);

blib.build.define(
	{'block':'bMenu', 'elem':'item'},
	function(data){
		var content = [{'elem':'link', 'tag':'a', 'attrs':{'href':data.link}, 'content':[{'elem':'link-text', 'content':data.name}]}];
		
		if(data.content){
			content[0].content.unshift({'elem':'flyout'});
			content.push({'elem':'child', 'content':data.content});
		}		
		
		this.opened = false;
		this.template = blib.clone(this.template);
		this.template.content = content;
		
	},
	//template
	{
		'tag':'li'
	},
	//actions
	{
		'onclick':function(e){
			var self = this,
				concurents = self.parent.children['bMenu__item'];
			
			for(key in concurents){
				if(!concurents[key].opened || concurents[key]===self)continue;
				concurents[key].opened=false;
				concurents[key]._setMode('active',self.opened);
				concurents[key]._setMode('opened',self.opened);
			}
			
			self.opened = !self.opened;
			self._setMode('active',self.opened);
			self._setMode('opened',self.opened);
			e.stopPropagation?e.stopPropagation():e.cancelBubble = true;
		}		
	}
);

blib.build.define(
	{'block':'bMenu', 'elem':'link'},
	function(data){
		this.template = data;		
	},
	false,
	{
		'onclick':function(e){
			var self = this,
				link = self.template.attrs.href,
				template;
			if(!link || blib.is(link,"null")){
				e.preventDefault ? e.preventDefault() : e.returnValue = false;
				return false;
			}else{
				template = blib('.bTemplate')[0].blib.template.template;
				
				blib.ajax({
					url:link,
					data:{'ajax':true, 'template':template},
					dataType:'json',
					'success':function(data){
						blib('body').html(blib.build(data));
						
						if(history.pushState){
							history.pushState({}, location.host , link);
						}else{
							window.chHashFlag = true;
							if(location.pathname){location.href = location.host+"#"+link;}else{
							location.hash = link;}
						}
					}
				});
				
				
				e.preventDefault ? e.preventDefault() : e.returnValue = false;
				return false;
			}
		}
	}
);

blib.build.define(
	{'block':'bMenu', 'elem':'child'},
	function(data){
		this.template = blib.clone(this.template);
		this.template.content = data.content;
	},
	//template
	{
		'tag':'ul'
	}
);