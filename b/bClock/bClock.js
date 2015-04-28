/**
 * Created by morozov on 27.04.2015.
 */


(function(){

    blib.build.define(
        {'block':'bClock'},
        function(data){
            var _this = this;

            _this.correction = null;
            _this.setServerDate(data.time, data.gmt);

            _this.template = {
                'content': [
                    {"elem":"interval", "mods":{"type":"hour"}},
                    {"elem":"dotted"},
                    {"elem":"interval", "mods":{"type":"minute"}},
                    {"elem":"interval", "mods":{"init":true, "type":"second"}}
                ]
            };

        },
        false,
        {
            'update':function(){
                var _this = this,
                    intervals = _this.children.bClock__interval,
                    i;

                for(i in intervals)intervals[i].update();
            },
            'setServerDate':function(time, gmt){
                var _this = this,
                    time = time || 0,
                    gmt = gmt*60*60*1000 || 0,
                    local = Date.now(),
                    server = time*1000;

                _this.correction =  (local - server)+gmt;
            }
        }
    );


    blib.build.define(
        {'block':'bClock', 'elem':'interval'},
        function(data){
            var _this = this;

            _this.interval = null;
            _this.firstDigit = null;
            _this.secondDigit = null;

            _this.template = {
                'mods':data.mods,
                'content':[
                    {"elem":"digit"},
                    {"elem":"digit"}
                ]
            };
        },
        false,
        {
            '_onSetMode':{
                'init':function(){
                    var _this = this,
                        type = _this._getMode('type'),
                        interval;

                    switch (type){
                        case "second":
                            interval = 1000;
                            break;
                        case "minute":
                            interval = 1000*60;
                            break;
                        case "hour":
                            interval = 1000*60*60;
                            break;
                    }

                    _this.block.update();

                    _this.interval = window.setInterval(function(){
                        if(!_this.update())_this.block.update();
                    }, interval);
                }
            },

            'update':function(){
                var _this = this,
                    serverTime = +new Date() +_this.block.correction,
                    time = new Date(serverTime),
                    type = _this._getMode('type'),
                    digit;

                switch (type){
                    case "hour":
                        digit = time.getUTCHours();
                        break;
                    case "minute":
                        digit = time.getUTCMinutes();
                        break;
                    case "second":
                        digit = time.getUTCSeconds();
                        break;
                }

                _this.setDigit(digit);

                return digit;
            },

            'setDigit':function(num){
                var _this = this,
                    hours = num,
                    first, second;

                first = Math.floor(hours/10);
                second = hours-(first*10);

                _this.firstDigit.setDigit(first);
                _this.secondDigit.setDigit(second);

            }
        }
    );

    blib.build.define(
        {'block':'bClock', 'elem':'digit'},
        function(data){
            var _this = this,
                parent = _this.parent;

            _this.lines = [];

            if(parent.firstDigit){
                parent.secondDigit = _this;
            }else{
                parent.firstDigit = _this;
            }


            _this.template = {'content':[
                {"elem":"digitLine", "mods":{"1":true}},
                {"elem":"digitLine", "mods":{"2":true}},
                {"elem":"digitLine", "mods":{"3":true}},
                {"elem":"digitLine", "mods":{"4":true}},
                {"elem":"digitLine", "mods":{"5":true}},
                {"elem":"digitLine", "mods":{"6":true}},
                {"elem":"digitLine", "mods":{"7":true}}
            ]};
        },
        {'tag':'ul'},
        {
            'getMap':function(num){
                var map = [
                    [0,1,2,4,5,6],
                    [2,5],
                    [0,2,3,4,6],
                    [0,2,3,5,6],
                    [1,2,3,5],
                    [0,1,3,5,6],
                    [0,1,3,4,5,6],
                    [0,2,5],
                    [0,1,2,3,4,5,6],
                    [0,1,2,3,5,6]
                ];

                return map[num];
            },
            'setDigit':function(num){
                var _this = this,
                    map = _this.getMap(num),
                    i;

                for(i in _this.lines)_this.lines[i]._setMode('active',false);


                for(i in map){
                    _this.lines[map[i]]._setMode('active', true);
                }

            }
        }
    );

    blib.build.define(
        {'block':'bClock', 'elem':'digitLine'},
        function(data){
            var _this = this,
                parent = _this.parent;

            parent.lines.push(_this);

            _this.template = data;
        },
        {'tag':'li'}
    );


})();