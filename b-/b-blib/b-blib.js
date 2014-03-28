(function( window ){
	
	/** PRIVATE VARIABLE */
    var config = {
			'_private':{
				'version':true,
				'alias':true,
				'ajax':true,
				'store':true
			},
            'version':'0.2.0',
			'alias':{
				'head': document.getElementsByTagName('head')[0] //под задачу
			},
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
			'system':{},
			'user':{}
        };
	
	/** PRIVATE METHODS */
	var Blib = function(){
            return new init(arguments);
        },
        init = function(args){
			
			if(!args.length){return this;}
			if(typeof(args[0]) === "function"){return Blib.ready(args[0]);}
			
			return merge(this, getElement(args));

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
		},
		block2url = function(name, extension, path){
			if(typeof(path) == "string") return path+name+"."+extension;
			return name.substr(0,1)+"/"+name+"/"+name+"."+extension;
		};
	
	
	/** LIBRARY METHODS */
	
	Blib.block2url = block2url;
	
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
		if(config["_private"][first] || (first == "_private" && !second && !(second in config["_private"]))) return Blib;
		
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
        'push':[].push,
		'sort':[].sort,
		'splice':[].splice
	};
	
	
	/** MAGIC */
    init.prototype = Blib.fn = Blib.prototype;
	if ( typeof window === "object" && typeof window.document === "object" ) {
		window.blib = Blib;
	}
	
	
	
})( window );

/** захреначить find на основе getElement.call(obj, handle) */

/** EXAMPLE FOR EXTEND LIBRARY & OBJECT METHODS */
(function( Blib ){
	//add object method
	Blib.fn.test = function(){
		console.log(this.length);
		console.log(this);
	}
	
	//add library method
	Blib.test = function(){
		console.log(this.length);
		console.log(this);
	}
})(blib);


/**
 * Blib.include library. Allows get all script or style file in one. And store in local cache.
 * 
 */
