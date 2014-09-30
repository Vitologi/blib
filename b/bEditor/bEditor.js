(function(){
	
	//standart function for element
	var buttonProto = {
			
		},
		buttonFunction = function(obj){
			for(var key in obj)this[key]=obj[key];
		};
		
	buttonFunction.prototype = buttonProto;
	
	//EDITOR
	blib.build.define(
		{'block':'bEditor'},
		function(data){
			this.output = data.output;
			
			this.config = blib.extend(true, (data.config || {}), this.config);
			this.template = {'content':[
				{'block':'bEditor', 'elem':'panel'},
				{'block':'bEditor', 'elem':'board'},
				{'block':'bEditor', 'elem':'example'}
			]};
		},
		false,
		{
			'config':{
				'type':'json',
				'callback':false,
				'error':false
			},
			'test':function(){
				var data = {'content':this.children.bEditor__board[0].dom.innerHTML};
				this.output(JSON.stringify(data));
				this.children.bEditor__example[0].setExample(data);				
			}
		}
	);
	
	//PANEL
	blib.build.define(
		{'block':'bEditor', 'elem':'panel'},
		function(data){
			this.template = data;
		}
	);
	
	//BOARD
	blib.build.define(
		{'block':'bEditor', 'elem':'board'},
		function(data){
			this.template = data;
		},
		{'attrs':{'contenteditable':true}}
	);
	
	//EXAMPLE
	blib.build.define(
		{'block':'bEditor', 'elem':'example'},
		function(data){
			this.template = data;
		},
		false,
		{
			'setExample':function(data){
				this._removeChildren();
				this._append(data);
			}
		}
	);
	
})();