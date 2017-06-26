<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('header', TEMPLATE_INCLUDEPATH)) : (include template('header', TEMPLATE_INCLUDEPATH));?>
<style>
	.star {
		color: red;
		margin-right: 5px;
		font-weight: bold;
	}
</style>
<ul class="nav nav-tabs">
	<li <?php  if($_GPC['status'] == 'all') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('shop', array('act' => 'display', 'status' => 'all'));?>">全部</a></li>
	<li <?php  if($_GPC['status'] == '0') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('shop', array('act' => 'display', 'status' => '0'));?>">待审核</a></li>
	<li <?php  if($_GPC['status'] == '1') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('shop', array('act' => 'display', 'status' => '1'));?>">已审核</a></li>
	<li <?php  if($_GPC['act'] == 'post'&&!$id) { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('shop', array('act' => 'post'));?>">添加商户</a></li>
	<?php  if($act == 'post' && $_GPC['id']) { ?><li class="active"><a href="<?php  echo $this->createWebUrl('shop', array('act' => 'post', 'id' => $id));?>">编辑商户</a></li><?php  } ?>
	<?php  if($act == 'plugin' && $_GPC['id']) { ?><li class="active"><a href="<?php  echo $this->createWebUrl('shop', array('act' => 'plugin', 'id' => $li['id']))?>">商户功能</a></li><?php  } ?>
	<?php  if($act == 'charge_sms_log') { ?><li class="active"><a href="<?php  echo $this->createWebUrl('shop', array('act' => 'charge_sms_log', 'id' => $_GPC['id']))?>">充值短信记录</a></li><?php  } ?>
	<?php  if($act == 'send_sms_log') { ?><li class="active"><a href="<?php  echo $this->createWebUrl('shop', array('act' => 'send_sms_log', 'id' => $_GPC['id']))?>">发送短信记录</a></li><?php  } ?>
</ul>
<?php  if($act=='display') { ?>
<div class="panel panel-info">
	<div class="panel-heading">筛选</div>
	<div class="panel-body">
		<form action="" class="form-horizontal" role="form">
			<input type="hidden" name="c" value="site">
			<input type="hidden" name="a" value="entry">
			<input type="hidden" name="do" value="shop">
			<input type="hidden" name="act" value="<?php  echo $_GPC['act'];?>">
			<input type="hidden" name="status" value="<?php  echo $_GPC['status'];?>">
			<input type="hidden" name="m" value="superman_mall">
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 control-label">商户名称</label>
				<div class="col-xs-12 col-sm-8 col-md-8 col-lg-6">
					<input class="form-control" name="title" type="text" value="<?php  echo $_GPC['title'];?>" placeholder="支持模糊搜索">
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 control-label">首页推荐</label>
				<div class="col-xs-12 col-sm-8 col-md-8 col-lg-6">
					<select class="form-control" name="ishome">
						<option value="all" <?php  if($_GPC['ishome'] == "all" || !$_GPC['ishome']) { ?>selected<?php  } ?>>全部</option>
						<option value="1" <?php  if($_GPC['ishome'] == "1") { ?>selected<?php  } ?>>
							是
						</option>
						<option value="0"  <?php  if($_GPC['ishome'] == "0") { ?>selected<?php  } ?>>
							否
						</option>
					</select>
				</div>
				<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
					<button class="btn btn-default"><i class="fa fa-search"></i>搜索</button>
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
						<th width="80">排序</th>
						<th>商户名称</th>
						<th width="130">首页推荐</th>
						<th width="190">申请人</th>
						<th width="120">手机号</th>
						<th width="80">状态</th>
						<th width="150">入驻时间</th>
						<th width="220" class="text-right">操作</th>
					</tr>
				</thead>
				<tbody>
					<?php  if($list) { ?>
					<?php  if(is_array($list)) { foreach($list as $li) { ?>
					<tr>
						<td>
							<input type="text" class="form-control text-center" name="displayorder[<?php  echo $li['id'];?>]" value="<?php  echo $li['displayorder'];?>">
						</td>
						<td><?php  echo $li['title'];?></td>
						<td><?php  if($li['ishome'] > 0) { ?>是<?php  } else { ?>否<?php  } ?></td>
						<td>
							<div class="clear">
								<?php  if(isset($li['user'])) { ?>
								<div class="pull-left" style="width: 40px;height: 40px; overflow: hidden; border-radius: 50%;">
									<img src="<?php  echo $li['user']['avatar'];?>" onerror="this.src='<?php  echo $_W['siteroot'];?>/app/resource/images/heading.jpg'" style="width: 100%">
								</div>
								<?php  } ?>
								<div class="pull-left" style="line-height: 40px; margin-left: 5px; width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php  echo $li['user']['nickname'];?>">
									<?php  if($li['user']['nickname']!='') { ?><?php  echo $li['user']['nickname'];?><?php  } else { ?><?php  echo $li['user']['uid'];?><?php  } ?>
								</div>
							</div>
						</td>
						<td><?php  echo $li['user']['mobile'];?></td>
						<td>
							<span class="<?php  echo SupermanUtil::get_shop_status_style($li['status'])?>"><?php  echo SupermanUtil::get_shop_status_title($li['status'])?></span>
						</td>
						<td><?php  echo $li['createtime'];?></td>
						<td class="text-right" style="overflow:visible;">
							<div class="btn-group">
								<a href="<?php  echo $this->createWebUrl('shop', array('act' => 'post', 'id' => $li['id']));?>" title="编辑" class="btn btn-default btn-sm"><i class="fa fa-edit"></i></a>
								<a href="javascript:;" data-url="<?php  echo $this->createWebUrl('shop', array('act' => 'delete', 'id' => $li['id']))?>" onclick="if(confirm('此操作不可恢复，确认吗？')){$(this).attr('href', $(this).attr('data-url'));return true;}return false;" title="删除" class="btn btn-default btn-sm"><i class="fa fa-times"></i></a>
								<a href="#" data-toggle="modal" data-shopid="<?php  echo $li['id'];?>" data-target=".quick_examine_modal" class="btn btn-default btn-sm btn_audit" title="审核"><i class="fa fa-check-circle-o"></i></a>
								<a href="<?php  echo $this->createWebUrl('shop', array('act' => 'plugin', 'id' => $li['id']))?>" class="btn btn-default btn-sm" title="功能模块"><i class="fa fa-cogs"></i></a>
								<a href="<?php  echo $this->createWebUrl('user', array('act' => 'user', 'op' => 'display', 'shopid' => $li['id']))?>" class="btn btn-default btn-sm" title="商户账号"><i class="fa fa-user"></i></a>
								<a href="<?php  echo $this->create_shop_web_url($li['id'])?>" class="btn btn-default btn-sm" title="登录商户后台" target="_blank"><i class="fa fa-sign-in"></i></a>
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
	<input name="displayorder_submit" type="submit" value="更新排序" class="btn btn-primary col-lg-1">
	<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
</form>
<div class="modal fade quick_examine_modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title">审核</h4>
			</div>
			<form action="" method="post">
				<div class="modal-body">
					<div class="form-group" style="min-height: 35px;">
						<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label text-right">状态</label>
						<div class="col-sm-8 col-md-8 col-xs-12">
							<select name="audit_status" class="form-control">
								<option value="0">待审核</option>
								<option value="1">审核通过</option>
								<option value="-1">审核失败</option>
							</select>
						</div>
					</div>
					<div class="form-group" style="min-height: 180px;">
						<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label text-right">审核备注</label>
						<div class="col-sm-8 col-md-8 col-xs-12">
							<textarea class="form-control" rows="6" name="remark"></textarea>
							<span class="help-block">审核备注内容将通过模板消息发送给申请人微信</span>
							<span class="help-block" style="margin-bottom: 0"><a href="<?php  echo $this->createWebUrl('message', array('act' => 'template'));?>" target="_blank">设置模板消息</a></span>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button name="submit" type="submit" value="yes" class="btn btn-primary">提交</button>
					<input type="hidden" name="token" value="<?php  echo $_W['token'];?>">
					<input type="hidden" name="shopid">
				</div>
			</form>
		</div>
	</div>
</div>
<script>
	require(['jquery', 'bootstrap'],function($){
		$('.btn_audit').click(function () {
			var t = this;
			var shopid = $(t).attr('data-shopid');
			$('input[name=shopid]').val(shopid);
		});
	});
</script>
<?php  } else if($act == 'post') { ?>
<form class="form-horizontal form" method="post" enctype="multipart/form-data">
	<div class="panel panel-default">
		<div class="panel-heading">
			<?php  if($id) { ?>编辑商户<?php  } else { ?>添加商户<?php  } ?>
		</div>
		<div class="panel-body">
			<!--<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">排序</label>
				<div class="col-sm-8 col-md-8 col-xs-12">
					<input class="form-control" name="displayorder" type="text" value="<?php  echo $item['displayorder'];?>">
					<span class="help-block"></span>
				</div>
			</div>-->
			<?php  if($id) { ?>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">后台网址</label>
				<div class="col-sm-8 col-md-8 col-xs-12">
					<div class="form-control-static">
						<?php $shop_admin_url = $_W['siteroot'].'/addons/superman_mall/admin/index.php?shopid='.$id?>
						<p>
							<a href="<?php  echo $shop_admin_url;?>" target="_blank">
								<?php  echo $shop_admin_url;?>
							</a>
						</p>
						<?php  if(!defined('LOCAL_DEVELOPMENT') && in_array($_W['account']['level'], array(3, 4))) { ?>
						<p>
							<a href="<?php  echo $shop_admin_url;?>" target="_blank">
								<?php  echo $this->short_url($shop_admin_url)?>
							</a>
						</p>
						<?php  } ?>
					</div>
					<span class="help-block"></span>
				</div>
			</div>
			<?php  } ?>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label"><span class="star">*</span>名称</label>
				<div class="col-sm-8 col-md-8 col-xs-12">
					<input class="form-control" name="title" type="text" value="<?php  echo $item['title'];?>">
					<span class="help-block">商户名称最多50个字符</span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 control-label"><span class="star">*</span>标志</label>
				<div class="col-sm-6 col-md-8 col-xs-12">
					<?php  echo tpl_form_field_image('logo', $item['logo'])?>
					<span class="help-block">商户Logo或门店图片，推荐尺寸：200x200像素</span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">简介</label>
				<div class="col-sm-8 col-md-8 col-xs-12">
					<textarea class="form-control" name="description"><?php  echo $item['description'];?></textarea>
					<span class="help-block"></span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">经营品类</label>
				<div class="col-sm-8 col-md-8 col-xs-12">
					<input class="form-control" name="business_scope" type="text" value="<?php  echo $item['business_scope'];?>">
					<span class="help-block">经营品类信息仅作审核参考使用</span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">商户地址</label>
				<div class="col-sm-8 col-md-8 col-xs-12">
					<?php  echo tpl_form_field_district('area', array('province' => $item['province'], 'city' => $item['city'], 'district' => $item['district']));?>
					<br/>
					<input class="form-control" name="address" type="text" value="<?php  echo $item['address'];?>">
					<span class="help-block"></span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label"><span class="star">*</span>地理位置</label>
				<div class="col-sm-8 col-md-8 col-xs-12">
					<?php  echo tpl_form_field_coordinate('location', $location);?>
					<span class="help-block"></span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">联系电话</label>
				<div class="col-sm-8 col-md-8 col-xs-12">
					<input class="form-control" name="phone" type="text" value="<?php  echo $item['phone'];?>">
					<span class="help-block"></span>
				</div>
			</div>
			<?php  if($id) { ?>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">申请人</label>
				<div class="col-sm-8 col-md-8 col-xs-12">
					<?php  if(isset($user_info)) { ?>
					<div style="width: 3.5rem; height: 3.5rem; display: inline-block; overflow: hidden">
						<img src="<?php  echo $user_info['avatar'];?>" onerror="this.src='<?php  echo $_W['siteroot'];?>/app/resource/images/heading.jpg'" style="width: 3.5rem; height: 3.5rem; overflow: hidden; border-radius: 50%;">
					</div>
					<span><?php  echo $user_info['nickname'];?></span>
					<?php  } else { ?>
					<span>无</span>
					<?php  } ?>
					<span class="help-block"></span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">手机号</label>
				<div class="col-sm-8 col-md-8 col-xs-12">
					<?php  echo $shop_user['mobile'];?>
				</div>
			</div>
			<?php  } ?>
			<?php  if($item['createtime']) { ?>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">注册时间</label>
				<div class="col-sm-8 col-md-8 col-xs-12">
					<span style="line-height: 30px;"><?php  echo $item['createtime'];?></span>
					<span class="help-block"></span>
				</div>
			</div>
			<?php  } ?>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">提现费率</label>
				<div class="col-sm-6 col-md-8 col-xs-12">
					<div class="input-group ">
						<input type="text" name="fee_rate" class="form-control" value="<?php  echo $item['fee_rate'];?>">
						<span class="input-group-btn">
							<button class="btn btn-default" type="button">%</button>
						</span>
					</div>
					<span class="help-block">商户申请提现时，每笔申请提现扣除的费用，默认为空，即提现不扣费，支持填写小数</span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">提现费用</label>
				<div class="col-sm-6 col-md-8 col-xs-12">
					<div class="input-group">
						<span class="input-group-btn">
							<button class="btn btn-default" type="button">最低</button>
						</span>
						<input type="text" name="fee_min" class="form-control" value="<?php  echo $item['fee_min'];?>">
						<span class="input-group-btn">
							<button class="btn btn-default" type="button">元</button>
						</span>
					</div>
					<div class="input-group" style="margin-top: .5rem">
						<span class="input-group-btn">
							<button class="btn btn-default" type="button">最高</button>
						</span>
						<input type="text" name="fee_max" class="form-control" value="<?php  echo $item['fee_max'];?>">
						<span class="input-group-btn">
							<button class="btn btn-default" type="button">元</button>
						</span>
					</div>
					<span class="help-block">商户提现时，提现费用的上下限，最高为空时，表示不限制扣除的提现费用<br>
						例如：提现100元，费率5%，最低1元，最高2元，商户最终提现金额=100-2=98<br>
						例如：提现100元，费率5%，最低1元，最高10元，商户最终提现金额=100-100*5%=95<br>
					</span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">充值短信</label>
				<div class="col-sm-6 col-md-8 col-xs-12">
					<div class="input-group">
						<input type="text" name="sms_total" class="form-control" value="<?php  echo $item['sms_total'];?>">
						<span class="input-group-btn">
							<button class="btn btn-default" type="button">条</button>
						</span>
					</div>
					<span class="help-block">
						商户账号可使用短信<strong>总条数</strong>，关闭短信服务设置空或0即可&nbsp;&nbsp;
						<a href="<?php  echo $this->createWebUrl('shop', array('act' => 'charge_sms_log', 'id' => $item['id']));?>">充值记录</a>&nbsp;&nbsp;
						<a href="<?php  echo $this->createWebUrl('shop', array('act' => 'send_sms_log', 'id' => $item['id']));?>">发送记录</a>
					</span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">首页推荐</label>
				<div class="col-sm-6 col-md-8 col-xs-12">
					<div class="input-group">
						<label class="radio-inline">
							<input type="radio" name="ishome" value="1" <?php  if(isset($item['ishome']) && $item['ishome'] == 1) { ?>checked<?php  } ?>> 是
						</label>
						<label class="radio-inline">
							<input type="radio" name="ishome" value="0" <?php  if(!isset($item['ishome']) || $item['ishome'] == 0) { ?>checked<?php  } ?>> 否
						</label>
					</div>
					<span class="help-block">商户是否显示到首页推荐店铺位置</span>
				</div>
			</div>
			<?php  if($shop_setting && $shop_setting['international']) { ?>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">国家地区</label>
				<div class="col-sm-6 col-md-8 col-xs-12">
					<select class="form-control" name="countryid">
						<?php  if(is_array($countrys)) { foreach($countrys as $li) { ?>
						<option value="<?php  echo $li['id'];?>" <?php  if($item['countryid']==$li['id']) { ?>selected<?php  } ?>><?php  echo $li['title'];?> （+<?php  echo $li['areacode'];?>）</option>
						<?php  } } ?>
					</select>
					<span class="help-block"></span>
				</div>
			</div>
			<?php  } ?>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">绑定商户子账户</label>
				<div class="col-sm-6 col-md-8 col-xs-12">
					<select class="form-control" name="payu_rid" <?php  if(!defined('SUPERMAN_CONNECT_BMPAYU')) { ?>disabled<?php  } ?>>
						<option value="0">请选择商户子账户</option>
						<?php  if($payu_list) { ?>
						<?php  if(is_array($payu_list)) { foreach($payu_list as $li) { ?>
						<option value="<?php  echo $li['id'];?>" <?php  if($item['payu_rid']==$li['id']) { ?>selected<?php  } ?>><?php  echo $li['name'];?></option>
						<?php  } } ?>
						<?php  } ?>
					</select>
					<?php  if(!defined('SUPERMAN_CONNECT_BMPAYU')) { ?>
					<span class="help-block" style="color: red">提示：当前环境未安装服务商版扫码收银台模块，无法使用微信支付服务商功能</span>
					<?php  } else { ?>
					<span class="help-block">
                        <a href="<?php  echo url('platform/reply', array('m' => 'bm_payu'))?>" target="_blank" onclick="return confirm('确认进入 [服务商版扫码收银台] 模块操作吗？')">点我去添加子账户</a>
                    </span>
					<span class="help-block" style="color: red">注意：绑定商户子账户后，该商户商品售出后，订单款直接进入商户子账户，将不再由平台代收</span>
					<span class="help-block" style="color: red;font-weight: bold;">涉及钱款结算，请谨慎操作，一定选择对应商户子账户！</span>
					<?php  } ?>
				</div>
			</div>
			<!--<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">审核状态</label>
				<div class="col-sm-8 col-md-8 col-xs-12">
					<label class="radio-inline">
						<input type="radio" name="status" value="-1" <?php  if(isset($item['status'])||$item['status']==-1) { ?>checked<?php  } ?>/>未通过
					</label>
					<label class="radio-inline">
						<input type="radio" name="status" value="0" <?php  if(isset($item['status'])&&$item['status']==0) { ?>checked<?php  } ?>/>审核中
					</label>
					<label class="radio-inline">
						<input type="radio" name="status" value="1" <?php  if(!isset($item['status'])||(isset($item['status'])&&$item['status']==1)) { ?>checked<?php  } ?>/>正常
					</label>
					<span class="help-block"></span>
				</div>
			</div>-->
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-12">
			<input name="submit" type="submit" value="提交" class="btn btn-primary col-lg-1">
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>">
			<input type="hidden" name="id" value="<?php  echo $id;?>">
		</div>
	</div>
</form>
<script>
	require(['jquery'], function($){
		$('form').submit(function(){
			var title = $('input[name=title]');
			var logo = $('input[name=logo]');
			var lng = $('input[name="location[lng]"]');
			var lat = $('input[name="location[lat]"]');
			if (title.val() == '') {
				util.message('商户名称为空，请重新填写', '', 'error');
				return false;
			}
			if (logo.val() == '') {
				util.message('商户标志为空，请上传标志', '', 'error');
				return false;
			}
			if (lng.val() == '') {
				util.message('地理经度为空，请选择坐标', '', 'error');
				return false;
			}
			if (lat.val() == '') {
				util.message('地理纬度为空，请选择坐标', '', 'error');
				return false;
			}
			return true;
		});
	});
</script>
<?php  } else if($act == 'plugin') { ?>
<div class="main">
	<form class="form-horizontal form" method="post">
		<div class="alert alert-info">
			<i class="fa fa-exclamation-circle"></i> 商户后台功能模块管理，可开启和关闭指定功能，开启后可设置功能有效期为永久或时间范围，到期后商户后台将无法使用该功能。
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				商户名称：<?php  echo $shop['title'];?>
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">秒杀</label>
					<div class="col-sm-6 col-md-8 col-xs-12">
						<div class="checkbox">
							<label>
								<input class="plugin_switch" type="checkbox" name="seckill[switch]" <?php  if(isset($setting['seckill']['open'])&&$setting['seckill']['open']==1) { ?>checked<?php  } ?>>开启
							</label>
						</div>
						<div class="checkbox">
							<label style="margin-right: 20px;">
								<input class="plugin_forever" type="checkbox" name="seckill[limit]" <?php  if(isset($setting['seckill']['endtime'])&&$setting['seckill']['endtime']==-1) { ?>checked<?php  } ?>>永久
							</label>
							<?php  echo tpl_form_field_daterange('seckill[usetime]', $usetime['seckill'], true);?>
						</div>
                        <span class="help-block">
                            开启秒杀功能后，每个商户可以发起商品秒杀活动，支持秒杀独立入口和秒杀倒计时
                        </span>
					</div>
				</div>
				<div class="form-group" style="margin-top: 40px;">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">拼团</label>
					<div class="col-sm-6 col-md-8 col-xs-12">
						<div class="checkbox">
							<label>
								<input class="plugin_switch" type="checkbox" name="mgroupon[switch]" <?php  if(isset($setting['mgroupon']['open'])&&$setting['mgroupon']['open']==1) { ?>checked<?php  } ?>>开启
							</label>
						</div>
						<div class="checkbox">
							<label style="margin-right: 20px;">
								<input class="plugin_forever" type="checkbox" name="mgroupon[limit]" <?php  if(isset($setting['mgroupon']['endtime'])&&$setting['mgroupon']['endtime']==-1) { ?>checked<?php  } ?>>永久
							</label>
							<?php  echo tpl_form_field_daterange('mgroupon[usetime]', $usetime['mgroupon'], true);?>
						</div>
                        <span class="help-block">
                            开启拼团功能后，每个商户都可以自定义添加拼团商品，支持拼团人数自定义
                        </span>
					</div>
				</div>
				<div class="form-group" style="margin-top: 40px;">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">分销</label>
					<div class="col-sm-6 col-md-8 col-xs-12">
						<div class="checkbox">
							<label>
								<input class="plugin_switch" type="checkbox" name="partner[switch]" <?php  if(isset($setting['partner']['open'])&&$setting['partner']['open']==1) { ?>checked<?php  } ?>>开启
							</label>
						</div>
						<div class="checkbox">
							<label style="margin-right: 20px;">
								<input class="plugin_forever" type="checkbox" name="partner[limit]" <?php  if(isset($setting['partner']['endtime']) && $setting['partner']['endtime']==-1) { ?>checked<?php  } ?>>永久
							</label>
							<?php  echo tpl_form_field_daterange('partner[usetime]', $usetime['partner'], true);?>
						</div>
                        <span class="help-block">
                            开启分销功能后，平台可以招募分销商入驻，支持三级分销
                        </span>
					</div>
				</div>
				<div class="form-group" style="margin-top: 40px;">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">营销工具</label>
					<div class="col-sm-6 col-md-8 col-xs-12">
						<div class="checkbox">
							<label>
								<input class="plugin_switch" type="checkbox" name="discount[switch]" <?php  if(isset($setting['discount']['open'])&&$setting['discount']['open']==1) { ?>checked<?php  } ?>>开启
							</label>
						</div>
						<div class="checkbox">
							<label style="margin-right: 20px;">
								<input class="plugin_forever" type="checkbox" name="discount[limit]" <?php  if(isset($setting['discount']['endtime']) && $setting['discount']['endtime']==-1) { ?>checked<?php  } ?>>永久
							</label>
							<?php  echo tpl_form_field_daterange('discount[usetime]', $usetime['discount'], true);?>
						</div>
                        <span class="help-block">
                            开启营销工具后，可创建满包邮、限时打折、满减优惠等多种营销活动
                        </span>
					</div>
				</div>
				<div class="form-group" style="margin-top: 40px;">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">打印机</label>
					<div class="col-sm-6 col-md-8 col-xs-12">
						<div class="checkbox">
							<label>
								<input class="plugin_switch" type="checkbox" name="printer[switch]" <?php  if(isset($setting['printer']['open'])&&$setting['printer']['open']==1) { ?>checked<?php  } ?>>开启
							</label>
						</div>
						<div class="checkbox">
							<label style="margin-right: 20px;">
								<input class="plugin_forever" type="checkbox" name="printer[limit]" <?php  if(isset($setting['printer']['endtime']) && $setting['printer']['endtime']==-1) { ?>checked<?php  } ?>>永久
							</label>
							<?php  echo tpl_form_field_daterange('printer[usetime]', $usetime['printer'], true);?>
						</div>
                        <span class="help-block">
                            开启打印机功能后，在订单提交、付款时，可关联打印机自动打印订单小票，需购买打印机硬件设备
                        </span>
					</div>
				</div>
				<div class="form-group" style="margin-top: 40px;">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">淘宝助手</label>
					<div class="col-sm-6 col-md-8 col-xs-12">
						<div class="checkbox">
							<label>
								<input class="plugin_switch" type="checkbox" name="tbast[switch]" <?php  if(isset($setting['tbast']['open'])&&$setting['tbast']['open']==1) { ?>checked<?php  } ?>>开启
							</label>
						</div>
						<div class="checkbox">
							<label style="margin-right: 20px;">
								<input class="plugin_forever" type="checkbox" name="tbast[limit]" <?php  if(isset($setting['tbast']['endtime']) && $setting['tbast']['endtime']==-1) { ?>checked<?php  } ?>>永久
							</label>
							<?php  echo tpl_form_field_daterange('tbast[usetime]', $usetime['tbast'], true);?>
						</div>
                        <span class="help-block"></span>
					</div>
				</div>
				<div class="form-group" style="margin-top: 40px;">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">微信支付服务商</label>
					<div class="col-sm-6 col-md-8 col-xs-12">
						<div class="checkbox">
							<label>
								<input class="plugin_switch" type="checkbox" name="bm_payu[switch]" <?php  if(isset($setting['bm_payu']['open'])&&$setting['bm_payu']['open']==1) { ?>checked<?php  } ?>>开启
							</label>
						</div>
						<div class="checkbox">
							<label style="margin-right: 20px;">
								<input class="plugin_forever" type="checkbox" name="bm_payu[limit]" <?php  if(isset($setting['bm_payu']['endtime']) && $setting['bm_payu']['endtime']==-1) { ?>checked<?php  } ?>>永久
							</label>
							<?php  echo tpl_form_field_daterange('bm_payu[usetime]', $usetime['bm_payu'], true);?>
						</div>
						<span class="help-block">
                            开启微信支付服务商功能后，可为每个商户开通微信支付子账户收款，<strong>开启后需要到商户编辑页面绑定子账户</strong>
                            <p style="color: red;">注意：微信支付服务商功能只在平台后台操作，商户后台没有任何操作入口</p>
                            <a href="https://pay.weixin.qq.com/service_provider/index.shtml" target="_blank">什么是微信支付服务商？</a>
                        </span>
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
<script>
	require(['jquery'], function($){
		$('.plugin_forever').click(function(){
			var obj = $('.daterange', $(this).parent().parent());
			if ($(this).prop('checked')) {
				obj.fadeOut();
			} else {
				obj.fadeIn();
			}
		});
		$('.plugin_forever').each(function(){
			var obj = $('.daterange', $(this).parent().parent());
			if ($(this).prop('checked')) {
				obj.hide();
			}
		});
	});
</script>
<?php  } else if($act == 'charge_sms_log') { ?>
<div class="main">
	<form action="" method="post">
		<div class="panel panel-default">
			<div class="table-responsive panel-body">
				<table class="table table-hover">
					<thead>
					<tr>
						<th width="400">商户名称</th>
						<th>原值</th>
						<th>新值</th>
						<th width="150">操作人</th>
						<th width="150">操作时间</th>
					</tr>
					</thead>
					<tbody>
					<?php  if($list) { ?>
					<?php  if(is_array($list)) { foreach($list as $li) { ?>
					<tr>
						<td><?php  echo $shop['title'];?></td>
						<td><?php  echo $li['total'];?></td>
						<td><?php  echo $li['new_total'];?></td>
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
<?php  } else if($act == 'send_sms_log') { ?>
<div class="main">
	<form action="" method="post">
		<div class="panel panel-default">
			<div class="table-responsive panel-body">
				<table class="table table-hover">
					<thead>
					<tr>
						<th width="400">商户名称</th>
						<th width="150">短信运营商</th>
						<th width="150">手机号</th>
						<th>消息内容</th>
						<th width="150">操作时间</th>
					</tr>
					</thead>
					<tbody>
					<?php  if($list) { ?>
					<?php  if(is_array($list)) { foreach($list as $li) { ?>
					<tr>
						<td><?php  echo $shop['title'];?></td>
						<td><?php  echo SupermanSms::$providers[$li['provider']]['title']?></td>
						<td><?php  echo $li['mobile'];?></td>
						<td style="white-space: normal;overflow: auto;"><?php  echo $li['content'];?></td>
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
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('footer', TEMPLATE_INCLUDEPATH)) : (include template('footer', TEMPLATE_INCLUDEPATH));?>