<?php defined('IN_IA') or exit('Access Denied');?><div class="main">
	<form class="form-horizontal form" id="setting_form" action="" method="post" enctype="multipart/form-data">
		<div class="panel panel-default">
			<div class="panel-heading">
				首页头条
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">头条名称</label>
					<div class="col-sm-6 col-md-8 col-xs-12">
						<input type="text" class="form-control" name="top[name]" value="<?php  echo $top['name'];?>"/>
						<span class="help-block"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">标题</label>
					<div class="col-sm-6 col-md-8 col-xs-12">
						<input type="text" class="form-control" name="top[title]" value="<?php  echo $top['title'];?>"/>
						<span class="help-block"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">链接</label>
					<div class="col-sm-6 col-md-8 col-xs-12">
						<input type="text" class="form-control" name="top[url]" value="<?php  echo $top['url'];?>"/>
						<span class="help-block"></span>
					</div>
				</div>
			</div>
		</div>
		<div class="form-group col-sm-12">
			<input name="id" type="hidden" value="<?php  echo $id;?>" />
			<input name="skey" type="hidden" value="home_top" />
			<input name="token" type="hidden" value="<?php  echo $_W['token'];?>" />
			<input type="submit" class="btn btn-primary col-lg-1" name="submit" value="提交" />
		</div>
	</form>
</div>
<script>
	require(['jquery'], function($){
	});
</script>
