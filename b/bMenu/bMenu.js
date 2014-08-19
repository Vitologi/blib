(function(){
	
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
			
			this.id = data.id;
			
			for(key in data.content){
				item = data.content[key];
				item.elem = 'item';
				if(+item.parent === 0){combine = item; continue;}
				if(!content[item.parent]){content[item.parent] = [];}
				content[item.parent].push(item);
			}
			
			temp = glueMenuItem(combine);
			this.template = data;
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
				var self = this,
					template;
					
				if(!self.link || blib.is(self.link,"null")){
					e.preventDefault ? e.preventDefault() : e.returnValue = false;
					self.animate();
					return false;
				}else{
					template = blib('.bTemplate')[0].blib.template.template;
					
					blib.ajax({
						url:self.link,
						data:{'ajax':true, 'template':template},
						dataType:'json',
						'success':function(data){
							blib('body').html(blib.build(data));
							self.setLocation();
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
					
				while(tracker.parent != self.block){
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

})(window);