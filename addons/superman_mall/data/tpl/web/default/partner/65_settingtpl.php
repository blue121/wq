<?php defined('IN_IA') or exit('Access Denied');?><form class="form-horizontal form" action="" method="post" enctype="multipart/form-data">
	<div class="panel panel-default">
		<div class="panel-heading">基本设置</div>
		<div class="panel-body">
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">分销层级</label>
				<div class="col-sm-8 col-md-8 col-xs-12">
					<select class="form-control" name="setting[base][level]">
						<option value="0" <?php  if(!isset($setting['base']['level']) || $setting['base']['level'] == 0) { ?>selected<?php  } ?>>请选择</option>
						<option value="1" <?php  if(isset($setting['base']['level']) && $setting['base']['level'] == 1) { ?>selected<?php  } ?>>一级分销</option>
						<option value="2" <?php  if(isset($setting['base']['level']) && $setting['base']['level'] == 2) { ?>selected<?php  } ?>>二级分销</option>
						<option value="3" <?php  if(isset($setting['base']['level']) && $setting['base']['level'] == 3) { ?>selected<?php  } ?>>三级分销</option>
					</select>
					<span class="help-block">未选择分销层级时，分销订单将不生成佣金数据，分销商将无法分润</span>
				</div>
			</div>
			<!--<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">是否显示分销佣金</label>
				<div class="col-sm-6 col-md-8 col-xs-12">
					<div class="input-group">
						<label class="radio-inline">
							<input type="radio" name="setting[base][show_commission]" value="1" <?php  if(isset($setting['base']['show_commission']) && $setting['base']['show_commission'] == 1) { ?>checked<?php  } ?>> 显示
						</label>
						<label class="radio-inline">
							<input type="radio" name="setting[base][show_commission]" value="0" <?php  if(!isset($setting['base']['show_commission']) || $setting['base']['show_commission'] == 0) { ?>checked<?php  } ?>> 不显示
						</label>
					</div>
					<span class="help-block">当分销商打开商品页时，可选择是否显示分销佣金，选择显示时，分销商将可以查看该商品的各级分销佣金，需要修改单个商品显示时，请到商品编辑页单独设置</span>
				</div>
			</div>-->
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">是否显示快递信息</label>
				<div class="col-sm-6 col-md-8 col-xs-12">
					<div class="input-group">
						<label class="radio-inline">
							<input type="radio" name="setting[base][show_express]" value="2" <?php  if(isset($setting['base']['show_express']) && $setting['base']['show_express'] == 2) { ?>checked<?php  } ?>> 显示
						</label>
						<label class="radio-inline">
							<input type="radio" name="setting[base][show_express]" value="1" <?php  if(isset($setting['base']['show_express']) && $setting['base']['show_express'] == 1) { ?>checked<?php  } ?>> 显示部分
						</label>
						<label class="radio-inline">
							<input type="radio" name="setting[base][show_express]" value="0" <?php  if(!isset($setting['base']['show_express']) || $setting['base']['show_express'] == 0) { ?>checked<?php  } ?>> 不显示
						</label>
					</div>
					<span class="help-block">分销商查看推广订单时，是否显示订单快递信息，“显示部分”为隐藏部分快递单号，“不显示”为隐藏所有快递信息</span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">查看物流跟踪信息</label>
				<div class="col-sm-6 col-md-8 col-xs-12">
					<div class="input-group">
						<label class="radio-inline">
							<input type="radio" name="setting[base][express_info]" value="1" <?php  if(isset($setting['base']['express_info']) && $setting['base']['express_info'] == 1) { ?>checked<?php  } ?>> 允许
						</label>
						<label class="radio-inline">
							<input type="radio" name="setting[base][express_info]" value="0" <?php  if(!isset($setting['base']['express_info']) || $setting['base']['express_info'] == 0) { ?>checked<?php  } ?>> 不允许
						</label>
					</div>
					<span class="help-block">分销商查看推广订单时，是否允许查看订单的快递物流跟踪信息，仅限查看下级分销商订单，不能跨级查看</span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">是否开启佣金排行</label>
				<div class="col-sm-6 col-md-8 col-xs-12">
					<div class="input-group">
						<label class="radio-inline">
							<input type="radio" name="setting[base][rank]" value="1" <?php  if(isset($setting['base']['rank']) && $setting['base']['rank'] == 1) { ?>checked<?php  } ?>> 开启
						</label>
						<label class="radio-inline">
							<input type="radio" name="setting[base][rank]" value="0" <?php  if(!isset($setting['base']['rank']) || $setting['base']['rank'] == 0) { ?>checked<?php  } ?>> 关闭
						</label>
					</div>
					<span class="help-block">关闭佣金排行后，分销中心将不显示入口</span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">佣金排行条数</label>
				<div class="col-sm-8 col-xs-12">
					<input type="text" class="form-control" name="setting[base][rank_pagesize]" placeholder="" <?php  if(isset($setting['base']['rank_pagesize'])) { ?>value="<?php  echo $setting['base']['rank_pagesize'];?>"<?php  } ?>>
					<span class="help-block">开启佣金排行后，可控制佣金排行榜输出数据条数，默认为空输出10条</span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 control-label">佣金排行顶部图片</label>
				<div class="col-sm-6 col-md-8 col-xs-12">
					<?php echo tpl_form_field_image('setting[base][top][img]', isset($setting['base']['top']['img'])?$setting['base']['top']['img']:'')?>
					<span class="help-block">推荐尺寸：360x200像素</span>
					<input type="text" class="form-control" name="setting[base][top][url]" placeholder="http://" <?php  if(isset($setting['base']['top']['url'])) { ?>value="<?php  echo $setting['base']['top']['url'];?>"<?php  } ?>>
					<span class="help-block">图片链接，可以为空，格式：http://xxx</span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 control-label">分销商注册顶部图片</label>
				<div class="col-sm-6 col-md-8 col-xs-12">
					<?php echo tpl_form_field_image('setting[base][top][reg_img]', isset($setting['base']['top']['reg_img'])?$setting['base']['top']['reg_img']:'')?>
					<span class="help-block">推荐尺寸：360x200像素</span>
					<input type="text" class="form-control" name="setting[base][top][reg_url]" placeholder="http://" <?php  if(isset($setting['base']['top']['reg_url'])) { ?>value="<?php  echo $setting['base']['top']['reg_url'];?>"<?php  } ?>>
					<span class="help-block">图片链接，可以为空，格式：http://xxx</span>
				</div>
			</div>
			<div class="alert alert-info">
				<i class="fa fa-exclamation-circle"></i> 商户编辑商品分销佣金属性时，可控制佣金最大值，防止商户提交恶意佣金数据，超过设置最大值无法提交更新
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 control-label">一级分销佣金</label>
				<div class="col-sm-6 col-md-8 col-xs-12">
					<div class="input-group" style="margin-bottom: .5rem">
						<span class="input-group-addon">最高</span>
						<input type="number" step="0.01" class="form-control" value="<?php  echo $setting['base']['commission1_rate_max'];?>" name="setting[base][commission1_rate_max]">
						<span class="input-group-addon">%</span>
					</div>
					<div class="input-group">
						<span class="input-group-addon">最高</span>
						<input type="number" step="0.01" class="form-control" value="<?php  echo $setting['base']['commission1_value_max'];?>" name="setting[base][commission1_value_max]">
						<span class="input-group-addon">元</span>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 control-label">二级分销佣金</label>
				<div class="col-sm-6 col-md-8 col-xs-12">
					<div class="input-group" style="margin-bottom: .5rem">
						<span class="input-group-addon">最高</span>
						<input type="number" step="0.01" class="form-control" value="<?php  echo $setting['base']['commission2_rate_max'];?>" name="setting[base][commission2_rate_max]">
						<span class="input-group-addon">%</span>
					</div>
					<div class="input-group">
						<span class="input-group-addon">最高</span>
						<input type="number" step="0.01" class="form-control" value="<?php  echo $setting['base']['commission2_value_max'];?>" name="setting[base][commission2_value_max]">
						<span class="input-group-addon">元</span>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 control-label">三级分销佣金</label>
				<div class="col-sm-6 col-md-8 col-xs-12">
					<div class="input-group" style="margin-bottom: .5rem">
						<span class="input-group-addon">最高</span>
						<input type="number" step="0.01" class="form-control" value="<?php  echo $setting['base']['commission3_rate_max'];?>" name="setting[base][commission3_rate_max]">
						<span class="input-group-addon">%</span>
					</div>
					<div class="input-group">
						<span class="input-group-addon">最高</span>
						<input type="number" step="0.01" class="form-control" value="<?php  echo $setting['base']['commission3_value_max'];?>" name="setting[base][commission3_value_max]">
						<span class="input-group-addon">元</span>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 control-label">分销商优惠价</label>
				<div class="col-sm-6 col-md-8 col-xs-12">
					<div class="input-group" style="margin-bottom: .5rem">
						<span class="input-group-addon">最低</span>
						<input type="number" step="0.01" class="form-control" value="<?php  echo $setting['base']['discount_rate_min'];?>" name="setting[base][discount_rate_min]">
						<span class="input-group-addon">%</span>
					</div>
					<div class="input-group">
						<span class="input-group-addon">最高</span>
						<input type="number" step="0.01" class="form-control" value="<?php  echo $setting['base']['discount_value_max'];?>" name="setting[base][discount_value_max]">
						<span class="input-group-addon">元</span>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">分销商设置</div>
		<div class="panel-body" style="line-height: 20px;">
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 control-label">加入条件</label>
				<div class="col-sm-6 col-md-8 col-xs-12">
					<div class="row" style="margin-bottom: .5rem">
						<div class="col-sm-12 col-md-12 col-xs-12" style="padding: 0">
							<div class="input-group">
								<label class="radio-inline">
									<input type="radio" name="setting[partner][join_condition][type]" value="1" <?php  if(!isset($setting['partner']['join_condition']['type']) || $setting['partner']['join_condition']['type'] == 1) { ?>checked<?php  } ?>> 无条件（关注公众号即可成为分销商）
								</label>
							</div>
						</div>
					</div>
					<div class="row" style="margin-bottom: .5rem">
						<div class="col-sm-12 col-md-12 col-xs-12" style="padding: 0">
							<div class="input-group">
								<label class="radio-inline">
									<input type="radio" name="setting[partner][join_condition][type]" value="2" <?php  if(isset($setting['partner']['join_condition']['type']) && $setting['partner']['join_condition']['type'] == 2) { ?>checked<?php  } ?>> 申请加入
								</label>
							</div>
						</div>
					</div>
					<div class="row" style="margin-bottom: .5rem">
						<div class="col-sm-6 col-md-2 col-xs-12" style="padding: 0">
							<div class="input-group">
								<label class="radio-inline">
									<input type="radio" name="setting[partner][join_condition][type]" value="3" <?php  if(isset($setting['partner']['join_condition']['type']) && $setting['partner']['join_condition']['type'] == 3) { ?>checked<?php  } ?>> 订单总数
								</label>
							</div>
						</div>
						<div class="col-sm-6 col-md-10 col-xs-12" style="padding: 0">
							<input class="form-control" name="setting[partner][join_condition][limit]" type="text" <?php  if(isset($setting['partner']['join_condition']['limit']) && isset($setting['partner']['join_condition']['type']) && $setting['partner']['join_condition']['type'] == 3) { ?>value="<?php  echo $setting['partner']['join_condition']['limit'];?>"<?php  } else { ?>disabled="disabled"<?php  } ?>>
						</div>
					</div>
					<div class="row" style="margin-bottom: .5rem">
						<div class="col-sm-6 col-md-2 col-xs-12" style="padding: 0">
							<div class="input-group">
								<label class="radio-inline">
									<input type="radio" name="setting[partner][join_condition][type]" value="4" <?php  if(isset($setting['partner']['join_condition']['type']) && $setting['partner']['join_condition']['type'] == 4) { ?>checked<?php  } ?>> 订单总金额
								</label>
							</div>
						</div>
						<div class="col-sm-6 col-md-10 col-xs-12" style="padding: 0">
							<input class="form-control" name="setting[partner][join_condition][limit]" type="text" <?php  if(isset($setting['partner']['join_condition']['limit']) && isset($setting['partner']['join_condition']['type']) && $setting['partner']['join_condition']['type'] == 4) { ?>value="<?php  echo $setting['partner']['join_condition']['limit'];?>"<?php  } else { ?>disabled="disabled"<?php  } ?> >
						</div>
					</div>
					<span class="help-block">
						订单数和订单金额条件，仅统计已完成状态的订单
					</span>
					<span class="help-block" style="color: red;">每10分钟检查一次</span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 control-label">下线条件</label>
				<div class="col-sm-6 col-md-8 col-xs-12">
					<div class="row" style="margin-bottom: .5rem">
						<div class="col-sm-12 col-md-12 col-xs-12" style="padding: 0">
							<div class="input-group">
								<label class="radio-inline">
									<input type="radio" name="setting[partner][downline]" value="1" <?php  if(!isset($setting['partner']['downline']) || $setting['partner']['downline'] == 1) { ?>checked<?php  } ?>> 点击邀请人分享链接
								</label>
							</div>
						</div>
					</div>
					<div class="row" style="margin-bottom: .5rem">
						<div class="col-sm-12 col-md-12 col-xs-12" style="padding: 0">
							<div class="input-group">
								<label class="radio-inline">
									<input type="radio" name="setting[partner][downline]" value="2" <?php  if(isset($setting['partner']['downline']) && $setting['partner']['downline'] == 2) { ?>checked<?php  } ?>> 首次下单——订单状态：已收货（待评价）
								</label>
							</div>
						</div>
					</div>
					<div class="row" style="margin-bottom: .5rem">
						<div class="col-sm-12 col-md-12 col-xs-12" style="padding: 0">
							<div class="input-group">
								<label class="radio-inline">
									<input type="radio" name="setting[partner][downline]" value="3" <?php  if(isset($setting['partner']['downline']) && $setting['partner']['downline'] == 3) { ?>checked<?php  } ?>> 首次下单——订单状态：已完成（已评价）
								</label>
							</div>
						</div>
					</div>
					<span class="help-block"></span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">默认分销等级</label>
				<div class="col-sm-8 col-md-8 col-xs-12">
					<select class="form-control" name="setting[partner][groupid]">
						<option value="0" <?php  if(!isset($setting['partner']['groupid']) || $setting['partner']['groupid'] == 0) { ?>selected<?php  } ?>>请选择</option>
						<?php  if($group_list) { ?>
						<?php  if(is_array($group_list)) { foreach($group_list as $group) { ?>
						<option value="<?php  echo $group['id'];?>" <?php  if(isset($setting['partner']['groupid']) && $setting['partner']['groupid'] == $group['id']) { ?>selected<?php  } ?>><?php  echo $group['title'];?></option>
						<?php  } } ?>
						<?php  } ?>
					</select>
					<span class="help-block">新加入分销商默认所属分销等级，不选择时分销商没有分销等级，此时需要设置每个商品的自定义佣金</span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">是否需要审核</label>
				<div class="col-sm-6 col-md-8 col-xs-12">
					<div class="input-group">
						<label class="radio-inline">
							<input type="radio" name="setting[partner][check]" value="1" <?php  if(isset($setting['partner']['check']) && $setting['partner']['check'] == 1) { ?>checked<?php  } ?>> 需要
						</label>
						<label class="radio-inline">
							<input type="radio" name="setting[partner][check]" value="0" <?php  if(!isset($setting['partner']['check']) || $setting['partner']['check'] == 0) { ?>checked<?php  } ?>> 不需要
						</label>
					</div>
					<span class="help-block">分销商加入后，是否需要管理员审核，选择需要审核时，必须审核通过后，才正式成为分销商</span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">是否需要完善资料</label>
				<div class="col-sm-6 col-md-8 col-xs-12">
					<div class="input-group">
						<label class="radio-inline">
							<input type="radio" name="setting[partner][member_info]" value="1" <?php  if(isset($setting['partner']['member_info']) && $setting['partner']['member_info'] == 1) { ?>checked<?php  } ?>> 需要
						</label>
						<label class="radio-inline">
							<input type="radio" name="setting[partner][member_info]" value="0" <?php  if(!isset($setting['partner']['member_info']) || $setting['partner']['member_info'] == 0) { ?>checked<?php  } ?>> 不需要
						</label>
					</div>
					<span class="help-block">分销商加入和提现等操作时，是否强制完善资料</span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">佣金结算</label>
				<div class="col-sm-6 col-md-8 col-xs-12">
					<div class="input-group">
						<label class="radio-inline">
							<input type="radio" name="setting[partner][commission_settlement]" value="1" <?php  if(isset($setting['partner']['commission_settlement']) && $setting['partner']['commission_settlement'] == 1) { ?>checked<?php  } ?>> 自动
						</label>
						<label class="radio-inline">
							<input type="radio" name="setting[partner][commission_settlement]" value="0" <?php  if(!isset($setting['partner']['commission_settlement']) || $setting['partner']['commission_settlement'] == 0) { ?>checked<?php  } ?>> 手动
						</label>
					</div>
					<span class="help-block">
						自动结算佣金：当结算周期条件满足后，分销商佣金自动进入分销商可提现佣金；
					</span>
					<span class="help-block">
						手动结算佣金：进入佣金管理列表，选择佣金结算并提交，需人工参与操作佣金结算；
					</span>
					<span class="help-block" style="color: red;">每5分钟检查一次</span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 control-label">结算周期</label>
				<div class="col-sm-6 col-md-8 col-xs-12">
					<div class="input-group">
						<input type="text" class="form-control" name="setting[partner][commission_interval]" <?php  if(isset($setting['partner']['commission_interval'])) { ?>value="<?php  echo $setting['partner']['commission_interval'];?>"<?php  } ?>>
						<span class="input-group-addon">天</span>
					</div>
					<span class="help-block">订单完成x天后，佣金可以结算到分销商可提现佣金账户，默认为空不限制结算周期，即订单状态为已完成时生成结算佣金</span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 control-label">提现额度</label>
				<div class="col-sm-6 col-md-8 col-xs-12">
					<div class="input-group">
						<input type="text" class="form-control" name="setting[partner][getcash_limit]" <?php  if(isset($setting['partner']['getcash_limit'])) { ?>value="<?php  echo $setting['partner']['getcash_limit'];?>"<?php  } ?>>
						<span class="input-group-addon">元</span>
					</div>
					<span class="help-block">分销商提现最低金额限制，未达提现额度时，不能申请提现</span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 control-label">分销等级变更</label>
				<div class="col-sm-6 col-md-8 col-xs-12">
					<div class="row" style="margin-bottom: .5rem">
						<div class="col-sm-12 col-md-12 col-xs-12" style="padding: 0">
							<div class="input-group">
								<label class="radio-inline">
									<input type="radio" name="setting[partner][group_upgrade]" value="0" <?php  if(!isset($setting['partner']['group_upgrade']) || $setting['partner']['group_upgrade'] == 0) { ?>checked<?php  } ?>> 不自动变更
								</label>
							</div>
						</div>
					</div>
					<div class="row" style="margin-bottom: .5rem">
						<div class="col-sm-12 col-md-12 col-xs-12" style="padding: 0">
							<div class="input-group">
								<label class="radio-inline">
									<input type="radio" name="setting[partner][group_upgrade]" value="1" <?php  if(isset($setting['partner']['group_upgrade']) && $setting['partner']['group_upgrade'] == 1) { ?>checked<?php  } ?>> 根据等级变更条件自动升降
								</label>
							</div>
						</div>
					</div>
					<div class="row" style="margin-bottom: .5rem">
						<div class="col-sm-12 col-md-12 col-xs-12" style="padding: 0">
							<div class="input-group">
								<label class="radio-inline">
									<input type="radio" name="setting[partner][group_upgrade]" value="2" <?php  if(isset($setting['partner']['group_upgrade']) && $setting['partner']['group_upgrade'] == 2) { ?>checked<?php  } ?>> 根据等级变更条件只升不降
								</label>
							</div>
						</div>
					</div>
					<span class="help-block">不自动变更：分销商等级变更只能由管理员编辑修改；</span>
					<span class="help-block">根据等级变更条件自动升降：系统根据等级变更条件，自动更新分销商等级，符合条件时可升级，反之降级；</span>
					<span class="help-block">根据等级变更条件只升不降：系统根据等级变更条件，自动更新分销商等级，符合条件时可升级，但不会降级；</span>
					<span class="help-block" style="color: red;">每小时检查一次</span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 control-label">分销等级变更条件</label>
				<div class="col-sm-6 col-md-8 col-xs-12">
					<?php  if(is_array($group_condition_titles)) { foreach($group_condition_titles as $key => $value) { ?>
					<div class="row" style="margin-bottom: .5rem">
						<div class="col-sm-12 col-md-12 col-xs-12" style="padding: 0">
							<div class="input-group">
								<label class="radio-inline">
									<input type="radio" name="setting[partner][group_condition]" value="<?php  echo $key;?>" <?php  if(!isset($setting['partner']['group_condition']) && $key == 1 || isset($setting['partner']['group_condition']) && $setting['partner']['group_condition'] == $key) { ?>checked<?php  } ?>> <?php  echo $value;?>
								</label>
							</div>
						</div>
					</div>
					<?php  } } ?>
					<span class="help-block">设置分销等级升级或降级条件后，必须选择等级变更条件，默认为分销订单总数</span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">邀请好友提示内容</label>
				<div class="col-sm-8 col-xs-12">
					<textarea  rows="3" class="form-control" name="setting[partner][invite_text]"><?php  if(isset($setting['partner']['invite_text'])) { ?><?php  echo $setting['partner']['invite_text'];?><?php  } ?></textarea>
					<div class="row" id="super_var_wrap2">
						<div class="col-xs-6 col-sm-6 col-md-3" style="color: #000"><strong>{平台}</strong> 表示公众号的名称 <a data-content="{平台}" href="javascript:;" title="点击复制">点击复制</a></div>
					</div>
					<script>
						require(['jquery', 'util'], function($, u){
							$('#super_var_wrap2 a').each(function(){
								var t = this;
								u.clip(t, $(t).attr('data-content'));
							});
						});
					</script>
					<span class="help-block">分销商好友打开邀请页时，可自定义提示内容</span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">分享标题</label>
				<div class="col-sm-8 col-xs-12">
					<input type="text" class="form-control" name="setting[partner][share_title]" <?php  if(isset($setting['partner']['share_title'])) { ?>value="<?php  echo $setting['partner']['share_title'];?>"<?php  } ?>>
					<span class="help-block">邀请好友分享标题，推荐字数13个汉字</span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 control-label">分享图片</label>
				<div class="col-sm-6 col-md-8 col-xs-12">
					<?php echo tpl_form_field_image('setting[partner][share_img]', isset($setting['partner']['share_img'])?$setting['partner']['share_img']:'')?>
					<span class="help-block">邀请好友分享图片，推荐尺寸：200x200像素</span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">分享描述</label>
				<div class="col-sm-8 col-xs-12">
					<textarea  rows="3" class="form-control" name="setting[partner][share_desc]"><?php  if(isset($setting['partner']['share_desc'])) { ?><?php  echo $setting['partner']['share_desc'];?><?php  } ?></textarea>
					<span class="help-block">邀请好友分享描述内容</span>
				</div>
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">文字自定义</div>
		<div class="panel-body">
			<div class="alert alert-info">
				<i class="fa fa-exclamation-circle"></i> 分销页面文字自定义，如需修改请在对应文字后面填写，输入框为空时，使用默认值
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">内部价</label>
				<div class="col-sm-8 col-xs-12">
					<input type="text" class="form-control" name="setting[text][innerprice]" placeholder="内部价" <?php  if(isset($setting['text']['innerprice'])) { ?>value="<?php  echo $setting['text']['innerprice'];?>"<?php  } ?>>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">我要分销</label>
				<div class="col-sm-8 col-xs-12">
					<input type="text" class="form-control" name="setting[text][distribution]" placeholder="我要分销" <?php  if(isset($setting['text']['distribution'])) { ?>value="<?php  echo $setting['text']['distribution'];?>"<?php  } ?>>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">佣金</label>
				<div class="col-sm-8 col-xs-12">
					<input type="text" class="form-control" name="setting[text][commission]" placeholder="佣金" <?php  if(isset($setting['text']['commission'])) { ?>value="<?php  echo $setting['text']['commission'];?>"<?php  } ?>>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">自己卖出</label>
				<div class="col-sm-8 col-xs-12">
					<input type="text" class="form-control" name="setting[text][self_sell]" placeholder="自己卖出" <?php  if(isset($setting['text']['self_sell'])) { ?>value="<?php  echo $setting['text']['self_sell'];?>"<?php  } ?>>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">直接邀请成员买/卖</label>
				<div class="col-sm-8 col-xs-12">
					<input type="text" class="form-control" name="setting[text][invite_direct]" placeholder="直接邀请成员买/卖" <?php  if(isset($setting['text']['invite_direct'])) { ?>value="<?php  echo $setting['text']['invite_direct'];?>"<?php  } ?>>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">间接邀请成员买/卖</label>
				<div class="col-sm-8 col-xs-12">
					<input type="text" class="form-control" name="setting[text][invite_indirect]" placeholder="间接邀请成员买/卖" <?php  if(isset($setting['text']['invite_indirect'])) { ?>value="<?php  echo $setting['text']['invite_indirect'];?>"<?php  } ?>>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">分销中心</label>
				<div class="col-sm-8 col-xs-12">
					<input type="text" class="form-control" name="setting[text][partner_center]" placeholder="分销中心" <?php  if(isset($setting['text']['partner_center'])) { ?>value="<?php  echo $setting['text']['partner_center'];?>"<?php  } ?>>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">赚佣金</label>
				<div class="col-sm-8 col-xs-12">
					<input type="text" class="form-control" name="setting[text][get_commission]" placeholder="赚佣金" <?php  if(isset($setting['text']['get_commission'])) { ?>value="<?php  echo $setting['text']['get_commission'];?>"<?php  } ?>>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">累计佣金</label>
				<div class="col-sm-8 col-xs-12">
					<input type="text" class="form-control" name="setting[text][commission_total]" placeholder="累计佣金" <?php  if(isset($setting['text']['commission_total'])) { ?>value="<?php  echo $setting['text']['commission_total'];?>"<?php  } ?>>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">已提佣金</label>
				<div class="col-sm-8 col-xs-12">
					<input type="text" class="form-control" name="setting[text][commission_received]" placeholder="已提佣金" <?php  if(isset($setting['text']['commission_received'])) { ?>value="<?php  echo $setting['text']['commission_received'];?>"<?php  } ?>>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">可提佣金</label>
				<div class="col-sm-8 col-xs-12">
					<input type="text" class="form-control" name="setting[text][commission_balance]" placeholder="可提佣金" <?php  if(isset($setting['text']['commission_balance'])) { ?>value="<?php  echo $setting['text']['commission_balance'];?>"<?php  } ?>>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">邀请人数</label>
				<div class="col-sm-8 col-xs-12">
					<input type="text" class="form-control" name="setting[text][invite_total]" placeholder="邀请人数" <?php  if(isset($setting['text']['invite_total'])) { ?>value="<?php  echo $setting['text']['invite_total'];?>"<?php  } ?>>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">订单数</label>
				<div class="col-sm-8 col-xs-12">
					<input type="text" class="form-control" name="setting[text][order_total]" placeholder="订单数" <?php  if(isset($setting['text']['order_total'])) { ?>value="<?php  echo $setting['text']['order_total'];?>"<?php  } ?>>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">一级人数</label>
				<div class="col-sm-8 col-xs-12">
					<input type="text" class="form-control" name="setting[text][downline1]" placeholder="一级人数" <?php  if(isset($setting['text']['downline1'])) { ?>value="<?php  echo $setting['text']['downline1'];?>"<?php  } ?>>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">二级人数</label>
				<div class="col-sm-8 col-xs-12">
					<input type="text" class="form-control" name="setting[text][downline2]" placeholder="二级人数" <?php  if(isset($setting['text']['downline2'])) { ?>value="<?php  echo $setting['text']['downline2'];?>"<?php  } ?>>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">三级人数</label>
				<div class="col-sm-8 col-xs-12">
					<input type="text" class="form-control" name="setting[text][downline3]" placeholder="三级人数" <?php  if(isset($setting['text']['downline3'])) { ?>value="<?php  echo $setting['text']['downline3'];?>"<?php  } ?>>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">本单奖励</label>
				<div class="col-sm-8 col-xs-12">
					<input type="text" class="form-control" name="setting[text][order_reward]" placeholder="本单奖励" <?php  if(isset($setting['text']['order_reward'])) { ?>value="<?php  echo $setting['text']['order_reward'];?>"<?php  } ?>>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">申请加入</label>
				<div class="col-sm-8 col-xs-12">
					<input type="text" class="form-control" name="setting[text][apply_join]" placeholder="申请加入" <?php  if(isset($setting['text']['apply_join'])) { ?>value="<?php  echo $setting['text']['apply_join'];?>"<?php  } ?>>
				</div>
			</div>
			<div class="alert alert-info">
				<i class="fa fa-exclamation-circle"></i> 分销中心首页九宫格文字
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">我的团队</label>
				<div class="col-sm-8 col-xs-12">
					<input type="text" class="form-control" name="setting[text][myteam]" placeholder="我的团队" <?php  if(isset($setting['text']['myteam'])) { ?>value="<?php  echo $setting['text']['myteam'];?>"<?php  } ?>>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">邀请好友</label>
				<div class="col-sm-8 col-xs-12">
					<input type="text" class="form-control" name="setting[text][invite_friend]" placeholder="邀请好友" <?php  if(isset($setting['text']['invite_friend'])) { ?>value="<?php  echo $setting['text']['invite_friend'];?>"<?php  } ?>>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">佣金排行</label>
				<div class="col-sm-8 col-xs-12">
					<input type="text" class="form-control" name="setting[text][commission_rank]" placeholder="佣金排行" <?php  if(isset($setting['text']['commission_rank'])) { ?>value="<?php  echo $setting['text']['commission_rank'];?>"<?php  } ?>>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">推广订单</label>
				<div class="col-sm-8 col-xs-12">
					<input type="text" class="form-control" name="setting[text][order]" placeholder="推广订单" <?php  if(isset($setting['text']['order'])) { ?>value="<?php  echo $setting['text']['order'];?>"<?php  } ?>>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">提现记录</label>
				<div class="col-sm-8 col-xs-12">
					<input type="text" class="form-control" name="setting[text][getcash_display]" placeholder="提现记录" <?php  if(isset($setting['text']['getcash_display'])) { ?>value="<?php  echo $setting['text']['getcash_display'];?>"<?php  } ?>>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">我要提现</label>
				<div class="col-sm-8 col-xs-12">
					<input type="text" class="form-control" name="setting[text][getcash_apply]" placeholder="我要提现" <?php  if(isset($setting['text']['getcash_apply'])) { ?>value="<?php  echo $setting['text']['getcash_apply'];?>"<?php  } ?>>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">我的海报</label>
				<div class="col-sm-8 col-xs-12">
					<input type="text" class="form-control" name="setting[text][my_poster]" placeholder="我的海报" <?php  if(isset($setting['text']['my_poster'])) { ?>value="<?php  echo $setting['text']['my_poster'];?>"<?php  } ?>>
				</div>
			</div>
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-12">
			<button type="submit" name="submit" value="yes" class="btn btn-primary">提交</button>
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>">
		</div>
	</div>
</form>
<script>
	require(['jquery'], function($){
		$('input[name="setting[partner][join_condition][type]"]').click(function(){
			$('input[name="setting[partner][join_condition][limit]"]').attr("disabled",true);
			$('input[name="setting[partner][join_condition][limit]"]', $(this).parent().parent().parent().next()).removeAttr("disabled");
		});
	});
</script>