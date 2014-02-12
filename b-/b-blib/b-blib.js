var Blib = (function(){
	
    var config = {
            'version':'0.1.0'
        },
        Blib = function(){
            return new init(arguments);
        },
        init = function(){
            this[0]='sdf';
            this.length = arguments.length;
        };
	
	Blib.prototype = {
		'constructor':Blib,
		'length':0,
        'init':init,
		
		'toArray': function() {
			return [].slice.call( this );
		},
        
        'test':function(){
            console.log(config);
        }
        
	}
    init.prototype = Blib.prototype;

	return Blib;
})();