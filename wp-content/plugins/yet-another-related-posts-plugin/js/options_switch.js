jQuery(document).ready(function($){
    $('#yarpp_switch_button').click(function(e){
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
});