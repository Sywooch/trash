/* ie8 and below implementation of indexOf */

if(!Array.prototype.indexOf) {
	Array.prototype.indexOf = function(needle) {
		for(var i = 0; i < this.length; i++) {
			if(this[i] === needle) {
				return i;
			}
		}
		return -1;
	};
}

/* ie8 and below implementation of indexOf end */