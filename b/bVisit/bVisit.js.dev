(function(){

	blib.localize({
		'bVisit':{
			'login': 'Логин',
			'time': 'Время',
			'ip': 'IP адрес'
		}
	},
	{
		'language': 'ru'
	});

	blib.build.define(
		{'block':'bVisit'},
		function(data){
			var _this = this,
				list = data.content,
				i, line;

			_this.template = {'content':[]};

			if(blib.is(list,'array')){

				for(i in list){
					line = list[i];
					line.elem = "line";
					_this.template.push(line);
				}

			}else{
				_this.template.mods = {'init':true};
			}
		},
		false,
		{
			'_onSetMode':{
				'init':function(){
					var _this = this,
						i, line;

					blib.ajax({
						'url':'/',
						'data':{
							'blib':'bVisit',
							'action':'index',
							'view':'json'
						},
						'dataType':'json',
						'success':function(data){
							if(!blib.is(data,'array'))return;

							for(i in data){
								line = data[i];
								line.block = "bVisit";
								line.elem = "line";
								_this._append(line);
							}
						}
					});
				}
			}
		}
	);

	blib.build.define(
		{'block':'bVisit', 'elem':'line'},
		function(data){
			var _this = this,
				time = data.time,
				note = data.note,
				login = data.login,
				ip = data.ip;


			_this.template = {'content':[
				{'elem':'lineItem', 'title':time.substr(0,10), 'content':note},
				{'elem':'hiddenLine', 'content':[
					{'elem':'lineItem', 'title':'bVisit.login', 'content':login},
					{'elem':'lineItem', 'title':'bVisit.time', 'content':time.substr(10)},
					{'elem':'lineItem', 'title':'bVisit.ip', 'content':ip}
				]}
			]};
		}
	);

	blib.build.define(
		{'block':'bVisit', 'elem':'lineItem'},
		function(data){
			var _this = this;

			_this.template = {'content':[
				{'elem':'lineTitle', 'content':data.title},
				{'elem':'lineContent', 'content':data.content}
			]};
		}
	);


})();