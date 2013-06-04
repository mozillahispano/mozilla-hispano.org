/**
 * SemanticForms.js
 *
 * Javascript utility functions for the Semantic Forms extension.
 *
 * @author Yaron Koren
 * @author Sanyam Goyal
 * @author Stephan Gambke
 * @author Jeffrey Stuckman
 * @author Harold Solbrig
 * @author Eugene Mednikov
 */

/**
 * combobox()
 */
(function(jQuery) {
	jQuery.widget("ui.combobox", {
		_create: function() {
			var self = this;
			var select = this.element.hide();
			var name= select[0].name;
			var id = select[0].id;
			var curval = select[0].options[0].value;
			curval = curval.replace('"', '&quot;' );
			var input = jQuery("<input id=\"" + id + "\" type=\"text\" name=\"" + name + "\" value=\"" + curval + "\">")
				.insertAfter(select)
				.attr("tabIndex", select.attr("tabIndex"))
				.attr("autocompletesettings", select.attr("autocompletesettings"))
				.css("width", select.attr("comboboxwidth"))
				.autocomplete({
					source: function(request, response) {
						if ( sfgAutocompleteOnAllChars ) {
							var matcher = new RegExp(request.term, "i");
						} else {
							var matcher = new RegExp("\\b" + request.term, "i");
						}
						response(select.children("option").map(function() {
							var text = jQuery(this).text();
							if (this.value && (!request.term || matcher.test(text))) {
								return {
									id: this.value,
									label: text,
									value: text
								};
							}
						}));
					},
					delay: 0,
					change: function(event, ui) {
						if (!ui.item) {
							if (select.attr("existingvaluesonly") == 'true') {
								// remove invalid value, as it didn't match anything
								jQuery(this).val("");
							}
							return false;
						}
						select.val(ui.item.id);
						self._trigger("selected", event, {
							item: select.find("[value='" + ui.item.id.replace("'", "\\'") + "']")
						});

					},
					minLength: 0
				})
			.addClass("ui-widget ui-widget-content ui-corner-left sfComboBoxActual");
		input.attr("origname", select.attr("origname"));
		jQuery('<button type="button">&nbsp;</button>')
			.attr("tabIndex", -1)
			.attr("title", "Show All Items")
			.insertAfter(input)
			.button({
				icons: {
					primary: "ui-icon-triangle-1-s"
				},
				text: false
			}).removeClass("ui-corner-all")
			.addClass("ui-corner-right ui-button-icon sfComboBoxActual")
			// Add some inline CSS, to override CSS set by the
			// jquery.ui.tabs module - this is necessary if form is
			// used in conjunction with the Header Tabs extension.
			// 'cssText' attribute is needed because the normal
			// .css() calls don't allow for setting "!important",
			// which is needed to counteract "!important" coming
			// from the jquery.ui.tabs CSS.
			.css('cssText', 'padding: 0 !important; margin: 0 !important; -moz-border-radius: 0; -webkit-border-radius: 0; width: 1.7em;')
			.click(function() {
				// close if already visible
				if (input.autocomplete("widget").is(":visible")) {
					input.autocomplete("close");
					return;
				}
				// pass empty string as value to search for, displaying all results
				input.autocomplete("search", "");
				input.focus();
			});
		}
	});

})(jQuery);

