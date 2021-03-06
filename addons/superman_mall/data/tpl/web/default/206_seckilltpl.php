<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('header', TEMPLATE_INCLUDEPATH)) : (include template('header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/shop-nav', TEMPLATE_INCLUDEPATH)) : (include template('common/shop-nav', TEMPLATE_INCLUDEPATH));?>
<ul class="nav nav-tabs">
	<li <?php  if($act == 'display') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('seckill');?>">秒杀</a></li>
	<li <?php  if($act == 'post' && !$_GPC['id']) { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('seckill', array('act' => 'post'));?>">添加秒杀</a></li>
	<?php  if($_GPC['id']) { ?><li <?php  if($act == 'post') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('seckill', array('act' => 'post', 'id' => $id));?>">编辑秒杀</a></li><?php  } ?>
</ul>
<?php  if($act=='display') { ?>
<div class="panel panel-info">
	<div class="panel-heading">筛选</div>
	<div class="panel-body">
		<form action="" method="post" class="form-horizontal" role="form">
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 control-label">商品名称</label>
				<div class="col-sm-8 col-md-8 col-lg-6 col-xs-12">
					<input class="form-control" name="title" type="text" value="<?php  echo $_GPC['title'];?>">
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 control-label">商品分类</label>
				<div class="col-xs-12 col-sm-4 col-md-3 col-lg-2">
					<select class="form-control" id="pcid" name="pcid">
						<option value="0">请选择一级分类</option>
						<?php  if($pcids) { ?>
						<?php  if(is_array($pcids)) { foreach($pcids as $pcid) { ?>
						<option value="<?php  echo $pcid['id'];?>" <?php  if($pcid['id']==$_GPC['pcid']) { ?> selected<?php  } ?>>
						<?php  echo $pcid['title'];?>
						</option>
						<?php  } } ?>
						<?php  } ?>
					</select>
				</div>
				<div class="col-xs-12 col-sm-4 col-md-3 col-lg-2">
					<select class="form-control" id="cid" name="cid">
						<option value="0">请选择二级分类</option>
					</select>
				</div>
				<div class="col-xs-12 col-sm-4 col-md-3 col-lg-2">
					<select class="form-control" id="ccid" name="ccid">
						<option value="0">请选择三级分类</option>
					</select>
				</div>
				<script>
					require(['jquery', 'jquery.ui'], function($){
						$('#pcid').change(function () {
							var html = '<option value="0">请选择二级分类</option>';
							var pcid = $(this).val();
							if (pcid == 0) {
								$('#cid').html(html);
								$('#ccid').html('<option value="0">请选择三级分类</option>');
								return;
							}
							var cid = '<?php  echo $_GPC["cid"];?>';
							$.ajax({
								url:'<?php  echo $this->createWebUrl("item", array("act" => "getcate"))?>',
								type: 'post',
								data: 'cid='+pcid,
								dataType: 'json',
								success: function(resp){
									if (resp.length > 0) {
										for(var i = 0; i< resp.length; i++){
											if (cid == resp[i]['id']) {
												html += '<option value="'+resp[i]['id']+'" selected>'+resp[i]['title']+'</option>';
											} else {
												html += '<option value="'+resp[i]['id']+'" >'+resp[i]['title']+'</option>';

											}
										}
									}
									$('#cid').html(html).trigger('change');
								}
							});
						});
						$('#pcid').trigger('change');
						$('#cid').change(function () {
							var html = '<option value="0">请选择三级分类</option>';
							var cid = $(this).val();
							if (cid == 0) {
								$('#ccid').html(html);
								return;
							}
							var ccid = '<?php  echo $_GPC["ccid"];?>';
							$.ajax({
								url:'<?php  echo $this->createWebUrl("item", array("act" => "getcate"))?>',
								type: 'post',
								data: 'cid='+cid,
								dataType: 'json',
								success: function(resp){
									if (resp.length > 0) {
										for(var i = 0; i< resp.length; i++){
											if (ccid == resp[i]['id']) {
												html += '<option value="'+resp[i]['id']+'" selected>'+resp[i]['title']+'</option>';

											} else {
												html += '<option value="'+resp[i]['id']+'">'+resp[i]['title']+'</option>';
											}
										}
									}
									$('#ccid').html(html)
								}
							});
						});
					})
				</script>
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
						<th width="100">封面图</th>
						<th>标题</th>
						<th width="100">秒杀价</th>
						<th width="100">状态</th>
						<th width="100">库存</th>
						<th width="90">销量</th>
						<th width="100" class="text-right">操作</th>
					</tr>
				</thead>
				<tbody>
					<?php  if($list) { ?>
					<?php  if(is_array($list)) { foreach($list as $li) { ?>
					<tr>
                        <td>
                            <input type="text" class="form-control text-center" name="position[<?php  echo $li['id'];?>]" value="<?php  echo $li['position'];?>">
                        </td>
						<td style="position: relative">
							<img src="<?php  echo tomedia($li['cover'])?>" onerror="this.src='<?php  echo $this->superman_placeholder?>'" height="60px"/>
						</td>
						<td><?php  echo $li['title'];?></td>
						<td><?php  echo $li['price'];?>元</td>
						<td>
							<?php  if($li['status'] != "1") { ?>
							<div style="margin-bottom: 5px">
								<?php  if($li['status'] == "0") { ?>
								<span class="label label-danger">下架</span>
								<?php  } else if($li['status'] == "2") { ?>
								<span class="label label-default">禁售</span>
								<?php  } ?>
							</div>
							<?php  } ?>
							<div><?php  echo $li['status_title'];?></div>
						</td>
						<td><?php  echo $li['total'];?></td>
						<td><?php  echo $li['sales'];?></td>
						<td class="text-right">
							<div class="btn-group">
								<a href="<?php  echo $this->createWebUrl('seckill', array('act' => 'post', 'id' => $li['id']))?>" title="编辑" class="btn btn-default btn-sm">
									<i class="fa fa-edit"></i>
								</a>
								<a onclick="return confirm('此操作不可恢复，确认吗？'); return false;" href="<?php  echo $this->createWebUrl('seckill', array('act' => 'delete', 'id' => $li['id']))?>" title="删除" class="btn btn-default btn-sm">
									<i class="fa fa-times"></i>
								</a>
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
	<input name="submit" type="submit" value="更新排序" class="btn btn-primary col-lg-1" />
	<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
</form>
<?php  } else if($act == 'post') { ?>
<style>
	.star {
		color: red;
		margin-right: 5px;
		font-weight: bold;
	}
	ul, li {
		list-style: none;
		margin: 0;
		padding: 0;
	}
	.item_spec {
		padding-bottom: 10px;
	}
	.item_attr, .del_item_attr{
		margin-left: -15px;
		margin-right: -15px;
	}
	.item_value {
		float: left;
		margin: 10px 10px 0 0;
	}
	.item_value label {
		font-weight: normal;
		margin-bottom: 0;
	}
	#item_sku_wrap {
		padding:0;
	}
	#item_sku_wrap th, #item_sku_wrap td {
		text-align: center;
	}
	.del_item_attr {
		line-height: 30px;
		color: #f00;
		cursor: pointer;
		margin-left: 30px;
	}
	.qrcode_wrap {
		position: fixed;
		top: 10rem;
		right: 2rem;
	}
	.qrcode_wrap img {
		width:11rem;
	}
	.qrcode_wrap > span {
		display: block;
		text-align: center;
	}
</style>
<?php  if($id > 0) { ?>
<div class="qrcode_wrap">
	<img src="<?php  echo $this->createWebUrl('qrcode', array('content' => urlencode($itemurl)))?>" onerror="this.src='<?php  echo $this->superman_placeholder?>'"/>
	<span>微信扫一扫</span>
</div>
<?php  } ?>
<form class="form-horizontal form" action="" method="post" enctype="multipart/form-data">
	<input type="hidden" name="id" value="<?php  echo $id;?>">
	<input type="hidden" name="copyid" value="<?php  echo $copyid;?>">
	<div class="panel panel-default">
		<div class="panel-heading">
			基本属性
		</div>
		<div class="panel-body">
			<?php  if($id > 0) { ?>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">访问地址</label>
				<div class="col-sm-8 col-md-8 col-xs-12">
					<div class="form-control-static">
						<a href="<?php  echo $itemurl;?>" target="_blank"><?php  echo $itemurl;?></a>
					</div>
					<span class="help-block">本网址为当前商品的唯一链接，可以拷贝到其他地方使用</span>
				</div>
			</div>
			<?php  } ?>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">
					<span class="star">*</span>商品标题
				</label>
				<div class="col-sm-8 col-md-8 col-xs-12">
					<input type="text" class="form-control" name="title" value="<?php  echo $item['title'];?>">
					<span class="help-block">最多30个字符</span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">
					<span class="star">*</span>商品分类
				</label>
				<div class="col-xs-12 col-sm-4 col-md-3 col-lg-3">
					<select class="form-control" id="pcid" name="pcid" data-pcid="<?php  echo $item['pcid'];?>">
						<option value="0">请选择一级分类</option>
						<?php  if($pcids) { ?>
						<?php  if(is_array($pcids)) { foreach($pcids as $pcid) { ?>
						<option value="<?php  echo $pcid['id'];?>" <?php  if(isset($item['pcid']) && $item['pcid'] == $pcid['id']) { ?>selected="selected"<?php  } ?>>
						<?php  echo $pcid['title'];?>
						</option>
						<?php  } } ?>
						<?php  } ?>
					</select>
				</div>
				<div class="col-xs-12 col-sm-4 col-md-3 col-lg-3">
					<select class="form-control" id="cid" name="cid" data-cid="<?php  echo $item['cid'];?>">
						<option value="0">请选择二级分类</option>
					</select>
				</div>
				<div class="col-xs-12 col-sm-4 col-md-3 col-lg-3">
					<select class="form-control" id="ccid" name="ccid" data-ccid="<?php  echo $item['ccid'];?>">
						<option value="0">请选择三级分类</option>
					</select>
				</div>
				<script>
					require(['jquery', 'jquery.ui'], function($){
						$('#pcid').change(function () {
							var html = '<option value="0">请选择二级分类</option>';
							var pcid = $(this).val();
							if (pcid == 0) {
								$('#cid').html(html);
								$('#ccid').html('<option value="0">请选择三级分类</option>');
								return;
							}
							$.ajax({
								url:'<?php  echo $this->createWebUrl("item", array("act" => "getcate"))?>',
								type: 'post',
								data: 'cid='+pcid,
								dataType: 'json',
								success: function(resp){
									if (resp.length > 0) {
										var cid = $('#cid').attr('data-cid');
										var selected = '';
										for(var i = 0; i< resp.length; i++){
											if (cid == resp[i]['id']) {
												selected = 'selected';
											} else {
												selected = '';
											}
											html += '<option '+selected+' value="'+resp[i]['id']+'">'+resp[i]['title']+'</option>';
										}
									}
									$('#cid').html(html).trigger('change');
								}
							});
						});
						$('#pcid').trigger('change');
						$('#cid').change(function () {
							var html = '<option value="0">请选择三级分类</option>';
							var cid = $(this).val();
							if (cid == 0) {
								$('#ccid').html(html);
								return;
							}
							$.ajax({
								url:'<?php  echo $this->createWebUrl("item", array("act" => "getcate"))?>',
								type: 'post',
								data: 'cid='+cid,
								dataType: 'json',
								success: function(resp){
									if (resp.length > 0) {
										var ccid = $('#ccid').attr('data-ccid');
										var selected = '';
										for(var i = 0; i< resp.length; i++){
											if (ccid == resp[i]['id']) {
												selected = 'selected';
											} else {
												selected = '';
											}
											html += '<option '+selected+' value="'+resp[i]['id']+'">'+resp[i]['title']+'</option>';
										}
									}
									$('#ccid').html(html)
								}
							});
						});
					})
				</script>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">
					商品类型
				</label>
				<div class="col-sm-8 col-xs-12">
					<label class="radio-inline">
						<input type="radio" name="type" value="1" <?php  if($item['type'] == 1) { ?>checked<?php  } ?>>实物商品
					</label>
					<label class="radio-inline">
						<input type="radio" name="type" value="2" <?php  if($item['type'] == 2) { ?>checked<?php  } ?>>虚拟商品
					</label>
					<span class="help-block">实物商品需要物流；虚拟商品无需物流</span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">
					<span class="star">*</span>封面
				</label>
				<div class="col-sm-8 col-xs-12">
					<?php  echo tpl_form_field_image('cover', $item['cover'])?>
					<span class="help-block">推荐尺寸：100*100</span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">
					<span class="star">*</span>相册
				</label>
				<div class="col-sm-8 col-xs-12">
					<?php  echo tpl_form_field_multi_image('album', $item['album'])?>
					<span class="help-block">推荐尺寸：400*300</span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">邮费设置</label>
				<div class="col-sm-8 col-xs-12">
					<div class="radio">
						<label>
							<input type="radio" name="postage_select" value="1" <?php  if($item['postage_select'] == 1) { ?>checked<?php  } ?>>
							统一邮费
						</label>
						<label class="input-inline">
							<input type="text" class="form-control" placeholder="" name="postage" value="<?php  echo $item['postage'];?>" />
						</label>
						元
						<span class="help-block" style="color: #f00">
						</span>
					</div>
					<div class="radio">
						<label>
							<input type="radio" name="postage_select" value="2" <?php  if($item['postage_select'] == 2) { ?>checked<?php  } ?>>
							邮费模板
						</label>
						<label class="select-inline">
							<select name="postage_tmplid" class="form-control" <?php  if($item['postage_select'] != 2) { ?>disabled<?php  } ?> data-shopid="<?php  echo $item['shopid'];?>">
							<?php  if($postage_tmpl) { ?>
							<?php  if(is_array($postage_tmpl)) { foreach($postage_tmpl as $value) { ?>
							<option value="<?php  echo $value['id'];?>" <?php  if($item['postage_tmplid'] == $value['id']) { ?>selected<?php  } ?>><?php  echo $value['title'];?></option>
							<?php  } } ?>
							<?php  } ?>
							</select>
						</label>
						<a href="<?php  echo $this->createWebUrl('postage', array('act' => 'post'));?>" target="_blank">
							<span class="fa fa-plus"></span>
							邮费模板
						</a>&nbsp;&nbsp;
						<a href="javascript:;" class="refresh_postage_tmpl">
							<span class="fa fa-refresh"></span>
							刷新
						</a>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">
					秒杀时间
				</label>
				<div class="col-sm-6 col-md-8 col-xs-12">
					<div class="pull-left" style="margin-right: 10px;">
						<?php  echo tpl_form_field_daterange('seckill_date', $item['seckill_date'])?>
					</div>
					<label class="radio-inline">
						<input type="radio" name="seckill_time" value="8" <?php  if($item['seckill_time'] == 8) { ?>checked<?php  } ?>>8点场
					</label>
					<label class="radio-inline">
						<input type="radio" name="seckill_time" value="12" <?php  if($item['seckill_time'] == 12) { ?>checked<?php  } ?>>12点场
					</label>
					<label class="radio-inline">
						<input type="radio" name="seckill_time" value="16" <?php  if($item['seckill_time'] == 16) { ?>checked<?php  } ?>>16点场
					</label>
					<label class="radio-inline">
						<input type="radio" name="seckill_time" value="20" <?php  if($item['seckill_time'] == 20) { ?>checked<?php  } ?>>20点场
					</label>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">状态</label>
				<div class="col-sm-6 col-md-8 col-xs-12">
					<div class="input-group">
						<label class="radio-inline">
							<input type="radio" name="status" value="1" <?php  if($item['status'] == 1) { ?>checked<?php  } ?>> 已上架
						</label>
						<label class="radio-inline">
							<input type="radio" name="status" value="0" <?php  if($item['status'] == 0) { ?>checked<?php  } ?>> 已下架
						</label>
						<label class="radio-inline">
							<input type="radio" name="status" value="2" <?php  if($item['status'] == 2) { ?>checked<?php  } ?>> 禁售
						</label>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">库存</label>
				<div class="col-sm-8 col-md-8 col-xs-12">
					<input type="text" class="form-control" name="total" value="<?php  echo $item['total'];?>">
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">重量(kg)</label>
				<div class="col-sm-8 col-md-8 col-xs-12">
					<input type="text" class="form-control" name="weight" value="<?php  echo $item['weight'];?>">
					<span class="help-block">按重量计算的邮费模板将按此处设置的重量计算</span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">
					市场价
				</label>
				<div class="col-sm-8 col-md-8 col-xs-12">
					<input type="text" class="form-control" name="market_price" value="<?php  echo $item['market_price'];?>">
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">
					<span class="star">*</span>秒杀价
				</label>
				<div class="col-sm-8 col-md-8 col-xs-12">
					<input type="text" class="form-control" name="price" value="<?php  echo $item['price'];?>">
				</div>
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			其它属性
		</div>
		<div class="panel-body">
			<!--<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">排序</label>
				<div class="col-sm-8 col-xs-12">
					<input type="text" class="form-control" placeholder="" name="displayorder" value="<?php  echo $item['displayorder'];?>">
					<span class="help-block">由大到小，排序值越大越靠前</span>
				</div>
			</div>-->
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">副标题</label>
				<div class="col-sm-8 col-xs-12">
					<input type="text" class="form-control" placeholder="" name="subtitle" value="<?php  echo $item['subtitle'];?>">
					<span class="help-block">商品被分享时，副标题的内容会展示在分享描述中</span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">货号</label>
				<div class="col-sm-8 col-xs-12">
					<input type="text" class="form-control" placeholder="" name="number" value="<?php  echo $item['number'];?>">
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">减库存方式</label>
				<div class="col-sm-9 col-xs-12">
					<label class="radio-inline">
						<input type="radio" name="minus_total" value="1" <?php  if($item['minus_total']==1) { ?>checked<?php  } ?>>付款减
					</label>
					<label class="radio-inline">
						<input type="radio" name="minus_total" value="2" <?php  if($item['minus_total']==2) { ?>checked<?php  } ?>>拍下减
					</label>
					<span class="help-block">选择付款减库存时，提交订单页面将提示如下信息：订单创建后，请尽快支付，否则可能出现库存不足</span>
					<span class="help-block">选择拍下减库存时，由于提交订单未支付问题，请注意补充库存</span>
					<span class="help-block" style="color: red">当设置为付款减时，由于支付时间差问题，存在超出库存的销售订单，如对库存有严格要求，可使用拍下减</span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">每单最多购买数</label>
				<div class="col-sm-8 col-xs-12">
					<input type="text" class="form-control" name="other_attr[order_buy_num]" value="<?php  if(isset($item['extend']['other_attr'])) { ?><?php  echo $item['extend']['other_attr']['order_buy_num'];?><?php  } ?>">
					<span class="help-block">每订单可买商品数，默认为空不限制</span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">账号最多购买数</label>
				<div class="col-sm-8 col-xs-12">
					<input type="text" class="form-control" name="other_attr[max_buy_num]" value="<?php  if(isset($item['extend']['other_attr'])) { ?><?php  echo $item['extend']['other_attr']['max_buy_num'];?><?php  } ?>">
					<span class="help-block">每账号可买商品数，默认为空不限制</span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">是否有发票</label>
				<div class="col-sm-6 col-md-8 col-xs-12">
					<div class="input-group">
						<label class="radio-inline">
							<input type="radio" name="isreceipt" value="1" <?php  if($item['isreceipt']) { ?>checked<?php  } ?>> 是
						</label>
						<label class="radio-inline">
							<input type="radio" name="isreceipt" value="0" <?php  if(!$item['isreceipt']) { ?>checked<?php  } ?>> 否
						</label>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">是否保修</label>
				<div class="col-sm-6 col-md-8 col-xs-12">
					<div class="input-group">
						<label class="radio-inline">
							<input type="radio" name="isrepair" value="1" <?php  if($item['isrepair']) { ?>checked<?php  } ?>> 是
						</label>
						<label class="radio-inline">
							<input type="radio" name="isrepair" value="0" <?php  if(!$item['isrepair']) { ?>checked<?php  } ?>> 否
						</label>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">是否可核销</label>
				<div class="col-sm-6 col-md-8 col-xs-12">
					<div class="input-group">
						<label class="radio-inline">
							<input type="radio" name="ischeckout" value="1" <?php  if($item['ischeckout']) { ?>checked<?php  } ?>> 是
						</label>
						<label class="radio-inline">
							<input type="radio" name="ischeckout" value="0" <?php  if(!$item['ischeckout']) { ?>checked<?php  } ?>> 否
						</label>
					</div>
					<span class="help-block">设置可核销后，提交的订单默认具有可核销属性</span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">简介</label>
				<div class="col-sm-8 col-xs-12">
					<textarea class="form-control" rows="8" name="summary"><?php  echo $item['summary'];?></textarea>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">详细描述</label>
				<div class="col-sm-8 col-xs-12">
					<?php  echo tpl_ueditor('description', $item['description'])?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">产品参数</label>
				<div class="col-sm-8 col-xs-12">
					<table class="table table-hover">
						<thead>
						<tr>
							<th width="25"></th>
							<th width="200">属性名</th>
							<th width="600">属性值</th>
							<th>操作</th>
						</tr>
						</thead>
						<tbody id="list_wrap" class="ui-sortable">
						<?php  if($all_params) { ?>
						<?php  if(is_array($all_params)) { foreach($all_params as $param) { ?>
						<tr>
							<td>
								<a href="javascript:;" class="fa fa-move" title="按住鼠标左键，拖动调整顺序">
									<i class="fa fa-arrows"></i>
								</a>
							</td>
							<td>
								<div class="form-group" style="margin-bottom: 0">
									<div class="col-xs-12">
										<input name="param_id[]" <?php  if(isset($copyid) && $copyid > 0) { ?>value=""<?php  } else { ?>value="<?php  echo $param['id'];?>"<?php  } ?> type="hidden" class="form-control"/>
										<input name="param_name[]" value="<?php  echo $param['name'];?>" type="text" class="form-control" placeholder=""/>
									</div>
								</div>
							</td>
							<td>
								<div class="form-group" style="margin-bottom: 0">
									<div class="col-xs-12">
										<input name="param_value[]" value="<?php  echo $param['value'];?>" type="text" class="form-control" placeholder=""/>
									</div>
								</div>
							</td>
							<td>
								<a href="javascript:;" class="del_param_link" onclick="delItem(this)" title="删除" data-id="<?php  echo $param['id'];?>">
									<i class='fa fa-remove'></i>
								</a>
							</td>
						</tr>
						<?php  } } ?>
						<?php  } ?>
						</tbody>
					</table>
					<button type="button" class="btn btn-warning add_params" title="添加属性">
						<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> 添加属性
					</button>
				</div>
			</div>
		</div>
		<?php  if($_W['isfounder']) { ?>
		<div class="panel-footer">
			上次操作人: <?php  echo $item['user']['username'];?>
		</div>
		<?php  } ?>
	</div>
	<div class="form-group">
		<div class="col-sm-12">
			<input name="submit" type="submit" value="提交" class="btn btn-primary col-lg-1">
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>">
		</div>
	</div>
</form>
<script>
require(['jquery', 'jquery.ui'], function($){
	$('.form').submit(function(){
		var title = $('input[name=title]');
		if (title.val() == '') {
			util.message('商品标题为空，请填写！', '', 'error');
			return false;
		}
		var pcid = $('select[name=pcid]');
		if (pcid.val() <= 0) {
			util.message('请选择一级分类！', '', 'error');
			return false;
		}
		var cid = $('select[name=cid]');
		if (cid.val() <= 0) {
			util.message('请选择二级分类！', '', 'error');
			return false;
		}
		/*var ccid = $('select[name=ccid]');
		if (ccid.val() <= 0) {
			util.message('请选择三级分类！', '', 'error');
			return false;
		}*/
		var cover = $('input[name=cover]');
		if (cover.val() == '') {
			util.message('封面为空，请上传！', '', 'error');
			return false;
		}
		var album = $('input[name="album[]"]');
		if (!album.val()) {
			util.message('相册为空，请上传！', '', 'error');
			return false;
		}
		var price = $('input[name=price]');
		if (price.val() <= 0) {
			return confirm('请确认是否设置秒杀价为0？');
		}
		return true;
	});

	//邮费切换
	$('input[name=postage_select]').click(function () {
		var tmpl = $(this).val();
		if (tmpl == 2) {
			loadPostageTmpl();
			$('input[name=postage]').prop('disabled', true);
		} else {
			$('select[name=postage_tmplid]').prop('disabled', true);
			$('input[name=postage]').prop('disabled', false);
		}
	});
	$('.refresh_postage_tmpl').click(function(){
		if ($('select[name=postage_tmplid]').prop('disabled')) {
			return;
		}
		loadPostageTmpl();
	});
	var loadPostageTmpl = function(){
		$('select[name=postage_tmplid]').prop('disabled', true);
		var shopid = $('select[name=postage_tmplid]').attr('data-shopid');
		$.ajax({
			type: 'post',
			url: '<?php  echo $this->createWebUrl("item", array("act" => "postage_tmpl"))?>',
			data: 'shopid='+shopid,
			dataType: 'json',
			success: function(resp){
				if (resp.length > 0) {
					var html = '', item;
					for (var i=0; i<resp.length; i++) {
						item = resp[i];
						html += '<option value="'+item.id+'">'+item.title+'</option>';
					}
					$('select[name=postage_tmplid]').html(html).prop('disabled', false);
				}
			}
		});
	};
	$('#list_wrap').sortable({handle: '.fa-move'});
	//添加产品参数
	$('.add_params').click(function(){
		$.ajax({
			url: "<?php  echo $this->createWebUrl('item', array('act' => 'params', 'behavior' => 'add'))?>",
			success:function(response) {
				$('#list_wrap').append(response);
			}
		});
	});
	//删除产品参数
	window.delItem = function(obj) {
		var id = $(obj).attr('data-id');
		if (!id) {	//new
			$(obj).parent().parent().remove();
			return;
		}
		$.ajax({
			'url': "<?php  echo $this->createWebUrl('item', array('act' => 'params', 'behavior' => 'delete'))?>"+'&paramid='+id,
			success:function(response) {
				if (response == 'success') {
					$(obj).parent().parent().remove();
				} else {
					util.message(response, '', 'error');
				}
			}
		});
	};
});
</script>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('footer', TEMPLATE_INCLUDEPATH)) : (include template('footer', TEMPLATE_INCLUDEPATH));?>