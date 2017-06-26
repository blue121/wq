$(function() {
    'use strict';
    $(document).on('pageInit', ".superpage_user", function(e, id, page) {
        superman.log('['+id+'] pageInit', 'info');
        function addItems(data) {
            var html = '', item;
            for (var i = 0; i < data.length; i++) {
                item = data[i];
                html += '<div class="list-block font7">';
                html += '<ul>';
                html += '<li class="item-content">';
                html += '<div class="item-inner">';
                html += '<div class="item-title">申请日期</div>';
                html += '<div class="item-after">'+item['create_date']+'</div>';
                html += '</div>';
                html += '</li>';
                html += '<li class="item-content">';
                html += '<div class="item-inner">';
                html += '<div class="item-title">提现金额</div>';
                html += '<div class="item-after">&yen;'+item['money']+'</div>';
                html += '</div>';
                html += '</li>';
                html += '<li class="item-content">';
                html += '<div class="item-inner">';
                html += '<div class="item-title">服务费</div>';
                html += '<div class="item-after">&yen;'+item['service_fee']+'</div>';
                html += '</div>';
                html += '</li>';
                html += '<li class="item-content">';
                html += '<div class="item-inner">';
                html += '<div class="item-title">到账金额</div>';
                html += '<div class="item-after">&yen;'+item['account_money']+'</div>';
                html += '</div>';
                html += '</li>';
                html += '<li class="item-content">';
                html += '<div class="item-inner">';
                html += '<div class="item-title">提现账户</div>';
                html += '<div class="item-after">'+item['account_type_title']+'</div>';
                html += '</div>';
                html += '</li>';
                html += '<li class="item-content">';
                html += '<div class="item-inner">';
                html += '<div class="item-title">状态</div>';
                html += '<div class="item-after">'+item['status_title']+'</div>';
                html += '</div>';
                html += '</li>';
                html += '</ul>';
                html += '</div>';
            }
            $('#log_list', page).append(html);
        }
        $(page).on('infinite', '.infinite-scroll',function() {
            var t = this;
            if ($(t).attr('data-flag') == '1') {
                return;
            }
            $(t).attr('data-flag', '1');
            var pageno = $(t).attr('data-page');
            pageno = parseInt(pageno) + 1;
            $.ajax({
                url: $(t).attr('data-url'),
                data: 'page='+pageno,
                dataType: 'json',
                success: function(response) {
                    $(t).attr('data-flag', '0');
                    if (response.length > 0) {
                        addItems(response);
                        $(t).attr('data-page', pageno);
                        $.refreshScroller();
                    } else {
                        $.detachInfiniteScroll($('.infinite-scroll', page));
                        $('.infinite-scroll-preloader', page).remove();
                        $('.nodata', page).show();
                    }
                }
            });
        });
    });
});