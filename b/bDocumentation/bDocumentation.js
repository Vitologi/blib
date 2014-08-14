(function(){
	
	blib.build.define(
		{'block':'bDocumentation'},
		function(data){
			var chapter = (data.chapter)?{"block":"bDocumentation", "elem":"chapter", "chapter":data.chapter}:false,
				item = (data.item)?{"block":"bDocumentation", "elem":"item", "item":data.item}:false;
			
			if(data.navigation)this.navigation = data.navigation;
			
			if(!(chapter && item) && this.singleton){
				return (chapter)?this.singleton.setChapter(chapter):this.singleton.setItem(item);
				
			}
			
			this.template = blib.clone(this.template);
			this.template.content = [
				{
					"elem":"outer",
					"content":[
						{
							"elem":"inner",
							"content":[
								chapter,
								item
							]
						}
					]
				}
			];
			
			this.constructor.prototype.singleton = this;
			
		},
		false,
		{
			'getNavigation':function(id){
				for(key in this.navigation){
					if(this.navigation[key].id == id)return this.navigation[key];
				}
			},
			'setChapter':function(data){
				this.children.bDocumentation__chapter[0]._replace(data);
			},
			'setItem':function(data){
			console.log(data);
				this.children.bDocumentation__item[0]._replace(data);
			}
		}
		
	);
	
	blib.build.define(
		{'block':'bDocumentation', 'elem':'chapter'},
		function(data){
			var navigation = this.block.navigation,
				header = "Content";
			
			this.template = blib.clone(this.template);	
			this.id = data.chapter;
			this.navigation = {};			
				
			for(key in navigation){
				if(!this.navigation[navigation[key].parent])this.navigation[navigation[key].parent]=[];
				this.navigation[navigation[key].parent].push(navigation[key]);
				if(navigation[key].id == this.id)header=navigation[key].name;
			}

			this.template.content = [
				{"elem":"header", "mods":{"center":true}, "content":header},
				{"tag":"hr", "attrs":{"id":"hr", "style":"width:95%,margin:2px auto;"}},
				this.getChild(this.id)
			];
			
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
			this.item = data.item;
			
			this.template = blib.clone(this.template);	
			this.template.content = [
				this.getBreadcrumbs()
			];
		},
		false,
		{
			'getBreadcrumbs':function(){
				
				return {
					"content":[
						{"elem":"link", "mods":{"breadcrumbs":true}, "item":this.item.parent, "content":this.block.getNavigation(this.item.parent).name},
						{"tag":"span", "content":this.block.getNavigation(this.item.id).name} 
					]
				};
			}
		}
	);
	
	blib.build.define(
		{'block':'bDocumentation', 'elem':'link'},
		function(data){
			
			this.item = data.item;
			this.template = blib.clone(this.template);	
			this.template.content = data.content;
			this.template.mods = data.mods;
		},
		{
			'tag':'a',
			'attrs':{
				'href':'#'
			}
		},
		{
			'onclick':function(){
				var self = this,
					links = self.block.children['bDocumentation__link'];
				
				if(!this._getMode("breadcrumbs")){for(key in links)links[key]._setMode("active",false)};
				
				blib.ajax({
					'data':{'blib':'bDocumentation', 'id':this.item, 'ajax':true},
					'dataType':'json',
					'success':function(data){
						var item = {
							'elem':'item',
							'content':data.item
						};
						blib.build(data);
						self._setMode("active",true);
					}
				});				
			}
		}
	);
	
	blib.build.define(
		{'block':'bDocumentation', 'elem':'ul'},
		function(data){
			this.template = blib.clone(this.template);	
			this.template.content = data.content;
			this.template.mods = data.mods || {};
		},
		{
			"tag":"ul"
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
				var ul = this.parent.children.bDocumentation__ul[0],
					status = ul._getMode("opened");
								
				if(status){
					ul._setMode("opened",false);
					this._setMode("opened",false);
					this.template.content = "+";
					this.dom.innerHTML = "+";
				}else{
					ul._setMode("opened",true);
					this._setMode("opened",true);
					this.template.content = "-";
					this.dom.innerHTML = "-";
				}
			}
		}
	);

})(window)