(function( Blib ){

	var config = {
		'item':true
	}
	Blib.config("include", config);
	Blib.config("_private.include", true);
	
	/*
	function(request){
		
		var store =  Blib.store,
			isset = config.store.isset;
			data = config.store.data;
		
		console.log(store.get());
		
	
		//storeSave
		
		var operation = request['operation'],
			type = request['type'],
			fileName = request['fileName'],
			fileCache = request['fileCache']||[],
			file = request['file'],
			obj = eval(type),
			html = document.getElementById(fileName);
		
		if(operation=='clear'){
			if(html){html.parentNode.removeChild(html);}
			delete obj[fileName];			
		}else if(operation=='set'){
			if(!head.appendChild(file)){return alert("непрошло"+fileName);}
			obj[fileName]={'version':version, 'list':fileCache};
		}else if(operation=='getAllFiles'){
			var arr=[];
			for(key in obj){
				if(obj[key]['list']){
					arr = arr.concat(obj[key]['list']);
				}else{
					arr.push(key);
				}
			}
			arr.sort();
			var i = arr.length;
			while (i--) {
				if (arr[i] == arr[i-1]){
					arr.splice(i, 1);
				}
			}
			return arr;
		}
		
		if(storageFlag){localStorage.setItem(type, JSON.stringify(obj));}
		
	};
	*/
	
	/**
	* include block (html+css+js in set container || css+js)
	* @param {string} file		path of block without extension
	* @param {string} target	selector where will be load block
	*/
	Blib.include =  function(file, target){
		/*
		cssFunction(file+'.css');

		if(target){
			var target = $(target);
			$.ajax({
				url:file+'.html?new='+version,
				dataType: "html",
				success: function(data){
					for(key in target){
						target[key].innerHTML=data;
					}
					jsFunction(file+'.js');
				}
			});
		}else{
			jsFunction(file+'.js');
		}
		*/
	};
	
	/**
	* include css file
	* @param {string} cssFile	name of css file
	* @param {string}[] inCache	files which contain in it
	* @return {object}			this
	*/
	Blib.include.css = function(cssFile, inCache){
		/*
		cssFile = cssFile.toString();
	
		if((cssFile in css) && (css[cssFile]['version']==version)){ return this; }
	
		for(key in css){
			var innerFiles = (css[key]['list'])?css[key]['list']:[];
			for(var len=innerFiles.length, i=0;i<len;i++){
				if(innerFiles[i]==cssFile){return cssFunction(key, innerFiles);}
			}
		}
		
		storageHandler({'operation':'clear', 'type':'css', 'fileName':cssFile});
		var cssLink  = document.createElement('link');
		cssLink.rel  = 'stylesheet';
		cssLink.type = 'text/css';
		cssLink.href = cssFile+"?new="+version;
		cssLink.media = 'all';
		cssLink.id = cssFile;
		storageHandler({'operation':'set', 'type':'css', 'fileName':cssFile, 'fileCache':inCache, 'file':cssLink});
		
		return this;
		*/
	};
	
	/**
	* include js file
	* @param {string} jsFile	name of js file
	* @param {string}[] inCache	files which contain in it
	* @return {object}			this
	*/
	Blib.include.js = function(jsFile, inCache){
		
		/*
		jsFile = jsFile.toString();

		if(!(jsFile in js)){
			for(key in js){
				var innerFiles = (js[key]['list'])?js[key]['list']:[];
				for(var i=0, len=innerFiles.length;i<len;i++){
					if(innerFiles[i]==jsFile){return jsFunction(key, innerFiles);}
				}
			}
		}
		
		storageHandler({'operation':'clear', 'type':'js', 'fileName':jsFile});
		var scriptLink  = document.createElement('script');
		scriptLink.id = jsFile;
		scriptLink.src = jsFile+"?new="+version;
		scriptLink.type="text/javascript";
		storageHandler({'operation':'set', 'type':'js', 'fileName':jsFile, 'fileCache':inCache, 'file':scriptLink});
		return this;
		*/
	};
	
	
	
	/**
	* method for get version of site and load all stylesheet/script in one file
	* @param {object} dataObject	object of setting
	* {bool} script					for glue javascripts
	* {srting}[]exception			blocks which will not be uploaded
	* {srting}[]order				first turn load sctipts/if 'script' is false, then 'order' set chosen blocks
	*/
	Blib.include.fire = function(dataObject){
		
		/*
		if(!dataObject['order'].length || dataObject['script']){
			// first include all cache 
			var arr = (storageFlag && localStorage.getItem('css'))?JSON.parse(localStorage.getItem('css')):{};
			for(key in arr){
				cssFunction(key, arr[key]['list']||[]);
			}
			var arr = (storageFlag && localStorage.getItem('js'))?JSON.parse(localStorage.getItem('js')):{};
			for(key in arr){
				jsFunction(key, arr[key]['list']||[]);
			}
		}
		
		// get files which we have 
		var allCss = storageHandler({'operation':'getAllFiles', 'type':'css'}),
			allJs = storageHandler({'operation':'getAllFiles', 'type':'js'}),
			requestData = {
				'version':version,
				'data':{
					'css':allCss,
					'js':(dataObject['script']?allJs:"orderOnly")
				},
				'exception':(dataObject['exception'] || []),
				'order':(dataObject['order'] ||["b-/b-blib-build/b-blib-build.js", "b-/b-jquery/b-jquery.js", "b-/b-jquery-ui/b-jquery-ui.js"])
			};	

		ready(function(){
			$.ajax({
				url:'b-/b-blib/b-blib.php',
				data:requestData,
				type:"DATA",
				dataType: "json",
				success: function(data){
					if(!data['status']){return false;}
					version = data['version'];
					if(storageFlag){localStorage.setItem("version", JSON.stringify(version));}
					if(data['css']){cssFunction(data['css']['name'], data['css']['list']);}
					if(data['js']){jsFunction(data['js']['name'], data['js']['list']);}
				}
			});
		});//ready
		
		*/
	};
	
})( window.blib );



