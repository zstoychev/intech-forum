function SimpleDict() {
	this.dict = new Array();
	
	this.add = function(key, value) {
		for (var i = 0; i < this.dict.length; i++) {
			if (this.dict[i][0] == key) {
				this.dict[i][1] = value;
				return;
			}
		}
		var newPair = new Array(2);
		newPair[0] = key;
		newPair[1] = value;
		this.dict[this.dict.length] = newPair;
	};
	
	this.get = function(key) {
		for (var i = 0; i < this.dict.length; i++) {
			if (this.dict[i][0] == key) {
				return this.dict[i][1];
			}
		}
		
		return null;
	};
	
	this.remove = function(key) {
		for (var i = 0; i < this.dict.length; i++) {
			if (this.dict[i][0] == key) {
				this.dict.splice(i, 1);
				return;
			}
		}
	};
	
	this.getKeys = function() {
		var result = new Array();

		for (var i = 0; i < this.dict.length; i++) {
			result[i] = this.dict[i][0];
		}
		
		return result;
	}
}

var EventUtils = {
	IECache: new SimpleDict(),

	addEventHandler: function(target, type, eventHandler) {
		if (document.implementation.hasFeature("Events", "2.0")) {
			target.addEventListener(type, eventHandler, false);
		} else if (target.attachEvent) {
			var handlerFunction = function(e) {
				eventHandler.handleEvent(EventUtils.formatIEEvent(window.event, this));
			}
			this.IECache.add(eventHandler, handlerFunction);
			target.attachEvent("on" + type, handlerFunction);
		}
	},

	removeEventHandler: function(target, type, eventHandler) {
		if (document.implementation.hasFeature("Events", "2.0")) {
			target.removeEventListener(type, eventHandler, false);
		} else if (target.detachEvent) {
			target.detachEvent("on" + type, this.IECache.get(eventHandler));
			this.IECache.remove(eventHandler);
		}
	},
		
	formatIEEvent: function(e, object) {
		e.currentTarget = object;
		e.eventPhase = 2;
		e.target = e.srcElement;
		e.timeStamp = new Date().getTime();
		e.preventDefault = function() {
            this.returnValue = false;
        };
		e.stopPropagation = function() {
            this.cancelBubble = true;
        };

		return e;
	}
};
