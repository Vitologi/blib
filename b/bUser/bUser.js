(function(){
	
	blib.build.define(
		{'block':'bUser'},
		function(data){
			var content;
			
			this.login = data.content || false;
			
			
			if(this.login){
				content = [
					{"content":[
						{"tag":"span", "content":"Вы авторизованы как: "},
						{"elem":"name", "tag":"span", "content":this.login}
					]},
					{"elem":"logout"}
				];
			}else{
				content = [
					{"elem":"login"},
					{"elem":"password"},
					{"content":[
						{'tag':'label', 'attrs':{'style':'display:inline-block;'}, "content":[
							{"elem":"save"},
							{"tag":"span", "content":"запомнить"}
						]},
						{"elem":"submit"}
					]}
				];
			}
			
			
			this.template = data;
			if(!('attrs' in this.template))this.template.attrs = {};
			this.template.attrs.action = location.href;
			this.template.content = content;

		},
		{
			'tag':'form', 'attrs':{'method':'POST', 'encoding':'application/x-www-form-urlencoded'}
		}
	);
	
	blib.build.define(
		{'block':'bUser', 'elem':'login'},
		function(data){
			
		},
		{
			'tag':'input', 'attrs':{'type':'text', 'name':'login', 'placeholder':'Логин'}
		}
	);

	blib.build.define(
		{'block':'bUser', 'elem':'password'},
		function(data){
			
		},
		{
			'tag':'input', 'attrs':{'type':'password', 'name':'password', 'placeholder':'Пароль'}
		}
	);
	
	blib.build.define(
		{'block':'bUser', 'elem':'save'},
		function(data){
			
		},
		{
			'tag':'input', 'attrs':{'type':'checkbox', 'name':'save'}
		}
	);
	
	blib.build.define(
		{'block':'bUser', 'elem':'submit'},
		function(data){
			
		},
		{
			'tag':'input', 'attrs':{'type':'submit'}
		}
	);
	
	blib.build.define(
		{'block':'bUser', 'elem':'logout'},
		function(data){
			
			this.template.content = [
				{"tag":"input","attrs":{"type":"hidden","name":"logout","value":"true"}},
				{"content":"Выйти"}
			];
			
		},
		{
			'tag':'a', 'attrs':{'href':'#'}
		},
		{
			'onclick':function(e){
				e.preventDefault ? e.preventDefault() : e.returnValue = false;
				this.block.dom.submit();
			}
		}
	);
	
})(window);
