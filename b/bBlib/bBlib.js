(function( window ){
	
	/** JSON hack for compatibility */
	"object"!==typeof JSON&&(JSON={});
	(function(){function m(a){return 10>a?"0"+a:a}function t(a){p.lastIndex=0;return p.test(a)?'"'+a.replace(p,function(a){var c=u[a];return"string"===typeof c?c:"\\u"+("0000"+a.charCodeAt(0).toString(16)).slice(-4)})+'"':'"'+a+'"'}function q(a,l){var c,d,h,r,g=e,f,b=l[a];b&&"object"===typeof b&&"function"===typeof b.toJSON&&(b=b.toJSON(a));"function"===typeof k&&(b=k.call(l,a,b));switch(typeof b){case "string":return t(b);case "number":return isFinite(b)?String(b):"null";case "boolean":case "null":return String(b);
	case "object":if(!b)return"null";e+=n;f=[];if("[object Array]"===Object.prototype.toString.apply(b)){r=b.length;for(c=0;c<r;c+=1)f[c]=q(c,b)||"null";h=0===f.length?"[]":e?"[\n"+e+f.join(",\n"+e)+"\n"+g+"]":"["+f.join(",")+"]";e=g;return h}if(k&&"object"===typeof k)for(r=k.length,c=0;c<r;c+=1)"string"===typeof k[c]&&(d=k[c],(h=q(d,b))&&f.push(t(d)+(e?": ":":")+h));else for(d in b)Object.prototype.hasOwnProperty.call(b,d)&&(h=q(d,b))&&f.push(t(d)+(e?": ":":")+h);h=0===f.length?"{}":e?"{\n"+e+f.join(",\n"+
	e)+"\n"+g+"}":"{"+f.join(",")+"}";e=g;return h}}"function"!==typeof Date.prototype.toJSON&&(Date.prototype.toJSON=function(){return isFinite(this.valueOf())?this.getUTCFullYear()+"-"+m(this.getUTCMonth()+1)+"-"+m(this.getUTCDate())+"T"+m(this.getUTCHours())+":"+m(this.getUTCMinutes())+":"+m(this.getUTCSeconds())+"Z":null},String.prototype.toJSON=Number.prototype.toJSON=Boolean.prototype.toJSON=function(){return this.valueOf()});var s,p,e,n,u,k;"function"!==typeof JSON.stringify&&(p=/[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
	u={"\b":"\\b","\t":"\\t","\n":"\\n","\f":"\\f","\r":"\\r",'"':'\\"',"\\":"\\\\"},JSON.stringify=function(a,l,c){var d;n=e="";if("number"===typeof c)for(d=0;d<c;d+=1)n+=" ";else"string"===typeof c&&(n=c);if((k=l)&&"function"!==typeof l&&("object"!==typeof l||"number"!==typeof l.length))throw Error("JSON.stringify");return q("",{"":a})});"function"!==typeof JSON.parse&&(s=/[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,JSON.parse=function(a,
	e){function c(a,d){var g,f,b=a[d];if(b&&"object"===typeof b)for(g in b)Object.prototype.hasOwnProperty.call(b,g)&&(f=c(b,g),void 0!==f?b[g]=f:delete b[g]);return e.call(a,d,b)}var d;a=String(a);s.lastIndex=0;s.test(a)&&(a=a.replace(s,function(a){return"\\u"+("0000"+a.charCodeAt(0).toString(16)).slice(-4)}));if(/^[\],:{}\s]*$/.test(a.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g,"@").replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g,"]").replace(/(?:^|:|,)(?:\s*\[)+/g,"")))return d=
	eval("("+a+")"),"function"===typeof e?c({"":d},""):d;throw new SyntaxError("JSON.parse");})})();
	
	/** PRIVATE VARIABLE */
    var config = {
			'_private':{
				'version':true,
				'store':true
			},
            'version':'0.2.0',
			'store':{
				'isset':('localStorage' in window && window['localStorage'] !== null)?true:false,
				'data':{}
			},
			'system':{
				
			},
			'user':{}
        };
	
	/** PRIVATE METHODS */
	var core = {
			'toString':Object.prototype.toString,
			'toLowerCase':String.prototype.toLowerCase,
			'push':[].push,
			'sort':[].sort,
			'splice':[].splice
		},
		is = function(obj, type){
			
			switch(type){
				case "undefined":
				case "string":
				case "boolean":
				case "function":
					return typeof(obj) === type;
					break;
					
				case "number":
					return (typeof(obj) === type && obj === obj);
					break;
				
				case "NaN":
					return (typeof(obj) === 'number' && obj !== obj);
					break;
					
				case "null":
					return obj === null;
					break;
					
				default:
					var tempType = core.toString.call(obj);
					tempType = tempType.substr(8,tempType.length-9).toLowerCase();
					
					switch(type){
						case "object":
						case "array":
						case "date":
						case "global":
							return tempType === type;
							break;
						
						default:
							
							if(Blib.is(type,'array')){
								var status = false,	i;
								for(i in type){
									status = status || Blib.is(obj, type[i]);
								}
								return status;
							}					
							
							return (obj===obj)?tempType:"NaN";
							break;
					}

					break;

			}
			
		},
		clone = function(obj){

			if(typeof(obj) != 'object' || obj === null){
				return obj;
			}
			
			//for DOM elements
			if(obj.cloneNode){
				var temp = obj.cloneNode(false);
				
				for(key in obj){
					if(key.substr(0,2) == "on" && obj[key] != null) temp[key]=clone(obj[key]);
				}
				
				if(obj.hasChildNodes()){
					var len=obj.childNodes.length;
					for(var i=0;i<len;i++){
						temp.appendChild(clone(obj.childNodes[i]));
					}
				}
				return temp;
			}
			
			//for all OTHER
			var temp = new obj.constructor();
			for (i in obj){
				if(temp[i] === obj[i]){continue;};
				temp[i] = clone(obj[i]);
			}
			return temp;
		},
		
		extend = function() {
			var options, name, src, copy, copyIsArray, clone,
				target = arguments[0] || {},
				i = 1,
				length = arguments.length,
				deep = false;

			// Handle a deep copy situation
			if ( is(target, 'boolean') ) {
				deep = target;
				target = arguments[1] || {};
				// skip the boolean and the target
				i = 2;
			}

			// Handle case when target is a string or something (possible in deep copy)
			if ( !is(target, 'object') && !is(target, 'function') ) {
				target = {};
			}

			// extend jQuery itself if only one argument is passed
			if ( length === i ) {
				target = this;
				--i;
			}

			for ( ; i < length; i++ ) {
				// Only deal with non-null/undefined values
				if ( (options = arguments[ i ]) != null ) {
					// Extend the base object
					for ( name in options ) {
						src = target[ name ];
						copy = options[ name ];

						// Prevent never-ending loop
						if ( target === copy ) {
							continue;
						}

						// Recurse if we're merging plain objects or arrays
						if ( deep && copy && ( is(copy, 'object') || (copyIsArray = is(copy, 'array')) ) ) {
							if ( copyIsArray ) {
								copyIsArray = false;
								clone = src && is(src, 'array') ? src : [];

							} else {
								clone = src && is(src, 'object') ? src : {};
							}

							// Never move original objects, clone them
							target[ name ] = extend( deep, clone, copy );

						// Don't bring in undefined values
						} else if ( copy !== undefined ) {
							target[ name ] = copy;
						}
					}
				}
			}

			// Return the modified object
			return target;
		},
		merge = function( first, second ) {
			var len = second.length,
				i = first.length,
				j = 0;

			if ( typeof len === "number" ) {
				for ( ; j < len; j++ ) {
					first[ i++ ] = second[ j ];
				}
			} else {
				while ( second[j] !== undefined ) {
					first[ i++ ] = second[ j++ ];
				}
			}

			first.length = i;

			return first;
		},
		getElement = function(selector){
			if(is(selector, "string"))selector = [selector];
			context = (this.cloneNode)?this:window.document;
			var els = context.getElementsByTagName('*'),
				elsLen=els.length,
				elements=[];
			
			for(var len=selector.length, i=0; i<len; i++){
				if(selector[i].cloneNode){ elements.push(selector[i]);}
				if(typeof(selector[i])!='string'){continue;}
				var element=selector[i],
					point = element.substr(0,1),
					pattern = element.substr(1);
			
				if(point=="."){
					for(var j=0;j<elsLen;j++){
						var temp = els[j].className.split(' ');
						for(var tmpLen=temp.length, k=0; k<tmpLen; k++){
							if(temp[k] == pattern){elements.push(els[j]);	break;}
						}
					}
				}else if(point=="#"){
					var temp = context.getElementById(pattern);
					if(temp){elements.push(temp);}
				}else{
					var temp = context.getElementsByTagName(element);
					for(var tagLen=temp.length,j=0; j<tagLen; j++){
						elements.push(temp[j]);
					}
				};
			}
			return elements;
		},
		navigate = function(obj, selector, value){
			
			if(!obj || typeof(selector) != 'string') return;
			
			var needle = (selector.indexOf('.')!=-1)?selector.split('.'):[selector],
				i = 0,
				len = needle.length
				isValue = typeof(value) !== 'undefined';
			
			while(i<len){
				if(isValue && i == len-1) break;
				if(!(needle[i] in obj)) return;
				obj = obj[needle[i]];
				i++;
			};

			if(!isValue) return obj;
			obj[needle[i]] = value;
		},
		storeSave = function(get){
			if(!config.store.isset) return;
			
			if(get){
				config.store.data =  JSON.parse(localStorage.getItem("blib")) || {};
			}else{
				localStorage.setItem("blib", JSON.stringify(config.store.data));
			}
		},
		Blib = function(){
            return new init(arguments);
        },
        init = function(args){

			if(typeof(args[0]) === "function"){return Blib.ready(args[0]);}
			
			return merge(this, getElement(args));

        };
	
	
	/** LIBRARY METHODS */
	Blib.is		= is;
	Blib.clone	= clone;
	Blib.navigate	= navigate;
	Blib.extend	= extend;
	Blib.merge = merge;
	
	/**
	 * Method for work with blib configuration
	 * 
	 * @constructor
	 * @param	{string}	option	- Path to configs option (use dots "." for dividing items)
	 * @param	{multiple}	value	- Installed option
	 * @return	{config}||{Blib}	- Returns the selected option or Blib for chaining
	 */
	Blib.config = function(option, value){
		
		if(typeof(option) != 'string') return clone(config);
		
		var result = config,
			needle = (option.indexOf('.')!=-1)?option.split('.'):[option];
			first = needle[0];
			second =  needle[1] || false;
		
		if(typeof(value)  === 'undefined') return clone(navigate(result, option));
		if(config["_private"][first] || (first == "_private" && (!second || second in config["_private"]))) return Blib;
		
		navigate(result, option, value);
	};
	
	Blib.store = {
		'get':function(get){
			if(typeof(navigate(config.store.data, get)) == 'undefined') storeSave('get');
			var data = config.store.data;
			if(typeof(get) != 'string')	return clone(data);
			return clone(navigate(data, get));
		},
		'set':function(key, value){
			if(typeof(key) == 'undefined')	config.store.data = {};
			var data = config.store.data;
			
			if(key && typeof(value) == 'undefined'){
				
				if(key.indexOf('.')!=-1){
					var needle = key.split('.'),
						i=0,
						len = needle.length;
					
					while(i<len){
						key = needle[i];
						if(i == len-1) break;
						data = data[key];
						i++;
					}
				}
				
				delete data[key];
		
			}else{
				navigate(data, key, value);
			}
			storeSave();
		}
	}
	
	//set handler on DOM ready event
	Blib.ready = function(handler, deep){
		var called = false;

		function onReady(){
			if(called){return false;}
			called = true;
			handler();
		}
		
		if(!deep){
			if( document.addEventListener ){
				document.addEventListener( "DOMContentLoaded", function(){onReady();}, false );
			}else if( document.attachEvent ){
				if( document.documentElement.doScroll && window == window.top ){
					function tryScroll(){
						if(called){return false;}
						if(!document.body){return false;}
						try{
							document.documentElement.doScroll("left");
							onReady();
						} catch(e) {
							setTimeout(tryScroll, 0);
						}
					}
					tryScroll();
				}
		
				document.attachEvent("onreadystatechange", function(){		
					if(document.readyState === "complete"){
						onReady();
					}
				})
			}
		}
		
		if (window.addEventListener){
			window.addEventListener('load', onReady, false);
		}else if (window.attachEvent){
			window.attachEvent('onload', onReady);
		}else{
			window.onload=onReady;
		}
	};
	
	
	
	//ajax
	Blib.ajax = (function() {
		var salt = 0;
		
		return function(param){
			var dataType	= param['dataType'] || "text",
				success		= param['success'] || function(){},
				data		= param['data'] || null,
				type		= param['type'] || "POST",
				url			= param['url'] || "/",
				headers		= param['headers'] || ["X-Requested-With","XMLHttpRequest"],
				head 		= getElement(['head'])[0],
				jsonpElement, temp;
				
			
			if(is(data, 'object') && type !== "DATA"){
				temp = "";
				for(key in data){
					if(is(data[key],['array', 'object'])){ data[key]=JSON.stringify(data[key]); }
					temp+=key+"="+data[key]+"&";
				}
				data = temp.substr(0, temp.length-1);
			}
			
			if(type==='JSONP'){
				temp = "jsonp"+salt++;
				
				Blib.ajax[temp] = function(){
					success.apply(null, arguments);
					delete Blib.ajax[temp];
				}
				
				jsonpElement  = document.createElement('script');
				jsonpElement.type="text/javascript";
				jsonpElement.src = url += (url.indexOf("?")!=-1?"&":"?")+data+'&callback=Blib.ajax.'+temp;
				head.appendChild(jsonpElement);
				
				window.setTimeout(function(){
					head.removeChild(jsonpElement);
				},10000);
				
				return;
			}
			
			var xhr;
			if (window.XMLHttpRequest) xhr = new XMLHttpRequest();
			else if (window.ActiveXObject) {
				try {
					xhr = new ActiveXObject('Msxml2.XMLHTTP');
				} catch (e){}
				try {
					xhr = new ActiveXObject('Microsoft.XMLHTTP');
				} catch (e){}
			}
			if (!xhr) {return this;}

			xhr.onreadystatechange = function(){
				if (xhr.readyState === 4 && xhr.status === 200) {
					temp = (dataType==="json")?JSON.parse(xhr.responseText):xhr.responseText;
					success(temp);
				}
			}
			
			switch(type){
				case "GET":
					headers = ["Content-Type", "text/html"];
					url += (url.indexOf("?")!=-1?"&":"?")+data;
					data = null;
				break;
				
				case "POST":
					headers=["Content-Type", "application/x-www-form-urlencoded"];
				break;
				
				case "DATA":
					headers = ["Content-Type", "application/json"];
					data = JSON.stringify(data);
					type = "POST";
				break;
				
			}

			xhr.open(type, url, true);
			xhr.setRequestHeader(headers[0],headers[1]);
			xhr.send(data);
		};
	})();
	
	/** OBJECT METHODS */
	Blib.prototype = {
		'constructor':Blib,
		'version': Blib.config('version'),
		'length':0,
        'init':init,
		
		'toArray': function() {
			return [].slice.call( this );
		},
        
		'find':function(needle){
			var result = [],
				temp = [],
				temp2 = [],
				i = j = len = lenJ = 0;
			this.each(function(){
				temp = getElement.call(this,needle);
				
				loop:
				for(i=0,len=temp.length; i<len;i++){
					for(j=0,lenJ=result.length; j<lenJ;j++){
						if(temp[i]===result[j])continue loop;
					}
					result.push(temp[i]);
				}
			});
			return result;
		},
		
		'each':function(handler){
			for(var len = this.length, i=0; i<len; i++){
				handler.apply(this[i]); //0_0 call + arguments
			}
			return this;
		},
		
		'html':function(obj){
			if(is(obj,"undefined"))return this[0].innerHTML;
			for(var len = this.length, i=0; i<len; i++){
				this[i].innerHTML = '';
				if(obj.cloneNode){
					this[i].appendChild(obj);
				}else{
					this[i].innerHTML = obj;
				}
			}
			return this;
		},
		
		'append':function(obj){
			if(typeof(obj)!="object"){return this;}
			var len = this.length;

			if(len==1){
				this[0].appendChild(obj);
				return this;
			}
			
			for(var i=0; i<len; i++){
				var temp =clone(obj);
				this[i].appendChild(temp);
			}
			return this;
		},
		
		//get Array methods
        'push':core.push,
		'sort':core.sort,
		'splice':core.splice
	};
	
	
	
	
	/** MAGIC */
    init.prototype = Blib.fn = Blib.prototype;
	if ( typeof window === "object" && typeof window.document === "object" ) {
		window.blib = Blib;
		window.Blib = Blib;
	}
	
	
	
})( window );

