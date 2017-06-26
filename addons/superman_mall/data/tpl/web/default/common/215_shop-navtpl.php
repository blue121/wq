<?php defined('IN_IA') or exit('Access Denied');?><nav class="navbar navbar-default">
	<div class="container-fluid">
		<div class="navbar-header">
			<a class="navbar-brand" href="javascript:;" style="cursor: auto;">当前商户：<?php  if($this->shop) { ?><?php  echo $this->shop['title']?><?php  } else { ?>全部<?php  } ?></a>
		</div>
		<form class="navbar-form navbar-left" role="search">
			<div class="btn-group" role="group" aria-label="...">
				<a href="<?php  echo $this->createWebUrl('shop', array('act' => 'switch', 'shop' => -1, 'referer' => urlencode($_W['siteurl'])))?>" class="btn btn-default <?php  if(empty($this->shop)) { ?>active<?php  } ?>">
					全部商户
				</a>
				<a href="<?php  echo $this->createWebUrl('shop', array('act' => 'display', 'switch' => 'shop', 'referer' => urlencode($_W['siteurl'])))?>" class="btn btn-default">
					<span class="fa fa-refresh"></span>
					切换商户
				</a>
			</div>
			<?php  if(isset($plugin_permission)) { ?>
			<?php  if(is_error($plugin_permission)) { ?>
			<span style="color: red; margin-left: 10px;">当前商户未开启该功能模块,或该功能模块已过期</span>&nbsp;&nbsp;
			<a href="<?php  echo $this->createWebUrl('shop', array('act' => 'plugin', 'id' => $this->shop['id']))?>" target="_blank">去开启</a>
			<?php  } else { ?>
			<span style="color: blue; margin-left: 10px;">该功能模块有效期：<?php  echo $plugin_permission['start'];?> ~ <?php  echo $plugin_permission['end'];?></span>
			<?php  } ?>
			<?php  } ?>
		</form>
		<form class="navbar-form navbar-right" role="search">
			<div class="btn-group" role="group" aria-label="...">
				<a href="<?php  if($this->shop['id']) { ?><?php echo $_W['siteroot'].'/addons/superman_mall/admin/index.php?shopid='.$this->shop['id']?><?php  } else { ?>javascript:;<?php  } ?>" class="btn btn-info <?php  if(!$this->shop['id']) { ?>disabled<?php  } ?>" target="_blank">
					<span class="fa fa-sign-in"></span>
					登录商户后台<?php  if(!$this->shop['id']) { ?> (未选择商户)<?php  } ?>
				</a>
			</div>
		</form>
	</div>
</nav>