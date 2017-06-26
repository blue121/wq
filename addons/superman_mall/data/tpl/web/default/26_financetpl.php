<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('header', TEMPLATE_INCLUDEPATH)) : (include template('header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/shop-nav', TEMPLATE_INCLUDEPATH)) : (include template('common/shop-nav', TEMPLATE_INCLUDEPATH));?>
<style>
	.star {
		color: red;
		margin-right: 5px;
		font-weight: bold;
	}
</style>
<ul class="nav nav-tabs">
	<li <?php  if($act == 'apply') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('finance', array('act' => 'apply'));?>">提现管理</a></li>
	<li <?php  if($act == 'stat') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('finance', array('act' => 'stat'));?>">商户结算</a></li>
	<li <?php  if($act == 'balance') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('finance', array('act' => 'balance'));?>">商户钱包</a></li>
	<li <?php  if($act == 'statement') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('finance', array('act' => 'statement'));?>">对账单下载</a></li>
	<?php  if($act == 'post') { ?><li class="active"><a href="<?php  echo $this->createWebUrl('finance', array('act' => 'post'));?>">编辑</a></li><?php  } ?>
	<?php  if($act == 'money_log') { ?><li class="active"><a href="<?php  echo $this->createWebUrl('finance', array('act' => 'money_log'));?>">流水明细</a></li><?php  } ?>
