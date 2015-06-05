(function(){

	blib.localize({
		'bFeedBack':{
			'submit':{
                'thread':'Добавить заявку',
                'reply':'Добавить комментарий'
            } ,
            'detail':'Для отправки заявки, просто введите сообщение в окно слева и нажмите "Добавить заявку". ' +
            '<br/> Для добавления комментария к заявке выделите ее и нажмите "Добавить комментарий".' +
            '<br/> В этом окне будет показана история Вашей переписки.',
            'message':{
                'ready':' ',
                'threadSuccess':'заявка успешно загеристрирована',
                'replySuccess':'комментарий успешно добавлен',
                'isBlocking':'идет обработка',
                'disabledThread':'заявка закрыта',
                'emptyContent':'заполните текстовое поле'
            },
            'content':'введите текст заявки/комментария',
            'I':'я: ',
            'manager':'менеджер: '
		}
	},
	{
		'language': 'ru'
	});

	blib.build.define(
		{'block':'bFeedBack'},
		function(data){
			var _this = this,
                threads = [], i;

            _this.meta = blib.extend({'themes':[],'threads':[]},data.meta);
            _this.detail = null;
            _this.message = null;
            _this.themeList = null;
            _this.content = null;
            _this.load = null;
            _this.submitObj = null;

            for(i in _this.meta.threads){
                threads.push({"elem":"thread","meta":_this.meta.threads[i]});
            }

            _this.template.mods = data.mods;
            _this.template.content = [
                {"elem":"tools","content":[
                    {"elem":"message"},
                    {"elem":"themeList"},
                    {"elem":"content"},
                    {"elem":"submit"},
                    {"elem":"load"}
                ]},
                {"elem":"tools","content":[
                    {"elem":"threadList", "content":threads}
                ]},
                {"elem":"detail", "content":'bFeedBack.detail'}
            ];
		},
        false,
        {
            'getThemes':function(){
                var _this = this;

                return _this.meta.themes;
            },
            'getTheme':function(id){
                var _this = this,
                    themes = _this.meta.themes,
                    i;

                for(i in themes){
                    if(themes[i]['id']==id)return themes[i]['name'];
                }
            },
            'getThread':function(id){
                var _this = this,
                    threads = _this.meta.threads,
                    i;

                for(i in threads){
                    if(threads[i]['id']==id)return threads[i];
                }
            },
            'getActiveThread':function(){
                var _this = this,
                    threads = _this.children.bFeedBack__thread || [],
                    i;

                for(i in threads){
                    if(threads[i]._getMode('selected'))return threads[i].meta;
                }
            },
            'addThread':function(data){
                var _this = this,
                    threadList = _this.children.bFeedBack__threadList[0],
                    thread = blib.build({"block":"bFeedBack", "elem":"thread","meta":data}, {'parent':_this, 'blocks':[_this]});

                threadList._append(thread.blib, true);
                threadList.dom.insertBefore(thread, threadList.dom.firstChild);

                thread.blib.onclick();
            },
            'addReply':function(reply, thread){
                var _this = this,
                    detail = _this.detail,
                    nodeType, content;

                if(reply.user == thread.user){
                    nodeType = 'author';
                    content = blib.localize('bFeedBack.I')+reply.content;
                }else {
                    nodeType = 'manager';
                    content = blib.localize('bFeedBack.manager')+reply.content;
                }

                detail._append({'block':'bFeedBack', 'elem':'note', 'mods':{'type':nodeType}, 'attrs':{'alt':reply.time}, 'content':content});


            },
            'showThread':function(thread){
                var _this = this,
                    detail = _this.detail;

                detail._removeChildren();

                if(_this.load.getStatus())return;
                _this.load.start();

                blib.ajax({
                    'data':{
                        'blib':'bFeedBack',
                        'action':'getReplies',
                        'view':'json',
                        'thread': thread.id
                    },
                    'dataType':'json',
                    'success':function(data){
                        var i;

                        detail._append({'block':'bFeedBack', 'elem':'note', 'mods':{'type':'main'},'content':thread.content});

                        for(i in data){
                            _this.addReply(data[i], thread);
                        }

                        _this.load.stop();
                    }
                });
            },
            'changeThreadStatus':function(threadId){

            },
            'submit':function(){
                var _this = this,
                    thread = _this.getActiveThread() || {'id':null},
                    content = _this.content.getContent(),
                    action, message, append;

                if(thread.id){
                    action='setReply';
                    message = 'bFeedBack.message.replySuccess';
                    append = 'addReply';
                }else{
                    action='setThread';
                    message = 'bFeedBack.message.threadSuccess';
                    append = 'addThread';
                }

                if(!content.length)return _this.message.setMessage('bFeedBack.message.emptyContent');

                if(_this.load.getStatus())return;
                _this.load.start();

                blib.ajax({
                    'data':{
                        'blib':'bFeedBack',
                        'action':action,
                        'view':'json',
                        'thread': thread.id,
                        'theme':_this.themeList.getTheme(),
                        'content':content
                    },
                    'dataType':'json',
                    'success':function(data){
                        _this.load.stop();
                        _this.message.setMessage(message);
                        _this[append](data, thread);
                        _this.content.clear();
                    }
                });
            }
        }
	);

    blib.build.define(
        {'block':'bFeedBack', 'elem':'detail'},
        function(data){
            var _this = this,
                block = _this.block;

            block.detail = _this;

            _this.template.content = [
                {'elem':'help', 'content':data.content}
            ];
        }
    );

    blib.build.define(
        {'block':'bFeedBack', 'elem':'thread'},
        function(data){
            var _this = this,
                block = _this.block;

            _this.template = {};
            _this.meta = data.meta;
            if(parseInt(_this.meta.status)){
                _this.template = {
                    'mods':{'disabled':true},
                    'content': [
                        {'content': '[№'+_this.meta.id +'] '+ block.getTheme(_this.meta.theme)}
                    ]
                };
            }else{
                _this.template = {
                    'content': [
                        {'elem':'threadCloser'},
                        {'content': '[№'+_this.meta.id +'] '+ block.getTheme(_this.meta.theme)}
                    ]
                };
            }


        },
        false,
        {
            'onclick':function(){
                var  _this = this,
                    block = _this.block,
                    concurrent = block.children.bFeedBack__thread,
                    selected = _this._getMode('selected'),
                    i;

                if(block.load.getStatus())return;

                for(i in concurrent){
                    concurrent[i]._setMode('selected',false);
                }


                if(selected){
                    _this._setMode('selected', false);
                    block.detail._removeChildren();
                    block.submitObj.setName('thread');
                    block.themeList.show();
                }else{
                    _this._setMode('selected', true);
                    block.showThread(_this.meta);
                    block.submitObj.setName('reply');
                    block.themeList.hide();
                }


            },

            'deactivate':function(){
                this.meta.status = 1;
                this._setMode('disabled', true);
            }
        }
    );

    blib.build.define(
        {'block':'bFeedBack', 'elem':'threadCloser'},
        function(){
            var _this = this;

            _this.template = {
                'content':'x'
            };
        },
        false,
        {
            'onclick':function(){
                var  _this = this,
                    block = _this.block,
                    parent = _this.parent;

                if(block.load.getStatus())return;
                block.load.start();

                blib.ajax({
                    'data':{
                        'blib':'bFeedBack',
                        'action':'setThreadStatus',
                        'view':'json',
                        'thread': parent.meta.id,
                        'status':1
                    },
                    'dataType':'json',
                    'success':function(){
                        block.load.stop();
                        block.message.setMessage('bFeedBack.message.disabledThread');
                        parent.deactivate();
                        _this._remove();
                    }
                });

            }
        }
    );


    blib.build.define(
        {'block':'bFeedBack', 'elem':'message'},
        function(){
            this.block.message = this;
        },
        false,
        {
            'setMessage':function(text){
                this.dom.innerHTML = blib.localize(text);
            }
        }
    );

    blib.build.define(
        {'block':'bFeedBack', 'elem':'themeList'},
        function(){
            var _this = this,
                block = _this.block,
                themes = [], i;

            _this.themes = block.getThemes();
            block.themeList = _this;

            for(i in _this.themes){
                themes.push({'tag':'option', 'attrs':{'value':_this.themes[i]['id']}, 'content':_this.themes[i]['name']});
            }

            _this.template.content = themes;
        },
        {'tag':'select'},
        {
            'getTheme':function(){
                var _this = this,
                    dom = _this.dom;

                return dom.value;
            },
            'hide':function(){
                this._setMode('hide',true);
            },
            'show':function(){
                this._setMode('hide',false);
            }
        }
    );

    blib.build.define(
        {'block':'bFeedBack', 'elem':'content'},
        function(){
            var _this = this,
                block = _this.block;

            block.content = _this;
        },
        {'tag':'textarea', 'attrs':{'placeholder':blib.localize('bFeedBack.content')}},
        {
            'getContent':function(){
                return this.dom.value;
            },
            'clear':function(){
                this.dom.value = null;
            }
        }
    );

    blib.build.define(
        {'block':'bFeedBack', 'elem':'load'},
        function(){
            var _this = this,
                block = _this.block;

            _this.isLoad = false;
            block.load = _this;

            _this.template.content = [
                {"elem":"loadInner"}
            ];
        },
        false,
        {
            'start':function(){
                this.isLoad = true;
                this._setMode('load',true);
            },
            'stop':function(){
                this.isLoad = false;
                this._setMode('load',false);
            },
            'getStatus':function(){
                return this.isLoad;
            }
        }
    );

    blib.build.define(
        {'block':'bFeedBack', 'elem':'submit'},
        function(){
            var _this = this,
                block  = _this.block;
            
            block.submitObj = _this;
            
            _this.isBlocking = false;
            _this.template.content = "bFeedBack.submit.thread";
        },
        false,
        {
            'onclick':function(){
                var _this = this,
                    block = _this.block;
                
                if(_this.isBlocking){
                    return block.message.setMessage('bFeedBack.message.isBlocking');
                }
                
                this.block.submit();

                _this.isBlocking = true;
                window.setTimeout(function(){
                    _this.isBlocking = false;
                    block.message.setMessage('bFeedBack.message.ready');
                }, 10000);
            },
            'setName':function(name){
                name = blib.localize("bFeedBack.submit."+name)||name;
                this.dom.innerHTML = name;
            }
        }
    );

})();