<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('header', TEMPLATE_INCLUDEPATH)) : (include template('header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/shop-nav', TEMPLATE_INCLUDEPATH)) : (include template('common/shop-nav', TEMPLATE_INCLUDEPATH));?>
<div class="alert alert-info">
	此页面触发器为【商户触发器】规则设置&nbsp;&nbsp;<a href="<?php  echo $this->createWebUrl('trigger', array('act' => 'display', 'isplatform' => '1'));?>">平台触发器</a>
</div>
<ul class="nav nav-tabs">
	<li <?php  if($act == 'display') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('trigger', array('act' => 'display', 'isshop' => '1'));?>">规则列表</a></li>
	<li <?php  if($act == 'post' && $id <= 0) { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('trigger', array('act' => 'post', 'isshop' => '1'));?>">添加规则</a></li>
	<?php  if($id > 0) { ?><li <?php  if($act == 'post') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('trigger', array('act' => 'post', 'id' => $id, 'isshop' => '1'));?>">编辑规则</a></li><?php  } ?>
</ul>
<?php  if($act == 'display') { ?>
<div class="panel panel-info">
	<div class="panel-heading">筛选</div>
	<div class="panel-body">
		<form action="" method="post" class="form-horizontal" role="form">
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 control-label">标题</label>
				<div class="col-sm-8 col-md-8 col-lg-8 col-xs-12">
					<input class="form-control" name="title" type="text" value="<?php  echo $_GPC['title'];?>" placeholder="支持模糊搜索">
				</div>
				<div class="pull-right col-xs-12 col-sm-2 col-md-2 col-lg-2">
					<button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
				</div>
			</div>
		</form>
	</div>
</div>
<form action="" method="post">
	<div class="panel panel-default">
		<div class="table-responsive panel-body">
			<table class="table table-hover">
				<thead>
				<tr>
					<th width="200">商户名称</th>
					<th>标题</th>
					<th width="150">状态</th>
					<th width="150">动作</th>
					<th width="240">通知</th>
					<th width="160">创建时间</th>
					<th width="90" class="text-right">操作</th>
				</tr>
				</thead>
				<tbody>
				<?php  if($list) { ?>
				<?php  if(is_array($list)) { foreach($list as $li) { ?>
				<tr>
					<td><?php  echo $li['shop']['title'];?></td>
					<td><?php  echo $li['title'];?></td>
					<td>
						<?php  if($li['status'] == 1) { ?>
						<span class="label label-success">已开启</span>
						<?php  } else { ?>
						<span class="label label-danger">已关闭</span>
						<?php  } ?>
					</td>
					<td><?php  echo $li['action_title'];?></td>
					<td>
						<?php  if(is_array($li['notices'])) { foreach($li['notices'] as $v) { ?>
						<?php  if($v['type'] == 1) { ?>
						<div style="height: 50px; line-height: 50px;">
							微信<img src="<?php  echo $v['avatar'];?>" onerror="this.src='<?php  echo $_W['siteroot'];?>/app/resource/images/heading.jpg'" style="width: 40px; height: 40px; border-radius: 50%; margin-left: .5rem; overflow: hidden" alt=""/>
							<span style="padding-left: .3rem"><?php  echo $v['nickname'];?></span>
						</div>
						<?php  } else if($v['type'] == 2) { ?>
						<div style="height: 30px; line-height: 30px;">
							短信<span style="padding-left: .5rem"><?php  echo $v['receiver'];?></span>
						</div>
						<?php  } ?>
						<?php  } } ?>
					</td>
					<td><?php  echo str_replace(' ', '<span style="padding-right: .8rem"></span>', date('Y-m-d H:i:s', $li['dateline']))?></td>
					<td style="white-space:nowrap;overflow: visible" class="text-right">
						<div class="btn-group">
							<a href="<?php  echo $this->createWebUrl('trigger', array('act' => 'post', 'id' => $li['id'], 'isshop' => '1'))?>" title="编辑" class="btn btn-default btn-sm"><i class="fa fa-edit"></i></a>
							<a href="<?php  echo $this->createWebUrl('trigger', array('act' => 'delete', 'id' => $li['id'], 'isshop' => '1'))?>" onclick="return confirm('此操作不可恢复，确认吗？'); return false;" title="删除" class="btn btn-default btn-sm"><i class="fa fa-times"></i></a>
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
<?php  } else if($act == 'post') { ?>
<form class="form-horizontal form" method="post">
	<div class="panel panel-default">
		<div class="panel-heading">
			<?php  if($id) { ?>编辑规则<?php  } else { ?>添加规则<?php  } ?>
		</div>
		<div class="panel-body">
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">状态</label>
				<div class="col-sm-6 col-md-8 col-xs-12">
					<div class="input-group">
						<label class="radio-inline">
							<input type="radio" name="status" value="1" <?php  if($item['status'] == 1) { ?>checked<?php  } ?>> 开启
						</label>
						<label class="radio-inline">
							<input type="radio" name="status" value="0" <?php  if($item['status'] == 0) { ?>checked<?php  } ?>> 关闭
						</label>
					</div>
					<span class="help-block"></span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">标题</label>
				<div class="col-sm-8 col-md-8 col-xs-12">
					<input class="form-control" name="title" type="text" value="<?php  echo $item['title'];?>">
					<span class="help-block"></span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">动作</label>
				<div class="col-sm-8 col-md-8 col-xs-12">
					<select class="form-control" name="action">
						<option value="">请选择</option>
						<?php  if(SupermanTrigger::$shop_actions) { ?>
						<?php  if(is_array(SupermanTrigger::$shop_actions)) { foreach(SupermanTrigger::$shop_actions as $k => $v) { ?>
						<option value="<?php  echo $k;?>" <?php  if($k == $item['action']) { ?>selected<?php  } ?>><?php  echo $v['title'];?></option>
						<?php  } } ?>
						<?php  } ?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-12 col-md-12 col-lg-2 control-label">通知</label>
				<div class="col-sm-9 col-md-9 col-xs-12" id="list_wrap">
					<?php  if(isset($item['notices']) && $item['notices']) { ?>
					<?php  if(is_array($item['notices'])) { foreach($item['notices'] as $notice) { ?>
					<div class="row notice" style="margin-bottom: 1rem;">
						<input type="hidden" name="nid[]" value="<?php  echo $notice['id'];?>">
						<div class="col-sm-11 col-md-11 col-xs-12" style="padding-left: 0; padding-right: 3rem; margin-bottom: 1rem">
							<select class="form-control" name="type[]">
								<option value="">请选择</option>
								<option value="1" <?php  if($notice['type'] == 1) { ?>selected<?php  } ?>>微信</option>
								<option value="2" <?php  if($notice['type'] == 2) { ?>selected<?php  } ?>>短信</option>
							</select>
						</div>
						<div class="col-sm-1 col-md-1 col-xs-1" style="padding: 0">
							<a href="javascript:;" data-toggle="tooltip" class="btn btn-danger" onclick="deleteNotice(this)" title="删除" data-id="<?php  echo $notice['id'];?>">
								<span class="glyphicon glyphicon-remove" aria-hidden="true"></span> 删除
							</a>
						</div>
						<div class="wrap_1 notice_wrap" <?php  if($notice['type'] == 2) { ?>style="display: none"<?php  } ?>>
							<div class="col-sm-11 col-md-11 col-xs-12" style="padding-left: 0; padding-right: 3rem; margin-bottom: 1rem">
								<select class="form-control" name="openid[]">
									<option value="">请选择账号</option>
									<?php  if($user_list) { ?>
									<?php  if(is_array($user_list)) { foreach($user_list as $user) { ?>
									<option <?php  if($user['openid'] == $notice['receiver']) { ?>selected<?php  } ?> <?php  if($user['openid']) { ?>value="<?php  echo $user['openid'];?>"<?php  } else { ?>disabled<?php  } ?>>
										<?php  echo $user['username'];?><?php  if($user['openid'] == '') { ?>--未绑定微信<?php  } ?>
									</option>
									<?php  } } ?>
									<?php  } ?>
								</select>
							</div>
						</div>
						<div class="wrap_2 notice_wrap" <?php  if($notice['type'] == 1) { ?>style="display: none"<?php  } ?>>
							<div class="col-sm-11 col-md-11 col-xs-12" style="padding-left: 0; padding-right: 3rem; margin-bottom: 1rem">
								<input class="form-control" name="mobile[]" type="text" <?php  if($notice['type'] == 2) { ?>value="<?php  echo $notice['receiver'];?>"<?php  } ?> placeholder="手机号">
								<?php  if(isset($sms['setting']['shop_trigger']['switch']) && $sms['setting']['shop_trigger']['switch'] == 1 && $sms['setting']['shop_trigger']['provider'] == 'heysky') { ?>
								<span class="help-block">本平台触发器短信配置为国际短信，手机号前需加国际区号，如中国区号为86，则中国手机号为86138643XXXXX</span>
								<?php  } ?>
							</div>
						</div>
						<div class="message_wrap">
							<div class="col-sm-11 col-md-11 col-xs-12" style="padding-left: 0; padding-right: 3rem; margin-bottom: 1rem">
								<textarea class="form-control" rows="3" name="message[]" placeholder="消息内容"><?php  echo $notice['message'];?></textarea>
							</div>
						</div>
					</div>
					<?php  } } ?>
					<?php  } else { ?>
					<div class="row notice" style="margin-bottom: 1rem;">
						<input type="hidden" name="nid[]" value="0">
						<div class="col-sm-11 col-md-11 col-xs-12" style="padding-left: 0; padding-right: 3rem; margin-bottom: 1rem">
							<select class="form-control" name="type[]">
								<option selected value="">请选择</option>
								<option value="1">微信</option>
								<option value="2">短信</option>
							</select>
						</div>
						<div class="col-sm-1 col-md-1 col-xs-1" style="padding: 0">
							<a href="javascript:;" data-toggle="tooltip" class="btn btn-danger" onclick="deleteNotice(this)" title="删除" data-id="0">
								<span class="glyphicon glyphicon-remove" aria-hidden="true"></span> 删除
							</a>
						</div>
						<div class="wrap_1 notice_wrap" style="display: none">
							<div class="col-sm-11 col-md-11 col-xs-12" style="padding-left: 0; padding-right: 3rem; margin-bottom: 1rem">
								<select class="form-control" name="openid[]">
									<option selected value="">请选择账号</option>
									<?php  if($user_list) { ?>
									<?php  if(is_array($user_list)) { foreach($user_list as $user) { ?>
									<option <?php  if($user['openid']) { ?>value="<?php  echo $user['openid'];?>"<?php  } else { ?>disabled<?php  } ?>>
									<?php  echo $user['username'];?><?php  if($user['openid'] == '') { ?>--未绑定微信<?php  } ?>
									</option>
									<?php  } } ?>
									<?php  } ?>
								</select>
							</div>
						</div>
						<div class="wrap_2 notice_wrap" style="display: none">
							<div class="col-sm-11 col-md-11 col-xs-12" style="padding-left: 0; padding-right: 3rem; margin-bottom: 1rem">
								<input class="form-control" name="mobile[]" type="text" value="" placeholder="手机号">
								<?php  if(isset($sms['setting']['shop_trigger']['switch']) && $sms['setting']['shop_trigger']['switch'] == 1 && $sms['setting']['shop_trigger']['provider'] == 'heysky') { ?>
								<span class="help-block">本平台触发器短信配置为国际短信，手机号前需加国际区号，如中国区号为86，则中国手机号为86138643XXXXX</span>
								<?php  } ?>
							</div>
						</div>
						<div class="message_wrap" style="display: none">
							<div class="col-sm-11 col-md-11 col-xs-12" style="padding-left: 0; padding-right: 3rem; margin-bottom: 1rem">
								<textarea class="form-control" rows="3" name="message[]" placeholder="消息内容">您好，数据已更新，请登录后台查看。</textarea>
							</div>
						</div>
					</div>
					<?php  } ?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label"></label>
				<div class="col-sm-8 col-md-8 col-xs-12">
					<button type="button" class="btn btn-warning add_notice">
						<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> 添加
					</button>
				</div>
			</div>
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
	require(['jquery.ui'], function($){
		//初始化通知
		$('select[name="type[]"]').change(function(){
			$('.message_wrap', $(this).parent().parent()).show();
			$('.notice_wrap', $(this).parent().parent()).hide();
			var notice_type = $(this).val();
			$('.wrap_'+notice_type, $(this).parent().parent()).show();
			if (notice_type == '') {
				$('.message_wrap', $(this).parent().parent()).hide();
			}
		});
		//添加通知
		$('.add_notice').click(function(){
			$.ajax({
				url: "<?php  echo $this->createWebUrl('trigger', array('act' => 'new', 'isshop' => '1'))?>",
				success:function(response) {
					$('#list_wrap').append(response);
					$('select[name="type[]"]').change(function(){
						$('.message_wrap', $(this).parent().parent()).show();
						$('.notice_wrap', $(this).parent().parent()).hide();
						var notice_type = $(this).val();
						$('.wrap_'+notice_type, $(this).parent().parent()).show();
						if (notice_type == '') {
							$('.message_wrap', $(this).parent().parent()).hide();
						}
					});
				}
			});
		});
		//删除通知
		window.deleteNotice = function(obj) {
			var id = $(obj).attr('data-id');
			if (id == 0) {	//new
				$(obj).parent().parent().remove();
				return;
			}
			$.ajax({
				'url': "<?php  echo $this->createWebUrl('trigger', array('act' => 'delete', 'isshop' => '1'))?>"+'&nid='+id,
				success:function(response) {
					if (response == 'success') {
						$(obj).parent().parent().remove();
					} else {
						util.message(response, '', 'error');
					}
				}
			});
		};
		//提交表单
		$('form').submit(function(){
			var title = $('input[name=title]');
			if (title.val() == '') {
				util.message('请输标题！', '', 'error');
				return false;
			}
			var action = $('select[name=action]');
			if (action.val() == '') {
				util.message('请选择动作！', '', 'error');
				return false;
			}
			var notice = $('.notice');
			var notice_flag = true;
			var notice_message;
			notice.each(function() {
				var type = $('select[name="type[]"]', this);
				if (type.val() == '') {
					notice_message = '请选择通知类型！';
					notice_flag = false;
					return false;
				}
				var openid = $('select[name="openid[]"]', this);
				var mobile = $('input[name="mobile[]"]', this);
				if (type.val() == "1") {
					if (openid.val() == '') {
						notice_message = '请选择微信号！';
						notice_flag = false;
						return false;
					}
				}
				if (type.val() == "2") {
					if (mobile.val() == '') {
						notice_message = '请填写手机号！';
						notice_flag = false;
						return false;
					}
				}
				var message = $('textarea[name="message[]"]', this);
				if (message.val() == '') {
					notice_message = '请填消息内容！';
					notice_flag = false;
					return false;
				}
			});
			if (!notice_flag) {
				util.message(notice_message, '', 'error');
				return false;
			}
			return true;
		});
	});
</script>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('footer', TEMPLATE_INCLUDEPATH)) : (include template('footer', TEMPLATE_INCLUDEPATH));?>