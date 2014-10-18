(function(){
	
	blib.build.define(
		{'block':'bSlider'},
		function(data){
			var temp = [],
				self = this;
			
			for(key in data.content){
				temp.push(data.content[key]);
			}
			this.length = temp.length;
			this.curent = 0;
			this.delay = data.delay || 10000;
			this.over = false;
			
			this.template = data;
			this.template.content = [{"elem":"wrapper", "content":temp},{"elem":"paginator"}];
			
			this.interval = window.setInterval(function(){
				if(self.over)return;
				self.curent = (self.curent == self.length-1)?0:self.curent+1;
				self.show(self.curent);
			}, this.delay);
		},
		false,
		{
			'show':function(id){
				this.children.bSlider__wrapper[0].show(id);
				this.children.bSlider__paginator[0].show(id);
			},
			'onmouseover':function(){
				console.log('over');
				this.over = true;
			},
			'onmouseout':function(){
				console.log('out');
				this.over = false;
			}
		}
	);
	
	blib.build.define(
		{'block':'bSlider', 'elem':'wrapper'},
		function(data){
			var block = this.block;
			
			this.template = data;
			this.template.attrs = {};
			switch(block._getMode('type')){
				case "horizontal":
					this.template.attrs.style = "width:"+(block.length*100)+"%;";
					this.width = this.template.attrs.style;
					break;
			}
		},
		false,
		{
			'show':function(id){
				id = id || 0;
				var block = this.block,
					margin = (id === 0)?"0":"-"+id+"00%";
				this._attr("style", this.width+"margin-left:"+margin+";");
			}
		}
	);
	
	blib.build.define(
		{'block':'bSlider', 'elem':'slide'},
		function(data){
			var block = this.block;
			
			this.template = data;
			this.template.attrs = {};
			switch(block._getMode('type')){
				case "horizontal":
					this.template.attrs.style = "width:"+(100/block.length)+"%;";
					break;
			}
		}
	);
	
	blib.build.define(
		{'block':'bSlider', 'elem':'paginator'},
		function(data){
			var block = this.block,
				len = block.length,
				temp=[], i=0;
			
			for(;i<len;i++)temp.push({'elem':'page', 'index':i});
			this.template = {'content':temp};
		},
		{'tag':'center'},
		{
			'show':function(id){
				id = id || 0;
				var self = this,
					pages = self.children.bSlider__page;
				
				for(key in pages)pages[key]._setMode('active');
				
				pages[id]._setMode('active',true);
			}
		}
	);
	
	blib.build.define(
		{'block':'bSlider', 'elem':'page'},
		function(data){
			this.index = data.index;
		},
		{'tag':'span'},
		{
			'onclick':function(){
				var block = this.block;
				block.show(this.index);
			}
		}
	);
	
})(window);