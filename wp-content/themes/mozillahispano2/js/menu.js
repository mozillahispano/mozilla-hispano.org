/* Meter class js al elemento html */
var isSupported = document.getElementById && document.getElementsByTagName;
if (isSupported) {
	document.documentElement.className = "js";
}

/* onReady multinavegador */
/* https://github.com/airportyh/onready */
(function(){
	var addLoadListener
	var removeLoadListener
	if (window.addEventListener){
		addLoadListener = function(func){
			window.addEventListener('DOMContentLoaded', func, false)
			window.addEventListener('load', func, false)
		}
		removeLoadListener = function(func){
			window.removeEventListener('DOMContentLoaded', func, false)
			window.removeEventListener('load', func, false)
		}
	}else if (document.attachEvent){
		addLoadListener = function(func){
			document.attachEvent('onreadystatechange', func)
			document.attachEvent('load', func)
		}
		removeLoadListener = function(func){
			document.detachEvent('onreadystatechange', func)
			document.detachEvent('load', func)
		}
	}
	
	var callbacks = null
	var done = false
	function __onReady(){
		done = true
		removeLoadListener(__onReady)
		if (!callbacks) return
		for (var i = 0, cbLength = callbacks.length; i < cbLength; i++){
			callbacks[i]()
		}
		callbacks = null
	}
	function OnReady(func){
		if (done){
			func()
			return
		}
		if (!callbacks){
			callbacks = []
			addLoadListener(__onReady)
		}
		callbacks.push(func)
	}
	window.OnReady = OnReady
})()

window.OnReady (
	function () {
		/* http://blog.stchur.com/2007/03/15/mouseenter-and-mouseleave-events-for-firefox-and-other-non-ie-browsers/ */
		var xb = {
			evtHash: [],

			ieGetUniqueID: function(_elem) 	{
				if (_elem === window) { return 'theWindow'; }
				else if (_elem === document) { return 'theDocument'; }
				else { return _elem.uniqueID; }
			},

			addEvent: function(_elem, _evtName, _fn, _useCapture) {
				if (typeof _elem.addEventListener != 'undefined') {
					if (_evtName == 'mouseenter')
						{ _elem.addEventListener('mouseover', xb.mouseEnter(_fn), _useCapture); }
					else if (_evtName == 'mouseleave')
						{ _elem.addEventListener('mouseout', xb.mouseEnter(_fn), _useCapture); } 
					else
						{ _elem.addEventListener(_evtName, _fn, _useCapture); }
				}
				else if (typeof _elem.attachEvent != 'undefined') {
					var key = '{FNKEY::obj_' + xb.ieGetUniqueID(_elem) + '::evt_' + _evtName + '::fn_' + _fn + '}';
					var f = xb.evtHash[key];
					if (typeof f != 'undefined')
						{ return; }
					
					f = function() {
						_fn.call(_elem);
					};
				
					xb.evtHash[key] = f;
					_elem.attachEvent('on' + _evtName, f);
			
					// attach unload event to the window to clean up possibly IE memory leaks
					window.attachEvent('onunload', function() {
						_elem.detachEvent('on' + _evtName, f);
					});
				
					key = null;
					//f = null;   /* DON'T null this out, or we won't be able to detach it */
				}
				else
					{ _elem['on' + _evtName] = _fn; }
			},	

			removeEvent: function(_elem, _evtName, _fn, _useCapture) {
				if (typeof _elem.removeEventListener != 'undefined')
					{ _elem.removeEventListener(_evtName, _fn, _useCapture); }
				else if (typeof _elem.detachEvent != 'undefined') {
					var key = '{FNKEY::obj_' + xb.ieGetUniqueID(_elem) + '::evt' + _evtName + '::fn_' + _fn + '}';
					var f = xb.evtHash[key];
					if (typeof f != 'undefined') {
						_elem.detachEvent('on' + _evtName, f);
						delete xb.evtHash[key];
					}
				
					key = null;
					//f = null;   /* DON'T null this out, or we won't be able to detach it */
				}
			},
			
			mouseEnter: function(_pFn) {
				return function(_evt) {
					var relTarget = _evt.relatedTarget;				
					if (this == relTarget || xb.isAChildOf(this, relTarget))
						{ return; }

					_pFn.call(this, _evt);
				}
			},
			
			isAChildOf: function(_parent, _child) {
				if (_parent == _child) { return false };
				
				while (_child && _child != _parent)
					{ _child = _child.parentNode; }
				
				return _child == _parent;
			}	
		};

		var liPadres;
		var
		dLastClick = +(new Date), /* establece cuando se hizo el último mostrar */
		showChild = function(e) {
			changeChild("showChild", this);
			dLastClick = +(new Date);
		},
		hiddeChild = function(e) {
			changeChild("hiddeChild", this);
		},
		toggleChild = function(e) {
			/* establecer un pequeño lag para mitigar el problema de gecko en android con pantallas táctiles que dispara un click + hover casi simultáneamente */
			if ((+(new Date) - dLastClick) > 200){
				changeChild("toggleChild", this.parentNode);
			}
		},
		changeChild = function(action, liPadre) {
			//var liPadre = this.parentNode;
			var liHermanos = liPadre.parentNode.children;
			for (var i = 0, numLi = liHermanos.length; i < numLi; i++) {
				if(liPadre === liHermanos[i]) {
					switch (action) {
						case "showChild" :
							addClassName(liHermanos[i], "visibleChilds");
							break;
						case "hiddeChild" :
							removeClassName(liHermanos[i], "visibleChilds");
							break;
						case "toggleChild" :
							toggleClassName(liHermanos[i], "visibleChilds");
							break;
					}
				}
				else {
					removeClassName(liHermanos[i], "visibleChilds");
				}
			}
		}

		var hasClassName = function(el, name) {
			return new RegExp("(?:^|\\s+)" + name + "(?:\\s+|$)").test(el.className);
		};
		var addClassName = function (el, name) {
			if (!hasClassName(el, name)) {
				el.className = el.className ? [el.className, name].join(' ') : name;
			}
		};
		var removeClassName = function(el, name) {
			if (hasClassName(el, name)) {
				var c = el.className;
				el.className = c.replace(new RegExp("(?:^|\\s+)" + name + "(?:\\s+|$)", "g"), "");
			}
		};
		var toggleClassName = function(el, name) {
			if (hasClassName(el, name)) {
				removeClassName(el, name)
			}
			else {
				addClassName(el, name);
			}
		};

		var menu = document.getElementById("menu");
		// Cogemos los li que despliegan hijos
		var liPadres = menu.getElementsByTagName("li");
		/* Detectar los items que tienen hijos */
		for (var i = 0, numLi = liPadres.length; i < numLi; i++) {  
			//console.log(liPadres[i]);//addEventListener
			if (liPadres[i].getElementsByTagName("ul").length != 0) {
				// con hijos, buscar el span
				var hijos = liPadres[i].children;
				for (var j = 0, numHijos = hijos.length; j < numHijos; j++) {
					if (hijos[j].nodeName == 'SPAN') {

						xb.addEvent(liPadres[i], 'mouseenter', showChild, false);
						xb.addEvent(liPadres[i], 'mouseleave', hiddeChild, false);
						xb.addEvent(hijos[j], 'click', toggleChild, false);
						addClassName(liPadres[i], "withChilds");
					}
				}
			}
		}
	}
);
