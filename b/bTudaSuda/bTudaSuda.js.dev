
(function(window){

	var handler = (function(){
		var allows = true,
			step, wave, interval,
			scrollMove = function(start, finish, delay, step, wave){
				
				allows = false;
				delay = (delay?delay:100);
				step = (step?step:1);
				wave = (wave?wave:1);
				step = (!finish && step<0 || finish && step>0)?step:-step;
				
				window.clearTimeout(interval);
				interval = window.setTimeout(function(){
					if(finish && start+step*wave < finish || !finish && start+step*wave > finish){
						step = step*wave;
						window.scrollBy(0, step);
						scrollMove(start+step, finish, delay, step, wave);
					}else{
						window.scrollTo(0, finish);
						allows = true;
					}
				}, delay);

			};
			
		return function(){
			var doc = window.document,
				body = doc.getElementsByTagName('body')[0],
				button = doc.createElement('div'),
				innerHeight = doc.documentElement.clientHeight,
				status = '',
				pageYLabel = 0,
				pageY;
			
			button.className = "bTudaSuda";
			button.innerHTML = "Наверх";
			body.appendChild(button);
			
			window.onscroll = function() {
				if(!allows)return;
				pageY = window.pageYOffset || document.documentElement.scrollTop;
				console.log(pageY, innerHeight, status);
				switch(status){
					case '':
						if (pageY > innerHeight) {
							status = 'up';
							button.className += ' bTudaSuda_status_up';
						}
						break;

					case 'up':
						if (pageY < innerHeight) {
							button.className = button.className.replace(/(\sbTudaSuda_status_up)/, '');
							status = '';
						}
						break;

					case 'down':
						if (pageY > innerHeight) {
							status = 'up';
							button.className = button.className.replace(/(bTudaSuda_status_down)/, 'bTudaSuda_status_up');
							button.innerHTML = 'Наверх';
						}
						break;
				}
			}

			button.onclick = function() {
				if(!allows)return;
				pageY = window.pageYOffset || document.documentElement.scrollTop;

				switch(status) {
					case 'up':
						pageYLabel = pageY;
						scrollMove(pageY, 0, 10, 1, 1.1);
						button.className = button.className.replace(/(bTudaSuda_status_up)/, 'bTudaSuda_status_down');
						button.innerHTML = 'Вниз';
						status = 'down';
						break;

					case 'down':
						scrollMove(0, pageYLabel, 10, 1, 1.1);
						button.className = button.className.replace(/(bTudaSuda_status_down)/, 'bTudaSuda_status_up');
						button.innerHTML = 'Наверх';
						status = 'up';
						break;
				}
			}
		}
	})();
	
	
	if(window.blib){
		blib.build.define(
			{'block':'bTudaSuda'},
			handler
		);
	}else{
		var oldOnload = (window.onload?window.onload:function(){});
		window.onload = function(e){
			oldOnload(e);
			handler();
		}
	}
	
})(window);
