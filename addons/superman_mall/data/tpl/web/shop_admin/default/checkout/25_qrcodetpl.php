<?php defined('IN_IA') or exit('Access Denied');?><?php  if($_GPC['op'] == 'post') { ?>
<div class="main">
    <form class="form-horizontal form" action="" method="post">
        <div class="panel panel-default">
            <div class="panel-heading">
                添加核销员
            </div>
            <div class="panel-body">
                <?php  if(!$user) { ?>
                <div class="form-group">
                    <div class="text-center">
                        <?php  $params = array('act' => 'checkout_user', 'shopid' => $this->shop['id'], 't' => TIMESTAMP);?>
                        <?php  $params['sign'] = SupermanUtil::get_sign($params, $_W['config']['setting']['authkey']);?>
                        <?php  $binding_url = $_W['siteroot'] . 'app/' . $this->createMobileUrl('openid', $params);?>
                        <img src="<?php  echo $this->createWebUrl('qrcode', array('content' => urlencode($binding_url)))?>" style="width: 300px;"/>
                        <p>请使用核销员手机微信扫一扫二维码，有效期5分钟</p>
                        <div id="check_info" style="display: none"></div>
                    </div>
                    <script>
                        setTimeout(function(){
                            window.location.reload();
                        }, 300000);
                    </script>
                </div>
                <script>
                    require(['jquery'], function($){
                        var checkUser = function(){
                            $.ajax({
                                type: 'get',
                                url: '<?php  echo $this->createWebUrl("checkout", array("act" => "qrcode", "op" => "check"))?>',
                                success: function(response) {
                                    if (response == 'yes') {
                                        var html = '<span class="fa fa-check-circle" style="color: #09BB07"></span> 扫描成功，页面即将刷新！';
                                        $('#check_info').html(html).fadeIn();
                                        setTimeout(function(){
                                            window.location.reload();
                                        }, 2000);
                                    } else {
                                        setTimeout(function(){
                                            checkUser();
                                        }, 1000);
                                    }
                                }
                            });
                        };
                        checkUser();
                    });
                </script>
                <?php  } else { ?>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 control-label">微信头像/昵称</label>
                    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-6">
                        <div class="pull-left" style="width: 40px;height: 40px; overflow: hidden; border-radius: 50%;">
                            <img src="<?php  echo $user['member']['avatar'];?>" onerror="this.src='<?php  echo $_W['siteroot'];?>/app/resource/images/heading.jpg'" style="width: 100%">
                        </div>
                        <div class="pull-left" style="line-height: 40px; margin-left: 5px; width: 90px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            <?php  if($user['member']['nickname']!='') { ?><?php  echo $user['member']['nickname'];?><?php  } else { ?>uid=<?php  echo $user['uid'];?><?php  } ?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">备注</label>
                    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-6">
                        <textarea class="form-control" name="remark"></textarea>
                        <input class="form-control" name="id" type="hidden" value="<?php  echo $user['id'];?>" readonly>
                        <span class="help-block"></span>
                    </div>
                </div>
                <?php  } ?>
            </div>
        </div>
        <div class="form-group col-sm-12">
            <input type="submit" class="btn btn-primary col-lg-1" name="submit" value="提交">
            <input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
        </div>
    </form>
</div>
<?php  } else { ?>
<style>
    .card_flow_box {
        border: 1px solid #e7e7eb;
        overflow: hidden;
    }
    .card_flow {
        position: relative;
        margin-bottom: 10px;
        overflow: hidden;
    }
    .card_flow .flow_item {
        position: relative;
        width: 49%;
        display: inline-block;
        vertical-align: top;
        text-align: center;
        box-sizing: border-box;
        padding: 0 45px;
    }
    .card_flow .flow_order {
        position: absolute;
        left: 10px;
        top: 0;
        font-family: Arial,Helvertica,sans-serif;
        line-height: 76px;
        font-style: italic;
        font-size: 120px;
        font-weight: bold;
        color: #eee;
        z-index: 1;
    }
    .card_flow .flow_title {
        position: relative;
        z-index: 2;
        margin-top: 54px;
        margin-bottom: 1em;
        font-size: 20px;
        font-weight: bold;
        text-align: left;
    }
    .card_flow .flow_content {
        min-height: 104px;
        text-align: left;
        color: #555;
    }
    .methods_exp_qrcode {
        padding: .5em .5em .5em 0;
        overflow: hidden;
    }
    .methods_exp_img {
        height: 90px;
        margin-right: .5em;
        float: left;
    }
    .methods_exp_qr_txt {
        margin-top: 3em;
    }
    .methods_exp_qr_txt p {
        margin-bottom: 0;
    }
    .card_flow .flow_item:after {
        position: absolute;
        width: 1px;
        border-right: 1px solid #e7e7eb;
        right: 0;
        top: 70px;
        height: 124px;
        content: " ";
    }
    .card_flow .last-child:after {
        border: 0;
    }
</style>
<div class="card_flow_box">
    <div class="card_flow">
        <div class="flow_item">
            <span class="flow_order">1</span>
            <h2 class="flow_title">店员关注“<?php  echo $_W['uniaccount']['name'];?>”公众号</h2>
            <div class="flow_content">
                <p>店员须关注“<?php  echo $_W['uniaccount']['name'];?>”公众号，才能配置核销权限。</p>
                <div class="methods_exp_qrcode">
                    <img class="methods_exp_img" src="<?php  echo tomedia('qrcode_'.$_W['acid'].'.jpg');?>?time=<?php echo TIMESTAMP;?>">
                    <div class="methods_exp_qr_txt">
                        <p>扫描二维码</p>
                        <p>关注“<?php  echo $_W['uniaccount']['name'];?>”</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="flow_item last-child">
            <span class="flow_order">2</span>
            <h2 class="flow_title">添加核销权限</h2>
            <div class="flow_content">
                <p>管理员须在本页面为店员添加核销权限，添加了核销权限的核销员可以在“<?php  echo $_W['uniaccount']['name'];?>”公众号内进行订单核销。</p>
                <p>打开手机微信，进入公众号超级商城=》我的=》扫码核销</p>
            </div>
        </div>
    </div>
</div>
<a href="<?php  echo $this->createWebUrl('checkout', array('act' => 'qrcode', 'op' => 'post'));?>" type="button" class="btn btn-default" style="margin-top: 1rem">
    <span class="fa fa-plus"></span>
    添加核销员
</a>
<div class="panel panel-default" style="margin-top: 1rem">
    <div class="table-responsive panel-body">
        <table class="table table-hover">
            <thead>
            <tr>
                <th width="160">核销员</th>
                <th>备注</th>
                <th width="100" class="text-right">操作</th>
            </tr>
            </thead>
            <tbody>
            <?php  if($list) { ?>
            <?php  if(is_array($list)) { foreach($list as $li) { ?>
            <tr>
                <td>
                    <div class="clear">
                        <div class="pull-left" style="width: 40px;height: 40px; overflow: hidden; border-radius: 50%;">
                            <img src="<?php  echo $li['avatar'];?>" onerror="this.src='<?php  echo $_W['siteroot'];?>/app/resource/images/heading.jpg'" style="width: 100%">
                        </div>
                        <div class="pull-left" style="line-height: 40px; margin-left: 5px; width: 90px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php  echo $li['nickname'];?>">
                            <?php  echo $li['nickname'];?>
                        </div>
                    </div>
                </td>
                <td style="white-space: normal;">
                    <?php  echo $li['remark'];?>
                </td>
                <td class="text-right">
                    <a onclick="return confirm('此操作不可恢复，确认吗？'); return false;" href="<?php  echo $this->createWebUrl('checkout' ,array('act' => 'qrcode', 'op' => 'delete', 'id' => $li['id']))?>" class="btn btn-default btn-danger btn_cancel">取消权限</a>
                </td>
            </tr>
            <?php  } } ?>
            <?php  } ?>
            </tbody>
        </table>
    </div>
</div>
<?php  echo $pager;?>
<?php  } ?>