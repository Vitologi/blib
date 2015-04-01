(function(){
	
	blib.build.define(
		{'block':'bTable'},
		function(data){
			var _this = this,
				temp = [],
				meta = blib.extend({
					'name':'defaultTable',
					'tunnel':{},
					'position':{},
					'fields':{},
					'key':[],
					'page':{
						'number':0,
						'rows':0,
						'count':0
					}
				}, data.meta);

			_this.meta = meta;
			_this.content = data.content;
			_this.numColumn = 0;
			_this.checkedRow = 0;
			
			temp.push(this.getHeader());
			temp.push(this.getBody());
			temp.push(this.getFooter());

			_this.template = {};

			_this.template.mods = data.mods;
			_this.template.content = temp;

			_this._static('bLink').setUphold(meta.name, this);

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
			},
			'_onRemove':[
				function(){
					var _this = this,
						meta = _this.meta;

					_this._static('bLink').dropUphold(meta.name, _this);
				}
			],
			'_getStatus':function(){
				return (this.checkedRow<1)?{'error':1, 'code':1, 'message':"There is no selected lines."}:{'error':0, 'code':0, 'message':"0"};
			},
			'checkRow':function(flag){
				this.checkedRow += flag?1:-1;
			}
		}
	);
	
	blib.build.define(
		{'block':'bTable', 'elem':'head'},
		function(data){
			var _this = this,
				block = _this.block,
				meta = _this.block.meta,
				position = meta.position,
				fields = meta.fields,
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
				var block = this.block;
				block.checkedRow = 0;
				this._replace({'block':'bTable', 'elem':'body', 'content':data});
			}
		}
	);
	
	blib.build.define(
		{'block':'bTable', 'elem':'tr'},
		function(data){
			var _this = this,
				meta = _this.block.meta,
				position = meta.position,
				fields = meta.fields,
				temp = [],
				key, name,
				content = _this.content = data.content;
			
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
				var _this = this,
					checked = _this._getMode('checked');
				
				if(checked){
					_this.uncheckItem();
				}else{
					_this.checkItem();
				}

				_this._setMode('checked', !checked);
			},
			
			'checkItem':function(){
				var _this = this,
					block = _this.block,
					meta = _this.block.meta,
					keys = meta.keys,
					items = blib.config('tunnel.'+meta.tunnel.blib+'.items')||[],
					tunnel = {},
					item = {},
					i;
				
				for(i in keys){
					item[keys[i]] = _this.content[keys[i]];
				}
				items.push(item);
				tunnel[meta.tunnel.blib] = {'items':items};
				blib.tunnel(tunnel);
				block.checkRow(true);
			},
			
			'uncheckItem':function(){
				var _this = this,
					block = _this.block,
					meta = _this.block.meta,
					keys = meta.keys,
					tunnel = blib.config('tunnel'),
					items = blib.config('tunnel.'+meta.tunnel.blib+'.items')||[],
					item, i, j, key, keyLen = 0, count;
					
				for(i in keys)keyLen++;
				
				for(i in items){
					item = items[i];
					count = 0;
					for(j in keys){
						key = keys[j];
						if(item[key] == this.content[key])count++;						
					}
					if(keyLen ==count)items.splice(i,1);
				}
				
				tunnel[meta.tunnel.blib]['items'] = items;
				blib.tunnel(tunnel,true);
				block.checkRow(false);
			}
		}
	);
	
	blib.build.define(
		{'block':'bTable', 'elem':'td'},
		function(data){
			var _this = this,
				meta = _this.block.meta,
				name = _this.name = data.name,
				metaTD = meta.fields[name],
				content = _this.content = data.content;
			
			_this.template.mods = metaTD.mods;
			_this.template.content = content;

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
			var _this = this,
				meta = _this.block.meta,
				page = meta.page,
				all = Math.ceil(page.count/page.rows),
				temp = [],
				i, mods = {};
			
			page.all = all;
			
			for(i=0;i<all;i++){
				temp.push({
					'block':'bTable',
					'elem':'pagewrap',
					'content':[
						{'block':'bTable', 'elem':'page', 'id':i, 'mods':{'active':(page.number === i)}}
					]
				});
			}
			
			this.template.content = [{'block':'bTable', 'elem':'paginatorwrap', 'attrs':{'style':'width:'+(all*25)+'px;'}, 'content':temp}];

		}
	);
	
	blib.build.define(
		{'block':'bTable', 'elem':'page'},
		function(data){
			this.id = data.id;
			this.template.mods = data.mods;
			this.template.attrs = data.attrs;
			this.template.content = this.id+1+'';
		},
		{'tag':'span'},
		{
			'onclick':function(){
				var _this = this,
					block = _this.block,
					meta = block.meta,
					name = meta.name,
					tunnel = meta.tunnel,
					data = {
						'blib':block.tunnel,
						'_tunnel':{}
					},
					body = block.children.bTable__body[0],
					tempTunnel;

				tempTunnel = {'_tunnel':{'bTable':{}}};
				tempTunnel['_tunnel']['bTable'][name]={'page':meta.page};
				tunnel = blib.extend(true, tunnel, tempTunnel);

				tunnel._tunnel.bTable[name].page.number = _this.id;

				blib.ajax({
					'data':tunnel,
					'dataType':'json',
					'success':function(data){
						meta.page.number = _this.id;
						body.rebuilding(data);
						_this.setActive();
					}
				});
			},
			'setActive':function(){
				var _this = this,
					block = _this.block,
					meta = _this.block.meta,
					number = meta.page.number,
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