(function(){
	
	blib.build.define(
		{'block':'bDocumentation'},
		function(data){
			var chapter = {"block":"bDocumentation", "elem":"chapter", "chapter":(data.chapter?data.chapter:false)},
				item = {"block":"bDocumentation", "elem":"item", "item":(data.item?data.item:false)};

			if(data.navigation)this.navigation = data.navigation;
			
			if(!(data.chapter && data.item) && this.singleton){
				return (data.chapter)?this.singleton.setChapter(chapter):this.singleton.setItem(item);
			}

			this.template = {
				"content":[
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
				]
			};

			this.constructor.prototype.singleton = this;			
		},
		false,
		{
			'getNavigation':function(id){

				for(key in this.navigation){
					if(this.navigation[key].id == id)return this.navigation[key];
				}
                return {};
			},
			'setChapter':function(data){
				this.children.bDocumentation__chapter[0]._replace(data);
			},
			'setItem':function(data){
				this.children.bDocumentation__item[0]._replace(data);
			},
			'_onRemove':[function(){
				this.constructor.prototype.singleton = false;
			}]
		}
		
	);
	
	blib.build.define(
		{'block':'bDocumentation', 'elem':'chapter'},
		function(data){
			if(!data.chapter)return;
			
			var navigation = this.block.navigation,
				header = "Content";
			
			this.id = data.chapter;
			this.navigation = {};			
				
			for(key in navigation){
				if(!this.navigation[navigation[key].bdocumentation_id])this.navigation[navigation[key].bdocumentation_id]=[];
				this.navigation[navigation[key].bdocumentation_id].push(navigation[key]);
				if(navigation[key].id == this.id)header=navigation[key].name;
			}

			this.template = { 
				"content":[
					{"elem":"header", "mods":{"center":true}, "content":header},
					{"tag":"hr", "attrs":{"id":"hr", "style":"width:95%;margin:2px auto;"}},
					this.getChild(this.id)
				]
			};
			
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
			if(!data.item)return;
			this.item = data.item;
			
			this.template = {
				"content":[
					this.getBreadcrumbs(),
					this.getDescription(),
					this.getContent()
				]
			};
		},
		false,
		{
			'getBreadcrumbs':function(){
				var parent = this.block.getNavigation(this.item.bdocumentation_id),
					name = (parent)?parent.name:"||";
				return {
					"content":[
						{"elem":"link", "mods":{"breadcrumbs":true}, "item":this.item.bdocumentation_id, "content":name},
						{"tag":"span", "content":this.block.getNavigation(this.item.id).name} 
					]
				};
			},
			'getDescription':function(){
				return {"elem":"itemDescription", "content":(this.item.description)?[this.item.description]:"Описание отсутствует"};
			},
			'getContent':function(){
				var grouping = this.item.content,
					content = {"elem":"content", "content":[]},
					group, temp, key, i, j, parent;
				
				this.grouping = {};


				for(key in grouping){
                    parent = grouping[key].bdocumentation_id;
                    if(blib.is(parent, 'null'))continue;

					if(!this.grouping[parent])this.grouping[parent]=[];
					this.grouping[parent].push(grouping[key]);
				}


				for(i in this.grouping){
					temp = this.grouping[i];
					group = {
						"elem":"group",
						"content":[
							{"elem":"link", "mods":{"big":true}, "item":i, "content":this.block.getNavigation(i).name}
						]
					};
					
					for(j in temp){
						group.content.push({"elem":"noteGroup", "content":[
							{"elem":"link", "item":temp[j].id, "content":temp[j].name},
							{"elem":"note", "content":temp[j].note}
						]});
					}
					
					content.content.push(group);
				}


				return content;
			}
		}
	);
	
	blib.build.define(
		{'block':'bDocumentation', 'elem':'link'},
		function(data){
			
			this.item = data.item;
			this.template = data;
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
					'dataType':'json',
					'success':function(data){
						var item = {
							'elem':'item',
							'content':data.item
						};
						blib.build(data);
						self.activate();
					}
				});				
			},
			'activate':function(){
				var links = this.block.children['bDocumentation__link'],
					key;
					
				for(key in links){
					if(links[key].item === this.item){links[key]._setMode("active",true);}else{links[key]._setMode("active",false);};
				}
			}
		}
	);
	
	blib.build.define(
		{'block':'bDocumentation', 'elem':'ul'},
		function(data){
			this.template = data;
		},
		{
			"tag":"ul"
		}
	);
	
	blib.build.define(
		{'block':'bDocumentation', 'elem':'opener'},
		function(data){
			this.template = data;
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

})(window);