// Activate autocomplete functionality for the specified field
(function(jQuery) {

	/* extending jQuery functions for custom highlighting */
	jQuery.ui.autocomplete.prototype._renderItem = function( ul, item) {

		var delim = this.element.context.delimiter;
		if ( delim == null ) {
			term = this.term;
		} else {
			term = this.term.split( delim ).pop();
		}
		var re = new RegExp("(?![^&;]+;)(?!<[^<>]*)(" + term.replace(/([\^\$\(\)\[\]\{\}\*\.\+\?\|\\])/gi, "\\$1") + ")(?![^<>]*>)(?![^&;]+;)", "gi");
		var loc = item.label.search(re);
		if (loc >= 0) {
			var t = item.label.substr(0, loc) + '<strong>' + item.label.substr(loc, term.length) + '</strong>' + item.label.substr(loc + term.length);
		} else {
			var t = item.label;
		}
		return jQuery( "<li></li>" )
			.data( "item.autocomplete", item )
			.append( " <a>" + t + "</a>" )
			.appendTo( ul );
	};

  jQuery.fn.attachAutocomplete = function() {
    return this.each(function() {
	// Get all the necessary values from the input's "autocompletesettings"
	// attribute. This should probably be done as three separate attributes,
	// instead.
	var field_string = jQuery(this).attr("autocompletesettings");
	
	if ( typeof field_string === 'undefined' ) {
		return;
	}
		
	var field_values = field_string.split(',');
	var delimiter = null;
	var data_source = field_values[0];
	if (field_values[1] == 'list') {
		delimiter = ",";
		if (field_values[2] != null) {
			delimiter = field_values[2];
		}
	}

	// Modify the delimiter. If it's "\n", change it to an actual
	// newline - otherwise, add a space to the end.
	// This doesn't cover the case of a delimiter that's a newline
	// plus something else, like ".\n" or "\n\n", but as far as we
	// know no one has yet needed that.
	if ( delimiter != null ) {
		if ( delimiter == "\\n" ) {
			delimiter = "\n";
		} else {
			delimiter += " ";
		}
	}
	// Store this value within the object, so that it can be used
	// during highlighting of the search term as well.
	this.delimiter = delimiter;

	/* extending jquery functions */
	jQuery.extend( jQuery.ui.autocomplete, {
	    filter: function(array, term) {
		if ( sfgAutocompleteOnAllChars ) {
			var matcher = new RegExp(jQuery.ui.autocomplete.escapeRegex(term), "i" );
		} else {
			var matcher = new RegExp("\\b" + jQuery.ui.autocomplete.escapeRegex(term), "i" );
		}
		return jQuery.grep( array, function(value) {
			return matcher.test( value.label || value.value || value );
		});
	    }
	});

    values = jQuery(this).data('autocompletevalues');
    if ( !values ) {
	values = sfgAutocompleteValues[field_string];
    }
    if (values != null) {
	// Local autocompletion

	if (delimiter != null) {
		// Autocomplete for multiple values

		function split(val) {
			return val.split(delimiter);
		}
		function extractLast(term) {
			return split(term).pop();
		}

		var thisInput = jQuery(this);

		jQuery(this).autocomplete({
			minLength: 0,
			source: function(request, response) {
				// We need to re-get the set of values, since
				// the "values" variable gets overwritten.
				values = thisInput.data( 'autocompletevalues' );
				if ( !values ) {
					values = sfgAutocompleteValues[field_string];
				}
				response(jQuery.ui.autocomplete.filter(values, extractLast(request.term)));
			},
			focus: function() {
				// prevent value inserted on focus
				return false;
			},
			select: function(event, ui) {
				var terms = split( this.value );
				// remove the current input
				terms.pop();
				// add the selected item
				terms.push( ui.item.value );
				// add placeholder to get the comma-and-space at the end
				terms.push("");
				this.value = terms.join(delimiter);
				return false;
			}
		});

        } else {
		// Autocomplete for a single value
                jQuery(this).autocomplete({
			source:values
		});
        }
    } else {
	// Remote autocompletion
	var myServer = wgScriptPath + "/api.php";
	var data_type = jQuery(this).attr("autocompletedatatype");
	myServer += "?action=sfautocomplete&format=json&" + data_type + "=" + data_source;

	if (delimiter != null) {
		function split(val) {
			return val.split(delimiter);
		}
		function extractLast(term) {
			return split(term).pop();
		}
		jQuery(this).autocomplete({
			source: function(request, response) {
				jQuery.getJSON(myServer, {
					substr: extractLast(request.term)
				}, function( data ) {
					response(jQuery.map(data.sfautocomplete, function(item) {
						return {
							value: item.title
						}
					}))
				});
			},
			search: function() {
				// custom minLength
				var term = extractLast(this.value);
				if (term.length < 1) {
					return false;
				}
			},
			focus: function() {
				// prevent value inserted on focus
				return false;
			},
			select: function(event, ui) {
				var terms = split( this.value );
				// remove the current input
				terms.pop();
				// add the selected item
				terms.push( ui.item.value );
				// add placeholder to get the comma-and-space at the end
				terms.push("");
				this.value = terms.join(delimiter);
				return false;
			}
		} );
	} else {
		jQuery(this).autocomplete({
			minLength: 1,
			source: function(request, response) {
				jQuery.ajax({
					url: myServer,
					dataType: "json",
					data: {
						substr:request.term
					},
					success: function( data ) {
						response(jQuery.map(data.sfautocomplete, function(item) {
							return {
								value: item.title
							}
						}))
					}
				});
			},
			open: function() {
				jQuery(this).removeClass("ui-corner-all").addClass("ui-corner-top");
			},
			close: function() {
				jQuery(this).removeClass("ui-corner-top").addClass("ui-corner-all");
			}
		} );
	}
    }
   });
  };
})( jQuery );


