<?php defined('IN_IA') or exit('Access Denied');?><ul class="nav nav-tabs">
	<li <?php  if($op=='setting') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('platform', array('act' => 'sms', 'op' => 'setting'));?>">短信配置</a></li>
	<li <?php  if($op=='template') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('platform', array('act' => 'sms', 'op' => 'template'));?>">短信模板</a></li>
	<li <?php  if($op=='account'&&$provider=='chanzor') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('platform', array('act' => 'sms', 'op' => 'account', 'provider' => 'chanzor'));?>">【畅卓】</a></li>
	<li <?php  if($op=='account'&&$provider=='smsbao') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('platform', array('act' => 'sms', 'op' => 'account', 'provider' => 'smsbao'));?>">【短信宝】</a></li>
	<li <?php  if($op=='account'&&$provider=='alidayu') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('platform', array('act' => 'sms', 'op' => 'account', 'provider' => 'alidayu'));?>">【阿里大鱼】</a></li>
	<li <?php  if($op=='account'&&$provider=='heysky') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('platform', array('act' => 'sms', 'op' => 'account', 'provider' => 'heysky'));?>">【海客】</a></li>
	<li <?php  if($op=='test') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('platform', array('act' => 'sms', 'op' => 'test'));?>">短信测试</a></li>
</ul>
<?php  if($op == 'setting') { ?>
<div class="main">
	<form class="form-horizontal form" id="" action="" method="post">
		<div class="alert alert-info">
			<i class="fa fa-exclamation-circle"></i> 短信配置之前，请先确保已设置短信账号，否则无法发送短信。
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				商户注册
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">短信验证</label>
					<div class="col-sm-6 col-md-8 col-xs-12">
						<div class="input-group">
							<label class="radio-inline">
								<input type="radio" name="setting[shop_reg][switch]" <?php  if($setting['shop_reg']['switch'] == 1) { ?>checked<?php  } ?> value="1"> 开启
							</label>
							<label class="radio-inline">
								<input type="radio" name="setting[shop_reg][switch]" <?php  if(!$setting['shop_reg']['switch']) { ?>checked<?php  } ?> value="0"> 关闭
							</label>
						</div>
						<span class="help-block">开启后商户入驻注册账号时，必须填写手机号和手机验证码，否则无法注册</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 control-label">短信服务商</label>
					<div class="col-xs-12 col-sm-8 col-md-8 col-lg-6">
						<select class="form-control" name="setting[shop_reg][provider]">
							<?php  if(is_array(SupermanSms::$providers)) { foreach(SupermanSms::$providers as $key => $val) { ?>
							<option value="<?php  echo $key;?>" <?php  if($setting['shop_reg']['provider']==$key) { ?>selected<?php  } ?> data-url="<?php  echo $val['url'];?>"><?php  echo $val['title'];?></option>
							<?php  } } ?>
						</select>
					</div>
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				商户提现
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">短信验证</label>
					<div class="col-sm-6 col-md-8 col-xs-12">
						<div class="input-group">
							<label class="radio-inline">
								<input type="radio" name="setting[shop_getcash][switch]" <?php  if($setting['shop_getcash']['switch'] == 1) { ?>checked<?php  } ?> value="1"> 开启
							</label>
							<label class="radio-inline">
								<input type="radio" name="setting[shop_getcash][switch]" <?php  if(!$setting['shop_getcash']['switch']) { ?>checked<?php  } ?> value="0"> 关闭
							</label>
						</div>
						<span class="help-block">开启后商户提现时，必须填写手机号和手机验证码，否则无法申请提现</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 control-label">短信服务商</label>
					<div class="col-xs-12 col-sm-8 col-md-8 col-lg-6">
						<select class="form-control" name="setting[shop_getcash][provider]">
							<?php  if(is_array(SupermanSms::$providers)) { foreach(SupermanSms::$providers as $key => $val) { ?>
							<option value="<?php  echo $key;?>" <?php  if($setting['shop_getcash']['provider']==$key) { ?>selected<?php  } ?> data-url="<?php  echo $val['url'];?>"><?php  echo $val['title'];?></option>
							<?php  } } ?>
						</select>
					</div>
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				商户触发器
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">短信</label>
					<div class="col-sm-6 col-md-8 col-xs-12">
						<div class="input-group">
							<label class="radio-inline">
								<input type="radio" name="setting[shop_trigger][switch]" <?php  if($setting['shop_trigger']['switch'] == 1) { ?>checked<?php  } ?> value="1"> 开启
							</label>
							<label class="radio-inline">
								<input type="radio" name="setting[shop_trigger][switch]" <?php  if(!$setting['shop_trigger']['switch']) { ?>checked<?php  } ?> value="0"> 关闭
							</label>
						</div>
						<span class="help-block">开启后商户添加触发器规则时，可以选择短信通知方式</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 control-label">短信服务商</label>
					<div class="col-xs-12 col-sm-8 col-md-8 col-lg-6">
						<select class="form-control" name="setting[shop_trigger][provider]">
							<?php  if(is_array(SupermanSms::$providers)) { foreach(SupermanSms::$providers as $key => $val) { ?>
							<option value="<?php  echo $key;?>" <?php  if($setting['shop_trigger']['provider']==$key) { ?>selected<?php  } ?> data-url="<?php  echo $val['url'];?>"><?php  echo $val['title'];?></option>
							<?php  } } ?>
						</select>
					</div>
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				分销商注册
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">短信</label>
					<div class="col-sm-6 col-md-8 col-xs-12">
						<div class="input-group">
							<label class="radio-inline">
								<input type="radio" name="setting[partner_reg][switch]" <?php  if($setting['partner_reg']['switch'] == 1) { ?>checked<?php  } ?> value="1"> 开启
							</label>
							<label class="radio-inline">
								<input type="radio" name="setting[partner_reg][switch]" <?php  if(!$setting['partner_reg']['switch']) { ?>checked<?php  } ?> value="0"> 关闭
							</label>
						</div>
						<span class="help-block">开启后分销商注册时，将使用短信验证码验证手机号</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 control-label">短信服务商</label>
					<div class="col-xs-12 col-sm-8 col-md-8 col-lg-6">
						<select class="form-control" name="setting[partner_reg][provider]">
							<?php  if(is_array(SupermanSms::$providers)) { foreach(SupermanSms::$providers as $key => $val) { ?>
							<?php  if($key != 'heysky') { ?>
							<option value="<?php  echo $key;?>" <?php  if($setting['partner_reg']['provider']==$key) { ?>selected<?php  } ?> data-url="<?php  echo $val['url'];?>"><?php  echo $val['title'];?></option>
							<?php  } ?>
							<?php  } } ?>
						</select>
					</div>
				</div>
			</div>
		</div>
		<div class="form-group col-sm-12">
			<input type="submit" class="btn btn-primary col-lg-1" name="submit" value="提交" data-original-title="" title="">
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>">
		</div>
	</form>
</div>
<?php  } else if($op == 'account' && $provider == 'chanzor') { ?>
<div class="main">
	<form class="form-horizontal form" action="" method="post">
		<div class="alert alert-info">
			<i class="fa fa-exclamation-circle"></i> 短信账号为您购买的发送短信服务，可自行联系第三方短信服务平台，购买短信服务后一般会提供账号及其相关信息，请在此设置。
			<p>
				<span style="color: red">申请畅卓免费短信试用，</span><a href="#" data-toggle="modal" data-target="#applysms">点击查看如何申请</a>
			</p>
		</div>
		<div class="modal fade" id="applysms" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content" style="width:740px;">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
						<h4 class="modal-title" id="myModalLabel">申请免费短信</h4>
					</div>
					<div class="modal-body">
						<p style="color: red">请参考如下截图，在申请表单第一个输入框填写：超人应用，可额外获得试用权限</p>
						<img src="<?php  echo $_W['siteroot'];?>/addons/superman_mall/template/web/default/images/applysms.jpg" style="max-width: 100%;"/>
					</div>
					<div class="modal-footer">
						<a href="http://www.chanzor.com/" class="btn btn-danger" target="_blank">点击申请</a>
					</div>
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				短信账号
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 control-label">接口网址</label>
					<div class="col-xs-12 col-sm-8 col-md-8 col-lg-6">
						<input class="form-control" name="account[chanzor][url]" type="text" value="<?php  echo $account['chanzor']['url'];?>" placeholder="http://">
						<span class="help-block">发送短信的接口网址，由短信服务平台提供，默认为http://sms.chanzor.com:8001/sms.aspx，一般情况下不需要填写</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 control-label">账号</label>
					<div class="col-xs-12 col-sm-8 col-md-8 col-lg-6">
						<input class="form-control" name="account[chanzor][username]" type="text" value="<?php  echo $account['chanzor']['username'];?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 control-label">密码</label>
					<div class="col-xs-12 col-sm-8 col-md-8 col-lg-6">
						<input class="form-control" onchange="this.name='account[chanzor][password]'" type="text" <?php  if(isset($account['chanzor']['password']) && $account['chanzor']['password']) { ?>value="<?php  echo SupermanUtil::hide_password($account['chanzor']['password'])?>"<?php  } ?>>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 control-label">签名</label>
					<div class="col-xs-12 col-sm-8 col-md-8 col-lg-6">
						<input class="form-control" name="account[chanzor][signature]" type="text" value="<?php  echo $account['chanzor']['signature'];?>">
					</div>
				</div>
			</div>
			<div class="panel-footer">
				<label>账户余额：<span style="color: red"><?php  echo $balance;?></span></label>
				<span style="font-size: 11px;">账户余额为短信服务商账户实时查询数据</span>
			</div>
		</div>
		<div class="form-group col-sm-12">
			<input type="submit" class="btn btn-primary col-lg-1" name="submit" value="提交">
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>">
		</div>
	</form>
</div>
<?php  } else if($op == 'account' && $provider == 'smsbao') { ?>
<div class="main">
	<form class="form-horizontal form" action="" method="post">
		<div class="alert alert-info">
			<i class="fa fa-exclamation-circle"></i> 短信账号为您购买的发送短信服务，可自行联系第三方短信服务平台，购买短信服务后一般会提供账号及其相关信息，请在此设置。
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				短信账号
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 control-label">接口网址</label>
					<div class="col-xs-12 col-sm-8 col-md-8 col-lg-6">
						<input class="form-control" name="account[smsbao][url]" type="text" value="<?php  echo $account['smsbao']['url'];?>" placeholder="http://">
						<span class="help-block">发送短信的接口网址，由短信服务平台提供，默认为http://www.cocsms.com/openapi/，一般情况下不需要填写</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 control-label">账号</label>
					<div class="col-xs-12 col-sm-8 col-md-8 col-lg-6">
						<input class="form-control" name="account[smsbao][username]" type="text" value="<?php  echo $account['smsbao']['username'];?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 control-label">密码</label>
					<div class="col-xs-12 col-sm-8 col-md-8 col-lg-6">
						<input class="form-control" onchange="this.name='account[smsbao][password]'" type="text" <?php  if(isset($account['smsbao']['password']) && $account['smsbao']['password']) { ?>value="<?php  echo SupermanUtil::hide_password($account['smsbao']['password'])?>"<?php  } ?>>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 control-label">签名</label>
					<div class="col-xs-12 col-sm-8 col-md-8 col-lg-6">
						<input class="form-control" name="account[smsbao][signature]" type="text" value="<?php  echo $account['smsbao']['signature'];?>">
					</div>
				</div>
			</div>
			<div class="panel-footer">
				<label>账户余额：<span style="color: red"><?php  echo $balance;?></span></label>
				<span style="font-size: 11px;">账户余额为短信服务商账户实时查询数据</span>
			</div>
		</div>
		<div class="form-group col-sm-12">
			<input type="submit" class="btn btn-primary col-lg-1" name="submit" value="提交">
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>">
		</div>
	</form>
</div>
<?php  } else if($op == 'account' && $provider == 'alidayu') { ?>
<div class="main">
	<form class="form-horizontal form" action="" method="post">
		<div class="alert alert-info">
			<i class="fa fa-exclamation-circle"></i> 短信账号为您购买的发送短信服务，可自行联系第三方短信服务平台，购买短信服务后一般会提供账号及其相关信息，请在此设置。
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				短信账号
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 control-label">App Key</label>
					<div class="col-xs-12 col-sm-8 col-md-8 col-lg-6">
						<input class="form-control" name="account[alidayu][app_key]" type="text" value="<?php  echo $account['alidayu']['app_key'];?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 control-label">App Secret</label>
					<div class="col-xs-12 col-sm-8 col-md-8 col-lg-6">
						<input class="form-control" name="account[alidayu][app_secret]" type="text" value="<?php  echo $account['alidayu']['app_secret'];?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 control-label">签名</label>
					<div class="col-xs-12 col-sm-8 col-md-8 col-lg-6">
						<input class="form-control" name="account[alidayu][signature]" type="text" value="<?php  echo $account['alidayu']['signature'];?>">
					</div>
				</div>
			</div>
			<!--<div class="panel-footer">
				<label>账户余额：<span style="color: red"><?php  echo $balance;?></span></label>
				<span style="font-size: 11px;">账户余额为短信服务商账户实时查询数据</span>
			</div>-->
		</div>
		<div class="form-group col-sm-12">
			<input type="submit" class="btn btn-primary col-lg-1" name="submit" value="提交">
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>">
		</div>
	</form>
</div>
<?php  } else if($op == 'account' && $provider == 'heysky') { ?>
<div class="main">
	<form class="form-horizontal form" action="" method="post">
		<div class="alert alert-info">
			<i class="fa fa-exclamation-circle"></i> 短信账号为您购买的发送短信服务，可自行联系第三方短信服务平台，购买短信服务后一般会提供账号及其相关信息，请在此设置。
			<p>
				<span style="color: red">支持发送国际短信，</span><a href="http://www.heysky.com/" target="_blank">点击查看详情</a>
			</p>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				短信账号
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 control-label">接口网址</label>
					<div class="col-xs-12 col-sm-8 col-md-8 col-lg-6">
						<input class="form-control" name="account[heysky][url]" type="text" value="<?php  echo $account['heysky']['url'];?>" placeholder="http://">
						<span class="help-block">发送短信的接口网址，由短信服务平台提供，默认为http://api2.santo.cc/submit，一般情况下不需要填写</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 control-label">账号</label>
					<div class="col-xs-12 col-sm-8 col-md-8 col-lg-6">
						<input class="form-control" name="account[heysky][username]" type="text" value="<?php  echo $account['heysky']['username'];?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 control-label">密码</label>
					<div class="col-xs-12 col-sm-8 col-md-8 col-lg-6">
						<input class="form-control" onchange="this.name='account[heysky][password]'" type="text" <?php  if(isset($account['heysky']['password']) && $account['heysky']['password']) { ?>value="<?php  echo SupermanUtil::hide_password($account['heysky']['password'])?>"<?php  } ?>>
					</div>
				</div>
			</div>
		</div>
		<div class="form-group col-sm-12">
			<input type="submit" class="btn btn-primary col-lg-1" name="submit" value="提交">
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>">
		</div>
	</form>
</div>
<?php  } else if($op == 'template') { ?>
<div class="main">
	<form class="form-horizontal form" action="" method="post">
		<div class="panel panel-default">
			<div class="panel-heading">
				验证码
			</div>
			<div class="panel-body">
				<div class="alert alert-warning">
					<div class="row" id="super_var_wrap3">
						<div class="col-xs-12 col-sm-12 col-md-12" style="color: #777">变量</div>
						<div class="col-xs-12 col-sm-12 col-md-12" style="color: #000"><strong>{验证码}</strong> 表示短信验证码  <a data-content="{验证码}" href="javascript:;" title="点击复制">点击复制</a></div>
						<div class="col-xs-12 col-sm-12 col-md-12" style="color: #000"><strong>{签名}</strong> 表示短信签名 <a data-content="{签名}" href="javascript:;" title="点击复制">点击复制</a></div>
					</div>
				</div>
				<script>
					require(['jquery', 'util'], function($, u){
						$('#super_var_wrap3 a').each(function(){
							var t = this;
							u.clip(t, $(t).attr('data-content'));
						});
					});
				</script>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">短信模板</label>
					<div class="col-xs-12 col-sm-8 col-md-8 col-lg-6">
						<textarea class="form-control" rows="6" name="template[verifycode]"><?php  echo $template['verifycode'];?></textarea>
						<span class="help-block">设置发送短信内容模板</span>
					</div>
				</div>
			</div>
		</div>
		<!--<div class="panel panel-default">
			<div class="panel-heading">
				触发器规则
			</div>
			<div class="panel-body">
				<div class="alert alert-warning">
					<div class="row" id="super_var_wrap4">
						<div class="col-xs-12 col-sm-12 col-md-12" style="color: #777">变量</div>
						<div class="col-xs-12 col-sm-12 col-md-12" style="color: #000"><strong>{管理员}</strong> 表示管理员名称  <a data-content="{管理员}" href="javascript:;" title="点击复制">点击复制</a></div>
						<div class="col-xs-12 col-sm-12 col-md-12" style="color: #000"><strong>{动作}</strong> 表示动作名称  <a data-content="{动作}" href="javascript:;" title="点击复制">点击复制</a></div>
						<div class="col-xs-12 col-sm-12 col-md-12" style="color: #000"><strong>{签名}</strong> 表示短信签名 <a data-content="{签名}" href="javascript:;" title="点击复制">点击复制</a></div>
					</div>
				</div>
				<script>
					require(['jquery', 'util'], function($, u){
						$('#super_var_wrap4 a').each(function(){
							var t = this;
							u.clip(t, $(t).attr('data-content'));
						});
					});
				</script>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">短信模板</label>
					<div class="col-xs-12 col-sm-8 col-md-8 col-lg-6">
						<textarea class="form-control" rows="6" name="template[trigger]"><?php  if(isset($template['trigger'])&&$template['trigger']) { ?><?php  echo $template['trigger'];?><?php  } else { ?>您好管理员（{<?php echo 管理员;?>}），动作（{<?php echo 动作;?>}）已更新，请登录后台查看。<?php  } ?></textarea>
						<span class="help-block">设置发送短信内容模板</span>
					</div>
				</div>
			</div>
		</div>-->
		<div class="panel panel-default">
			<div class="panel-heading">
				验证码（阿里大鱼）
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">模板ID</label>
					<div class="col-xs-12 col-sm-8 col-md-8 col-lg-6">
						<input class="form-control" name="template[alidayu][verifycode][id]" type="text" value="<?php  echo $template['alidayu']['verifycode']['id'];?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">验证码变量</label>
					<div class="col-xs-12 col-sm-8 col-md-8 col-lg-6">
						<input class="form-control" name="template[alidayu][verifycode][variable]" type="text" value="<?php  echo $template['alidayu']['verifycode']['variable'];?>" placeholder="变量格式为：code">
					</div>
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				触发器规则（阿里大鱼）
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">模板ID</label>
					<div class="col-xs-12 col-sm-8 col-md-8 col-lg-6">
						<input class="form-control" name="template[alidayu][trigger][id]" type="text" value="<?php  echo $template['alidayu']['trigger']['id'];?>">
					</div>
				</div>
				<!--<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">模板变量</label>
					<div class="col-xs-12 col-sm-8 col-md-8 col-lg-6">
						<input style="margin-bottom: 10px;" class="form-control" name="template[alidayu][trigger][variables][]" type="text" value="name" readonly>
						<input class="form-control" name="template[alidayu][trigger][variables][]" type="text" value="action" readonly>
						<span class="help-block">请在添加短信模板时，复制以下 <span style="color: #0000ff">蓝色字体</span> 内容添加，触发器规则短信模板为固定内容，变量不可修改</span>
						<span class="help-block" style="color: #0000ff">您好管理员（${name}），动作（${action}）已更新，请登录后台查看。</span>
					</div>
				</div>-->
			</div>
		</div>
		<div class="form-group col-sm-12">
			<input type="submit" class="btn btn-primary col-lg-1" name="submit" value="提交" data-original-title="" title="">
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>">
		</div>
	</form>
</div>
<?php  } else if($op == 'test') { ?>
<div class="main">
	<form class="form-horizontal form" action="" method="post">
		<div class="panel panel-default">
			<div class="panel-heading">
				短信测试
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 control-label">短信服务商</label>
					<div class="col-xs-12 col-sm-8 col-md-8 col-lg-6">
						<select class="form-control" name="provider">
							<option value="">请选择</option>
							<?php  if(is_array(SupermanSms::$providers)) { foreach(SupermanSms::$providers as $key => $val) { ?>
							<option value="<?php  echo $key;?>" data-url="<?php  echo $val['url'];?>"><?php  echo $val['title'];?></option>
							<?php  } } ?>
						</select>
					</div>
				</div>
				<div class="form-group sms_template">
					<label class="col-xs-12 col-sm-2 col-md-2 control-label">短信模板</label>
					<div class="col-xs-12 col-sm-8 col-md-8 col-lg-6">
						<select class="form-control" name="template_name">
							<option value="">请选择</option>
							<?php  if(is_array(SupermanSms::$templates)) { foreach(SupermanSms::$templates as $key => $val) { ?>
							<option value="<?php  echo $key;?>"><?php  echo $val['title'];?></option>
							<?php  } } ?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 control-label">手机号</label>
					<div class="col-xs-12 col-sm-8 col-md-8 col-lg-6">
						<input class="form-control" name="mobile" type="text">
						<span class="help-block">如使用海客测试短信发送，请输入带国际区号的手机号，例如中国区号为86，填入手机号为86138643XXXXX</span>
					</div>
				</div>
			</div>
		</div>
		<div class="form-group col-sm-12">
			<input type="submit" class="btn btn-primary col-lg-1" name="submit" value="发送短信">
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>">
		</div>
	</form>
</div>
<script>
	require(['jquery'], function($){
		$('form').bind('submit', function(){
			var provider = $('select[name=provider]');
			if (provider.val() == '') {
				util.message('请选择短信服务商！', '', 'error');
				return false;
			}
			var template_name = $('select[name=template_name]');
			if (template_name.val() == '') {
				util.message('请选择短信模板！', '', 'error');
				return false;
			}
			return true;
		});
	});
</script>
<?php  } ?>
