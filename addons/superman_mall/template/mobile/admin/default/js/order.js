$(function() {
    'use strict';
    $(document).on('pageInit', ".superpage_order", function(e, id, page) {
        superman.log('['+id+'] pageInit', 'info');
        //改价
        var initEvent = function () {
            $('.edit_price', page).unbind('click').click(function() {
                var url = window.location.href;
                var t = this;
                var data_url = $(t).attr('data-url');
                var price = $(t).attr('data-price');
                var id = $(t).attr('data-id');
                $.modal({
                    title:  '修改价格',
                    text: '原价：&yen;'+price+'<br>'+
                    '<input type="number" min="0.01" step="0.01" name="new_price" value="" placeholder="现价">',
                    buttons: [
                        {
                            text: '取消',
                        },
                        {
                            text: '确定',
                            onClick: function() {
                                var new_price = $('input[name=new_price]').val();
                                if (new_price <= 0) {
                                    $.toast('现价必须为大于0的价格');
                                    return false;
                                }
                                $.ajax({
                                    type: 'post',
                                    url: data_url,
                                    data: 'id='+id+'&value='+new_price+'&field=price',
                                    dataType: 'json',
                                    success: function (resp) {
                                        if (resp.errno == 0) {
                                            $.toast('改价成功');
                                            setTimeout(function(){
                                                $.showIndicator();
                                                window.location.href=url;
                                            }, 2000);
                                        } else {
                                            if (resp.errmsg) {
                                                $.toast(resp.errmsg);
                                            }
                                        }
                                    }
                                });
                            }
                        },
                    ]
                })
            });
            $('.close_order', page).unbind('click').click(function() {
                var url = window.location.href;
                var t = this;
                var data_url = $(t).attr('data-url');
                var id = $(t).attr('data-id');
                $.confirm('确定关闭？', function () {
                    $.ajax({
                        type: 'post',
                        url: data_url,
                        data: 'id='+id+'&value='+1+'&field=close',
                        dataType: 'json',
                        success: function (resp) {
                            if (resp.errno == 0) {
                                $.toast('关闭成功');
                                setTimeout(function(){
                                    $.showIndicator();
                                    window.location.href=url;
                                }, 2000);
                            } else {
                                if (resp.errmsg) {
                                    $.toast(resp.errmsg);
                                }
                            }
                        }
                    });
                });
            });
        }
        $('.open-more', page).click(function () {
            $.popup('.popup-more');
        });
        $('.btn_submit', page).click(function () {
            var delivery_type = $('input[name=delivery_type]').val();
            var express_no = $('input[name=express_no]', page).val();
            var express_company = $('select[name=express_company]').val();
            if (delivery_type == 1) {
                if (!express_company) {
                    $.toast('快递公司选择错误');
                    return false;
                }
                if (!express_no) {
                    $.toast('快递单号不能为空');
                    return false;
                }
            }
            if ($(this).hasClass('disabled')) {
                return false;
            }
            $.showIndicator();
            $(this).addClass('disabled');
            $('.order_send_form').submit();
        });
        //选择物流发货
        $('.no_delivery', page).click(function () {
            $('input[name=delivery_type]').val(0);
        });

        $('.has_delivery', page).click(function () {
            $('input[name=delivery_type]').val(1);
        });

        //订单无限加载
        if ($('.order_list', page).length) {
            var order_list = function(){
                //滚动加载
                function addItems(data, params) {
                    var html = '', item;
                    for (var i = 0; i < data.length; i++) {
                        item = data[i];
                        html += '<div class="list-block media-list order_list_wrap">';
                        html += '<ul class="order_list_ul">';
                        if (params.order_type == 1) {
                            if (item['type'] == 1 && item['mg_status'] == 1) {
                                html += '<div class="mgroupon_success">';
                                html += '<img src="'+window.sysinfo.mobile_path+'/images/mgroupon_success.png" onerror="this.src=\''+window.sysinfo.placeholder + '\'"/>';
                                html += '</div>';
                            }
                        }
                        html += '<li>';
                        html += '<a href="'+params.orderpost_url+'&id='+item['id']+'" class="item-content" data-no-cache="true">';
                        html += '<div class="item-inner">';
                        html += '<div class="item-title-row font8">';
                        html += '<div class="item-after">'+item['ordersn']+'</div>';
                        html += '<div class="font6 color-gray">'+item['status_title']+'</div>';
                        html += '</div>';
                        html += '</div>';
                        html += '</a>';
                        html += '</li>';
                        if (item['items'].length > 0) {
                            for (var j = 0; j < item['items'].length; j++) {
                                var items = item['items'][j];
                                    html += '<li>';
                                    html += '<a href="'+params.orderpost_url+'&id='+item['id']+'" class="item-content" data-no-cache="true">';
                                    html += '<div class="item-media">';
                                    html += '<img class="item_img" src="'+tomedia(items['cover'])+'" onerror="this.src=\''+window.sysinfo.placeholder + '\'"/>';
                                    html += '</div>';
                                    html += '<div class="item-inner">';
                                    html += '<div class="item-subtitle order_item_title">'+items['title']+'</div>';
                                    html += '<div class="item-title-row">';
                                    html += '<div class="item-title font6 color-gray">'+items['sku']+'</div>';
                                    html += '<div class="item-after">×'+items['total']+'</div>';
                                    html += '</div>';
                                    html += '</div>';
                                    html += '</a>';
                                    html += '</li>';
                            }
                        }
                        html += '<li>';
                        html += '<a href="'+params.orderpost_url+'&id='+item['id']+'" class="item-content" data-no-cache="true">';
                        html += '<div class="item-inner">';
                        html += '<div class="item-subtitle color_default">共'+item['total']+'件，合计&yen;'+item['price']+'</div>';
                        html += '</div>';
                        html += '</a>';
                        html += '</li>';
                        if (item['status'] == 0) {
                        html += '<li>';
                        html += '<div class="item-inner row no-gutter text-center">';
                        html += '<div class="col-50 edit_price" data-price="'+item['price']+'" data-id="'+item['id']+'" data-url="'+params.ordermodify_url+'">';
                        html += '<a href="javascript:;" data-no-cache="true" class="font8">';
                        html += '<span class="iconfont font8">&#xe625;</span> 改价';
                        html += '</a>';
                        html += '</div>';
                        html += '<div class="col-50 close_order" data-id="'+item['id']+'" data-url="'+params.ordermodify_url+'">';
                        html += '<a href="javascript:;" data-no-cache="true" class="font8">';
                        html += '<span class="iconfont font8">&#xe62b;</span> 关闭';
                        html += '</a>';
                        html += '</div>';
                        html += '</div>';
                        html += '</li>';
                        } else if (item['status'] == 1) {
                            if (item['type'] == 0 || item['type'] == 1 && item['mg_status'] == 1 ) {
                                html += '<li>';
                                html += '<div class="item-inner row no-gutter text-center">';
                                html += '<div class="col-100">';
                                html += '<a href="'+params.ordersend_url+'&id='+item['id']+'" data-no-cache="true" class="font8 send_btn">';
                                html += '<span class="iconfont font8">&#xe612;</span> 发货';
                                html += '</a>';
                                html += '</div>';
                                html += '</div>';
                                html += '</li>';
                            }
                        }
                        html += '</ul>';
                        html += '</div>';
                    }
                    $('.order_loop_wrap', page).append(html);
                    //异步加载后绑定点击事件
                    initEvent();
                }
                $(page).on('infinite', '.infinite-scroll', function () {
                    var t = this;
                    if ($(t).attr('data-flag') == '1') {
                        return;
                    }
                    $(t).attr('data-flag', '1');
                    var status = $(t).attr('data-status');
                    var order_type = $(t).attr('data-type');
                    var pay_type = $(t).attr('data-pay-type');
                    var dispatch_type = $(t).attr('data-dispatch-type');
                    var pageno = $(t).attr('data-page');
                    pageno = parseInt(pageno) + 1;
                    var data = 'page=' + pageno;
                    if (status != '') {
                        data += '&status='+status;
                    }
                    if (order_type != '') {
                        data += '&type='+order_type;
                    }
                    if (pay_type != '') {
                        data += '&pay_type='+pay_type;
                    }
                    if (dispatch_type != '') {
                        data += '&dispatch_type='+dispatch_type;
                    }
                    $.ajax({
                        url: $(t).attr('data-list-url'),
                        data: data,
                        dataType: 'json',
                        success: function (response) {
                            $(t).attr('data-flag', '0');
                            if (response.length > 0) {
                                var params = {
                                    order_type: $(t).attr('data-type'),
                                    orderpost_url: $(t).attr('data-orderpost-url'),
                                    ordermodify_url: $(t).attr('data-ordermodify-url'),
                                    ordersend_url: $(t).attr('data-ordersend-url'),
                                };

                                addItems(response, params);
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
            }();
        }
        initEvent();
    });
});