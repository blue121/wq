<?php defined('IN_IA') or exit('Access Denied');?><style>
    .star {
        color: red;
        margin-right: 5px;
        font-weight: bold;
    }
</style>
<?php  if($op == 'display') { ?>
<div style="margin-bottom: 10px;">
    <a href="<?php  echo $this->createWebUrl('user', array('act' => 'user', 'op' => 'post'))?>" class="btn btn-default">
        <i class="fa fa-plus"> 添加账号</i>
    </a>
</div>
<form action="" method="post">
    <div class="panel panel-default">
        <div class="table-responsive panel-body">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th width="100">账号</th>
                    <th>商户名称</th>
                    <th width="160">绑定微信账号</th>
                    <th width="130">身份</th>
                    <th width="80">状态</th>
                    <th width="190">有效期</th>
                    <th width="150">注册时间</th>
                    <th width="90" class="text-right">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php  if($list) { ?>
                <?php  if(is_array($list)) { foreach($list as $li) { ?>
                <tr>
                    <td><?php  echo $li['username'];?></td>
                    <td><?php  echo $li['shop']['title'];?></td>
                    <td>
                        <?php  if($li['openid']) { ?>
                        <div style="height: 50px; padding-top: 6px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;">
                            <img src="<?php  echo tomedia($li['member']['avatar'])?>" onerror="this.src='<?php  echo $_W['siteroot'];?>/app/resource/images/heading.jpg'" style="width: 40px; height: 40px; border-radius: 50%; margin-left: .5rem; overflow: hidden" alt=""/>
                            <span style="padding-left: .3rem;"><?php  echo $li['member']['nickname'];?></span>
                        </div>
                        <?php  } ?>
                    </td>
                    <td><?php  echo $li['shop_user_group']['title'];?></td>
                    <td>
                        <?php  if($li['status']==0) { ?>
                        <span class="label label-default">待审核</span>
                        <?php  } else if($li['status']==1) { ?>
                        <span class="label label-success">正常</span>
                        <?php  } else if($li['status']==2) { ?>
                        <span class="label label-danger">禁用</span>
                        <?php  } ?>
                    </td>
                    <td>
                        <span <?php  if($li['starttime'] > TIMESTAMP ) { ?>style="font-weight: bold;"<?php  } ?>><?php  echo date('Y-m-d', $li['starttime'])?></span> ~
                        <span <?php  if($li['expiretime'] > 0 && $li['expiretime'] < TIMESTAMP) { ?>style="color: red;"<?php  } ?>><?php echo $li['expiretime'] > 0?date('Y-m-d', $li['expiretime']):永久?></span>
                    </td>
                    <td><?php  if($li['dateline']) { ?><?php  echo date('Y-m-d H:i:s', $li['dateline'])?><?php  } ?></td>
                    <td style="white-space:nowrap;overflow: visible" class="text-right">
                        <div class="btn-group">
                            <a href="<?php  echo $this->createWebUrl('user', array('act' => 'user', 'op' => 'post', 'id' => $li['id']));?>" title="编辑" data-toggle="tooltip" data-placement="top" class="btn btn-default btn-sm"><i class="fa fa-edit"></i></a>
                            <?php  if($li['groupid'] != 0) { ?>
                            <a onclick="return confirm('此操作不可恢复，确认吗？'); return false;" href="<?php  echo $this->createWebUrl('user', array('act' => 'user', 'op' => 'delete', 'id' => $li['id']));?>" title="删除" data-toggle="tooltip" data-placement="top" class="btn btn-default btn-sm"><i class="fa fa-times"></i></a>
                            <?php  } ?>
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
<form class="form-horizontal form" method="post">
    <div class="panel panel-default">
        <div class="panel-heading">
            <?php  if($id) { ?>编辑账号<?php  } else { ?>添加账号<?php  } ?>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label"><span class="star">*</span>账号</label>
                <div class="col-sm-8 col-md-8 col-xs-12">
                    <input class="form-control" name="username" type="text" value="<?php  echo $user['username'];?>">
                    <span class="help-block">由4-16位字母、数字、下划线组成</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label"><span class="star">*</span>新密码</label>
                <div class="col-sm-8 col-md-8 col-xs-12">
                    <input class="form-control" name="password" type="password">
                    <span class="help-block">6-16位密码（大小写英文字母、数字以及下划线） <?php  if($id) { ?><span style="color: red">不修改密码，请留空</span><?php  } ?></span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label"><span class="star">*</span>确认密码</label>
                <div class="col-sm-8 col-md-8 col-xs-12">
                    <input class="form-control" name="repassword" type="password">
                </div>
            </div>
            <?php  if($this->shop_user['groupid'] == '0') { ?>
            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">手机号</label>
                <div class="col-sm-8 col-md-8 col-xs-12">
                    <input class="form-control" name="mobile" type="text" value="<?php  echo $user['mobile'];?>">
                    <span class="help-block">管理员可直接修改手机号</span>
                </div>
            </div>
            <?php  } ?>
            <?php  if(!$id || (isset($user['groupid']) && $user['groupid']!=0)) { ?>
            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label"><span class="star">*</span>身份</label>
                <div class="col-sm-8 col-md-8 col-xs-12">
                    <select class="form-control" name="groupid">
                        <?php  if($user_groups) { ?>
                        <?php  if(is_array($user_groups)) { foreach($user_groups as $li) { ?>
                        <option value="<?php  echo $li['id'];?>" <?php  if($user['groupid']==$li['id']) { ?>selected<?php  } ?>><?php  echo $li['title'];?></option>
                        <?php  } } ?>
                        <?php  } ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">状态</label>
                <div class="col-sm-8 col-md-8 col-xs-12">
                    <select class="form-control" name="status">
                        <!--<option value="0" <?php  if($user['status']==0) { ?>selected<?php  } ?>>待审核</option>-->
                        <option value="1" <?php  if($user['status']==1) { ?>selected<?php  } ?>>正常</option>
                        <option value="2" <?php  if($user['status']==2) { ?>selected<?php  } ?>>禁用</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">有效期</label>
                <div class="col-sm-8 col-md-8 col-xs-12">
                    <label style="font-weight: normal">
                        <input name="usetime_limit" type="checkbox" <?php  if(isset($user['expiretime']) && $user['expiretime'] == 0) { ?>checked<?php  } ?>> 永久
                    </label>
                    <div id="usetime_wrap" <?php  if(isset($user['expiretime']) && $user['expiretime'] == 0) { ?>style="display:none"<?php  } ?>>
                    <?php  echo tpl_form_field_daterange('usetime', $usetime);?>
                </div>
					<span class="help-block">
						<?php  if(isset($user['expiretime']) && $user['expiretime'] > 0 && $user['expiretime'] < TIMESTAMP) { ?>
						<span style="color: red">已过期</span>
						<?php  } ?>
					</span>
            </div>
        </div>
        <?php  } ?>
        <?php  if($id) { ?>
        <div class="form-group">
            <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">上次登录时间</label>
            <div class="col-sm-8 col-md-8 col-xs-12">
                <span style="line-height: 30px;"><?php  if($user['lastvisit']) { ?><?php  echo date('Y-m-d H:i:s', $user['lastvisit'])?><?php  } ?></span>
                <span class="help-block"></span>
            </div>
        </div>
        <div class="form-group">
            <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">上次登录IP</label>
            <div class="col-sm-8 col-md-8 col-xs-12">
					<span style="line-height: 30px;">
						<?php  if($user['lastip']) { ?><?php  echo $user['lastip'];?>  <a href="http://www.ip138.com/ips138.asp?ip=<?php  echo $user['lastip'];?>" target="_blank">查看IP地理位置</a><?php  } ?>
					</span>
                <span class="help-block"></span>
            </div>
        </div>
        <div class="form-group">
            <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">注册时间</label>
            <div class="col-sm-8 col-md-8 col-xs-12">
                <span style="line-height: 30px;"><?php  if($user['dateline']) { ?><?php  echo date('Y-m-d H:i:s', $user['dateline'])?><?php  } ?></span>
                <span class="help-block"></span>
            </div>
        </div>
        <?php  } ?>
        <div class="form-group">
            <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">备注</label>
            <div class="col-sm-8 col-md-8 col-xs-12">
                <textarea name="remark" class="form-control"><?php  echo $user['remark'];?></textarea>
                <span class="help-block"></span>
            </div>
        </div>
        <?php  if($shop_setting && $shop_setting['international']) { ?>
        <div class="form-group">
            <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">国家地区</label>
            <div class="col-sm-6 col-md-8 col-xs-12">
                <select class="form-control" name="countryid">
                    <?php  if(is_array($countrys)) { foreach($countrys as $li) { ?>
                    <option value="<?php  echo $li['id'];?>" <?php  if($user['countryid']==$li['id']) { ?>selected<?php  } ?>><?php  echo $li['title'];?> （+<?php  echo $li['areacode'];?>）</option>
                    <?php  } } ?>
                </select>
                <span class="help-block"></span>
            </div>
        </div>
        <?php  } ?>
    </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <input name="submit" type="submit" value="提交" class="btn btn-primary col-lg-1">
            <input type="hidden" name="token" value="<?php  echo $_W['token'];?>">
        </div>
    </div>
</form>
<script>
    require(['jquery'], function($){
        //有效期
        $('input[name=usetime_limit]').change(function(){
            if ($(this).prop('checked')) {
                $('#usetime_wrap').fadeOut();
            } else {
                $('#usetime_wrap').fadeIn();
            }
        });
        //表单验证
        $('form').submit(function(){
            var username = $('input[name=username]');
            if (username.val() == '') {
                util.message('账号名为空，请重新输入！', '', 'error');
                return false;
            }
            var reg = /^[a-z\d_]{4,16}$/i;
            var re = new RegExp(reg);
            if (!re.test(username.val())) {
                util.message('账号名不合法，请重新输入！', '', 'error');
                return false;
            }
            var password = $('input[name=password]');
            var repassword = $('input[name=repassword]');
            if (password.val() != '') {
                re = new RegExp(/^\w{6,16}$/i);
                if (!re.test(password.val())) {
                    util.message('密码不合法，请重新输入！', '', 'error');
                    return false;
                }
                if (password.val() != repassword.val()) {
                    util.message('两次输入的密码不一致，请重新输入！', '', 'error');
                    return false;
                }
            }
            var mobile = $('input[name=mobile]');
            if (mobile.val() != '') {
                if (mobile.val().search(/^([0-9]{11})?$/) == -1) {
                    util.message('手机号不合法，请重新输入！', '', 'error');
                    return false;
                }
            }
            return true;
        });
    });
</script>
<?php  } ?>