$(function() {
    'use strict';
    $(document).on('pageInit', ".superpage_user", function(e, id, page) {
        superman.log('['+id+'] pageInit', 'info');
        $('.btn_submit', page).click(function () {
            if ($(this).hasClass('disabled')) {
                return false;
            }
            $.showIndicator();
            $(this).addClass('disabled');
            $('.user_post_form').submit();
        });
    });
});