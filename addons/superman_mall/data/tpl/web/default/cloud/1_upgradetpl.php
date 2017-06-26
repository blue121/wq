<?php defined('IN_IA') or exit('Access Denied');?><?php  if($op == 'upgrade_check') { ?>
<div class="main">
    <div class="panel panel-default">
        <div class="panel-heading">更新信息</div>
        <div class="panel-body">
            <div id="loading_tips">
                <img src="<?php echo MODULE_URL;?>template/web/default/images/loading.gif"/> 正在加载...
            </div>
            <style>
                .text-gray {
                    color: #d2d6de;
                }
                .bg-olive {
                    background-color: #3d9970 !important;
                    color: #fff !important;
                }
                .text-muted {
                    color: #777;
                }
                .btn-flat {
                    border-radius: 0;
                    -webkit-box-shadow: none;
                    -moz-box-shadow: none;
                    box-shadow: none;
                    border-width: 1px;
                }
                dt,dd {
                    line-height: 30px;
                }
                .license_item label {
                    font-weight: 400;
                    margin-bottom: 0;
                    cursor: pointer;
                }
                .upgrade_file {
                    font-style: italic;
                }
            </style>
            <form method="post">
                <input type="hidden" name="check_upgrade" value="no">
                <input type="hidden" name="token" value="<?php  echo $_W['token'];?>">
                <div class="upgrade_info">
                    <dl class="dl-horizontal"></dl>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    require(['jquery', 'util'], function($, util){
        var initEvent = function () {
            $('button[type=submit]').unbind('click').click(function(){
                var checkUpgrade = $(this).attr('data-check-upgrade');
                $('input[name=check_upgrade]').val(checkUpgrade);
            });
            $('form').unbind('submit').submit(function () {
                if ($('input[name=check_upgrade]').val() != 'yes') {
                    var checked = true;
                    $('.lisence').each(function () {
                        if (!$(this).prop('checked')) {
                            checked = false;
                            return;
                        }
                    });
                    if (!checked) {
                        util.message('抱歉，更新前请仔细阅读更新协议！', '', 'warning');
                        return false;
                    }
                }
                return true;
            });
        };
        var loadUpgradeData = function (resp) {
            var html = '';
            var data = resp.data;
            if (resp.errno == 200) {
                html += '<dl class="dl-horizontal">';
                html += '<dd>';
                html += '<button type="submit" name="submit" class="btn btn-flat bg-olive" data-check-upgrade="yes" value="yes">立即检查新版本</button>';
                html += '<p class="text-gray">当前系统 <span class="text-muted">v'+data.from.version+'-'+data.from.release_time+'</span> ';
                html += '未检测到有新版本, 你可以点击立即检查新版本按钮检测新版本</p>';
                html += '</dd>';
                html += '</dl>';
                $('.upgrade_info').html(html).show();
                initEvent();
            } else if (resp.errno == 0) {
                html += '<input type="hidden" name="filekey" value="'+data.filekey+'">';
                //html += '<input type="hidden" name="first_file" value="'+data.firstFile+'">';
                html += '<dl class="dl-horizontal">';
                html += '<dt>版本</dt>';
                html += '<dd>当前版本：v'+data.from.version+'-'+data.from.release_time+'</dd>';
                html += '<dd>新的版本：v'+data.to.version+'-'+data.to.release_time+'</dd>';
                /*if (data.db) {
                    html += '<dt>数据库</dt>';
                    html += '<dd>'+data.db+'</dd>';
                }*/
                html += '<dt>更新文件</dt>';
                html += '<dd>';
                html += '<small class="text-muted">';
                html += '文件数：'+data.files.length+'个';
                html += '</small>';
                html += '</dd>';
                for (var i=0; i<data.files.length; i++) {
                    var file = data.files[i];
                    html += '<dd class="upgrade_file">'+file['status']+' '+file['path']+'</dd>';
                }
                html += '<dt>更新协议</dt>';
                html += '<dd>';
                html += '<div class="license_item">';
                html += '<label><input type="checkbox" class="minimal-red lisence"> 我已经做好了相关文件的备份工作</label>';
                html += '</div>';
                html += '<div class="license_item">';
                html += '<label><input type="checkbox" class="minimal-red lisence"> 认同官方的更新行为并自愿承担更新所存在的风险</label>';
                html += '</div>';
                html += '<div class="license_item">';
                html += '<label><input type="checkbox" class="minimal-red lisence"> 理解官方的辛勤劳动并报以感恩的心态点击更新按钮</label>';
                html += '</div>';
                if ('<?php  echo defined("SUPERMAN_DEVELOPMENT")?>') {
                    html += '<div class="license_item">';
                    html += '<label style="color:#f00"><input type="checkbox" name="force_upgrade" value="1" class="minimal-red"> 本地开发环境强制更新，请确认已备份代码！</label>';
                    html += '</div>';
                }
                html += '<br>';
                html += '<dt>&nbsp;</dt>';
                html += '<dd>';
                html += '<button type="submit" name="submit" class="btn btn-flat bg-olive" data-check-upgrade="no" value="yes">立即更新</button>';
                html += '</dd>';
                html += '</dl>';
                $('.upgrade_info').html(html).show();
                initEvent();
            } else {
                util.message(resp.errno+': '+resp.errmsg, '', 'error');
                return;
            }
        };
        $.ajax({
            type: 'post',
            url: '<?php  echo $upgrade_url;?>&callback=?',
            dataType: 'jsonp',
            timeout: 5000,
            complete: function () {
                $('#loading_tips').hide();
            },
            success:function (resp) {
                loadUpgradeData(resp);
            },
            error: function (xhr, errmsg, exception) {
                if (xhr.statusText == 'timeout') {
                    util.message('网络请求超时或服务器未响应，请刷新重试！');
                } else {
                    util.message(errmsg +'<br>'+ exception, '', 'error');
                }
            }
        });
        initEvent();
    });
</script>
<?php  } else if($op == 'upgrade_download') { ?>
<style>
    .progress {
        width: 230px;
        margin: 0 auto;
    }
    .redirect_url {
        font-size: 12px;
        margin: 10px 0;
        display: inline-block;
    }
</style>
<div class="main">
    <div class="panel panel-default">
        <div class="panel panel-body text-center">
            <h4>更新中……</h4>
            <div class="progress">
                <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="<?php  echo $progress;?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php  echo $progress;?>%; min-width: 30px;">
                    <span><?php  echo $current;?>/<?php  echo $count;?></span>
                    <!--<span><?php  echo $progress;?>%</span>-->
                </div>
            </div>
            <a href="<?php  echo $redirect_url;?>" class="redirect_url">如果您的浏览器没有自动跳转，请点击这里</a>
        </div>
    </div>
</div>
<script>
    if ('<?php  echo $redirect_url;?>' != '') {
        setTimeout(function(){
            window.location.href = '<?php  echo $redirect_url;?>';
        }, 200);
    }
</script>
<?php  } ?>