/*
 * Functions to register/unregister methods for the initialization and
 * validation of inputs.
 */

// Initialize data object to hold initialization and validation data
function setupSF() {

	jQuery("#sfForm").data("SemanticForms",{
		initFunctions : [],
		validationFunctions : []
	});

}

// Register a validation method
//
// More than one method may be registered for one input by subsequent calls to
// SemanticForms_registerInputValidation.
//
// Validation functions and their data are stored in a numbered array
//
// @param valfunction The validation functions. Must take a string (the input's id) and an object as parameters
// @param param The parameter object given to the validation function
jQuery.fn.SemanticForms_registerInputValidation = function(valfunction, param) {

	if ( ! this.attr("id") ) return this;

	if ( ! jQuery("#sfForm").data("SemanticForms") ) {
		setupSF();
	}

	jQuery("#sfForm").data("SemanticForms").validationFunctions.push({
		input : this.attr("id"),
		valfunction : valfunction,
		parameters : param
	});

	return this;
};

// Register an initialization method
//
// More than one method may be registered for one input by subsequent calls to
// SemanticForms_registerInputInit. This method also executes the initFunction
// if the element referenced by /this/ is not part of a multipleTemplateStarter.
//
// Initialization functions and their data are stored in a associative array
//
// @param initFunction The initialization function. Must take a string (the input's id) and an object as parameters
// @param param The parameter object given to the initialization function
// @param noexecute If set, the initialization method will not be executed here
jQuery.fn.SemanticForms_registerInputInit = function( initFunction, param, noexecute ) {

	// return if element has no id
	if ( ! this.attr("id") ) return this;

	// setup data structure if necessary
	if ( ! jQuery("#sfForm").data("SemanticForms") ) {
		setupSF();
	}

	// if no initialization function for this input was registered yet,
	// create entry
	if ( ! jQuery("#sfForm").data("SemanticForms").initFunctions[this.attr("id")] ) {
		jQuery("#sfForm").data("SemanticForms").initFunctions[this.attr("id")] = new Array();
	}

	// record initialization function
	jQuery("#sfForm").data("SemanticForms").initFunctions[this.attr("id")].push({
		initFunction : initFunction,
		parameters : param
	});

	// execute initialization if input is not part of multipleTemplateStarter
	// and if not forbidden
	if ( this.closest(".multipleTemplateStarter").length == 0 && !noexecute) {
		var input = this;
		// ensure initFunction is only exectued after doc structure is complete
		jQuery(function() {initFunction ( input.attr("id"), param )});
	}

	return this;
};

// Unregister all validation methods for the element referenced by /this/
jQuery.fn.SemanticForms_unregisterInputValidation = function() {

	if ( this.attr("id") && jQuery("#sfForm").data("SemanticForms") ) {
		delete jQuery("#sfForm").data("SemanticForms").validationFunctions[this.attr("id")];
	}

	return this;
}

// Unregister all initialization methods for the element referenced by /this/
jQuery.fn.SemanticForms_unregisterInputInit = function() {

	if ( this.attr("id") && jQuery("#sfForm").data("SemanticForms") ) {
		delete jQuery("#sfForm").data("SemanticForms").initFunctions[this.attr("id")];
	}

	return this;
}

/*
 * Functions for handling 'show on select'
 */

// Display a div that would otherwise be hidden by "show on select".
function showDiv(div_id, instanceWrapperDiv, speed) {
	var elem = jQuery('[id="' + div_id + '"]', instanceWrapperDiv);
	elem.find(".hiddenBySF").removeClass('hiddenBySF');

	elem.each( function() {
		if ( jQuery(this).css('display') == 'none' ) {

			jQuery(this).slideDown(speed, function() {
				jQuery(this).fadeTo(speed,1);
			});

		}
	});
}

