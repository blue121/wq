<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('header', TEMPLATE_INCLUDEPATH)) : (include template('header', TEMPLATE_INCLUDEPATH));?>
<style>
	.text-overflow {
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
	}
	.shop_btn {
		color: #d9534f;
		background-color: transparent;
		border-color: #d9534f;
	}
</style>
<div class="panel panel-info">
	<div class="panel-heading">筛选</div>
	<div class="panel-body">
		<form action="" method="post" class="form-horizontal" role="form">
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 control-label">商户名称</label>
				<div class="col-xs-12 col-sm-8 col-md-8 col-lg-6">
					<input class="form-control" name="title" type="text" value="<?php  echo $_GPC['title'];?>" placeholder="支持模糊搜索">
				</div>
				<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
					<button class="btn btn-default"><i class="fa fa-search"></i>搜索</button>
				</div>
			</div>
		</form>
	</div>
</div>
<div class="panel panel-default">
	<div class="panel-heading">
		商户列表
	</div>
	<div class="panel-body row">
		<?php  if($list) { ?>
		<?php  if(is_array($list)) { foreach($list as $li) { ?>
		<div class="col-lg-2 col-md-3 col-sm-4 col-xs-12">
			<div class="thumbnail">
				<img src="<?php  echo tomedia($li['logo'])?>" onerror="this.src='<?php  echo $this->superman_placeholder?>'" style="width: 100%; min-height: 200px; max-height: 200px;">
				<div class="caption">
					<h4 class="text-overflow"><?php  echo $li['title'];?></h4>
					<p>
						<a href="<?php  echo $this->createWebUrl('shop', array('act' => 'switch', 'shopid' => $li['id'], 'referer' => $_GPC['referer']))?>" class="btn btn-danger shop_btn btn-block" role="button">
							<span class="fa fa-cog" aria-hidden="true"></span> 管理商户
						</a>
					</p>
				</div>
			</div>
		</div>
		<?php  } } ?>
		<?php  } ?>
	</div>
	<div style="padding: 0 30px;">
		<?php  echo $pager;?>
	</div>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('footer', TEMPLATE_INCLUDEPATH)) : (include template('footer', TEMPLATE_INCLUDEPATH));?>