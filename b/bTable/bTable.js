(function(){
	
	blib.build.define(
		{'block':'bTable'},
		function(data){
			var temp = [],
				meta = blib.extend({'position':{},'fields':{}}, data.meta);

			this.tunnel = data.tunnel || false;
			this.table = data.table;
			this.position = meta.position;
			this.fields = meta.fields;
			this.keys = meta.keys;
			this.page = meta.page;
			this.content = data.content;
			this.numColumn = 0;
			
			temp.push(this.getHeader());
			temp.push(this.getBody());
			temp.push(this.getFooter());
			
			this.template.mods = data.mods;
			this.template.content = temp;

		},
		{'tag':'table'},
		{
			'getHeader':function(){
				return {'block':'bTable', 'elem':'head'};
			},
			'getBody':function(){
				return {'block':'bTable', 'elem':'body', 'content':this.content};
			},
			'getFooter':function(){
				return {'block':'bTable', 'elem':'footer'};
			},
			'clearTunnel':function(){
				blib.config('tunnel.'+this.tunnel+'.items',[]);
			}
		}
	);
	
	blib.build.define(
		{'block':'bTable', 'elem':'head'},
		function(data){
			var block = this.block,
				position = block.position,
				fields = block.fields,
				temp = [],
				key, name;
			
			for(key in position){
				name = position[key];
				if(fields[name].type == 'hidden')continue;
				block.numColumn++;
				temp.push({'block':'bTable', 'elem':'th', 'note':fields[name].note, 'content':fields[name].title});
			}
			
			this.template.content = temp;

		},
		{'tag':'tr'}
	);
	
	
	
	blib.build.define(
		{'block':'bTable', 'elem':'th'},
		function(data){
			this.template = data;
		},
		{'tag':'th'}
	);
	
	blib.build.define(
		{'block':'bTable', 'elem':'body'},
		function(data){
			var content = data.content,
				temp = [], key;
			
			for(key in content){
				temp.push({'block':'bTable', 'elem':'tr', 'content':content[key]});
			}

			this.template.content = temp;

		},
		{'tag':'tbody'},
		{
			'rebuilding':function(data){
				this._replace({'block':'bTable', 'elem':'body', 'content':data});
			}
		}
	);
	
	blib.build.define(
		{'block':'bTable', 'elem':'tr'},
		function(data){
			var block = this.block,
				position = block.position,
				fields = block.fields,
				temp = [],
				key, name,
				content = this.content = data.content;
			
			for(key in position){
				name = position[key];
				if(fields[name].type == 'hidden')continue;
				temp.push({'block':'bTable', 'elem':'td', 'name':name, 'content':content[name]});
			}
			
			this.template.content = temp;

		},
		{'tag':'tr'},
		{
			'onclick':function(){
				var checked = this._getMode('checked');
				
				if(checked){
					this.uncheckItem();
				}else{
					this.checkItem();
				}
				
				this._setMode('checked', !checked);				
			},
			
			'checkItem':function(){
				var block = this.block,
					keys = block.keys,
					items = blib.config('tunnel.'+block.tunnel+'.items')||[],
					tunnel = {},
					item = {},
					i;
				
				for(i in keys){
					item[keys[i]] = this.content[keys[i]];
				}
				items.push(item);
				tunnel[block.tunnel] = {'items':items};
				blib.tunnel(tunnel);
			},
			
			'uncheckItem':function(){
				var block = this.block,
					keys = block.keys,
					items = blib.config('tunnel.'+block.tunnel+'.items')||[],
					tunnel = {},
					item, i, j, key, keyLen = 0, count;
					
				for(i in keys)keyLen++;
				
				for(i in items){
					item = items[i];
					count = 0;
					for(j in keys){
						key = keys[j];
						if(item[key] == this.content[key])count++;						
					}
					if(keyLen ==count)delete items[i];
				}
				
				tunnel[block.tunnel] = {'items':items};
				blib.tunnel(tunnel);
			}
		}
	);
	
	blib.build.define(
		{'block':'bTable', 'elem':'td'},
		function(data){
			var block = this.block,
				name = this.name = data.name,
				meta = block.fields[name],
				content = this.content = data.content;
			
			this.template.mods = meta.mods;
			this.template.content = content;

		},
		{'tag':'td'}
	);
	
	blib.build.define(
		{'block':'bTable', 'elem':'footer'},
		function(data){
			var block = this.block,
				numColumn = block.numColumn;

			this.template.content = [{'tag':'td', 'attrs':{'colspan':numColumn}, 'content':[{'block':'bTable', 'elem':'paginator'}]}];

		},
		{'tag':'tr'}
	);
	
	blib.build.define(
		{'block':'bTable', 'elem':'paginator'},
		function(data){
			var block = this.block,
				page = block.page,
				all = Math.ceil(page.count/page.rows),
				paginator = page.paginator,//(all>page.paginator?page.paginator:all),
				temp = [],
				i;
			
			page.paginator = paginator;
			page.all = all;
			
			for(i=0;i<all;i++){
				temp.push({
					'block':'bTable',
					'elem':'pagewrap',
					//'attrs':{'style':'width:'+(100/paginator)+'%;'},
					'content':[
						{'block':'bTable', 'elem':'page', 'id':i}
					]
				});
			}
			
			this.template.content = [{'block':'bTable', 'elem':'paginatorwrap', 'attrs':{'style':'width:'+(all*25)+'px;'}, 'content':temp}];

		},
		false,
		{
			
		}
	);
	
	blib.build.define(
		{'block':'bTable', 'elem':'page'},
		function(data){
			this.id = data.id;
			//if(this.block.page.number == )
			this.template.attrs = data.attrs;
			this.template.content = this.id+'';
		},
		{'tag':'span'},
		{
			'onclick':function(){
				var self = this,
					block = self.block,
					table = block.table,
					body = block.children.bTable__body[0];
				
				block.page.number = this.id;
				
				blib
				.tunnel({'bTable':{'page':block.page}})
				.ajax({
					'data':{'blib':'bTable','table':table},
					'dataType':'json',
					'success':function(data){
						body.rebuilding(data);
						self.setActive();
					}
				});
			},
			'setActive':function(){
				var block = this.block,
					number = block.page.number,
					concurent = block.children.bTable__page,
					key;
				
				for(key in concurent){
					concurent[key]._setMode('active',false);
				}
				
				if(number == this.id)this._setMode('active',true);
			}
		}
	);


})(window);