/**
 * Blib.include library. Allows get all script or style file in one. And store in local cache.
 * 
 */
(function( Blib ){
	
	
	/** PRIVATE VARIABLE AND METHODS */
	var is = Blib.is,
		//local config
		config = {
			'head': document.getElementsByTagName('head')[0],
			'version':1,
			'blocks':{}
		},
		
		/**
		 * Translate block's name into it path on server
		 * 
		 * @param {string} name 		- block's name
		 * @param {string} extension 	- block's extension like css, js, html
		 * @param {string} name 		- alternative block's folder
		 * @return {string} 			- url
		 */
		block2url = function(name, extension, path){
			var server = Blib.config('system.server')||window.location.protocol+"//"+window.location.host;
			if(is(path,"string")) return server+path+name+"."+extension;
			return server+"/"+name.substr(0,1)+"/"+name+"/"+name+"."+extension;
		},
		
		wait = 0,
		handlers = [],
		tick,
		
		/**
		 * Function for manipulate blocks in site (add or delete them from DOM)
		 * 
		 * @param {object} param 		- set of parameters
		 * @example {'action':"add", 'extention':"css", 'name':"bJquery" , 'list':["bJquery1", "bJquery2", "bJquery3"]}
		 */
		combine = function(param){
			var version = config.version,
				cachePath = is(param.list, "array")?"/b/bInclude/__cache/":false,
				link = block2url(param.name, param.extention, cachePath)+"?version="+version,
				id = param.name+"."+param.extention,
				blocks = config.blocks,
				domElement, newElement, temp;
			
			switch(param.action){
				case "del":
					domElement = Blib("#"+id)[0];
					if(domElement){
						domElement.parentNode.removeChild(domElement);
						if(param.extention === "js") delete blocks[param.name];
					}
					break;
				
				case "add":
					
					for(key in blocks){
						if(!is(blocks[key]['list'], "array"))continue;
						temp = blocks[key]['list'];
						
						for(var i=0, len = temp.length; i<len; i++){
							if(temp[i]!==param.name) continue;
							combine({'action':"add", 'extention':param.extention, 'name':key , 'list':temp});
							return;
						}
						
					}
					
					
					combine({'action':"del", 'extention':param.extention, 'name':param.name , 'list':param.list});
					
					switch(param.extention){
						case "css":
							
							domElement  = document.createElement('link');
							domElement.rel  = 'stylesheet';
							domElement.type = 'text/css';
							domElement.media = 'all';
							domElement.id = id;
							domElement.href = link;
							
							break;
							
						case "js":
							
							domElement  = document.createElement('script');
							domElement.type="text/javascript";
							domElement.id = id;
							domElement.src = link;
							domElement.async = false;
							wait++;
							
							
							break;
						
						default:
							break;
					}	
					
					
					if(!config.head.appendChild(domElement)){return console.log("error(missing:"+link+")");};
					if(param.extention === "js"){
						blocks[param.name] = {'version':version, 'list':param.list};
						
						domElement  = document.createElement('script');
						domElement.type="text/javascript";
						domElement.src = block2url('bInclude__decrement', 'js', '/b/bInclude/__decrement/');
						domElement.async = false;					
						config.head.appendChild(domElement);
						
						window.setTimeout(function(){
							config.head.removeChild(domElement);
						},10000);
						
					}
					
					
					
					break;
					
				default:
					break;
			}
			

		};
	
	
	//save config into global config and protect them
	Blib.config("bInclude", config);
	Blib.config("_private.bInclude", true);
	
	/**
	* Include block/s
	* @param {string|array|some else} blocks	name/s
	* @param {string} target - selector for loaded block
	*/
	Blib.include =  function(blocks, target){
		var version = config.version,
			server = block2url('index', 'php', '/'),
			ajaxType = "DATA",
			domElement;
		
		if(is(blocks, "string")){
			blocks=[blocks];
		}else if(!is(blocks, "array")){
			blocks=[];
		}
		
		if(blocks.length !== 1){
			wait++;
			
			if(Blib.config('system.server')){ ajaxType = "JSONP";}
			
			Blib.ajax({
				url:server,
				data:{'blib':'bInclude', 'list':blocks},
				type:ajaxType,
				dataType: "json",
				success: function(data){
					config.version = data['version'];
					combine({'action':"add", 'extention':"css", 'name':data['name'] , 'list':data['list']});
					combine({'action':"add", 'extention':"js", 'name':data['name'] , 'list':data['list']});
					wait--;
				}
			});
			
			return Blib.include;
			
		}
		
		combine({'action':"add", 'extention':'css', 'name':blocks[0]});
		
		if(target){
			var target = Blib(target);
			wait++;
			Blib.ajax({
				url:block2url(blocks[0], "html") + '?ver='+version, //html ajax work only with self server
				dataType: "html",
				success: function(data){
					target.html(data);
					combine({'action':"add", 'extention':'js', 'name':blocks[0]});
					wait--;
				}
			});
		}else{
			combine({'action':"add", 'extention':'js', 'name':blocks[0]});
		}
		
		return Blib.include;
	};
	
	Blib.include.decrement = function(){
		wait--;
	}
	
	Blib.include.complete = function(handler, delay){
		
		handlers.push(handler);
		if(tick){window.clearInterval(tick);}
		
		tick = window.setInterval(function(){

			if(wait>0) return;
			wait = 0;
			window.clearInterval(tick);
			
			for(key in handlers){
				temp = handlers[key];
				delete handlers[key];
				temp();
			};
			
		},delay || 100);
	}
	
	
})( window.blib );