// Hide a div due to "show on select". The CSS class is there so that SF can
// ignore the div's contents when the form is submitted.
function hideDiv(div_id, instanceWrapperDiv, speed) {
	// IDs can't contain spaces, and jQuery won't work with such IDs - if
	// this one has a space, display an alert.
	if ( div_id.indexOf( ' ' ) > -1 ) {
		// TODO - this should probably be a language value, instead of
		// hardcoded in English.
		alert( "Warning: this form has \"show on select\" pointing to an invalid element ID (\"" + div_id + "\") - IDs in HTML cannot contain spaces." );
	}

	var elem = jQuery('[id="' + div_id + '"]', instanceWrapperDiv);
	elem.find("span, div").addClass('hiddenBySF');

	elem.each( function() {
		if ( jQuery(this).css('display') != 'none' ) {

			// if 'display' is not 'hidden', but the element is hidden otherwise
			// (e.g. by having height = 0), just hide it, else animate the hiding
			if ( jQuery(this).is(':hidden') ) {
				jQuery(this).hide();
			} else {
			jQuery(this).fadeTo(speed, 0, function() {
				jQuery(this).slideUp(speed);
			});
			}
		}
	});
}

// Show this div if the current value is any of the relevant options -
// otherwise, hide it.
function showDivIfSelected(options, div_id, inputVal, instanceWrapperDiv, initPage) {
	for ( var i = 0; i < options.length; i++ ) {
		// If it's a listbox and the user has selected more than one
		// value, it'll be an array - handle either case.
		if ((jQuery.isArray(inputVal) && jQuery.inArray(options[i], inputVal) >= 0) ||
		    (!jQuery.isArray(inputVal) && (inputVal == options[i]))) {
			showDiv( div_id, instanceWrapperDiv, initPage ? 0 : 'fast' );
			return;
		}
	}
	hideDiv(div_id, instanceWrapperDiv, initPage ? 0 : 'fast' );
}

// Used for handling 'show on select' for the 'dropdown' and 'listbox' inputs.
jQuery.fn.showIfSelected = function(initPage) {
	var inputVal = this.val();
	var showOnSelectVals = sfgShowOnSelect[this.attr("id")];

	var instanceWrapperDiv = this.closest('.multipleTemplateInstance');
	if ( instanceWrapperDiv.length == 0 ) {
		instanceWrapperDiv = null;
	}

	if ( showOnSelectVals !== undefined ) {
		for ( var i = 0; i < showOnSelectVals.length; i++ ) {
			var options = showOnSelectVals[i][0];
			var div_id = showOnSelectVals[i][1];
			showDivIfSelected( options, div_id, inputVal, instanceWrapperDiv, initPage );
		}
	}

	return this;
}

// Show this div if any of the relevant selections are checked -
// otherwise, hide it.
jQuery.fn.showDivIfChecked = function(options, div_id, instanceWrapperDiv, initPage ) {
	for ( var i = 0; i < options.length; i++ ) {
		if (jQuery(this).find('[value="' + options[i] + '"]').is(":checked")) {
			showDiv(div_id, instanceWrapperDiv, initPage ? 0 : 'fast' );
			return this;
		}
	}
	hideDiv(div_id, instanceWrapperDiv);

	return this;
}

// Used for handling 'show on select' for the 'checkboxes' and 'radiobutton'
// inputs.
jQuery.fn.showIfChecked = function(initPage) {

	var showOnSelectVals = sfgShowOnSelect[this.attr("id")];

	var instanceWrapperDiv = this.closest('.multipleTemplateInstance');
	if ( instanceWrapperDiv.length == 0 ) {
		instanceWrapperDiv = null;
	}

	if ( showOnSelectVals !== undefined ) {
		for ( var i = 0; i < showOnSelectVals.length; i++ ) {
			var options = showOnSelectVals[i][0];
			var div_id = showOnSelectVals[i][1];
			this.showDivIfChecked(options, div_id, instanceWrapperDiv, initPage );
		}
	}

	return this;
}

// Used for handling 'show on select' for the 'checkbox' input.
jQuery.fn.showIfCheckedCheckbox = function(initPage) {

	var div_id = sfgShowOnSelect[this.attr("id")];

	var instanceWrapperDiv = this.closest('.multipleTemplateInstance');
	if ( instanceWrapperDiv.length == 0 ) {
		instanceWrapperDiv = null;
	}

	if (jQuery(this).is(":checked")) {
		showDiv(div_id, instanceWrapperDiv, initPage ? 0 : 'fast' );
	} else {
		hideDiv(div_id, instanceWrapperDiv, initPage ? 0 : 'fast' );
	}

	return this;
}

