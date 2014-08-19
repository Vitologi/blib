(function(){
	
	blib.build.define(
		{'block':'bForm'},
		function(data){
			
			this.ajax = data.ajax || data.attrs.ajax;
			this.processor = data.processor || false;
			this.action = data.action || data.attrs.action;
			this.fields = {};
		
			this.template = data;
		},
		{
			'tag':"form"
		},
		{
			'serialize':function(){
				
			}
		}
	);
	
	blib.build.define(
		{'block':'bForm', 'elem':'message'},
		function(data){
			this.template = data;
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
			var block = this.block;
			
			block.fields[data.name || data.attrs.name] =  data.value  || data.attrs.value;
			this.template = data;
			
		},
		{'tag':"input", 'attrs':{'type':"text"}}
	);

	blib.build.define(
		{'block':'bForm', 'elem':'submit'},
		function(data){
			this.template = data;
		},
		{'tag':"input", 'attrs':{'type':"submit"}},
		{
			'onclick':function(e){
				var self = this,
					block = self.block,
					request = {};
				
				if(block.processor)request.blib = block.processor;
					
				if(block.ajax){
					e.preventDefault();
					
					blib.ajax({
						'url':block.action,
						'data':request,
						'success':function(data){
							block.children.bForm__message[0]._append({"content":data},true);
						}
					});
				}else{
					
					
				}
				
				
				
			}
		}
	);
	
})();
