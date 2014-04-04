jQuery(document).ready(function($){
    $('.yarpp_switch_button').click(function( e){
        e.preventDefault();
        var url = $(this).attr('href'),
            data = { go : $(this).data('go') };

        $.get(
            url,
            data,
            function(resp){
                if(resp === 'ok'){
                    window.location.href = './options-general.php?page=yarpp';
                }
            }
        );
    });
/* MARK: API Setting override
    $('#yarpp_pro_api_settings_unlock').click(function(){
        $('#yarpp_pro_aid, #yarpp_pro_api_key, #yarpp_pro_settings_submit').attr('disabled',false);
        $('#yarpp_pro_aid').focus();
        $(this).attr('disabled',true);
    });

    $('#yarpp_pro_api_settings_unlock').click(function(){
        $('#yarpp_pro_aid, #yarpp_pro_api_key').attr('disabled',false);
        $('#yarpp_pro_aid').focus();
    });

    $('#yarpp_pro_api_settings').submit(function(e){
        $('#yarpp_pro_aid, #yarpp_pro_api_key').each(function (idx,obj){
            if ($(obj).val() === ''){
                var msg = 'This field is empty. Please be sure to fill-in the right data before proceeding.';
                $(obj).next('.yarpp_warning').html(msg).css('display','inline-block');
                e.preventDefault();
            }
        });
    });
*/
});