<?php defined('IN_IA') or exit('Access Denied');?><ul class="nav nav-tabs">
	<li <?php  if($act == 'payments') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('platform', array('act' => 'payments'));?>">支付方式</a></li>
	<li <?php  if($act == 'paycert') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('platform', array('act' => 'paycert'));?>">支付证书</a></li>
</ul>
<div class="main">
	<form class="form-horizontal form" action="" method="post" enctype="multipart/form-data">
		<div class="panel panel-default">
			<div class="panel-heading">
				基本参数
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">余额支付</label>
					<div class="col-sm-6 col-md-8 col-xs-12">
						<div class="input-group">
							<label class="radio-inline">
								<input type="radio" name="setting[credit_open]" value="1" <?php  if(isset($setting['credit_open']) && $setting['credit_open'] == 1 || !isset($setting['credit_open'])) { ?>checked<?php  } ?>> 开启
							</label>
							<label class="radio-inline">
								<input type="radio" name="setting[credit_open]" value="0" <?php  if(isset($setting['credit_open']) && $setting['credit_open'] == 0) { ?>checked<?php  } ?>> 关闭
							</label>
						</div>
						<span class="help-block">开启余额支付时，收银台页面可以选择账户余额支付，反之不可以</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">微信支付</label>
					<div class="col-sm-6 col-md-8 col-xs-12">
						<div class="input-group">
							<label class="radio-inline">
								<input type="radio" name="setting[wechat_open]" value="1" <?php  if(isset($setting['wechat_open']) && $setting['wechat_open'] == 1 || !isset($setting['wechat_open'])) { ?>checked<?php  } ?>> 开启
							</label>
							<label class="radio-inline">
								<input type="radio" name="setting[wechat_open]" value="0" <?php  if(isset($setting['wechat_open']) && $setting['wechat_open'] == 0) { ?>checked<?php  } ?>> 关闭
							</label>
						</div>
						<span class="help-block">开启微信支付时，需要配置微信支付参数 <a href="<?php  echo wurl('profile/payment')?>">点击去配置</a> </span>
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