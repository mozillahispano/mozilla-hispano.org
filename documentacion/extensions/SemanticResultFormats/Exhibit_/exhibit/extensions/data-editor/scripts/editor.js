/* ========================================================================
 * LensSpec.  Stores markup etc. from editor lenses found in HTML.
 *   this._type              Item type.
 *   this._lensHTML          Raw editor lens markup.
 * ======================================================================== */
Exhibit.DataEdit.LensSpec = function(ty,html) {
	this._type = ty;
	this._lensHTML = html;
}

/** Lens registry. */
Exhibit.DataEdit.LensSpec._lenses = [];
/** Debug mode? */
Exhibit.DataEdit.LensSpec._DEBUG_ = false;

/** Return type. */
Exhibit.DataEdit.LensSpec.prototype.getType = function() { return this._type; }
/** Return raw lens HTML. */
Exhibit.DataEdit.LensSpec.prototype.getHTML = function() { return this._lensHTML; }

/** Bootstrap. */
$(document).ready(function() {
	var filter = function(idx) { return $(this).attr("ex:role")=="editor"; }
	if(window.console && Exhibit.DataEdit.LensSpec._DEBUG_) { console.log("Searching for editor lenses"); }
	$('*').filter(filter).each(function(idx) {
		try {
			var ty = $(this).attr("ex:itemType");  // Type
			var ht = $(this).html();  // Markup, raw
			if(window.console && Exhibit.DataEdit.LensSpec._DEBUG_) { console.log("  Lens found: "+ty); }
			Exhibit.DataEdit.LensSpec._lenses[ty] = new Exhibit.DataEdit.LensSpec(ty,ht);
			$(this).remove();  // Erase lens from DOM
		} catch(err) { SimileAjax.Debug.warn(err); }
	});
});


/* ========================================================================
 * Editor is the object that implements an editor when deployed/rendered
 * to page.  Editor can be created from a LensSpec if one exists, or from
 * the item's HTML if not.
 *   this._itemId             Item being edited.
 *   this._jqThis             JQuery ref to DOM node of item in view.
 *   this._fields             Array of field components.
 *   this._validator          Optional validator function
 * ======================================================================== */
Exhibit.DataEdit.Editor = function(itemId,jqThis) {
	this._itemId = itemId;
	this._jqThis = jqThis;
	this._fields = {};  // Field componentns stored here
}

/** Debug mode? */
Exhibit.DataEdit.Editor._DEBUG_ = false;

/** Cols, used by components. */
Exhibit.DataEdit.Editor._BGCOL_ = '#dddddd';
Exhibit.DataEdit.Editor._ERRCOL_ = '#ff8888';
/** Components. */
Exhibit.DataEdit.Editor._COMPONENTS_ = [ 'TextField','NumberField','EnumField','ListField' ];


/** Apply this editor lens. */
Exhibit.DataEdit.Editor.prototype.apply = function() {
	var type = database.getObject(this._itemId , "type");
	var lens = (type) ? Exhibit.DataEdit.LensSpec._lenses[type] : null;
	if(lens) {
		this.log("applyWithLens() "+this._itemId+":"+type);
		this.applyWithLens(lens);
	} else {
		this.log("applyWithoutLens() "+this._itemId+":"+type);
		this.applyWithoutLens();
	}
}

/* ======================================================================== */

/** Apply with editor lens. */
Exhibit.DataEdit.Editor.prototype.applyWithLens = function(lens) {	
	var self = this;
	// Get rid of existing display lens, replace with edit lens raw HTML
	$(self._jqThis).html(lens._lensHTML);
	// Array of functions to run after each component has been rendered.
	var onShow = [];
	// Walk raw HTML, injecting field editor components
	for(var i=0;i<Exhibit.DataEdit.Editor._COMPONENTS_.length;i++) {
		var c = Exhibit.DataEdit.Editor._COMPONENTS_[i];
		self.log("Scanning for "+c);
		Exhibit.DataEdit.Editor[c].domFilter(self._jqThis,function(idx) {
			var prop,val,f;
			try {
				// Get ex:content property (predicate)
				prop = self._getContent(this);
				if(!prop) { throw "Missing target (ex:content)"; }			
				// Get value (object) and build editor
				val = (self._exists(prop)) ? self._getValues(prop) : undefined ;
				try { 
					f = new Exhibit.DataEdit.Editor[c](this,self._itemId,prop,val);
					f._validators = $(this).attr("ex:validators");
					f._undefined = (val==undefined);
				} catch(err) { 
					self.log(err,prop,val,this); 
					SimileAjax.Debug.warn(err);
				}
				self._addFieldComponent(this,prop,f,onShow);
			}catch(err) { self.log(err,prop,val,this); }
		});
	}
	// Call each onShow function
	for(var i=0;i<onShow.length;i++) { onShow[i](); }
}