/*
 * Validation functions
 */

// Display an error message on the end of an input.
jQuery.fn.addErrorMessage = function(msg) {
	this.append(' <span class="errorMessage">' + msg + '</span>');
}

jQuery.fn.validateMandatoryField = function() {
	var fieldVal = this.find(".mandatoryField").val();
	if (fieldVal == null) {
		var isEmpty = true;
	} else if (jQuery.isArray(fieldVal)) {
		var isEmpty = (fieldVal.length == 0);
	} else {
		var isEmpty = (fieldVal.replace(/\s+/, '') == '');
	}
	if (isEmpty) {
		this.addErrorMessage(sfgBlankErrorStr);
		return false;
	} else {
		return true;
	}
}

jQuery.fn.validateMandatoryComboBox = function() {
	if (this.find("input").val() == '') {
		this.addErrorMessage(sfgBlankErrorStr);
		return false;
	} else {
		return true;
	}
}

jQuery.fn.validateMandatoryDateField = function() {
	if (this.find(".dayInput").val() == '' ||
	    this.find(".monthInput").val() == '' ||
	    this.find(".yearInput").val() == '') {
		this.addErrorMessage(sfgBlankErrorStr);
		return false;
	} else {
		return true;
	}
}

// Special handling for radiobuttons, because what's being checked
// is the first radiobutton, which has an empty value.
jQuery.fn.validateMandatoryRadioButton = function() {
	if (this.find("[value='']").is(':checked')) {
		this.addErrorMessage(sfgBlankErrorStr);
		return false;
	} else {
		return true;
	}
}

jQuery.fn.validateMandatoryCheckboxes = function() {
	// Get the number of checked checkboxes within this span - must
	// be at least one.
	var numChecked = this.find("input:checked").size();
	if (numChecked == 0) {
		this.addErrorMessage(sfgBlankErrorStr);
		return false;
	} else {
		return true;
	}
}

/*
 * Type-based validation
 */

jQuery.fn.validateURLField = function() {
	var fieldVal = this.find("input").val();
	// code borrowed from http://snippets.dzone.com/posts/show/452
	var url_regexp = /(ftp|http|https|rtsp|news):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
	if (fieldVal == "" || url_regexp.test(fieldVal)) {
		return true;
	} else {
		this.addErrorMessage(sfgBadURLErrorStr);
		return false;
	}
}

jQuery.fn.validateEmailField = function() {
	var fieldVal = this.find("input").val();
	// code borrowed from http://javascript.internet.com/forms/email-validation---basic.html
	var email_regexp = /^\s*\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,6})+\s*$/;
	if (fieldVal == '' || email_regexp.test(fieldVal)) {
		return true;
	} else {
		this.addErrorMessage(sfgBadEmailErrorStr);
		return false;
	}
}

jQuery.fn.validateNumberField = function() {
	var fieldVal = this.find("input").val();
	// Handle "E notation"/scientific notation ("1.2e-3") in addition
	// to regular numbers
	if (fieldVal == '' ||
	fieldVal.match(/^\s*[\-+]?((\d+[\.,]?\d*)|(\d*[\.,]?\d+))([eE]?[\-\+]?\d+)?\s*$/)) {
		return true;
	} else {
		this.addErrorMessage(sfgBadNumberErrorStr);
		return false;
	}
}

jQuery.fn.validateDateField = function() {
	// validate only if day and year fields are both filled in
	var dayVal = this.find(".dayInput").val();
	var yearVal = this.find(".yearInput").val();
	if (dayVal == '' || yearVal == '') {
		return true;
	} else if (dayVal.match(/^\d+$/) && dayVal <= 31) {
		// no year validation, since it can also include
		// 'BC' and possibly other non-number strings
		return true;
	} else {
		this.addErrorMessage(sfgBadDateErrorStr);
		return false;
	}
}

