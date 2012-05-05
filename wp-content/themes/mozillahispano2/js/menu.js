jQuery(document).ready(function(){
	jQuery("#menu ul li").hover(
		function () {
			jQuery(this).addClass("hover");
		},
		function () {
			jQuery(this).removeClass("hover");
		}
	)
}); 
