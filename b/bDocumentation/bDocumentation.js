(function(){
	
	blib.build.define(
		{'block':'bDocumentation'},
		(function(){
			var singleton = false;
			
			
			return function(data){
				var group = data.group,
					item = data.item;
				
				if(singleton){
					if(group)singleton.setGroup(group);
					if(item)singleton.setItem(item);
				}else{
					singleton = this;
					this.template = blib.clone(this.template);
					this.template.content = [
						{'elem':'group', 'content':group},
						{'elem':'item', 'content':item}
					];
				}
			};			
		})(),
		false,
		{
			'setGroup':function(elem){
				this.children.bDocumentation__group[0]._replace(elem);
			},
			'setItem':function(elem){
				this.children.bDocumentation__item[0]._replace(elem);
			}
		}
		
	);
	
	blib.build.define(
		{'block':'bDocumentation', 'elem':'group'},
		function(data){
			this.template = blib.clone(this.template);	
			this.id = data.content.start;
			this.navigation = {};
			
			var navigation = data.content.navigation;
			for(key in navigation){
				if(!this.navigation[navigation[key].group])this.navigation[navigation[key].group]=[];
				this.navigation[navigation[key].group].push(navigation[key]);
			}
			
			this.template.content = [this.getChild(this.id)];
		},
		false,
		{
			'getChild':function(id){
				if(!id)return {};
				var nav = this.navigation[id],
					content = [],
					temp;
				
				for(key in nav){
					temp = {'elem':'li', 'tag':'li', 'content':[
						{'elem':'opener', 'content':"+"},
						{'elem':'link', 'item':nav[key].id, 'content':nav[key].name}
					]};
					temp.content.push(this.getChild(nav[key].id));
					content.push(temp);
				}
				
				return {'elem':'ul', 'tag':'ul', 'content':content};
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

})(window)