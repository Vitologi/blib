(function(){
	
	/* PRIVATE VARIABLE */
    var config = {
            'version':'0.2.0',
			'settings':{},
			'events':{
				'onclick':true,
				'onmouseover':true,
				'onmouseout':true,
				'onfocus':true,
				'onblur':true,
				'onkeyup':true,
				'onkeydown':true
			},
			'ajax':{
				'dataType':"text",
				'success':function(){},
				'data':null,
				'type':"POST",
				'url':"/",
				'headers':["X-Requested-With","XMLHttpRequest"]
			},
			'alias':{
				'head': document.getElementsByTagName('head')[0],
				'body': document.getElementsByTagName('body')[0]
			}
        };
	
	/* PRIVATE METHODS */
	var Blib = function(){
            return new init(arguments);
        },
        init = function(args){
			
			if(!args.length){return this;}
			if(typeof(args[0]) === "function"){return Blib.ready(args[0]);}
			
			return merge(this, getElement(args));

        },
		clone = function(obj){
			var temp = obj.cloneNode(false);
			for(key in config.events){
				if(key in obj){temp[key]=obj[key];};
			}
			
			if(obj.hasChildNodes()){
				var len=obj.childNodes.length;
				for(var i=0;i<len;i++){
					temp.appendChild(clone(obj.childNodes[i]));
				}
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
		};
	
	
	/* LIBRARY METHODS */
	//set settings
	Blib.settings = function(settings){
		if(typeof settings === "object"){
			config.settings = settings;
		}
		return this;
	};
	
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
	
	/* OBJECT METHODS */
	Blib.prototype = {
		'constructor':Blib,
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
		
        'test':function(){
            console.log(config);
        },
		
		//get Array methods
        'push':[].push,
		'sort':[].sort,
		'splice':[].splice
	};
	
	
	/* MAGIC */
    init.prototype = Blib.prototype;
	if ( typeof window === "object" && typeof window.document === "object" ) {
		window.blib = Blib;
	}

})();