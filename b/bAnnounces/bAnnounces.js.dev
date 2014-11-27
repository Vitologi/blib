(function(){
	
	blib.build.define(
		{"block":"bAnnounces"},
		function(data){
			var _this = this,
				meta = blib.extend({'count':0, 'limit':8, 'rows':4, 'height':25, 'rotationDelay':5000}, data.meta);			
			
			_this.count = meta.count;
			_this.limit = meta.limit;
			_this.storeInitialize();
			_this.rows = meta.rows;
			_this.height = meta.height;
			_this.rotationDelay = meta.rotationDelay;
			_this.announceLoadInterval = false;
			
			_this.template = {
				'mods':{'init':true},
				'attrs':{'style':'height:'+(meta.rows*meta.height)+'px;'},
				'content':[
					{'elem':'pushpin'},
					{'elem':'popup'},
					{'elem':'items', 'content':[]}
				]
			};
		},
		false,
		{
			'_onSetMode':{
				'init':function(){
					var _this = this;
					_this.items = _this.children.bAnnounces__items[0];
					_this.popup = _this.children.bAnnounces__popup[0];
					_this.getAnnounces();
					_this.items.rotation(_this.rotationDelay);
				}
			},
			'getAnnounces':function(limit){
				var _this = this,
					items = _this.items,
					limit = limit || _this.limit;
				
				blib.ajax({
					'data':{'blib':'bAnnounces', 'count':_this.count, 'limit':limit},
					'dataType':'json',
					'success':function(data){
						var item, i;
							
						for(i in data){
							item = data[i];
							items._append({"block":"bAnnounces", "elem":"item", "meta":item, 'content':item.content});
							_this.count++;
						}
					}
				});
			},
			'storeInitialize':function(){
				var store = blib.store;
				if(!store.get('bAnnounces'))store.set('bAnnounces',{});
			},
			'getRead':function(id){
				var store = blib.store;
				return store.get('bAnnounces.'+id);
			},
			'setRead':function(id){
				var store = blib.store;
				return store.set('bAnnounces.'+id, true);
			},
			'toggleModal':function(){
				var _this = this;
				_this.modal = !_this.modal;
				_this._setMode("modal", _this.modal);

				if(_this.modal){
					_this.items.rotationReset();
					_this.getAnnounces(10);
				}else{
					_this.items.rotation(_this.rotationDelay);
				}
			},
			'onmouseover':function(){
				var _this = this;
				_this.items.rotation();
			},
			'onmouseout':function(){
				var _this = this;
				if(_this.modal)return;
				_this.items.rotation(_this.rotationDelay);
			},
			'showMessage':function(){
				var _this = this;
				
			},
			'onscroll':function(){
				var _this = this,
					dom = _this.dom;
					
				window.clearTimeout(_this.announceLoadInterval);
					
				_this.announceLoadInterval = window.setTimeout(function(){
					var	height = dom.clientHeight,
						scrollHeight = dom.scrollHeight,
						position = dom.scrollTop,
						finish = scrollHeight - position;
					if(finish<=height)_this.getAnnounces();
				},300);
			}
		}
	);
	
	blib.build.define(
		{"block":"bAnnounces", "elem":"items"},
		function(data){
			var _this = this;
			
			_this.interval = false;
			_this.position = 0;
			_this.template = {'content':data.content};
		},
		false,
		{
			'show':function(num){
				var _this = this,
					block = _this.block,
					slideHeight = block.rows*block.height,
					margin;
					
				_this.position += num;
				
				if((block.count/block.rows)<=_this.position){
					margin = _this.position = 0;
				}else{
					margin = _this.position*slideHeight;
				}

				_this._attr('style','margin-top:-'+margin+'px;');				
			},
			'rotation':function(start){
				var _this = this;
				
				window.clearInterval(_this.interval);
				
				if(start){
					_this.interval = window.setInterval(function(){
						_this.show(1);
					}, start);
				}			
			},
			'rotationReset':function(){
				var _this = this;
				_this.position = 0;
				_this._attr('style','margin-top:0;');
				window.clearInterval(_this.interval);
			}
		}
	);
	
	blib.build.define(
		{"block":"bAnnounces", "elem":"item"},
		function(data){
			var _this = this,
				block = _this.block,
				meta = data.meta;
			
			_this.id = meta.id;
			_this.date = meta.date;
			_this.title = meta.title;
			_this.content = data.content;
			
			_this.isRead = block.getRead(_this.id);
			
			_this.template = {
				'attrs':{'style':'height:'+(block.height-5)+'px;'},
				'content':[
					{"block":"bAnnounces", "elem":"itemIcon", "mods":{"envelope":!_this.isRead}},
					{"block":"bAnnounces", "elem":"itemDate", "content":_this.date},					
					{"block":"bAnnounces", "elem":"itemContent", "content":_this.content.substr(0,40)+'...'}
				]
			};
		},
		false,
		{
			'onclick':function(){
				var _this = this,
					block = _this.block,
					icon = _this.children.bAnnounces__itemIcon[0];

				block.setRead(_this.id);
				icon.close();
				
				block.popup.show(_this.title, _this.content);
			}
		}
	);
	
	blib.build.define(
		{"block":"bAnnounces", "elem":"itemIcon"},
		function(data){
			var _this = this,
				mods = data.mods;
			_this.template = {'content':[{'block':'bIcomoon', 'mods':mods}]};
		},
		false,
		{
			'close':function(){
				var _this = this,
					icon = _this.children.bIcomoon[0];
				icon._attr("style","opacity:0.2;");
			}
		}
	);

	blib.build.define(
		{"block":"bAnnounces", "elem":"pushpin"},
		function(data){
			var _this = this,
				mods = data.mods;
			_this.template = {'content':[{'block':'bIcomoon', 'mods':{"pushpin":true}}]};
		},
		false,
		{
			'onclick':function(){
				var _this = this,
					block = _this.block;
				block.toggleModal();
			}
		}
	);
	
	blib.build.define(
		{"block":"bAnnounces", "elem":"popup"},
		function(data){
			var _this = this;
			_this.header = false;
			_this.content = false;

			_this.template = {
				'mods':{'init':true},
				'content':[
					{
						'elem':'popupWrapper',
						'content':[
							{'elem':'popupCloser'},
							{'elem':'popupHeader'},
							{'elem':'popupContent'}
						]
					}
				]
			};
		},
		false,
		{
			'_onSetMode':{
				'init':function(){
					var _this = this,
						wrapper = _this.children.bAnnounces__popupWrapper[0];
					_this.header = wrapper.children.bAnnounces__popupHeader[0];
					_this.content = wrapper.children.bAnnounces__popupContent[0];
				}
			},
			'show':function(title, text){
				var _this = this;
				_this._setMode('active', true);
				_this.header.dom.innerHTML = title;
				_this.content.dom.innerHTML = text;
			},
			'close':function(){
				var _this = this;
				_this._setMode('active', false);
			}
		}
	);
	
	blib.build.define(
		{"block":"bAnnounces", "elem":"popupCloser"},
		function(data){
			var _this = this;
			_this.template = {'content':'X'};
		},
		false,
		{
			'onclick':function(){
				var _this = this,
					popup = _this.parent.parent;
				popup.close();
			}
		}
	);
	
})();
