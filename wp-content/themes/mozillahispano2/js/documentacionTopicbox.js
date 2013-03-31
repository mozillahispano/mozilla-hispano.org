jQuery(document).ready( function() {
	jQuery(".topicbox table td")
		.hover(
			function () {
				jQuery(this).find(".desc").fadeOut(500);
				jQuery(this).find(".longdesc").fadeIn(500);
			},
			function () {
				jQuery(this).find(".desc").fadeIn(500);
				jQuery(this).find(".longdesc").fadeOut(500);
			}
		);
});
