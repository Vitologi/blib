blib.build.define(
	{'block':'bIndex'},
	(function(){
		var count = 0;
		return function(data){
			var self=this;
			this.setTemplate({
				'content':[
					{'elem':'header', 'content':[
						{'block':'bImageSprite', 'mods':{'sprite':'blib', 'type':'logo'}}
					]},
					{'elem':'body', 'content':[
						{'elem':'helper', 'content':'helper'},
						{'elem':'content', 'content':'content'}
					]},
					{'elem':'footer', 'content':'footer'}
				]
			});
			
			if(count === 0){
				count++;
				blib.ajax({
					data:{rewrite:true},
					dataType:'json',
					success:function(data){
						blib.build.redefine(
							{'block':'bIndex'},
							function(){
								this.setTemplate({'content':data.content});
							}
						);
						blib('body').html(blib.build({block:'bIndex'}));
					}
				});
			}
		}
	})()
);
