(function(){
	
	var actions = {
		'val':function(){
			return this.dom.value;
		}
	}
	
	
	blib.build.define(
		{'block':'bUser'},
		function(data){
			var content,
				_this = this;
			
			_this.login = data.content || false;
			
			
			if(_this.login){
				content = [
					{"content":[
						{"tag":"span", "content":"Вы авторизованы как: "},
						{"elem":"name", "tag":"span", "content":_this.login}
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
			
			
			
			_this.template = data;
			if(!blib.is(_this.template.attrs,'object'))_this.template.attrs = {};
			_this.template.attrs.action = location.href;
			_this.template.content = content;

		},
		{
			'tag':'form', 'attrs':{'method':'POST', 'encoding':'application/x-www-form-urlencoded'}
		},
		{
			'submit':function(){
				var _this = this,
					url = _this.template.attrs.action,
					data = ( _this.login)?{'logout':true}:{
						'login': _this.getLogin(),
						'password': _this.getPassword()
					},
					temp;
				
				if(!_this.login && (temp = _this.getSave())) data.save = temp;
				data.ajax = true;
				
				blib.ajax({
					'url':url,
					'data':data,
					'dataType':'json',
					'success':function(data){
						blib.build(data);
					}
				});
			},
			'getLogin':function(){
				var _this = this;
				return _this.children.bUser__login[0].val();
			},
			'getPassword':function(){
				var _this = this;
				return _this.children.bUser__password[0].val();
			},
			'getSave':function(){
				var _this = this;
				return _this.children.bUser__save[0].val();
			}
		}
	);
	
	blib.build.define(
		{'block':'bUser', 'elem':'login'},
		function(data){
			
		},
		{
			'tag':'input', 'attrs':{'type':'text', 'name':'login', 'placeholder':'Логин'}
		},
		actions
	);

	blib.build.define(
		{'block':'bUser', 'elem':'password'},
		function(data){
			
		},
		{
			'tag':'input', 'attrs':{'type':'password', 'name':'password', 'placeholder':'Пароль'}
		},
		actions
	);
	
	blib.build.define(
		{'block':'bUser', 'elem':'save'},
		function(data){
			
		},
		{
			'tag':'input', 'attrs':{'type':'checkbox', 'name':'save'}
		},
		{
			'val':function(){
				return (this.dom.checked?this.dom.value:undefined);
			}
		}
	);
	
	blib.build.define(
		{'block':'bUser', 'elem':'submit'},
		function(data){
			
		},
		{
			'tag':'input', 'attrs':{'type':'submit'}
		},
		{
			'onclick':function(e){
				e.preventDefault ? e.preventDefault() : e.returnValue = false;
				this.block.submit();
			}
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
				this.block.submit();
			}
		}
	);
	
})(window);
