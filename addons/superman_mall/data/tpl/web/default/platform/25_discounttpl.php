<?php defined('IN_IA') or exit('Access Denied');?><div class="main">
	<form class="form-horizontal form" action="" method="post" enctype="multipart/form-data">
		<div class="panel panel-default">
			<div class="panel-heading">
				积分设置
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">返积分</label>
					<div class="col-sm-6 col-md-8 col-xs-12">
						<div class="input-group" style="margin-bottom: 1rem">
							<label class="radio-inline">
								<input type="radio" name="setting[credit][remark_open]" value="1" <?php  if(isset($setting['credit']['remark_open']) && $setting['credit']['remark_open'] == 1) { ?>checked<?php  } ?>> 开启
							</label>
							<label class="radio-inline">
								<input type="radio" name="setting[credit][remark_open]" value="0" <?php  if(!isset($setting['credit']['remark_open']) || $setting['credit']['remark_open'] == 0) { ?>checked<?php  } ?>> 关闭
							</label>
						</div>
						<div class="input-group" style="margin-bottom: 1rem">
							<span class="input-group-addon">订单金额</span>
							<input type="number" step="0.01" class="form-control" name="setting[credit][remark_rate]" <?php  if(isset($setting['credit']['remark_rate']) && $setting['credit']['remark_rate']) { ?>value="<?php  echo $setting['credit']['remark_rate'];?>"<?php  } ?> <?php  if(!isset($setting['credit']['remark_open']) || $setting['credit']['remark_open'] == 0) { ?>disabled<?php  } ?>>
							<span class="input-group-addon">%</span>
						</div>
						<div class="input-group" style="margin-bottom: .5rem">
							<span class="input-group-addon">订单固定</span>
							<input type="number" step="0.01" class="form-control" name="setting[credit][remark_value]" <?php  if(isset($setting['credit']['remark_value']) && $setting['credit']['remark_value']) { ?>value="<?php  echo $setting['credit']['remark_value'];?>"<?php  } ?> <?php  if(!isset($setting['credit']['remark_open']) || $setting['credit']['remark_open'] == 0) { ?>disabled<?php  } ?>>
							<span class="input-group-addon">积分</span>
						</div>
						<span class="help-block">返积分有两种方式，选择其中一种即可，支持订单金额百分比和固定积分，<strong>优先级：订单金额 &gt; 订单固定</strong></span>
						<span class="help-block" style="color: red">订单评价完成后，触发返积分逻辑，当订单下有多件商品时，需要评价全部商品后，统一返积分</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">积分类型</label>
					<div class="col-sm-8 col-md-8 col-xs-12">
						<select class="form-control" name="setting[credit][credit_type]">
							<?php  if($credit_group) { ?>
							<?php  if(is_array($credit_group)) { foreach($credit_group as $k => $v) { ?>
							<option value="<?php  echo $k;?>" <?php  if(isset($setting['credit']['credit_type']) && $setting['credit']['credit_type'] == $k) { ?>selected<?php  } ?>>
								<?php  echo $v['title'];?>
							</option>
							<?php  } } ?>
							<?php  } ?>
						</select>
						<span class="help-block">选择积分类型，请勿随意修改积分类型，更改积分类型会导致已下发的积分失效</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">订单最低金额</label>
					<div class="col-sm-6 col-md-8 col-xs-12">
						<div class="input-group ">
							<input type="number" step="0.01" name="setting[credit][min_order_amount]" <?php  if(isset($setting['credit']['min_order_amount']) && $setting['credit']['min_order_amount']) { ?>value="<?php  echo $setting['credit']['min_order_amount'];?>"<?php  } ?> class="form-control">
							<span class="input-group-btn">
								<button class="btn btn-default" type="button">元</button>
							</span>
						</div>
						<span class="help-block">设置返积分订单最低金额，低于最低金额时，将不产生积分</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">积分抵现</label>
					<div class="col-sm-6 col-md-8 col-xs-12">
						<div class="input-group">
							<label class="radio-inline">
								<input type="radio" name="setting[credit][cash_open]" value="1" <?php  if(isset($setting['credit']['cash_open']) && $setting['credit']['cash_open'] == 1) { ?>checked<?php  } ?>> 开启
							</label>
							<label class="radio-inline">
								<input type="radio" name="setting[credit][cash_open]" value="0" <?php  if(!isset($setting['credit']['cash_open']) || $setting['credit']['cash_open'] == 0) { ?>checked<?php  } ?>> 关闭
							</label>
						</div>
						<span class="help-block">开启积分抵现后，下单时可以使用积分抵扣现金</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">抵现比例</label>
					<div class="col-sm-6 col-md-8 col-xs-12">
						<div class="input-group ">
							<input type="number" step="0.01" name="setting[credit][cash_rate]" <?php  if(isset($setting['credit']['cash_rate'])) { ?>value="<?php  echo $setting['credit']['cash_rate'];?>"<?php  } ?> class="form-control" disabled>
							<span class="input-group-btn">
								<button class="btn btn-default" type="button">积分  兑换￥1.00元</button>
							</span>
						</div>
						<span class="help-block">支持小数数值，推荐设置为整数，例如：1000积分 兑换 1元</span>
					</div>
				</div>
			</div>
		</div>
		<div class="form-group col-sm-12">
			<button type="submit" class="btn btn-primary" name="submit" value="yes">提交</button>
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>">
		</div>
	</form>
</div>
<script>
	require(['jquery'], function($){
		$('input[name="setting[credit][remark_open]"]').click(function(){
			if ($(this).val() == 1) {
				$('input[name="setting[credit][remark_rate]"]').prop("disabled",false);
				$('input[name="setting[credit][remark_value]"]').prop("disabled",false);
			} else {
				$('input[name="setting[credit][remark_rate]"]').prop("disabled",true);
				$('input[name="setting[credit][remark_value]"]').prop("disabled",true);
			}
		});
		if ($('input[name="setting[credit][cash_open]"]').prop('checked')) {
			$('input[name="setting[credit][cash_rate]"]').prop("disabled",false);
		}
		$('input[name="setting[credit][cash_open]"]').click(function(){
			if ($(this).val() == 1) {
				$('input[name="setting[credit][cash_rate]"]').prop("disabled",false);
			} else {
				$('input[name="setting[credit][cash_rate]"]').prop("disabled",true);
			}
		});
		$('form').submit(function(){
			if ($('input[name="setting[credit][cash_open]"]').prop('checked')) {
				var cash_rate = $('input[name="setting[credit][cash_rate]"]');
				if (cash_rate.val() == '' || cash_rate.val() <= 0) {
					util.message('抵现比例为0或空，请重新填写！', '', 'error');
					return false;
				}
			}
			return true;
		});
	});
</script>