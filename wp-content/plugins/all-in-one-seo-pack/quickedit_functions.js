function aioseop_ajax_edit_meta_form( post_id, post_title, meta ) {
	var uform = jQuery('#aioseop_'+meta+'_' + post_id);
	var element = uform.html(); var input;
	input = '<input type="text" id="aioseop_new_'+meta+'_' + post_id + '" value="' + post_title + '" style="font-size:10px;width:65%;float:left" />';
	input += '<label style="float:left">';
	input += '<a class="aioseop_mpc_SEO_admin_options_edit" href="javascript:void(0);" id="aioseop_'+meta+'_save_' + post_id + '" >';
	input += '<img src="' + aioseopadmin.imgUrl+'accept.png" border="0" alt="" title="'+meta+'" /></a>';
	input += '<a class="aioseop_mpc_SEO_admin_options_edit" href="javascript:void(0);" id="aioseop_'+meta+'_cancel_' + post_id + '" >';
	input += '<img src="' + aioseopadmin.imgUrl+'delete.png" border="0" alt="" title="'+meta+'" /></a>';
	input += '</label>';
	uform.html( input );
	jQuery('#aioseop_'+meta+'_cancel_' + post_id).click(function() {
		uform.html( element );
	});
	jQuery('#aioseop_'+meta+'_save_' + post_id).click(function() {
		var new_meta = jQuery( 'input#aioseop_new_'+meta+'_' + post_id ).val();
		handle_post_meta( post_id, new_meta, meta );
	});
}

function handle_post_meta( p, t, m ) {
	jQuery("div#aioseop_"+m+"_"+p).fadeOut('fast', function() {
		var loading = '<label class="aioseop_'+m+'_loading">';
		loading += '<img style="width:20px;margin-right:5px;float:left" align="absmiddle" ';
		loading += 'src="'+aioseopadmin.imgUrl+'activity.gif" border="0" alt="" title="'+m+'" /></a>';
		loading += '</label><div style="float:left">Please wait…</div>';
		jQuery("div#aioseop_"+m+"_"+p).fadeIn('fast', function() {
			var aioseop_sack = new sack(aioseopadmin.requestUrl);
			aioseop_sack.execute = 1; 
			aioseop_sack.method = 'POST';
			aioseop_sack.setVar( "action", "aioseop_ajax_save_meta");
			aioseop_sack.setVar( "post_id", p );
			aioseop_sack.setVar( "new_meta", t );
			aioseop_sack.setVar( "target_meta", m );
			aioseop_sack.setVar( "_inline_edit", jQuery('input#_inline_edit').val() );
			aioseop_sack.onError = function() {alert('Ajax error on saving title'); };
			aioseop_sack.runAJAX();
		})
		jQuery("div#aioseop_"+m+"_"+p).html(loading);
	})
};