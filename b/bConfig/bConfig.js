
blib.build.define(
    {'block':'bConfig'},
    function(data){}
);

blib.build.define(
	{'block':'bConfig', 'elem':'list'},
	function(data){
		var _this = this,
            selected = data.selected,
            content = [],
			block, key;

        _this.list = data.content;

		for(key in data.content){
			block = data.content[key];

			content.push({
				'tag':'option',
                'attrs':{
                    'value':key,
                    'selected':(key == selected)
                },
				'content':block+'.name'
			});
		}

		this.template.content = content;
	},
    {'tag':'select'},
    {
        'onchange': function () {
            var _this = this,
                block = _this.dom.value;

            blib.build({
                "block":"bLink",
                "data":{'bPanel':{'controller':'bConfig__bPanel'},'bConfig__bPanel':{"block":block}, 'view':'json'},
                "go":true
            });

        }
    }
);
