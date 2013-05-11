
jQuery(function(){

	// Tabs
	jQuery("#fbfwTabs").tabs();

	// Hide Donation and twitter stuff on tabs other than Info
	jQuery("#fbfwTabs li a").click(function(){
		jQuery("#mfbfwd").hide();
	});

	jQuery("#show-mfbfwd").click(function(){
		jQuery("#mfbfwd").show();
	});


	// Hide form fields when not needed (swithed by checkbox)
	function switchBlock(block,button) {
		var buttonValue = jQuery(button + "#:checked").val();
		if (buttonValue == "on") { jQuery(block).css("display", "inline"); }
		else { jQuery(block).css("display", "none"); }

		jQuery(button).click(function(){
			jQuery(block).animate({opacity: "toggle", height: "toggle"}, 500);
		});
	}

	switchBlock("#borderColorBlock","#border");
	switchBlock("#closeButtonBlock","#showCloseButton");
	switchBlock("#overlayBlock","#overlayShow");
	switchBlock("#titleBlock","#titleShow");
	switchBlock("#callbackBlock","#callbackEnable");
	switchBlock("#extraCallsBlock","#extraCallsEnable");
	switchBlock("#easingBlock","#easing");


	// Hide Title Color if not needed
	var titlePosition = jQuery("input:radio[class=titlePosition]:checked").val();

	switch (titlePosition) {
		case "float":
		case "outside":
		case "over":
			jQuery("#titleColorBlock").css("display", "none");
	}

	jQuery("#titlePositionFloat, #titlePositionOutside, #titlePositionOver").click(function () {
		jQuery("#titleColorBlock").hide("slow");
	});

	jQuery("#titlePositionInside").click(function () {
		jQuery("#titleColorBlock").show("slow");
	});


	// Gallery Type
	var galleryType = jQuery("input:radio[class=galleryType]:checked").val();

	switch (galleryType) {
		case "all":
		case "none":
		case "post":
			jQuery("#customExpressionBlock").css("display", "none");
	}

	jQuery("#galleryTypeAll, #galleryTypeNone, #galleryTypePost").click(function () {
		jQuery("#customExpressionBlock").hide("slow");
	});

	jQuery("#galleryTypeCustom").click(function () {
		jQuery("#customExpressionBlock").show("slow");
	});

})

function confirmDefaults() {
	if (confirm(defaults_prompt) == true)
		return true;
	else
		return false;
}

var defaults_prompt = "Are you sure you want to restore FancyBox for WordPress to default settings?";