/* ======================================================================== */

/** Apply with display lens HTML. */
Exhibit.DataEdit.Editor.prototype.applyWithoutLens = function() {
	var self = this;
	// Array of functions to run after each component has been rendered.
	var onShow = [];
	// Walk over DOM looking for ex:content attributes
	var filter = function(idx) { return $(this).attr("ex:content"); }
	$('*',self._jqThis).filter(filter).each(function(idx) { // See http://bugs.jquery.com/ticket/3729
		var prop,val,f;
		try {
			// ex:content expression
			var exp = $(this).attr("ex:content");
			if(!exp) { return; }
			// Get first prop in path
			var prop = self._getContent(this); // ex:content
			if(prop) {
				val = self._getValues(prop); // DB vals, scalar or array
				try {
					var t = self._guessFieldType(exp,val);
					f = new Exhibit.DataEdit.Editor[t](this,self._itemId,prop,val);
					f._undefined = (val==undefined);
					f._matchDisplayLens = true;
				} catch(err) {
					self.log(err,prop,val,self);
					SimileAjax.Debug.warn(err);
				}
				self._addFieldComponent(this,prop,f,onShow); // jq = this
			}
		}catch(err) { self.log(err,prop,val,self); }
	});
	// Call each onShow function
	for(var i=0;i<onShow.length;i++) { onShow[i](); }
}
/** exp = expression from ex:content, val = db value */
Exhibit.DataEdit.Editor.prototype._guessFieldType = function(exp,val) {
	// Check for multiple parts of expression, so prop must be an
	// item ref...
	//if(exp.match(/(\.[^\.]+){2,}/)) { return 'ReferenceField'; }
	// Otherwise check database value
	if((typeof val == 'object') && (val instanceof Array)) { return 'ListField'; }
	if(typeof val == 'number') { return 'NumberField'; }
	if(typeof val == 'string') { return 'TextField'; }
	return 'TextField';
}

Exhibit.DataEdit.Editor.prototype._exists = function(prop) {
	return database.getObject(this._itemId,prop);
}
Exhibit.DataEdit.Editor.prototype._getValues = function(prop) {
	// Get value in database, either as array or scalar 
	var valArr = [];
	database.getObjects(this._itemId,prop).visit(function(val) { valArr.push(val); });
	return (valArr.length>1) ? valArr : valArr[0];
}
Exhibit.DataEdit.Editor.prototype._getContent = function(jq) {
	var exp = $(jq).attr("ex:content");
	if(!exp) { return null; }
	var m = exp.match(/^\.(.+)?\.?/);
	return (m && m.length>1) ? m[1] : null;
}
Exhibit.DataEdit.Editor.prototype._addFieldComponent = function(jq,prop,f,onShow) {
	if(f) {
		this._fields[prop] = f;
		$(jq).replaceWith(f.getHTML(onShow));
	} else {
		$(jq).replaceWith('<span style="color:Red;">Failed to initalise</span>');
	}
}

/* ======================================================================== */

