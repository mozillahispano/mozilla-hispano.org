<script type='text/javascript'>
    jQuery(function($){
        $(document.body).on('click', '#yarpp-optin-button', function(){
            $(this).closest('p').find('.button').attr('disabled',true);
            $('#yarpp-optin').attr('checked', true);
            $.ajax({
                type:'POST',
                url : ajaxurl,
                data: {
                    action: 'yarpp_optin_<?php echo $optinAction?>',
                    '_ajax_nonce': $('#yarpp_optin-nonce').val()
                },
                success: yarppRedirectAdmin
            });
        });
    });

    function yarppRedirectAdmin(resp){
        if (resp === 'ok'){
            window.location.href = 'options-general.php?page=yarpp';
        }
    }
</script>