window.validateAll = function () {
	var num_errors = 0;

	// Remove all old error messages.
	jQuery(".errorMessage").remove();

	// Make sure all inputs are ignored in the "starter" instance
	// of any multiple-instance template.
	jQuery(".multipleTemplateStarter").find("span, div").addClass("hiddenBySF");

	jQuery("span.inputSpan.mandatoryFieldSpan").not(".hiddenBySF").each( function() {
		if (! jQuery(this).validateMandatoryField() ) num_errors += 1;
	});
	jQuery("div.ui-widget.mandatory").not(".hiddenBySF").each( function() {
		if (! jQuery(this).validateMandatoryComboBox() ) num_errors += 1;
	});
	jQuery("span.dateInput.mandatoryFieldSpan").not(".hiddenBySF").each( function() {
		if (! jQuery(this).validateMandatoryDateField() ) num_errors += 1;
	});
	jQuery("span.radioButtonSpan.mandatoryFieldSpan").not(".hiddenBySF").each( function() {
		if (! jQuery(this).validateMandatoryRadioButton() ) num_errors += 1;
	});
	jQuery("span.checkboxesSpan.mandatoryFieldSpan").not(".hiddenBySF").each( function() {
		if (! jQuery(this).validateMandatoryCheckboxes() ) num_errors += 1;
	});
	jQuery("span.URLInput").not(".hiddenBySF").each( function() {
		if (! jQuery(this).validateURLField() ) num_errors += 1;
	});
	jQuery("span.emailInput").not(".hiddenBySF").each( function() {
		if (! jQuery(this).validateEmailField() ) num_errors += 1;
	});
	jQuery("span.numberInput").not(".hiddenBySF").each( function() {
		if (! jQuery(this).validateNumberField() ) num_errors += 1;
	});
	jQuery("span.dateInput").not(".hiddenBySF").each( function() {
		if (! jQuery(this).validateDateField() ) num_errors += 1;
	});

	// call registered validation functions
	var sfdata = jQuery("#sfForm").data('SemanticForms');

	if ( sfdata && sfdata.validationFunctions.length > 0 ) { // found data object?

		// for every registered input
		for ( var i = 0; i < sfdata.validationFunctions.length; i++ ) {

			// if input is not part of multipleTemplateStarter
			if ( jQuery("#" + sfdata.validationFunctions[i].input).closest(".multipleTemplateStarter").length == 0 ) {

				if (! sfdata.validationFunctions[i].valfunction(
						sfdata.validationFunctions[i].input,
						sfdata.validationFunctions[i].parameters)
					)
					num_errors += 1;
			}
		}
	}

	if (num_errors > 0) {
		// add error header, if it's not there already
		if (jQuery("#form_error_header").size() == 0) {
			jQuery("#contentSub").append('<div id="form_error_header" class="errorbox" style="font-size: medium"><img src="' + sfgScriptPath + '/skins/MW-Icon-AlertMark.png" />&nbsp;' + sfgFormErrorsHeader + '</div><br clear="both" />');
		}
		scroll(0, 0);
	} else {
		// Disable inputs hidden due to either "show on select" or
		// because they're part of the "starter" div for
		// multiple-instance templates, so that they aren't
		// submitted by the form.
		jQuery('.hiddenBySF').find("input, select, textarea").attr('disabled', 'disabled');
	}
	return (num_errors == 0);
}

/**
 * Functions for multiple-instance templates.
 */