/** Return unique property values (predicate objects) for given item (subject) type. */
Exhibit.DataEdit.Editor._uniqueObjects = function(itemType,predicate) {
	var v = [];
	var itemKeys = database.getAllItems().toArray();
	for(var i=0;i<itemKeys.length;i++) {
		var item = database.getItem( itemKeys[i] );
		if(item.type!=itemType) { continue; }
		database.getObjects(item.id,predicate).visit(function(val) { v[val]=true; });
	}
	var list = [];
	for(var i in v) { list.push(i); }
	return list.sort();
}
/** Text is true or false. */
Exhibit.DataEdit.Editor._isTrueFalse = function(s,def) {
	def = (def==undefined) ? false : def;
	if(!s) { return def; }
	s=s.toLowerCase();
	return (s==='true' || s==='yes');
}
/** Create opening <tag>, using list of attrs and source element. */
Exhibit.DataEdit.Editor._htmlTag = function(elName,attr,srcEl,closed) {
	// Copy attributes from srcEl to attr, if unset
	for(var i=0;i<srcEl.attributes.length;i++) { 
		var n = srcEl.attributes[i].name;
		var v = srcEl.attributes[i].value;
		if((srcEl.attributes[i].specified!=undefined) && !srcEl.attributes[i].specified) { continue; } // Fix IE7 $%@# up!
		if(attr[n]==undefined) { attr[n]=v; }
	}
	// Create tag
	var s = '<'+elName;
	for(k in attr) { 
		if(attr[k]) { s=s+' '+k+'="'+attr[k]+'"'; }
	}
	s=s+((closed)?'/>':'>');
	return s;
}
/** Take jQuery obj, and extract certain CSS props into String */
Exhibit.DataEdit.Editor._extractStyle = function(jq) {
	var props = [ 'font-family','font-size','font-weight','font-style',
		'text-transform','text-decoration','letter-spacing',
		'word-spacing','line-height','text-align','vertical-align',
		'margin-top','margin-right','margin-bottom','margin-left',
		'padding-top','padding-right','padding-bottom','padding-left'
	];
	var s="";
	for(var i=0;i<props.length;i++) {
		var v = $(jq).css(props[i])+""; // Add "" to placate IE7 (returns Number for ints)
		if(v) { 
			v = v.replace(/["|']/gi,'');
			s = s + props[i]+':'+v+'; ';
		}
	}
	return s;
}
/** Return computed style in cross browser way */
/*Exhibit.DataEdit.Editor._getComputedStyle = function(el,style) {	
	if(el.currentStyle) { 
		return el.currentStyle[style];  // IE
	} else if(document.defaultView!=undefined && document.defaultView.getComputedStyle!=undefined) {
		return document.defaultView.getComputedStyle(el,null).getPropertyValue(style);  // Moz/W3C
	} else {
		return null;
	}
}*/
/** Return line height of element, as best it can be guessed. */
Exhibit.DataEdit.Editor._getLineHeight = function(el) {
	var h = null;
	if(el.currentStyle) { 
		h = el.currentStyle['lineHeight'];  // IE
	} else if(document.defaultView!=undefined && document.defaultView.getComputedStyle!=undefined) {
		h = document.defaultView.getComputedStyle(el,null).getPropertyValue('line-height');  // Moz/Chrome/W3C
	}
	// Parse
	if(h) { 
		if(h.indexOf('px')>=0) { return parseInt(h.replace(/px/,'')); }
		else if(el.parentNode!=null) { return Exhibit.DataEdit.Editor._getLineHeight(el.parentNode); }
		else { return 16; }
	} else {
		return 16;
	}
}
/** Return line height of element, as best it can be guessed. */
Exhibit.DataEdit.Editor._getLineHeight2 = function(el) {
	var el2 = document.createElement('div');
	el2.style.fontFamily = el.style.fontFamily;
	el2.style.fontSize = el.style.fontSize;
	el2.style.fontStyle = el.style.fontStyle;
	el2.style.fontVariant = el.style.fontVariant;
	el2.style.fontWeight = el.style.fontWeight;
	el2.style.lineHeight = el.style.lineHeight;
	el2.innerHTML = "Mj";
	
	return el2.offsetHeight;
}
/** Return match, or closest match. */
Exhibit.DataEdit.Editor.matchExactStringFromList = function(str,list) {
	var lstr = str.toLowerCase();
	// Look for straight match
	for(var i=0;i<list.length;i++) {
		if(lstr===list[i].toLowerCase()) { return list[i]; }
	}
	return null;
}
Exhibit.DataEdit.Editor.matchClosestStringFromList = function(str,list) {
	var lstr = str.toLowerCase();
	// No direct match, so find closest match
	var closest = 9999;
	var closestIdx = -1;
	for(var i=0;i<list.length;i++) {
		var li = list[i].substring(0,lstr.length).toLowerCase();
		var m = Exhibit.DataEdit.Editor._levenshteinDistance(lstr,li);
		if(m<closest) { closest=m;  closestIdx=i; }
	}
	// Return
	if(closestIdx!=-1) { return list[closestIdx]; }
		else { return null; }
}
Exhibit.DataEdit.Editor._levenshteinDistance = function(s1,s2) {
	if(s1.length<s2.length) { var t=s1;  s1=s2;  s2=t; }
	var sz1 = s1.length;
	var sz2 = s2.length;
	// Seed array
	var r = new Array();
	r[0] = new Array();
	for(var i=0;i<sz1+1;i++) { r[0][i]=i; }
	// Lowest value from three
	var smallest = function(p1,p2,p3) {
		if(p1<p2 && p1<p3) { return p1; }
			else if(p2<p1 && p2<p3) { return p2; }
				else { return p3; }
	}
	// Match
	for(var i=1;i<sz1+1;i++) {
		r[i] = new Array();
		r[i][0] = i;
		for(var j=1;j<sz2+1;j++) {
			var cost = (s1.charAt(i-1)==s2.charAt(j-1)) ? 0 : 1;
			r[i][j] = smallest(r[i-1][j]+1,r[i][j-1]+1,r[i-1][j-1]+cost);
		}
	}
	return r[sz1][sz2];
}

/** Get field for property name. */
/*Exhibit.DataEdit.Editor.prototype.getField = function(prop) {
	return this._fields[prop];
}*/

/** Fields. */
Exhibit.DataEdit.Editor.prototype.getFields = function() {
	return this._fields;
}
/** Varargs log function, using special array 'arguments', and apply() */
Exhibit.DataEdit.Editor.prototype.log = function() {
	if(window.console && Exhibit.DataEdit.Editor._DEBUG_) { console.log.apply(this,arguments); }
}		