/**
 * Blib.build library. For construct html from blocks.
 * 
 */
(function( Blib ){
	
	var is = Blib.is,
		clone = Blib.clone,
		navigate = Blib.navigate,
		extend = Blib.extend,
		merge = Blib.merge,
		//local config
		config = {
			'block': {},
			'tree':[]
		},
		baseProto = {
			'action':{
				'onSetMode':{
					'js':{
						'init':function(){alert('block inited')}
					}
				}
			},
			'template':{},
			'setTemplate':function(tmpl, reset){
				if(reset){ this.constructor.prototype.template = tmpl; }
				extend(true, this.constructor.prototype.template, tmpl);
			},
			'setAction':function(act, reset){
				if(reset){ return this.constructor.prototype.action = act; }
				extend(true, this.constructor.prototype.action, act);
			},
			'setDom':function(dom){
				this.dom = dom;
			},
			'setChildren':function(name, elem){
				if(!this.children){this.children = [];};
				if(!this.children[name]){this.children[name] = [];};
				this.children[name].push(elem);
			},
			'setParent':function(elem){
				this.parent = elem;
			},
			'setBlock':function(block, deep){
				if(deep){
					while(block && is(block.block,'object')){
						block = block.block;
					}
				}
				this.block = block;
			},
			'setMode':function(mode, value){
				var block = this.template.block,
					elem = (this.template.elem?'__'+this.template.elem:''),
					_mode = '_'+mode,
					_value = (is(value,'string')?'_'+value:''),
					regexp = new RegExp('('+block+elem+_mode+'\\S*)'),
					changed = false,
					fullName = block+elem+_mode+_value
					newClass = this.dom.className.replace(regexp, function(handle){
						changed = true;
						return (value)?fullName:'';
					}),
					handler = navigate(this.action.onSetMode, mode+(is(value,'string')?'.'+value:''));
					
				if(changed){
					this.dom.className = newClass;
				}else{
					this.dom.className += ' '+fullName;
				}
				
				if(is(handler, 'function')){
					handler.call(this);
				};
				
			}
		},
		defaultBlock = function(){};
	
	defaultBlock.prototype = baseProto;
		
	Blib.config("bBuild", config);
	Blib.config("_private.bBuild", true);	
	
	var /** применение отложенных заданий*/
		deferredTask = {},
		applyDeferredTask = function(){
			var set=false;
			for(key in deferredTask){
				if(!Blib(key).length){continue;}
				set=true;
				var tObj = deferredTask[key]['block'],
					tKey = key,
					tData = (tObj)?JSON.parse(JSON.stringify(deferredTask[key])):deferredTask[key], /* 0_0 риск удалить дом элемент*/
					temp;
					
				delete deferredTask[key];
				temp = (tObj)?applyConstructor(tData.block, tData):tData; 
				if(temp){Blib(key).html("").append(temp);}
			}
			if(set){applyDeferredTask()};
			deferredTask={};
		},
		
		/** сборка серверного ответа */
		build = function(data, blockName, parent, blocks, deep){
			if(!data){return;}
			if(is(deep,['NaN','undefined'])){deep=0;}
			if(!blocks){blocks=[];}
			
			var currentClass, result, container,
				obj, factory,
				attr, temp,
				block = false;
			
			if(data['block']){
				blockName = data['block'];
			}
			
			if(blocks.length && data['block'] && data['elem'] && data['block'] !== block[0]){
				for(key in blocks){
					if(blocks[key].template.block === data['block']){
						block = blocks[key];
						break;
					}
				}
			}else if(blocks.length){
				block = blocks[0];
			}
			
			if(factory = navigate(config.block, (data['elem'])?(blockName+"."+data['elem']):data['block'])){
				if(!block || factory !== block.constructor){
					obj = new factory(data);
					data = obj.template;
				}
			}else{
				obj=new defaultBlock();
				obj.template.block = blockName;
			}
			
			//[первый в ответе, текущий блок, имя обьекта, ДОМ-результат, есть ли контейнер]
			currentClass = (data['elem'])?(blockName+"__"+data['elem']):data['block'];
			result = document.createElement(data['tag']||"div");
			container = (data['container'])?(Blib(data['container']).length>0):false;

			
			result.blib = obj;
			obj.setDom(result);
			obj.setParent(parent);
			if(parent){
				block.setChildren(currentClass||"noname", obj);
				if(!data['block'] || data['elem']){
					obj.setBlock(block, data['elem']?true:false);
				}
			};
			
			
			for(evt in obj.action){
				if(!is(obj.action[evt], 'function') || evt.substr(0,2) !== "on" || evt === 'onSetMode')continue;

				var wrap = obj.action[evt],
					wrappedAction = function(event){
					if(is(event, "undefined")){event = {};}
					event.blib = obj;
					return wrap.call(this, event);
				}
				
				if (result.addEventListener){   
					result.addEventListener(evt.substr(2,evt.length-1), wrappedAction, false); 		
				} else if (result.attachEvent){ 
					result.attachEvent(evt, wrappedAction); 
				} else{ 
					if(result[evt]){
						wrappedAction = (function (){
							var old = result[evt],
								now = wrappedAction;
							return function(){
								old();
								now();
							};								
						})();
					}
					
					result[evt] = wrappedAction;
				}
			}
			
			//оформляем классом
			if(currentClass){result.className = currentClass};
			
			//устанавливаем модификаторы
			if(currentClass && data['mods']){
				for(key in data['mods']){
					result.className +=' '+currentClass+"_"+key+(is(data['mods'][key], "string")?"_"+data['mods'][key]:"");
				}
			}
			
			//задаем атрибуты
			if(data['attrs']){
				for(key in data['attrs']){
					attr = data['attrs'][key];
					
					switch(is(attr)){
						case "array":
						case "object":
							temp = JSON.stringify(attr);
						break;
						case "function":
							result[key]=attr;
							continue;
						break;
						default:
							temp = attr;
						break;
					}

					if(key=="className"){
						result[key] += " "+temp;
					}else if(result.setAttribute){
						result.setAttribute(key, temp);
					}else{
						result[key] = temp;
					}
				}
			}
			
			
			//проверяем есть ли вложенность и рекурсивно обрабатываем если есть
			switch(is(data['content'])){
				case "object":
				case "array":
					for(key in data['content']){						
						temp = build(data['content'][key], blockName, obj, (obj.template.block && !obj.template.elem?merge([obj],blocks):blocks), deep+1);
						temp = temp || {};
						if(!temp.dom)continue;
						if(typeof(temp.dom)=="object"){result.appendChild(temp.dom);}else{result.innerHTML+=temp.dom;}
					}
				break;
				case "string":
					result.innerHTML+=data['content'];
				break;
			}

			//если есть контейнер то добавляем в него
			if(container){
				Blib(data['container']).html(result);
				applyDeferredTask();
			}else if(data['container']){
				deferredTask[data['container']]=result;
				return false;
			}else{
				if(!deep){applyDeferredTask();}
				return (!deep)?result:{'dom':result, 'obj':obj};
			}
			
	
			
		},
		
		/** колбэки после получения ответа и перестройки дерева */
		readyFunctions = [],
		ready = function(obj){
			if(is(obj, 'function')){return readyFunctions.push(obj);}
			
			for(var len = readyFunctions.length, i=0; i<len; i++){
				readyFunctions[i](obj);
			}
			
		},
	
		//заносим блок/елемент в коллекцию
		define = function(name, factory, template, action){
			if(!is(factory, 'function') && !is(name.block, 'string'))return;
			
			extend(true, factory.prototype, {'template':name}, baseProto);
			if(is(template, 'object')){factory.prototype.setTemplate(template)};
			if(is(action, 'object')){factory.prototype.setAction(action)};
			
			if(name.elem){ name.block = name.block+'.'+name.elem; }

			return navigate(config.block, name.block, factory);
		};
		
		//переопределяем блок/елемент в коллекцию
		redefine = function(name, factory){
			if(!is(factory, 'function') && !is(name.block, 'string'))return;
			if(name.elem){ name.block = name.block+'.'+name.elem; }
			var oldFactory = navigate(config.block, name.block);
				newFactory = function(data){
					extend(true, factory.prototype, new oldFactory(data));
					return new factory(data);
				};
			
			for(key in oldFactory){
				newFactory[key]=oldFactory[key];
			}
			
			return navigate(config.block, name.block, newFactory);
			
		};
	
	build.ready = ready;
	build.define = define;
	build.redefine = redefine;
	
	Blib.build = build;	
	
})(blib);


/** захреначить find на основе getElement.call(obj, handle) */