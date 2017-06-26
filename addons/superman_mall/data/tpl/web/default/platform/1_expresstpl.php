<?php defined('IN_IA') or exit('Access Denied');?><div class="alert alert-info">
	快递公司为系统自动初始化数据，物流数据接口为快递100，当前主流快递公司基本都支持，暂不支持添加、删除、修改等功能。<br>
	<strong>每个商户的后台都可以自行选择本商户的快递公司，查看和编辑请进入：订单管理=》快递公司</strong>
</div>
<div class="panel panel-default">
	<div class="table-responsive panel-body">
		<table class="table table-hover">
			<thead>
			<tr>
				<th>名称</th>
				<th>代号</th>
			</tr>
			</thead>
			<tbody>
			<?php  if($list) { ?>
			<?php  if(is_array($list)) { foreach($list as $li) { ?>
			<tr>
				<td><?php  echo $li['title'];?></td>
				<td><?php  echo $li['alias'];?></td>
			</tr>
			<?php  } } ?>
			<?php  } ?>
			</tbody>
		</table>
	</div>
	<div class="panel-footer">
		总共 <strong><?php  echo $total;?></strong> 条
	</div>
</div>