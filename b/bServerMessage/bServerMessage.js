(function(blib){

	var init = false,
		dialog = null;

	blib.build.define(
		{'block':'bServerMessage'},
		function(data){

			if(!init){
				init = true;
				blib('body').append(blib.build({'block':'bServerMessage'}));

			}

			if(dialog){
				dialog._append({
					'block':'bServerMessage',
					'elem':'message',
					'content':data.content
				});
				this.template = false;
			}
		},
		{'mods':{'init':true}},
		{
			'_onSetMode':{
				'init':function(){
					dialog = this;
				}
			}
		}
	);

	blib.build.define(
		{'block':'bServerMessage', 'elem':'message'},
		function(data){
			this.template.content = data.content;
		},
		{'mods':{'init':true}},
		{
			'_onSetMode':{
				'init':function(){
					var _this = this,
						timer = window.setTimeout(function(){
							_this._remove();
						}, 10000);
				}
			}
		}
	);

})( Blib );


(function(blib){

	blib.localize({
		'accesserror':'Доступ к запрошенному сервису закрыт'
	},
	{
		'language':'ru'
	});

})( Blib );