</ul>
<?php  if($act == 'apply') { ?>
<div class="main">
	<form action="" method="post">
		<div class="panel panel-default">
			<div class="table-responsive panel-body">
				<table class="table table-hover">
					<thead>
					<tr>
						<th width="150">申请日期</th>
						<th>商户名称</th>
						<th width="100">提现金额</th>
						<th width="100">服务费</th>
						<th width="100">到账金额</th>
						<th width="150">提现账户</th>
						<th width="80">状态</th>
						<th width="150">支付时间</th>
						<th width="150" class="text-right">操作</th>
					</tr>
					</thead>
					<tbody>
					<?php  if($list) { ?>
					<?php  if(is_array($list)) { foreach($list as $li) { ?>
					<tr>
						<td><?php  echo $li['createtime'];?></td>
						<td><?php  echo $li['shop_title'];?></td>
						<td>&yen;<?php  echo $li['money'];?></td>
						<td>&yen;<?php  echo SupermanUtil::float_format($li['service_fee'], 2);?></td>
						<td>&yen;<?php  echo SupermanUtil::float_format($li['money'] - $li['service_fee'], 2);?></td>
						<td><?php  echo SupermanUtil::get_getcash_account_type_title($li['account_type'])?></td>
						<td>
							<span class="<?php  echo SupermanUtil::get_getcash_status_style($li['status'])?>">
								<?php  echo SupermanUtil::get_getcash_status_title($li['status'])?>
							</span>
						</td>
						<td><?php  echo $li['paytime'];?></td>
						<td class="text-right" style="overflow:visible;">
							<div class="btn-group">
								<a href="<?php  echo $this->createWebUrl('finance', array('act' => 'post', 'id' => $li['id']));?>" title="编辑" class="btn btn-default btn-sm"><i class="fa fa-edit"></i></a>
								<a href="<?php  echo $this->createWebUrl('finance', array('act' => 'delete', 'id' => $li['id']));?>" onclick="return confirm('此操作不可恢复，确认吗？'); return false;" title="删除" class="btn btn-default btn-sm"><i class="fa fa-times"></i></a>
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
</div>
<?php  } else if($act == 'post') { ?>
<div class="main">
	<form class="form-horizontal form" action="" method="post" enctype="multipart/form-data">
		<input type="hidden" name="id" value="<?php  echo $id;?>">
		<div class="panel panel-default">
			<div class="panel-heading">
				提现信息
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 control-label">商户名称</label>
					<div class="col-sm-6 col-md-8 col-xs-12">
						<input type="text" class="form-control" disabled="disabled" value="<?php  echo $row['shop_title'];?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 control-label">提现金额</label>
					<div class="col-sm-6 col-md-8 col-xs-12">
						<div class="input-group">
							<input type="text" class="form-control" disabled="disabled" value="<?php  echo $row['money'];?>" name="money">
							<span class="input-group-addon">元</span>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 control-label">服务费</label>
					<div class="col-sm-6 col-md-8 col-xs-12">
						<div class="input-group">
							<input type="text" class="form-control" disabled="disabled" value="<?php  echo SupermanUtil::float_format($row['service_fee'], 2);?>" name="service_fee">
							<span class="input-group-addon">元</span>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 control-label">到账金额</label>
					<div class="col-sm-6 col-md-8 col-xs-12">
						<div class="input-group">
							<input type="text" class="form-control" disabled="disabled" value="<?php  echo SupermanUtil::float_format($row['money'] - $row['service_fee'], 2);?>" name="arrival_fee">
							<span class="input-group-addon">元</span>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">提现账号</label>
					<div class="col-sm-6 col-md-8 col-xs-12 control-label" style="text-align: left">
						<span><?php  echo SupermanUtil::get_getcash_account_type_title($row['account_type'])?><!--支付宝银行--></span>
						<div class="panel panel-default reply-container" style="padding-top:2em; margin-top: 1rem">
							<!--判断提现方式为微信-->
							<?php  if($row['account_type'] == 'wechat') { ?>
							<div class="form-group row">
								<label class="col-xs-12 col-sm-3 col-md-2 control-label text-right">OpenID</label>
								<div class="col-sm-9 col-xs-12">
									<input type="text" class="form-control" disabled="disabled" name="" value="<?php  echo $row['account']['openid'];?>">
								</div>
							</div>
							<div class="form-group row">
								<label class="col-xs-12 col-sm-3 col-md-2 control-label text-right">微信昵称</label>
								<div class="col-sm-9 col-xs-12">
									<input type="text" class="form-control" disabled="disabled" name="" value="<?php  echo $row['shop_admin']['nickname'];?>">
								</div>
							</div>
							<div class="form-group row">
								<label class="col-xs-12 col-sm-3 col-md-2 control-label text-right" style="line-height: 30px;">微信头像</label>
								<div class="col-sm-9 col-xs-12">
									<div style="width: 40px;height: 40px; overflow: hidden; border-radius: 50%;">
										<img src="<?php  echo $row['shop_admin']['avatar'];?>" onerror="this.src='<?php  echo $_W['siteroot'];?>app/resource/images/heading.jpg'" style="width: 100%">
									</div>
								</div>
							</div>
							<!--判断提现方式为支付宝-->
							<?php  } else if($row['account_type'] == 'alipay') { ?>
							<div class="form-group row">
								<label class="col-xs-12 col-sm-3 col-md-2 control-label text-right">账户名称</label>
								<div class="col-sm-9 col-xs-12">
									<input type="text" class="form-control" name="" disabled value="<?php  echo $row['account']['alipay_account'];?>">
								</div>
							</div>
							<div class="form-group row">
								<label class="col-xs-12 col-sm-3 col-md-2 control-label text-right">姓名</label>
								<div class="col-sm-9 col-xs-12">
									<input type="text" class="form-control" name="" disabled value="<?php  echo $row['account']['alipay_username'];?>">
								</div>
							</div>
							<!--判断提现方式为银行-->
							<?php  } else if($row['account_type'] == 'bank') { ?>
							<div class="form-group row">
								<label class="col-xs-12 col-sm-3 col-md-2 control-label text-right">银行名称</label>
								<div class="col-sm-9 col-xs-12">
									<input type="text" class="form-control" name="" disabled value="<?php  echo $row['account']['bank_name'];?>">
								</div>
							</div>
							<div class="form-group row">
								<label class="col-xs-12 col-sm-3 col-md-2 control-label text-right">开户行</label>
								<div class="col-sm-9 col-xs-12">
									<input type="text" class="form-control" name="" disabled value="<?php  echo $row['account']['bank_account'];?>">
								</div>
							</div>
							<div class="form-group row">
								<label class="col-xs-12 col-sm-3 col-md-2 control-label text-right">银行卡号</label>
								<div class="col-sm-9 col-xs-12">
									<input type="text" class="form-control" name="" disabled value="<?php  echo $row['account']['bank_cardno'];?>">
								</div>
							</div>
							<div class="form-group row">
								<label class="col-xs-12 col-sm-3 col-md-2 control-label text-right">开卡人姓名</label>
								<div class="col-sm-9 col-xs-12">
									<input type="text" class="form-control" name="" disabled value="<?php  echo $row['account']['bank_username'];?>">
								</div>
							</div>
							<?php  } ?>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 control-label">申请备注</label>
					<div class="col-sm-6 col-md-8 col-xs-12">
						<textarea name="" rows="4" class="form-control" disabled><?php  echo $row['apply_remark'];?></textarea>
						<span class="help-block"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 control-label">提交时间</label>
					<div class="col-sm-6 col-md-8 col-xs-12">
						<input type="text" class="form-control" disabled="disabled" value="<?php  echo $row['createtime'];?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 control-label">支付时间</label>
					<div class="col-sm-6 col-md-8 col-xs-12">
						<?php  if($row['account_type'] == 'wechat') { ?>
						<input type="text" class="form-control" disabled="disabled" value="<?php  echo $row['paytime'];?>">
						<?php  } else { ?>
						<?php  echo tpl_form_field_date('paytime', $row['paytime'], true);?>
						<?php  } ?>
					</div>
				</div>
				<?php  if($row['account_type'] != 'wechat') { ?>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">状态</label>
					<div class="col-sm-6 col-md-8 col-xs-12">
						<div class="input-group">
							<label class="radio-inline">
								<input type="radio" name="status" value="0" <?php  if($row['status'] == 0) { ?>checked<?php  } ?>> 待支付
							</label>
							<label class="radio-inline">
								<input type="radio" name="status" value="1" <?php  if($row['status'] == 1) { ?>checked<?php  } ?>> 已支付
							</label>
						</div>
						<span class="help-block" style="color: #f00">更新状态时，请确认已完成相关付款操作</span>
					</div>
				</div>
				<?php  } ?>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 control-label">备注</label>
					<div class="col-sm-6 col-md-8 col-xs-12">
						<textarea name="remark" rows="4" class="form-control"><?php  echo $row['remark'];?></textarea>
						<span class="help-block">此备注信息只有管理员可见</span>
					</div>
				</div>
				<?php  if($row['account_type'] == 'wechat' && $row['wxpay_orderno']) { ?>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">微信付款订单号</label>
					<div class="col-sm-6 col-md-8 col-xs-12 control-label" style="text-align: left">
						<?php  echo $row['wxpay_orderno'];?>
					</div>
				</div>
				<?php  } ?>
				<?php  if($row['account_type'] == 'wechat' && $row['wxpay_paymentno']) { ?>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">微信付款交易号</label>
					<div class="col-sm-6 col-md-8 col-xs-12 control-label" style="text-align: left">
						<?php  echo $row['wxpay_paymentno'];?>
					</div>
				</div>
				<?php  } ?>
				<?php  if($row['account_type'] == 'wechat') { ?>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 control-label">微信付款结果</label>
					<div class="col-sm-6 col-md-8 col-xs-12">
						<textarea name="" rows="4" class="form-control" disabled><?php  echo $row['wxpay_result'];?></textarea>
						<span class="help-block"></span>
					</div>
				</div>
				<?php  } ?>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">更新时间</label>
					<div class="col-sm-6 col-md-8 col-xs-12 control-label" style="text-align: left">
						<?php  echo $row['updatetime'];?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">操作人</label>
					<div class="col-sm-6 col-md-8 col-xs-12 control-label" style="text-align: left">
						<?php  echo $row['operator'];?>
					</div>
				</div>
			</div>
		</div>
		<?php  if($row['account_type'] == 'wechat') { ?>
		<div class="alert alert-danger">
			<i class="fa fa-exclamation-circle"></i>
			微信付款调用微信支付商户后台企业付款接口，请确认账户金额充足，否则无法支付成功。
			查看账户余额（请复制链接使用IE浏览器登录）：<a href="http://pay.weixin.qq.com" target="_blank">http://pay.weixin.qq.com</a>
		</div>
		<div class="form-group col-sm-12">
			<?php  if($row['status'] == 1) { ?>
			<button type="submit" class="btn btn-success" name="wxpay_submit" disabled value="wechat">
				微信付款（已支付）
			</button>
			<?php  } else { ?>
			<button type="submit" class="btn btn-success col-lg-1" name="wxpay_submit" value="wechat">微信付款</button>
			<?php  } ?>
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
		</div>
		<?php  } else { ?>
		<div class="form-group col-sm-12">
			<button type="submit" class="btn btn-primary col-lg-1" name="submit" value="submit">提交</button>
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
		</div>
		<?php  } ?>
	</form>
</div>
<script>
	require(['jquery'], function($){
		$('button[name=wxpay_submit]').click(function(){
			return confirm('确认付款到到商户微信账号吗？');
		});
	});
</script>
<?php  } else if($act == 'stat') { ?>
<div class="main">
	<div class="alert alert-info">
		<p style="margin-bottom: 10px">
			商户结算操作金额将进入商户账户可提现余额中，未操作结算时，商户账户没有可提现余额将无法提现。
		</p>
		<ol>
			<li>每个商户每天生成一条结算数据，包含订单总数和总金额；</li>
			<li>重复点击生成结算数据时，会产生数据覆盖；</li>
			<li>生成结算数据时，根据平台商户数和结算日期不同，可能需要较长执行时间；</li>
			<li>货到付款订单和微信服务商支付的订单均不在商户结算范围内（因商户已自行收款）；</li>
		</ol>
		<p style="color: red">&nbsp;&nbsp;&nbsp;&nbsp;商户结算数据涉及钱款操作，请认真审核，谨慎操作！</p>
	</div>
	<div class="panel panel-info">
		<div class="panel-heading">商户结算</div>
		<div class="panel-body">
			<form action="" method="post" class="form-horizontal" role="form">
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 control-label">结算日期</label>
					<div class="col-xs-12 col-sm-8 col-md-8 col-lg-2">
						<?php  echo tpl_form_field_daterange('time_limit', $time_limit, false);?>
					</div>
					<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
						<button class="btn btn-default">生成结算数据</button>
					</div>
				</div>
			</form>
		</div>
	</div>
	<div style="margin: 10px 0;" class="clearfix">
		<div class="btn-group pull-left" style="margin-right: 10px;">
			<a class="btn btn-default <?php  if(!isset($_GPC['status'])) { ?>active<?php  } ?>" href="<?php  echo $this->createWebUrl('finance', array('act' => 'stat', 'pagesize' => $pagesize))?>">
				全部
			</a>
			<a class="btn btn-default <?php  if(isset($_GPC['status'])&&$_GPC['status']==0) { ?>active<?php  } ?>" href="<?php  echo $this->createWebUrl('finance', array('act' => 'stat', 'status' => 0, 'pagesize' => $pagesize))?>">
				未结算
			</a>
			<a class="btn btn-default <?php  if($_GPC['status']==1) { ?>active<?php  } ?>" href="<?php  echo $this->createWebUrl('finance', array('act' => 'stat', 'status' => 1, 'pagesize' => $pagesize))?>">
				已结算
			</a>
		</div>
		<form action="" method="post" class="form-inline pull-left" role="form" id="form_pagesize">
			<input type="hidden" name="page" value="<?php  echo $_GPC['page'];?>">
			<input type="hidden" name="status" value="<?php  echo $_GPC['status'];?>">
			<div class="form-group">
				<select class="form-control" name="pagesize">
					<option value="20" <?php  if($pagesize==20) { ?>selected<?php  } ?>>20条</option>
					<option value="50" <?php  if($pagesize==50) { ?>selected<?php  } ?>>50条</option>
					<option value="100" <?php  if($pagesize==100) { ?>selected<?php  } ?>>100条</option>
					<option value="200" <?php  if($pagesize==200) { ?>selected<?php  } ?>>200条</option>
					<option value="500" <?php  if($pagesize==500) { ?>selected<?php  } ?>>500条</option>
				</select>
				<script>
					$('select[name=pagesize]').change(function(){
						window.location.href = '<?php  echo $this->createWebUrl("finance", array("act" => "stat", "status" => $_GPC["status"], "page" => $_GPC["page"]))?>&pagesize='+$(this).val();
					});
				</script>
			</div>
		</form>
	</div>
	<form action="" method="post">
		<div class="panel panel-default">
			<div class="table-responsive panel-body">
				<table class="table table-hover">
					<thead>
						<tr>
							<th width="50"></th>
							<th width="160">日期</th>
							<th>商户名称</th>
							<th width="160">订单数</th>
							<th width="160">金额</th>
							<th width="160">状态</th>
							<th width="160">结算时间</th>
							<th width="160" class="text-right">操作人</th>
						</tr>
					</thead>
					<tbody>
						<?php  if($list) { ?>
						<?php  if(is_array($list)) { foreach($list as $li) { ?>
						<tr>
							<td>
								<input type="checkbox" name="id[<?php  echo $li['shopid'];?>][]" value="<?php  echo $li['id'];?>" <?php  if($li['status'] == 1) { ?>disabled<?php  } ?>>
							</td>
							<td><?php  echo $li['stat_date'];?></td>
							<td><?php  echo $li['shop']['title'];?></td>
							<td><?php  echo $li['order_total'];?></td>
							<td>&yen;<?php  echo $li['order_price'];?></td>
							<td>
								<span class="<?php  echo SupermanUtil::get_money_status_style($li['status'])?>">
									<?php  echo SupermanUtil::get_money_status_title($li['status'])?>
								</span>
							</td>
							<td><?php  echo $li['dateline'];?></td>
							<td class="text-right"><?php  echo $li['operator'];?></td>
						</tr>
						<?php  } } ?>
						<?php  } ?>
						
					</tbody>
				</table>
				<div style="padding-left: 8px;padding-top: 8px;">
					<label style="cursor: pointer;">
						<input type="checkbox" name="checkall"> 全选&nbsp;&nbsp;
					</label>
					<button type="submit" name="submit" value="yes" class="btn btn-success">批量结算</button>
					<input type="hidden" name="token" value="<?php  echo $_W['token'];?>">
				</div>
			</div>
		</div>
		<?php  echo $pager;?>
	</form>
</div>
<script>
	require(['jquery'], function($) {
		$('input[name=checkall]').click(function(){
			if ($(this).prop('checked')) {
				$('input[type=checkbox]').not(':disabled').prop('checked', true);
			} else {
				$('input[type=checkbox]').not(':disabled').prop('checked', false);
			}
		});
	})
</script>
<?php  } else if($act == 'balance') { ?>
<div class="main">
	<div class="alert alert-info">总金额：&yen;<?php  if($balance_total) { ?><?php  echo $balance_total;?><?php  } else { ?>0.00<?php  } ?></div>
	<form action="" method="post">
		<div class="panel panel-default">
			<div class="table-responsive panel-body">
				<table class="table table-hover">
					<thead>
						<tr>
							<th>商户名称</th>
							<th width="100">可提金额</th>
							<th width="150">最后结算时间</th>
							<th width="150" class="text-right">操作</th>
						</tr>
					</thead>
					<tbody>
						<?php  if(is_array($list)) { foreach($list as $li) { ?>
						<tr>
							<td><?php  echo $li['shop']['title'];?></td>
							<td>&yen;<?php  echo $li['balance'];?></td>
							<td><?php  echo $li['updatetime'];?></td>
							<td class="text-right">
								<a href="<?php  echo $this->createWebUrl('finance', array('act' => 'money_log', 'id' => $li['shop']['id']))?>">查看流水明细</a>
							</td>
						</tr>
						<?php  } } ?>
					</tbody>
				</table>
			</div>
		</div>
		<?php  echo $pager;?>
	</form>
</div>
<?php  } else if($act == 'money_log') { ?>
<div class="main">
	<form action="" method="post">
		<div class="panel panel-default">
			<div class="table-responsive panel-body">
				<table class="table table-hover">
					<thead>
					<tr>
						<th width="100">类型</th>
						<th>备注</th>
						<th width="150">金额</th>
						<th width="120">操作人</th>
						<th width="150">更新时间</th>
					</tr>
					</thead>
					<tbody>
					<?php  if($list) { ?>
					<?php  if(is_array($list)) { foreach($list as $li) { ?>
					<tr>
						<td>
							<?php  if($li['type']==1) { ?>
							<span class="label label-success">收入</span>
							<?php  } else { ?>
							<span class="label label-danger">支出</span>
							<?php  } ?>
						</td>
						<td style="white-space: normal;overflow: auto;"><?php  echo $li['remark'];?></td>
						<td><?php  if($li['type']==1) { ?>+<?php  } else { ?>-<?php  } ?>&yen;<?php  echo $li['money'];?></td>
						<td><?php  echo $li['operator'];?></td>
						<td><?php  echo date('Y-m-d H:i:s', $li['dateline'])?></td>
					</tr>
					<?php  } } ?>
					<?php  } ?>
					</tbody>
				</table>
			</div>
		</div>
		<?php  echo $pager;?>
	</form>
</div>
<?php  } else if($act == 'statement') { ?>
<div class="main">
    <div class="alert alert-info">
		<p>对账单中涉及金额的字段单位为“元”；</p>
		<p>对账单接口只能下载三个月以内的账单；</p>
		<p>下载账单时间段选择不要过长，因下载账单接口为单日期接口。</p>
		<p>微信在次日9点启动生成前一天的对账单，建议商户10点后再获取；</p>
    </div>
    <form class="form-horizontal form" action="" method="post">
        <div class="panel panel-default">
            <div class="panel-heading">
                对账单下载
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">账单类型</label>
                    <div class="col-sm-6 col-md-8 col-xs-12">
                        <div class="input-group">
                            <label class="radio-inline">
                                <input type="radio" name="bill_type" value="ALL" checked> 所有账单
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="bill_type" value="SUCCESS"> 支付账单
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="bill_type" value="REFUND"> 退款账单
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="bill_type" value="REVOKED"> 撤销账单
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">账单时间</label>
                    <div class="col-sm-6 col-md-8 col-xs-12">
                        <div class="input-group">
                            <?php  echo tpl_form_field_daterange('bill_date', $date, false);?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group col-sm-12">
            <button type="submit" name="submit" value="yes" class="btn btn-success">下载对账单</button>
            <input type="hidden" name="token" value="<?php  echo $_W['token'];?>">
        </div>
    </form>
</div>

<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('footer', TEMPLATE_INCLUDEPATH)) : (include template('footer', TEMPLATE_INCLUDEPATH));?>