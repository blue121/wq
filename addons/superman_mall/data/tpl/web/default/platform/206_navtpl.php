<?php defined('IN_IA') or exit('Access Denied');?><div class="main">
	<style>
		.icon_wrap img {
			width: 48px;
			height: 48px;
		}
	</style>
	<form action="" method="post">
		<input type="hidden" class="form-control" name="id" value="<?php  echo $id;?>">
		<div class="alert alert-info">图标推荐尺寸：128*128</div>
		<div class="panel panel-default">
			<div class="table-responsive panel-body">
				<table class="table">
					<thead>
					<tr>
						<th width="80">排序</th>
						<th width="320">图标</th>
						<th>名称</th>
						<th>链接</th>
						<th width="120" class="text-right">是否显示</th>
					</tr>
					</thead>
					<tbody>
					<?php  if(is_array($apps)) { foreach($apps as $k => $app) { ?>
					<tr>
						<td>
							<input type="text" class="form-control text-center" name="displayorder[<?php  echo $k;?>]" value="<?php  echo $app['displayorder'];?>">
						</td>
						<td class="icon_wrap">
							<?php  echo tpl_form_field_image('icon['.$k.']', $app['icon'])?>
						</td>
						<td>
							<input type="text" class="form-control" name="title[<?php  echo $k;?>]" value="<?php  echo $app['title'];?>">
						</td>
						<td>
							<input type="text" class="form-control" name="url[<?php  echo $k;?>]" value="<?php  echo $app['url'];?>">
						</td>
						<td class="text-right">
							<input type="checkbox" name="isshow[<?php  echo $k;?>]" <?php  if($app['isshow']) { ?>checked<?php  } ?>>
						</td>
					</tr>
					<?php  } } ?>
					</tbody>
				</table>
			</div>
		</div>
		<input name="submit" type="submit" value="提交" class="btn btn-primary col-lg-1">
		<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
	</form>
</div>