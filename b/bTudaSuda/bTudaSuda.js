(function(){
	
	blib.build.define(
		{'block':'bTudaSuda'},
		function(){
			var doc = window.document,
				innerHeight = doc.documentElement.clientHeight,
				self = this;

			this.delay = 100;
			this.pageY = 0;
			this.wave = 1;
			this.step = 1;
			this.pageYLabel = 0;
			this.status = '';
			this.interval = false;
			this.allows = true;
			this.template = blib.clone(this.template);
			this.template.content = "Наверх";

			window.onscroll = function() {
				if(!self.allows)return;
				self.pageY = window.pageYOffset || document.documentElement.scrollTop;

				switch(self.status){
					case '':
						if (self.pageY > innerHeight) {
							self.status = 'up';
							self._setMode("status", "up");
						}
						break;

					case 'up':
						if (self.pageY < innerHeight) {
							self._setMode("status", false);
							self.status = '';
						}
						break;

					case 'down':
						if (self.pageY > innerHeight) {
							self.status = 'up';
							self._setMode("status", "up");
							self.template.content = 'Наверх';
							self.dom.innerHTML = 'Наверх';
						}
						break;
				}
			}
		},
		false,
		{
			'onclick':function() {
				if(!this.allows)return;
				this.pageY = window.pageYOffset || document.documentElement.scrollTop;

				switch(this.status) {
					case 'up':
						this.pageYLabel = this.pageY;
						this.scrollMove(this.pageY, 0, 10, 1, 1.1);
						this._setMode("status", "down");
						this.dom.innerHTML = 'Вниз';
						this.template.content = 'Вниз';
						this.status = 'down';
						break;

					case 'down':
						this.scrollMove(0, this.pageYLabel, 10, 1, 1.1);
						this._setMode("status", "up");
						this.dom.innerHTML = 'Наверх';
						this.template.content = 'Наверх';
						this.status = 'up';
						break;
				}
			},
			'scrollMove':function(start, finish, delay, step, wave){
				var self = this;
				
				this.allows = false;
				this.delay = (delay?delay:100),
				this.step = (step?step:1),
				this.wave = (wave?wave:1),
				this.step = (!finish && this.step<0 || finish && this.step>0)?this.step:-this.step;
				
				window.clearTimeout(this.interval);
				this.interval = window.setTimeout(function(){
					if(finish && start+self.step*self.wave < finish || !finish && start+self.step*self.wave > finish){
						self.step = self.step*self.wave;
						window.scrollBy(0, self.step);
						self.scrollMove(start+self.step, finish, self.delay, self.step, self.wave);
					}else{
						window.scrollTo(0, finish);
						self.allows = true;
					}
				}, this.delay);
			}
		}
	);
	
})();
