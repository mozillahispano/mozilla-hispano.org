jQuery(document).ready(function($){
	$('.ts-fab-tabs > div').hide();
	$('.ts-fab-tabs > div:first-child').show();
	$('.ts-fab-list li:first-child').addClass('active');

	$('.ts-fab-list li a').click(function() {
		$(this).closest('.ts-fab-wrapper').find('li').removeClass('active');
		$(this).parent().addClass('active');
		var currentTab = $(this).attr('href');
		if(currentTab.indexOf('#') != -1) {
			currentTabExp = currentTab.split('#');
			currentTab = '#' + currentTabExp[1];
		}

		$(this).closest('.ts-fab-wrapper').find('.ts-fab-tabs > div').hide();
		$(currentTab).show();

		return false;
	});
});