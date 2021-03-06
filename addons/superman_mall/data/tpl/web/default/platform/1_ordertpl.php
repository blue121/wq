<?php defined('IN_IA') or exit('Access Denied');?><div class="main">
	<form class="form-horizontal form"  action="" method="post" enctype="multipart/form-data">
		<div class="panel panel-default">
			<div class="panel-heading">
				订单设置
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">虚拟商品是否需要快递</label>
					<div class="col-sm-6 col-md-8 col-xs-12">
						<div class="input-group">
							<label class="radio-inline">
								<input type="radio" name="setting[virtual_need_express]" value="1" <?php  if(!isset($setting['virtual_need_express'])||$setting['virtual_need_express']) { ?>checked<?php  } ?>> 需要
							</label>
							<label class="radio-inline">
								<input type="radio" name="setting[virtual_need_express]" value="0" <?php  if(isset($setting['virtual_need_express'])&&!$setting['virtual_need_express']) { ?>checked<?php  } ?>> 不需要
							</label>
						</div>
						<span class="help-block">单笔订单中实物和虚拟商品一起下单时，此时也需要填写收货地址</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">未发货订单退款</label>
					<div class="col-sm-6 col-md-8 col-xs-12">
						<div class="input-group ">
							<input type="text" name="setting[self_refund_limit]" value="<?php  echo $setting['self_refund_limit'];?>" class="form-control">
							<span class="input-group-btn">
								<button class="btn btn-default" type="button">分钟</button>
							</span>
						</div>
						<span class="help-block">订单支付成功后，在该时间内可申请退款，超过后无法申请退款，默认为空不限制，即未发货前都可以申请退款</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">订单退款/售后</label>
					<div class="col-sm-6 col-md-8 col-xs-12">
						<div class="input-group ">
							<input type="text" name="setting[after_sale_limit]" value="<?php  echo $setting['after_sale_limit'];?>" class="form-control">
							<span class="input-group-btn">
								<button class="btn btn-default" type="button">天</button>
							</span>
						</div>
						<span class="help-block">订单完成后，多少天内允许申请退款/售后，超过限制天数后，手机端我的订单退款/售后页面将不显示申请入口</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">未付款订单自动关闭</label>
					<div class="col-sm-6 col-md-8 col-xs-12">
						<div class="input-group ">
							<input type="text" name="setting[order_auto_close]" value="<?php  echo $setting['order_auto_close'];?>" class="form-control">
							<span class="input-group-btn">
								<button class="btn btn-default" type="button">小时</button>
							</span>
						</div>
						<span class="help-block">
							订单提交后，未付款状态下，多少小时后自动关闭，默认为空，不自动关闭订单（订单商品件数原样返回库存）
							<span style="color: red;">每5分钟检查一次</span>
						</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">自动确认收货</label>
					<div class="col-sm-6 col-md-8 col-xs-12">
						<div class="input-group ">
							<input type="text" name="setting[order_auto_receive]" value="<?php  echo $setting['order_auto_receive'];?>" class="form-control">
							<span class="input-group-btn">
								<button class="btn btn-default" type="button">天</button>
							</span>
						</div>
						<span class="help-block">
							订单发货后，买家未确认收货时，系统在多少天后自动确认收货，默认为空，不自动收货
							<span style="color: red;">每10分钟检查一次</span>
						</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">自动评论</label>
					<div class="col-sm-6 col-md-8 col-xs-12">
						<div class="input-group ">
							<input type="text" name="setting[order_auto_comment]" value="<?php  echo $setting['order_auto_comment'];?>" class="form-control">
							<span class="input-group-btn">
								<button class="btn btn-default" type="button">天</button>
							</span>
						</div>
						<span class="help-block">
							订单确认后，买家未评价时，系统在订单确认多少天后自动好评完成，默认为空，不自动好评
							<span style="color: red;">每5分钟检查一次</span>
						</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">取消订单是否更新库存</label>
					<div class="col-sm-6 col-md-8 col-xs-12">
						<div class="input-group">
							<label class="radio-inline">
								<input type="radio" name="setting[cancel_update_total]" value="1" <?php  if($setting['cancel_update_total']) { ?>checked<?php  } ?>> 是
							</label>
							<label class="radio-inline">
								<input type="radio" name="setting[cancel_update_total]" value="0" <?php  if(!$setting['cancel_update_total']) { ?>checked<?php  } ?>> 否
							</label>
						</div>
						<span class="help-block">手机端取消订单时，如果允许更新库存，那么订单中的商品数将更新到商品库存中</span>
					</div>
				</div>
			</div>
		</div>
		<div class="form-group col-sm-12">
			<input name="token" type="hidden" value="<?php  echo $_W['token'];?>" />
			<input type="submit" class="btn btn-primary col-lg-1" name="submit" value="提交" />
		</div>
	</form>
</div> 
<script>
	require(['jquery'], function($){
		$('#setting_form').submit(function(){
			return true;
		});
	});
</script>
