(function(){
	
	blib.build.define(
		{'block':'bMenu'},
		function(data){
		
			var content = {},
				combine = {},
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
				},
				temp;
			
			this.id = data.id;
			
			for(key in data.content){
				item = data.content[key];
				item.elem = 'item';
				if(item.id == this.id){combine = item; continue;}
				if(!content[item.bmenu_id]){content[item.bmenu_id] = [];}
				content[item.bmenu_id].push(item);
			}
			
			temp = glueMenuItem(combine);
			this.template = data;
			this.template.content = [
				{'elem':'opener'},
				{'elem':'container', 'content':temp.content},
				{'elem':'clear'}
			];

		},
		{
			'tag':'ul'
		},
		{
			'_onSetMode':{
				'position':{
					'float':function(){
						
					}
				}
			}
		}
	);

	blib.build.define(
		{'block':'bMenu', 'elem':'item'},
		function(data){
			var content = [{'elem':'link', 'tag':'a', 'id':data.id, 'attrs':{'href':data.link}, 'content':[{'elem':'link-text', 'content':data.name}]}];
			
			if(data.content){
				content[0].content.unshift({'elem':'flyout'});
				content.push({'elem':'child', 'content':data.content});
			}		
			
			this.opened = false;
			this.template = {
				"content":content
			};
			
		},
		//template
		{
			'tag':'li'
		},
		//actions
		{
			'toggle':function(){
				var self = this;
				self.resetLevel();
				self.opened = !self.opened;
				self._setMode('active',self.opened);
				self._setMode('opened',self.opened);
			},
			'reset':function(){
				var self = this;
				self.opened = false;
				self._setMode('active',false);
				self._setMode('opened',false);
			},
			'resetLevel':function(){
				var self = this,
					concurents = self.parent.children['bMenu__item'];
				
				for(key in concurents){
					if(concurents[key]===self)continue;
					concurents[key].reset();
				}
			},
			'resetAll':function(){
				var block = this.block,
					items = block.children.bMenu__item;
				for(key in items)items[key].reset();
			}
		}
	);

	blib.build.define(
		{'block':'bMenu', 'elem':'link'},
		function(data){
			this.id = data.id;
			this.link = data.attrs.href;
			this.template = data;		
		},
		false,
		{
			'onclick':function(e){
				var self = this;
					
				if(!self.link || blib.is(self.link,"null")){
					e.preventDefault ? e.preventDefault() : e.returnValue = false;
					self.animate();
					return false;
				}else{
					
					blib.ajax({
						url:self.link,
						data:{'ajax':true},
						dataType:'json',
						'success':function(data){
							self.setLocation();
							blib('body').html(blib.build(data));
							self.parent.resetAll();							
							self.animate();
							self.rollUp();
							self.rollUpOther();
						}
					});
					
					
					e.preventDefault ? e.preventDefault() : e.returnValue = false;
					return false;
				}
			},
			'setLocation':function(){
				var self = this;
				
				blib.config('bLink.curloc', self.link);
				
				if(history.pushState){
					history.pushState({}, location.host , self.link);
				}else{
					window.chHashFlag = true;
					location.hash = self.link;
				}
			},
			'animate':function(){
				var self = this,
					tracker = self.parent;
				tracker.resetLevel();
				tracker.toggle();
			},
			'rollUpOther':function(){
				var self = this,
					id = self.id,
					bMenu__link = blib('.bMenu__link')
					i = 0, len = bMenu__link.length;
				
				for( ;i<len;i++){
					if(bMenu__link[i].blib === self || bMenu__link[i].blib.id !== id)continue;
					bMenu__link[i].blib.rollUp();
				}					
				
			},
			'rollUp':function(){
				var self = this,
					tracker = self.parent;
				
				while(tracker.parent.parent != self.block){
					tracker = tracker.parent;
				}
				
				tracker.resetLevel();
				tracker.reset();
				tracker._setMode('active',true);
			}
		}
	);

	blib.build.define(
		{'block':'bMenu', 'elem':'child'},
		function(data){
			this.template = data;
		},
		//template
		{
			'tag':'ul'
		}
	);
	
	blib.build.define(
		{'block':'bMenu', 'elem':'opener'},
		function(data){
			this.template = data;
			this.template.content = 'Menu';
		},
		//template
		{},
		{
			'onclick':function(){
				var container = this.block.children.bMenu__container[0];
				container.toggle();
			}
		}
	);
	
	blib.build.define(
		{'block':'bMenu', 'elem':'container'},
		function(data){
			this.template = data;
		},
		//template
		{},
		{
			'toggle':function(){
				var open = this._getMode('open');
				this._setMode('open', !open);
			}
		}
	);

})(window);