jQuery.fn.addInstance = function() {
	// Global variable.
	num_elements++;

	// Create the new instance
	var new_div = this.closest(".multipleTemplateWrapper")
		.find(".multipleTemplateStarter")
		.clone()
		.removeClass('multipleTemplateStarter')
		.addClass('multipleTemplateInstance')
		.addClass('multipleTemplate') // backwards compatibility
		.removeAttr("id")
		.fadeTo(0,0)
		.slideDown('fast', function() {
			jQuery(this).fadeTo('fast', 1);
		});

	// Make internal ID unique for the relevant form elements, and replace
	// the [num] index in the element names with an actual unique index
	new_div.find("input, select, textarea").each(
		function() {
			// Add in a 'b' at the end of the name to reduce the
			// chance of name collision with another field
			if (this.name) {
				var old_name = this.name.replace(/\[num\]/g, '');
				jQuery(this).attr('origName', old_name);
				this.name = this.name.replace(/\[num\]/g, '[' + num_elements + 'b]');
			}

			if (this.id) {

				var old_id = this.id;

				this.id = this.id.replace(/input_/g, 'input_' + num_elements + '_');

				// TODO: Data in sfgShowOnSelect should probably be stored in
				//  jQuery("#sfForm").data('SemanticForms')
				if ( sfgShowOnSelect[ old_id ] ) {
					sfgShowOnSelect[ this.id ] = sfgShowOnSelect[ old_id ];
				}

				// register initialization and validation methods for new inputs

				var sfdata = jQuery("#sfForm").data('SemanticForms');
				if ( sfdata && sfdata.initFunctions[old_id] ) { // found data object?

					// For every initialization method for
					// input with id old_id, register the
					// method for the new input.
					for ( var i = 0; i < sfdata.initFunctions[old_id].length; i++ ) {

						jQuery(this).SemanticForms_registerInputInit(
							sfdata.initFunctions[old_id][i].initFunction,
							sfdata.initFunctions[old_id][i].parameters,
							true //do not yet execute
						);
					}

					// For every validation method for the
					// input with ID old_id, register it
					// for the new input.
					for ( var i = 0; i < sfdata.validationFunctions.length; i++ ) {

						if ( sfdata.validationFunctions[i].input == old_id ) {

							jQuery(this).SemanticForms_registerInputValidation(
								sfdata.validationFunctions[i].valfunction,
								sfdata.validationFunctions[i].parameters
							);
						}
					}
				}
			}
		}
	);

	new_div.find('a').attr('href', function() {
		return this.href.replace(/input_/g, 'input_' + num_elements + '_');
	});
	/*
	new_div.find('span').attr('id', function() {
		return this.id.replace(/span_/g, 'span_' + num_elements + '_');
	});
	*/

	// Add the new instance
	this.closest(".multipleTemplateWrapper")
		.find(".multipleTemplateList")
		.append(new_div);

	// Enable the new remover
	new_div.find('.remover').click( function() {

		// Unregister initialization and validation for deleted inputs -
		// probably unnecessary, since the used IDs will never be
		// assigned a second time, but it's the clean solution (if
		// only to free memory)
		jQuery(this).parent().find("input, select, textarea").each(
			function() {
				jQuery(this).SemanticForms_unregisterInputInit();
				jQuery(this).SemanticForms_unregisterInputValidation();
			}
		);

	});

	// Somewhat of a hack - remove the divs that the combobox() call
	// adds on, so that we can just call combobox() again without
	// duplicating anything. There's probably a nicer way to do this,
	// that doesn't involve removing and then recreating divs.
	new_div.find('.sfComboBoxActual').remove();

	new_div.initializeJSElements(true);

	// Initialize new inputs
	new_div.find("input, select, textarea").each(
		function() {

			if (this.id) {

				var sfdata = jQuery("#sfForm").data('SemanticForms');
				if ( sfdata && sfdata.initFunctions[this.id] ) { // if anything registered at all
					// Call every initialization method
					// for this input
					for ( var i = 0; i < sfdata.initFunctions[this.id].length; i++ ) {
						sfdata.initFunctions[this.id][i].initFunction(
							this.id,
							sfdata.initFunctions[this.id][i].parameters
						)
					}
				}
			}
		}
	);

}

// The first argument is needed, even though it's an attribute of the element
// on which this function is called, because it's the 'name' attribute for
// regular inputs, and the 'origName' attribute for inputs in multiple-instance
// templates.
jQuery.fn.setDependentAutocompletion = function( dependentField, baseField, baseValue ) {
	propName = sfgFieldProperties[dependentField];
	baseProp = sfgFieldProperties[baseField];
	var myServer = wgScriptPath + "/api.php";
	myServer += "?action=sfautocomplete&format=json&property=" + propName + "&baseprop=" + baseProp + "&basevalue=" + baseValue;
	var dependentValues = [];
	var thisInput = jQuery(this);
	// We use jQuery.ajax() here instead of jQuery.getJSON() so that the
	// 'async' parameter can be set. That, in turn, is set because
	// if the 2nd, "dependent" field is a combo box, it can have weird
	// behavior: clicking on the down arrow for the combo box leads to a
	// "blur" event for the base field, which causes the possible
	// values to get recalculated, but not in time for the dropdown to
	// change values - it still shows the old values. By setting
	// "async: false", we guarantee that old values won't be shown - if
	// the values haven't been recalculated yet, the dropdown won't
	// appear at all.
	// @TODO - handle this the right way, by having special behavior for
	// the dropdown - it should get delayed until the values are
	// calculated, then appear.
	jQuery.ajax({
		url: myServer,
		dataType: 'json',
		async: false,
		success: function(data) {
			realData = data.sfautocomplete;
			jQuery.each(realData, function(key, val) {
				dependentValues.push(val.title);
			});
			thisInput.data('autocompletevalues', dependentValues);
			thisInput.attachAutocomplete();
		}
	});
}

