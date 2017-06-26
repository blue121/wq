$(function() {
    'use strict';
    var initImgSquare = function(page){
        var img_square1 = $('.img_square1', page);
        if (img_square1.length) {
            img_square1.css('height', img_square1.width());
        }
        var img_square2 = $('.img_square2', page);
        if (img_square2.length) {
            img_square2.css('height', img_square2.width());
        }
        var img_square3 = $('.img_square3', page);
        if (img_square3.length) {
            img_square3.css('height', img_square3.width());
        }
    };
    $(document).on('pageInit', '.page', function(e, id, page) {
        superman.log('[main] pageInit', 'info');
        $('.todo', page).click(function(){
            $.toast('开发中...');
            return false;
        });
        $('.search_button', page).click(function(){
            if ($(this).hasClass('disabled')) {
                return false;
            }
            $.showIndicator();
            $(this).addClass('disabled');
            var formid = $(this).attr('data-formid');
            $('#'+formid).submit();
        });
        if ($('.wechat_share_tips', page).length) {
            $('.wechat_share_tips', page).bind('click', function(){
                $.overlay.toggle('<img class="pull-right" src="'+window.sysinfo.mobile_path+'/images/sharer.png"/>', true);
            });
        }
        if ($('.redirect').length) {
            var href = $('.redirect').attr('href');
            if (href != '') {
                setTimeout(function(){
                    $.showIndicator();
                    window.location.href = href;
                }, 2000);
            }
        }
        //global hook
        if (window.sysinfo.member.uid && window.sysinfo.global_hook_url) {
            $.ajax({
                type: 'get',
                url: window.sysinfo.global_hook_url,
                success:function(){}
            });
        }
        if (window.sysinfo.lbs == 1) {
            wx.ready(function () {
                var latitude = $.fn.cookie(window.sysinfo.cookie.pre + 'latitude');
                console.log(latitude);
                var longitude = $.fn.cookie(window.sysinfo.cookie.pre + 'longitude');
                console.log(longitude);
                if (!latitude || !longitude || window.location.href.indexOf('&location=refresh')>0) {
                    console.log('true');
                    wx.getLocation({
                        type: 'wgs84', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
                        success: function (res) {
                            console.log(res);
                            /*var latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
                             var longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。
                             var speed = res.speed; // 速度，以米/每秒计
                             var accuracy = res.accuracy; // 位置精度
                             console.log(latitude+','+longitude+','+speed+','+accuracy);*/
                            $.fn.cookie(window.sysinfo.cookie.pre + 'latitude', res.latitude, {expire: 7});
                            $.fn.cookie(window.sysinfo.cookie.pre + 'longitude', res.longitude, {expire: 7});
                            window.location.href = window.location.href.replace('&location=get', '&getlocal=1').replace('&location=refresh', '&getlocal=1'); //页面加参数
                        }
                    });
                }/* else {
                 window.location.href = window.location.href.replace('&op=getlocation', ''); //页面加参数
                 }*/
            });
        }
        initImgSquare(page);
        $(page).on('infinite', '.infinite-scroll',function() {
            initImgSquare(page);
        });
        $('.content', page).on('scroll', function(){
            initImgSquare(page);
        });
        //返回顶部
        $('.content', page).on('scroll', function(){
            if ($(this).scrollTop() > 100) {
                var gotop = $('#gotop');
                gotop.show();
                if (gotop.attr('data-click') != '1') {
                    gotop.attr('data-click', '1').bind('click', function(){
                        $('.content', page).scrollTop(0);
                    });
                }
            } else {
                $('#gotop').hide();
            }
        });
    });
    $(document).on('pageReinit', '.page', function(e, id, page) {
        initImgSquare(page);
    });
    $.init();
});