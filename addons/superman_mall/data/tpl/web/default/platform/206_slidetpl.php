<?php defined('IN_IA') or exit('Access Denied');?><div class="main">
	<form class="form-horizontal form" id="setting_form" action="" method="post" enctype="multipart/form-data">
		<div class="panel panel-default">
			<div class="panel-heading">首页幻灯片</div>
			<div class="panel-body">
				<style>
					.table td {
						vertical-align: top !important;
					}
				</style>
				<table class="table table-hover">
					<thead>
					<tr>
						<th width="25"></th>
						<th width="25%">标题</th>
						<th>图片</th>
						<th width="25%">链接</th>
						<th width="44" class="text-right">操作</th>
					</tr>
					</thead>
					<tbody id="list_wrap">
					<?php  if($slides) { ?>
					<?php  if(is_array($slides)) { foreach($slides as $item) { ?>
					<tr>
						<td>
							<a href="javascript:;" class="fa fa-move" title="按住鼠标左键，拖动调整顺序">
								<i class="fa fa-arrows"></i>
							</a>
						</td>
						<td>
							<div class="form-group">
								<div class="col-sm-12 col-xs-12">
									<input name="slide[title][]" type="text" class="form-control" value="<?php  echo $item['title'];?>"/>
								</div>
							</div>
						</td>
						<td>
							<div class="form-group">
								<div class="col-sm-12 col-xs-12">
									<?php  echo tpl_form_field_image('slide[img][]', $item['img']);?>
									<span class="help-block">推荐尺寸：414*230</span>
								</div>
							</div>
						</td>
						<td>
							<div class="form-group">
								<div class="col-sm-12 col-xs-12">
									<input name="slide[url][]" type="text" class="form-control" value="<?php  echo $item['url'];?>"/>
								</div>
							</div>
						</td>
						<td class="text-right">
							<a href="javascript:;" class="del_item_link" data-toggle="tooltip" onclick="delItem(this)" title="删除">
								<i class='fa fa-remove' style="color: #f00"></i>
							</a>
						</td>
					</tr>
					<?php  } } ?>
					<?php  } ?>
					</tbody>
					<tbody>
					<tr>
						<td colspan="5">
							<button type="button" class="btn btn-warning add_slide" title="添加">
								<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> 添加
							</button>
						</td>
					</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class="form-group col-sm-12">
			<input name="id" type="hidden" value="<?php  echo $id;?>" />
			<input name="token" type="hidden" value="<?php  echo $_W['token'];?>" />
			<input type="submit" class="btn btn-primary col-lg-1" name="submit" value="提交" />
		</div>
	</form>
</div>
<script>
	require(['jquery.ui'], function($){
		$('#list_wrap').sortable({handle: '.fa-move'});
		$('.add_slide').click(function(){
			$.ajax({
				'url': "<?php  echo $this->createWebUrl('platform', array('act' => 'slide', 'add' => 'yes'))?>",
				success: function (response) {
					$('#list_wrap').append(response);
				}
			});
		});
		window.delItem = function(obj) {
			$(obj).parent().parent().remove();
		};
	});
</script>
