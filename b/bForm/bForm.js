(function(){
	
	blib.build.define(
		{'block':'bForm'},
		function(data){
			
			this.ajax = data.ajax;
			this.processor = data.processor;
			this.action = data.attrs.action;
		
			this.template = blib.clone(this.template);
			this.template.mods = data.mods;
			this.template.content = data.content;
			this.template.attrs = data.attrs;
			

		},
		{
			'tag':"form"
		}
	);
	
	blib.build.define(
		{'block':'bForm', 'elem':'message'},
		function(data){
			this.template = blib.clone(this.template);
			this.template.mods = data.mods;
			this.template.content = data.content;
			this.template.attrs = data.attrs;
		},
		false,
		{
			'setText':function(text){
				this.template.content = text;
				this.dom.innerHTML = text;
			}
		}
	);
	
	blib.build.define(
		{'block':'bForm', 'elem':'text'},
		function(data){
			this.template = blib.clone(this.template);
			this.template.mods = data.mods;
			this.template.content = data.content;
			this.template.attrs = data.attrs;
		},
		{'tag':"input", 'attrs':{'type':"text"}}
	);

	blib.build.define(
		{'block':'bForm', 'elem':'submit'},
		function(data){
			this.template = blib.clone(this.template);
			this.template.mods = data.mods;
			this.template.content = data.content;
			this.template.attrs = data.attrs;
		},
		{'tag':"input", 'attrs':{'type':"submit"}},
		{
			'onclick':function(e){
				var self = this,
					block = self.block;
					
				if(block.ajax){
					e.preventDefault();
					
					blib.ajax({
						'url':block.action,
						'data':{'blib':block.processor},
						'success':function(data){
							block.children.bForm__message[0].setText(data);
						}
					});
					
				}
			}
		}
	);
	
})();
