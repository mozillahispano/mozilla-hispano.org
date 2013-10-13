jQuery(document).ready(function($) {

	$('.pickcolor').click(function(e) {
		colorPicker = $(this).next('div');
		input = $(this).prev('input');
		clicked = $(this);
	
		$.farbtastic($(colorPicker), function(a) {
			$(input).val(a);
			$(clicked).css('background', a);
		});
	
		colorPicker.show();
		e.preventDefault();
	
		$(document).mousedown( function() { $(colorPicker).hide(); });
	});


	$('.ts-fab-color-input').keyup( function() {
		var a = $(this).val(),
			b = a;

		a = a.replace(/[^a-fA-F0-9]/, '');
		if ( '#' + a !== b )
			$(this).val(a);
		if ( a.length === 3 || a.length === 6 ) {
			$(this).val( '#' + a );
			$(this).parent().find('.pickcolor').css('background', '#' + a);
		}
	});
			
	$('#ts-fab-reset-colors').click(function() {
		$('#inactive_tab_background').val('#e9e9e9');
		$('#pickcolor_inactive_tab_background').css('background', '#e9e9e9');
		$('#inactive_tab_border').val('#e9e9e9');
		$('#pickcolor_inactive_tab_border').css('background', '#e9e9e9');
		$('#inactive_tab_color').val('#333');
		$('#pickcolor_inactive_tab_color').css('background', '#333');

		$('#active_tab_background').val('#333');
		$('#pickcolor_active_tab_background').css('background', '#333');
		$('#active_tab_border').val('#333');
		$('#pickcolor_active_tab_border').css('background', '#333');
		$('#active_tab_color').val('#fff');
		$('#pickcolor_active_tab_color').css('background', '#fff');

		$('#tab_content_background').val('#f9f9f9');
		$('#pickcolor_tab_content_background').css('background', '#f9f9f9');
		$('#tab_content_border').val('#333');
		$('#pickcolor_tab_content_border').css('background', '#333');
		$('#tab_content_color').val('#555');
		$('#pickcolor_tab_content_color').css('background', '#555');
		
		return false;
	});

});