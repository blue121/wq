<?php defined('IN_IA') or exit('Access Denied');?><style>
	.plugin_item {
		min-width: 150px;
	}
</style>
<form class="form-inline" action="" method="post">
	<div class="alert alert-info">
		<i class="fa fa-exclamation-circle"></i> 选择是否开启功能模块，未开启时功能模块不可用
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">功能模块</div>
		<div class="panel-body table-responsive" style="overflow:visible">
			<div class="checkbox plugin_item">
				<label>
					<input type="checkbox" value="1" name="setting[seckill]" <?php  if(isset($setting['seckill']) && $setting['seckill'] == 1) { ?>checked<?php  } ?>> 秒杀
				</label>
			</div>
			<div class="checkbox plugin_item">
				<label>
					<input type="checkbox" value="1" name="setting[mgroupon]" <?php  if(isset($setting['mgroupon']) && $setting['mgroupon'] == 1) { ?>checked<?php  } ?>> 拼团
				</label>
			</div>
			<div class="checkbox plugin_item">
				<label>
					<input type="checkbox" value="1" name="setting[partner]" <?php  if(isset($setting['partner']) && $setting['partner'] == 1) { ?>checked<?php  } ?>> 分销
				</label>
			</div>
			<div class="checkbox plugin_item">
				<label>
					<input type="checkbox" value="1" name="setting[discount]" <?php  if(isset($setting['discount']) && $setting['discount'] == 1) { ?>checked<?php  } ?>> 营销
				</label>
			</div>
			<div class="checkbox plugin_item">
				<label>
					<input type="checkbox" value="1" name="setting[printer]" <?php  if(isset($setting['printer']) && $setting['printer'] == 1) { ?>checked<?php  } ?>> 打印机
				</label>
			</div>
			<div class="checkbox plugin_item">
				<label>
					<input type="checkbox" value="1" name="setting[tbast]" <?php  if(isset($setting['tbast']) && $setting['tbast'] == 1) { ?>checked<?php  } ?>> 淘宝助手
				</label>
			</div>
			<div class="checkbox plugin_item">
				<label>
					<input type="checkbox" value="1" name="setting[bm_payu]" <?php  if(isset($setting['bm_payu']) && $setting['bm_payu'] == 1) { ?>checked<?php  } ?>> 微信服务商支付
				</label>
			</div>
		</div>
	</div>
	<button type="submit" class="btn btn-primary" name="submit" value="yes">提交</button>
	<input type="hidden" name="token" value="<?php  echo $_W['token'];?>">
</form>