$(function() {
    'use strict';
    $(document).on('pageInit', ".superpage_item", function(e, id, page) {
        superman.log('['+id+'] pageInit', 'info');
        $('.btn_submit', page).click(function () {
            if ($(this).hasClass('disabled')) {
                return false;
            }
            $.showIndicator();
            $(this).addClass('disabled');
            $('.item_post_form').submit();
        });
        //商品操作
        var initEvent = function () {
            //已售罄
            $('.stockout-actions', page).unbind('click').click(function() {
                var t = this;
                var itempost_url = $(t).attr('data-itempost-url');
                var item_id = $(t).attr('data-item-id');
                var setattr_url = $(t).attr('data-setattr-url');
                var buttons1 = [
                    {
                        text: '库存/售价',
                        onClick: function() {
                            $.router.load(itempost_url, true);
                            /*$.prompt('请输入库存', function (value) {
                             $.alert(value);
                             });*/
                        }
                    }
                ];
                var buttons2 = [
                    {
                        text: '取消',
                        bg: 'danger'
                    }
                ];
                var groups = [buttons1, buttons2];
                initActions(groups);
            });
            //出售中
            $('.upshelf-actions', page).unbind('click').click(function() {
                var t = this;
                var itempost_url = $(t).attr('data-itempost-url');
                var item_id = $(t).attr('data-item-id');
                var setattr_url = $(t).attr('data-setattr-url');
                var buttons1 = [
                    {
                        text: '库存/售价',
                        onClick: function() {
                            $.router.load(itempost_url, true);
                            /*$.prompt('请输入库存', function (value) {
                             $.alert(value);
                             });*/
                        }
                    },
                    {
                        text: '下架',
                        onClick: function() {
                            //$.alert(item_id);
                            $.confirm('确定下架？',
                                function () {
                                    $.ajax({
                                        type: 'post',
                                        url: setattr_url,
                                        data: 'id='+item_id+'&value='+0+'&field=status',
                                        dataType: 'json',
                                        success: function (resp) {
                                            if (resp.errno == 0) {
                                                $.toast('下架成功');
                                                setTimeout(function(){
                                                    $.showIndicator();
                                                    window.location.reload();
                                                }, 2000);
                                            } else {
                                                if (resp.errmsg) {
                                                    $.toast(resp.errmsg);
                                                }
                                            }
                                        }
                                    });
                                }
                            );
                        }
                    }
                ];
                var buttons2 = [
                    {
                        text: '取消',
                        bg: 'danger'
                    }
                ];
                var groups = [buttons1, buttons2];
                initActions(groups);
            });
            //仓库中
            $('.offshelf-actions', page).unbind('click').click(function() {
                var t = this;
                var itempost_url = $(t).attr('data-itempost-url');
                var item_id = $(t).attr('data-item-id');
                var setattr_url = $(t).attr('data-setattr-url');
                var buttons1 = [
                    {
                        text: '库存/售价',
                        onClick: function() {
                            $.router.load(itempost_url, true);
                            /*$.prompt('请输入库存', function (value) {
                             $.alert(value);
                             });*/
                        }
                    },
                    {
                        text: '上架',
                        onClick: function() {
                            //$.alert(item_id);
                            $.confirm('确定上架？',
                                function () {
                                    $.ajax({
                                        type: 'post',
                                        url: setattr_url,
                                        data: 'id='+item_id+'&value='+1+'&field=status',
                                        dataType: 'json',
                                        success: function (resp) {
                                            if (resp.errno == 0) {
                                                $.toast('上架成功');
                                                setTimeout(function(){
                                                    $.showIndicator();
                                                    window.location.reload();
                                                }, 2000);
                                            } else {
                                                if (resp.errmsg) {
                                                    $.toast(resp.errmsg);
                                                }
                                            }
                                        }
                                    });
                                }
                            );
                        }
                    }
                ];
                var buttons2 = [
                    {
                        text: '取消',
                        bg: 'danger'
                    }
                ];
                var groups = [buttons1, buttons2];
                initActions(groups);
            });
        }
        var initActions = function (groups) {
            $.actions(groups);
        }

        //商品无限加载
        if ($('.item_list', page).length) {
            var item_list = function(){
                //滚动加载
                function addItems(data, params) {
                    var html = '', item;
                    for (var i = 0; i < data.length; i++) {
                        item = data[i];
                        html += '<li class="item-content">';
                        html += '<div class="item-media">';
                        html += '<a href="'+params.item_url+'&itemid='+item['id']+'" data-no-cache="true" external>';
                        html += '<img class="item_img" src="'+tomedia(item['cover'])+'" onerror="this.src=\''+window.sysinfo.placeholder + '\'"/>';
                        html += '</a>';
                        html += '</div>';
                        html += '<div class="item-inner">';
                        html += '<a href="javascript:;" data-no-cache="true" external>';
                        html += '<div class="item-text">';
                        html += item['title'];
                        html += '</div>';
                        html += '<div class="item-subtitle text-strong color-danger font8">&yen;' + item['price'] + '</div>';
                        html += '</a>';
                        html += '<div class="item-title-row row">';
                        html += '<div class="col-33">';
                        html += '<a class="font6 color-gray" href="javascript:;" data-no-cache="true" external>';
                        html += '库存:' + item['total'];
                        html += '</a>';
                        html += '</div>';
                        html += '<div class="col-33">';
                        html += '<a class="font6 color-gray" href="javascript:;" data-no-cache="true" external>';
                        html += '销量:' + item['sales'];
                        html += '</a>';
                        html += '</div>';
                        html += '<div class="col-33">';
                        html += '<a class="button button-light button-small '+params.status+'-actions item_actions" data-item-id="'+item['id']+'" data-itempost-url="'+params.itempost_url+'&id='+item['id']+'" data-setattr-url="'+params.setattr_url+'">';
                        html += '<span class="font6">操作</span>';
                        html += '</a>';
                        html += '</div>';
                        html += '</div>';
                        html += '</div>';
                        html += '</li>';
                    }
                    $('.item_list_wrap .item_list_ul', page).append(html);
                    //异步加载后绑定点击事件
                    initEvent();
                }
                $(page).on('infinite', '.infinite-scroll', function () {
                    var t = this;
                    if ($(t).attr('data-flag') == '1') {
                        return;
                    }
                    $(t).attr('data-flag', '1');
                    var kw = $(t).attr('data-kw');
                    var status = $(t).attr('data-status');
                    var pageno = $(t).attr('data-page');
                    pageno = parseInt(pageno) + 1;
                    var data = 'page=' + pageno + '&status=' + status;
                    if (kw != '') {
                        data += '&keyword='+kw;
                    }
                    $.ajax({
                        url: $(t).attr('data-list-url'),
                        data: data,
                        dataType: 'json',
                        success: function (response) {
                            $(t).attr('data-flag', '0');
                            if (response.length > 0) {
                                var params = {
                                    item_url: $(t).attr('data-item-url'),
                                    status: $(t).attr('data-status'),
                                    itempost_url: $(t).attr('data-itempost-url'),
                                    setattr_url: $(t).attr('data-setattr-url')
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