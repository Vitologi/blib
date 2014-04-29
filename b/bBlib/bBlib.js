(function( window ){
	
	/** PRIVATE VARIABLE */
    var config = {
			'_private':{
				'version':true,
				'ajax':true,
				'store':true
			},
            'version':'0.2.0',
			'ajax':{
				'dataType':"text",
				'success':function(){},
				'data':null,
				'type':"POST",
				'url':"/",
				'headers':["X-Requested-With","XMLHttpRequest"]
			},
			'store':{
				'isset':('localStorage' in window && window['localStorage'] !== null)?true:false,
				'data':{}
			},
			'system':{
				'server':''
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
		Blib = function(){
            return new init(arguments);
        },
        init = function(args){

			if(typeof(args[0]) === "function"){return Blib.ready(args[0]);}
			
			return merge(this, getElement(args));

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
			if(typeof(selector)==="String")selector = [selector];
			var els = document.getElementsByTagName('*'),
				elsLen=els.length,
				elements=[];
			
			for(var len=selector.length, i=0; i<len; i++){
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
					var temp = document.getElementById(pattern);
					if(temp){elements.push(temp);}
				}else{
					var temp = document.getElementsByTagName(element);
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
		};
	
	
	/** LIBRARY METHODS */
	Blib.is		= is;
	Blib.clone	= clone;
	
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
	Blib.ready = function(handler){
		var called = false;

		function onReady(){
			if(called){return false;}
			called = true;
			handler();
		}
		
		if(document.readyState=="complete"){return handler()}; //can use rotate window.onload for crosbrowser
		
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
		if (window.addEventListener){
			window.addEventListener('load', onReady, false);
		}else if (window.attachEvent){
			window.attachEvent('onload', onReady);
		}else{
			window.onload=onReady;
		}
	};
	
	//ajax
	Blib.ajax = function(dataObject) {
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
		
		for(key in config.ajax){
			if(!(key in dataObject)){
				dataObject[key]=config.ajax[key];
			}
		}

		xhr.onreadystatechange = function(){
			if (xhr.readyState === 4 && xhr.status === 200) {
				var rData = (dataObject['dataType']=="json")?JSON.parse(xhr.responseText):xhr.responseText;
				dataObject['success'](rData);
			}
		}
		
		if(typeof(dataObject['data'])=="object" && dataObject['type']!="DATA"){
			var temp = "";
			for(key in dataObject['data']){
				temp+=key+"="+dataObject['data'][key]+"&";
			}
			dataObject['data'] = temp.substr(0, temp.length-1);
		}
		
		switch(dataObject['type']){
			case "GET":
				dataObject['headers']=["Content-Type", "text/html"];
				dataObject['url']+=(dataObject['url'].indexOf("?")!=-1?"&":"?")+dataObject['data'];
				dataObject['data']=null;
			break;
			
			case "POST":
				dataObject['headers']=["Content-Type", "application/x-www-form-urlencoded"];
			break;
			
			case "DATA":
				dataObject['headers']=["Content-Type", "application/json"];
				dataObject['data']=JSON.stringify(dataObject['data']);
				dataObject['type']="POST";
			break;
			
		}

		xhr.open(dataObject['type'], dataObject['url'], true);
		xhr.setRequestHeader(dataObject['headers'][0],dataObject['headers'][1]);
		xhr.send(dataObject['data']);
		
	};
	
	/** OBJECT METHODS */
	Blib.prototype = {
		'constructor':Blib,
		'version': Blib.config('version'),
		'length':0,
        'init':init,
		
		'toArray': function() {
			return [].slice.call( this );
		},
        
		'each':function(handler){
			for(var len = this.length, i=0; i<len; i++){
				handler.apply(this[i]); //0_0 call + arguments
			}
			return this;
		},
		
		'html':function(obj){
			if(typeof(obj)=="undefined")return this[0].innerHTML;
			for(var len = this.length, i=0; i<len; i++){
				this[i].innerHTML = obj;
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

/** захреначить find на основе getElement.call(obj, handle) */

/** EXAMPLE FOR EXTEND LIBRARY & OBJECT METHODS */
(function( Blib ){
	//add object method
	Blib.fn.test = function(){
		console.log('object method');
	}
	
	//add library method
	Blib.test = function(){
		console.log('library method');
	}
})(blib);


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
			var server = Blib.config('system.server');
			if(is(path,"string")) return server+path+name+"."+extension;
			return server+name.substr(0,1)+"/"+name+"/"+name+"."+extension;
		},
		
		/**
		 * Function for manipulate blocks in site (add or delete them from DOM)
		 * 
		 * @param {object} param 		- set of parameters
		 * @example {'action':"add", 'extention':"css", 'name':"bJquery" , 'list':["bJquery1", "bJquery2", "bJquery3"]}
		 */
		combine = function(param){
			var version = config.version,
				cachePath = is(param.list, "array")?"b/bInclude/__cache/":false,
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
							
							break;
						
						default:
							break;
					}	
					
				
					if(!config.head.appendChild(domElement)){return console.log("error(missing:"+link+")");};
					if(param.extention === "js") blocks[param.name] = {'version':version, 'list':param.list};
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
			server = Blib.config('system.server'),
			domElement;
		
		if(is(blocks, "string")){
			blocks=[blocks];
		}else if(!is(blocks, "array")){
			blocks=[];
		}
		
		if(blocks.length !== 1){
			
			if(server === ""){
				Blib.ajax({
					url:'/',
					data:{'blib':'bInclude', 'list':blocks},
					type:"DATA",
					dataType: "json",
					success: function(data){
						config.version = data['version'];
						combine({'action':"add", 'extention':"css", 'name':data['name'] , 'list':data['list']});
						combine({'action':"add", 'extention':"js", 'name':data['name'] , 'list':data['list']});
					}
				});
			}else{
				domElement  = document.createElement('script');
				domElement.type="text/javascript";
				domElement.src = server+'?blib=bInclude&callback=Blib.include.callback&list='+JSON.stringify(blocks);
				config.head.appendChild(domElement);
				
				window.setTimeout(function(){
					config.head.removeChild(domElement);
				},10000);
			}
			
			return;
			
		}
		
		combine({'action':"add", 'extention':'css', 'name':blocks[0]});
		
		if(target){
			var target = Blib(target);
			Blib.ajax({
				url:block2url(blocks[0], "html") + '?ver='+version, //html ajax work only with self server
				dataType: "html",
				success: function(data){
					target.html(data);
					combine({'action':"add", 'extention':'js', 'name':blocks[0]});
				}
			});
		}else{
			combine({'action':"add", 'extention':'js', 'name':blocks[0]});
		}

	};
	
	Blib.include.callback = function(data){
		config.version = data['version'];
		combine({'action':"add", 'extention':"css", 'name':data['name'] , 'list':data['list']});
		combine({'action':"add", 'extention':"js", 'name':data['name'] , 'list':data['list']});
	}
	
})( window.blib );

