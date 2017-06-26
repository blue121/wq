<?php defined('IN_IA') or exit('Access Denied');?><style>
    .star {
        color: red;
        margin-right: 5px;
        font-weight: bold;
    }
</style>
<?php  if($op == 'display') { ?>
<div style="margin-bottom: 10px;">
    <a href="<?php  echo $this->createWebUrl('user', array('act' => 'group', 'op' => 'post'))?>" class="btn btn-default">
        <i class="fa fa-plus"> 添加身份</i>
    </a>
</div>
<form action="" method="post">
    <div class="panel panel-default">
        <div class="table-responsive panel-body">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>名称</th>
                    <th width="90" class="text-right">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php  if($list) { ?>
                <?php  if(is_array($list)) { foreach($list as $li) { ?>
                <tr>
                    <td><?php  echo $li['title'];?></td>
                    <td style="white-space:nowrap;overflow: visible" class="text-right">
                        <div class="btn-group">
                            <a href="<?php  echo $this->createWebUrl('user', array('act' => 'group', 'op' => 'post', 'id' => $li['id']));?>" title="编辑" data-toggle="tooltip" data-placement="top" class="btn btn-default btn-sm"><i class="fa fa-edit"></i></a>
                            <a onclick="return confirm('此操作不可恢复，确认吗？'); return false;" href="<?php  echo $this->createWebUrl('user', array('act' => 'group', 'op' => 'delete', 'id' => $li['id']));?>" title="删除" data-toggle="tooltip" data-placement="top" class="btn btn-default btn-sm"><i class="fa fa-times"></i></a>
                        </div>
                    </td>
                </tr>
                <?php  } } ?>
                <?php  } ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php  echo $pager;?>
</form>
<?php  } else if($op == 'post') { ?>
<form class="form-horizontal form group_post_form" method="post">
    <div class="panel panel-default">
        <div class="panel-heading">
            <?php  if($_GPC['id']) { ?>编辑身份<?php  } else { ?>添加身份<?php  } ?>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label"><span class="star">*</span>名称</label>
                <div class="col-sm-8 col-md-8 col-xs-12">
                    <input class="form-control" name="title" type="text" value="<?php  echo $user_group['title'];?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">权限</label>
                <div class="col-sm-8 col-md-8 col-xs-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="checkbox">
                                <label><input type="checkbox" class="permission_item" <?php  if($user_group['permission'] == 'all') { ?>checked<?php  } ?>>功能模块</label>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-xs-2 checkbox">
                                    <label><input type="checkbox" name="permission[]" <?php  if($user_group['permission'] == 'all' || ($user_group['permission'] && in_array('superman_mall_menu_plugin_seckill', $user_group['permission']))) { ?>checked<?php  } ?> value="superman_mall_menu_plugin_seckill">秒杀</label>
                                </div>
                                <div class="col-xs-2 checkbox">
                                    <label><input type="checkbox" name="permission[]" <?php  if($user_group['permission'] == 'all' || ($user_group['permission'] && in_array('superman_mall_menu_plugin_mgroupon', $user_group['permission']))) { ?>checked<?php  } ?> value="superman_mall_menu_plugin_mgroupon">拼团</label>
                                </div>
                                <div class="col-xs-2 checkbox">
                                    <label><input type="checkbox" name="permission[]" <?php  if($user_group['permission'] == 'all' || ($user_group['permission'] && in_array('superman_mall_menu_discount', $user_group['permission']))) { ?>checked<?php  } ?> value="superman_mall_menu_discount">营销</label>
                                </div>
                                <div class="col-xs-2 checkbox">
                                    <label><input type="checkbox" name="permission[]" <?php  if($user_group['permission'] == 'all' || ($user_group['permission'] && in_array('superman_mall_menu_printer', $user_group['permission']))) { ?>checked<?php  } ?> value="superman_mall_menu_printer">打印机</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="checkbox">
                                <label><input type="checkbox" class="permission_item" <?php  if($user_group['permission'] == 'all') { ?>checked<?php  } ?>>账号管理</label>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-xs-2 checkbox">
                                    <label><input type="checkbox" name="permission[]" <?php  if($user_group['permission'] == 'all' || ($user_group['permission'] && in_array('superman_mall_menu_user_user', $user_group['permission']))) { ?>checked<?php  } ?> value="superman_mall_menu_user_user">账号</label>
                                </div>
                                <div class="col-xs-2 checkbox">
                                    <label><input type="checkbox" name="permission[]" <?php  if($user_group['permission'] == 'all' || ($user_group['permission'] && in_array('superman_mall_menu_user_group', $user_group['permission']))) { ?>checked<?php  } ?> value="superman_mall_menu_user_group">身份</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="checkbox">
                                <label><input type="checkbox" class="permission_item" <?php  if($user_group['permission'] == 'all') { ?>checked<?php  } ?>>商品管理</label>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-xs-2 checkbox">
                                    <label><input type="checkbox" name="permission[]" <?php  if($user_group['permission'] == 'all' || ($user_group['permission'] && in_array('superman_mall_menu_item_post', $user_group['permission']))) { ?>checked<?php  } ?> value="superman_mall_menu_item_post" >发布商品</label>
                                </div>
                                <div class="col-xs-2 checkbox">
                                    <label><input type="checkbox" name="permission[]" <?php  if($user_group['permission'] == 'all' || ($user_group['permission'] && in_array('superman_mall_menu_item_display', $user_group['permission']))) { ?>checked<?php  } ?> value="superman_mall_menu_item_display">管理商品</label>
                                </div>
                                <div class="col-xs-2 checkbox">
                                    <label><input type="checkbox" name="permission[]" <?php  if($user_group['permission'] == 'all' || ($user_group['permission'] && in_array('superman_mall_menu_item_spec', $user_group['permission']))) { ?>checked<?php  } ?> value="superman_mall_menu_item_spec">商品规格</label>
                                </div>
                                <div class="col-xs-2 checkbox">
                                    <label><input type="checkbox" name="permission[]" <?php  if($user_group['permission'] == 'all' || ($user_group['permission'] && in_array('superman_mall_menu_item_postage', $user_group['permission']))) { ?>checked<?php  } ?> value="superman_mall_menu_item_postage">邮费模板</label>
                                </div>
                                <div class="col-xs-2 checkbox">
                                    <label><input type="checkbox" name="permission[]" <?php  if($user_group['permission'] == 'all' || ($user_group['permission'] && in_array('superman_mall_menu_item_myfetch', $user_group['permission']))) { ?>checked<?php  } ?> value="superman_mall_menu_item_myfetch">自提门店</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="checkbox">
                                <label><input type="checkbox" class="permission_item"  <?php  if($user_group['permission'] == 'all') { ?>checked<?php  } ?>>订单管理</label>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-xs-2 checkbox">
                                    <label><input type="checkbox" name="permission[]" <?php  if($user_group['permission'] == 'all' || ($user_group['permission'] && in_array('superman_mall_menu_order_overview', $user_group['permission']))) { ?>checked<?php  } ?> value="superman_mall_menu_order_overview">订单概况</label>
                                </div>
                                <div class="col-xs-2 checkbox">
                                    <label><input type="checkbox" name="permission[]" <?php  if($user_group['permission'] == 'all' || ($user_group['permission'] && in_array('superman_mall_menu_order_display', $user_group['permission']))) { ?>checked<?php  } ?> value="superman_mall_menu_order_display">管理订单</label>
                                </div>
                                <div class="col-xs-2 checkbox">
                                    <label><input type="checkbox" name="permission[]" <?php  if($user_group['permission'] == 'all' || ($user_group['permission'] && in_array('superman_mall_menu_order_refund', $user_group['permission']))) { ?>checked<?php  } ?> value="superman_mall_menu_order_refund">售后管理</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="checkbox">
                                <label><input type="checkbox" class="permission_item" <?php  if($user_group['permission'] == 'all') { ?>checked<?php  } ?>>评价管理</label>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-xs-2 checkbox">
                                    <label><input type="checkbox" name="permission[]" <?php  if($user_group['permission'] == 'all' || ($user_group['permission'] && in_array('superman_mall_menu_comment_display', $user_group['permission']))) { ?>checked<?php  } ?> value="superman_mall_menu_comment_display">管理评价</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="checkbox">
                                <label><input type="checkbox" class="permission_item" <?php  if($user_group['permission'] == 'all') { ?>checked<?php  } ?>>线下核销</label>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-xs-2 checkbox">
                                    <label><input type="checkbox" name="permission[]" <?php  if($user_group['permission'] == 'all' || ($user_group['permission'] && in_array('superman_mall_menu_checkout_display', $user_group['permission']))) { ?>checked<?php  } ?> value="superman_mall_menu_checkout_display">核销记录</label>
                                </div>
                                <div class="col-xs-2 checkbox">
                                    <label><input type="checkbox" name="permission[]" <?php  if($user_group['permission'] == 'all' || ($user_group['permission'] && in_array('superman_mall_menu_checkout_qrcode', $user_group['permission']))) { ?>checked<?php  } ?> value="superman_mall_menu_checkout_qrcode">扫码核销</label>
                                </div>
                                <div class="col-xs-2 checkbox">
                                    <label><input type="checkbox" name="permission[]" <?php  if($user_group['permission'] == 'all' || ($user_group['permission'] && in_array('superman_mall_menu_checkout_oneself', $user_group['permission']))) { ?>checked<?php  } ?> value="superman_mall_menu_checkout_oneself">自助核销</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="checkbox">
                                <label><input type="checkbox" class="permission_item"  <?php  if($user_group['permission'] == 'all') { ?>checked<?php  } ?>>数据统计</label>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-xs-2 checkbox">
                                    <label><input type="checkbox" name="permission[]" <?php  if($user_group['permission'] == 'all' || ($user_group['permission'] && in_array('superman_mall_menu_stat', $user_group['permission']))) { ?>checked<?php  } ?> value="superman_mall_menu_stat">商品数据</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="checkbox">
                                <label><input type="checkbox" class="permission_item"  <?php  if($user_group['permission'] == 'all') { ?>checked<?php  } ?>>财务管理</label>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-xs-2 checkbox">
                                    <label><input type="checkbox" name="permission[]" <?php  if($user_group['permission'] == 'all' || ($user_group['permission'] && in_array('superman_mall_menu_finance_apply', $user_group['permission']))) { ?>checked<?php  } ?> value="superman_mall_menu_finance_apply">申请提现</label>
                                </div>
                                <div class="col-xs-2 checkbox">
                                    <label><input type="checkbox" name="permission[]" <?php  if($user_group['permission'] == 'all' || ($user_group['permission'] && in_array('superman_mall_menu_finance_log', $user_group['permission']))) { ?>checked<?php  } ?> value="superman_mall_menu_finance_log">提现记录</label>
                                </div>
                                <div class="col-xs-2 checkbox">
                                    <label><input type="checkbox" name="permission[]" <?php  if($user_group['permission'] == 'all' || ($user_group['permission'] && in_array('superman_mall_menu_finance_user', $user_group['permission']))) { ?>checked<?php  } ?> value="superman_mall_menu_finance_user">提现账户</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="checkbox">
                                <label><input type="checkbox" class="permission_item"  <?php  if($user_group['permission'] == 'all') { ?>checked<?php  } ?>>触发器规则</label>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-xs-2 checkbox">
                                    <label><input type="checkbox" name="permission[]" <?php  if($user_group['permission'] == 'all' || ($user_group['permission'] && in_array('superman_mall_menu_trigger_display', $user_group['permission']))) { ?>checked<?php  } ?> value="superman_mall_menu_trigger_display">规则列表</label>
                                </div>
                                <div class="col-xs-2 checkbox">
                                    <label><input type="checkbox" name="permission[]" <?php  if($user_group['permission'] == 'all' || ($user_group['permission'] && in_array('superman_mall_menu_trigger_post', $user_group['permission']))) { ?>checked<?php  } ?> value="superman_mall_menu_trigger_post">编辑规则</label>
                                </div>
                                <div class="col-xs-2 checkbox">
                                    <label><input type="checkbox" name="permission[]" <?php  if($user_group['permission'] == 'all' || ($user_group['permission'] && in_array('superman_mall_menu_trigger_delete', $user_group['permission']))) { ?>checked<?php  } ?> value="superman_mall_menu_trigger_delete">删除规则</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <input name="submit" type="submit" value="提交" class="btn btn-primary col-lg-1">
            <input type="hidden" name="token" value="<?php  echo $_W['token'];?>">
            <input type="hidden" name="all" value="0">
        </div>
    </div>
</form>
<script>
    require(['jquery'], function($){
        $('.permission_item').click(function(){
            if ($(this).prop('checked')) {
                $('input[type=checkbox]', $(this).parent().parent().parent().next()).prop('checked', true);
            } else {
                $('input[type=checkbox]', $(this).parent().parent().parent().next()).prop('checked', false);
            }
        });
        $('.group_post_form').submit(function(){
            var all_checked = 1;
            $('input[name="permission[]"]').each(function(){
                if (!$(this).prop('checked')) {
                    all_checked = 0;
                    return false;
                }
                all_checked = 1;
            });
            if (all_checked) {
                $('input[name=all]').val('1');
            } else {
                $('input[name=all]').val('0');
            }
            return true;
        });
        $('form').submit(function(){
            var title = $('input[name=title]');
            if (title.val() == '') {
                util.message('名称为空，请输入！', '', 'error');
                return false;
            }
            return true;
        });
    });
</script>
<?php  } ?>
