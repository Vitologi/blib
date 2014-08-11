(function(){
	
	blib.build.define(
		{'block':'bDocumentation'},
		(function(){
			var singleton = false;
			
			
			return function(data){
				var navigation = data.navigation || false,
					item = data.item || false;
				
				if(navigation)navigation.elem = "navigation";
				if(item)item.elem = "item";
				
				if(singleton){
					if(navigation)singleton.setNavigation(navigation);
					if(item)singleton.setItem(item);
				}else{
					singleton = this;
					this.template = blib.clone(this.template);
					this.template.content = [
						navigation,
						item
					];
				}
			};			
		})(),
		false,
		{
			'setNavigation':function(elem){
				this.children.bDocumentation__navigation[0]._replace(elem);
			},
			'setItem':function(elem){
				this.children.bDocumentation__item[0]._replace(elem);
			}
		}
		
	);
	
	blib.build.define(
		{'block':'bDocumentation', 'elem':'navigation'},
		function(data){
			this.template = blib.clone(this.template);	
			this.id = data.start;
			this.navigation = {};
			
			var navigation = data.content;
			for(key in navigation){
				if(!this.navigation[navigation[key].parent])this.navigation[navigation[key].parent]=[];
				this.navigation[navigation[key].parent].push(navigation[key]);
			}
			
			this.template.content = [this.getChild(this.id)];
		},
		false,
		{
			'getChild':function(id,deep){
				if(!id)return {};
				deep = deep || 0;
				
				var nav = this.navigation[id],
					content = [],
					temp;
				
				for(key in nav){
					innerList = this.getChild(nav[key].id, deep+1);
					
					temp = {'elem':'li', 'tag':'li', 'content':[
						{'elem':'link', 'item':nav[key].id, 'content':nav[key].name}
					]};
					
					if(innerList){
						temp.content.unshift({'elem':'opener', 'content':"+"});
						temp.content.push(innerList);
					}
					
					content.push(temp);
				}
				
				temp = {'elem':'ul', 'tag':'ul', 'content':content};
				if(!deep)temp.mods = {"opened":true};
				
				return content.length?temp:false;
			}
		}
	);
	
	blib.build.define(
		{'block':'bDocumentation', 'elem':'item'},
		function(data){
			//console.log(data);
		}
	);
	
	blib.build.define(
		{'block':'bDocumentation', 'elem':'link'},
		function(data){
			this.item = data.item;
			this.template = blib.clone(this.template);	
			this.template.content = data.content;
		},
		{
			'tag':'a',
			'attrs':{
				'href':'#'
			}
		},
		{
			'onclick':function(){
				var self = this;
				blib.ajax({
					'data':{'blib':'bDocumentation', 'id':this.item, 'ajax':true},
					'success':function(data){
						var item = {
							'elem':'item',
							'content':data.item
						};						
						self.block.setItem(item);
					}
				});				
			}
		}
	);
	
	blib.build.define(
		{'block':'bDocumentation', 'elem':'ul'},
		function(data){
			this.opened = false;
			this.template = blib.clone(this.template);	
			this.template.content = data.content;
			this.template.mods = data.mods;
		},
		{
			"tag":"ul"
		},
		{
			'toggle':function(){
				if(this.opened){
					this._setMode("opened",false);
					this.opened = false;
				}else{
					this._setMode("opened",true);
					this.opened = true;
				}
			}
		}
	);
	
	blib.build.define(
		{'block':'bDocumentation', 'elem':'opener'},
		function(data){
			this.template = blib.clone(this.template);	
			this.template.content = data.content;
		},
		false,
		{
			'onclick':function(){
				this.parent.children.bDocumentation__ul[0].toggle();	
			}
		}
	);

})(window)