/**
 * Called on a 'base' field (e.g., for a country) - sets the autocompletion
 * for its 'dependent' field (e.g., for a city).
 */
jQuery.fn.setAutocompleteForDependentField = function( partOfMultiple ) {
	curValue = jQuery(this).val();
	if ( curValue == null ) { return this; }

	nameAttr = partOfMultiple ? 'origName' : 'name';
	name = jQuery(this).attr(nameAttr);
	for ( var i in sfgDependentFields ) {
		dependentFieldPair = sfgDependentFields[i];
		if ( dependentFieldPair[0] == name ) {
			dependentField = dependentFieldPair[1];
			if ( partOfMultiple ) {
				jQuery(this).closest(".multipleTemplateInstance")
					.find('[origName="' + dependentField + '"]')
					.setDependentAutocompletion(dependentField, name, curValue);
			} else {
				jQuery('[name="' + dependentField + '"]')
					.setDependentAutocompletion(dependentField, name, curValue);
			}
		}
	}

	return this;
}

/**
 * Initialize all the JS-using elements contained within this block - can be
 * called for either the entire HTML body, or for a div representing an
 * instance of a multiple-instance template.
 */
jQuery.fn.initializeJSElements = function( partOfMultiple ) {
	this.find(".sfShowIfSelected").each( function() {
		jQuery(this)
		.showIfSelected(true)
		.change( function() {
			jQuery(this).showIfSelected(false);
		});
	});

	this.find(".sfShowIfChecked").each( function() {
		jQuery(this)
		.showIfChecked(true)
		.click( function() {
			jQuery(this).showIfChecked(false);
		});
	});

	this.find(".sfShowIfCheckedCheckbox").each( function() {
		jQuery(this)
		.showIfCheckedCheckbox(true)
		.click( function() {
			jQuery(this).showIfCheckedCheckbox(false);
		});
	});

	this.find(".remover").click( function() {
		// Remove the encompassing div for this instance.
		jQuery(this).closest(".multipleTemplateInstance")
		.fadeTo('fast', 0, function() {
			jQuery(this).slideUp('fast', function() {
				jQuery(this).remove();
			});
		});
	});

	this.find('.autocompleteInput').attachAutocomplete();
	this.find('.sfComboBox').combobox();
	this.find('.autoGrow').autoGrow();
	this.find('.sfFancyBox').fancybox({
		'width'         : '75%',
		'height'        : '75%',
		'autoScale'     : false,
		'transitionIn'  : 'none',
		'transitionOut' : 'none',
		'type'          : 'iframe',
		'overlayColor'  : '#222',
		'overlayOpacity' : '0.8'
	});

	// @TODO - this should ideally be called only for inputs that have
	// a dependent field - which might involve changing the storage of
	// "dependent fields" information from a global variable to a
	// per-input HTML attribute.
	this.find('input, select[class!="sfComboBox"]').each( function() {
		jQuery(this)
		.setAutocompleteForDependentField( partOfMultiple )
		.blur( function() {
			jQuery(this).setAutocompleteForDependentField( partOfMultiple );
		});
	});
	// The 'blur' event doesn't get triggered for radio buttons for
	// Chrome and Safari (the WebKit-based browsers) so use the 'change'
	// event in addition.
	// @TODO - blur() shuldn't be called at all for radio buttons.
	this.find('input:radio')
		.change( function() {
			jQuery(this).setAutocompleteForDependentField( partOfMultiple );
		});
}

var num_elements = 0;

// Once the document has finished loading, set up everything!
jQuery(document).ready(function() {
	jQuery('body').initializeJSElements();

	jQuery('.multipleTemplateInstance').initializeJSElements(true);
	jQuery('.multipleTemplateAdder').click( function() {jQuery(this).addInstance();} );
	jQuery('.multipleTemplateList').sortable({
		axis: 'y',
		handle: '.rearrangerImage'
	});


	// If the form is submitted, validate everything!
	jQuery('#sfForm').submit( function() {return validateAll();} );
});
