<?php defined('IN_IA') or exit('Access Denied');?>	<div class="navbar navbar-inverse navbar-static-top" role="navigation" style="position:static;">
		<div class="container-fluid">
			<ul class="nav navbar-nav">
				<li style="max-width: 200px;min-width: 150px;">
					<a href="<?php  echo url('site/entry/dashboard', array('m' => 'superman_mall'))?>" title="<?php  echo $this->shop['title']?>" style="padding: 0; line-height: 50px;color: #aaa;font-size: 16px;">
						<?php  if(!$this->shop['logo']) { ?>
							<?php  echo $this->shop['title']?>
						<?php  } else { ?>
							<img src="<?php  echo tomedia($this->shop['logo'])?>" title="<?php  echo $this->shop['title']?>" height="50"/>
						<?php  } ?>
					</a>
				</li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<li class="dropdown">
					<?php  if($this->shop_user) { ?>
					<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" style="display:block; max-width:185px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; ">
						<i class="fa fa-user"></i><?php  echo $this->shop_user['username']?><b class="caret"></b>
					</a>
					<ul class="dropdown-menu">
						<?php  if($this->shop_user['groupid'] == 0) { ?>
						<li><a href="<?php  echo url('site/entry/shop', array('act' => 'post', 'id' => $this->shop['id'], 'm' => 'superman_mall'))?>"><i class="fa fa-cog fa-fw"></i> 商户资料</a></li>
						<li class="divider"></li>
						<?php  } ?>
						<li><a href="<?php  echo url('site/entry/profile', array('m' => 'superman_mall'))?>"><i class="fa fa-user fa-fw"></i> 我的账号</a></li>
						<li><a href="<?php  echo url('user/logout');?>"><i class="fa fa-sign-out fa-fw"></i> 退出系统</a></li>
					</ul>
					<?php  } ?>
				</li>
			</ul>
		</div>
	</div>
	<div class="container-fluid">
		<?php  if(defined('IN_MESSAGE')) { ?>
		<div class="jumbotron clearfix alert alert-<?php  echo $label;?>">
			<div class="row">
				<div class="col-xs-12 col-sm-3 col-lg-2">
					<i class="fa fa-5x fa-<?php  if($label=='success') { ?>check-circle<?php  } ?><?php  if($label=='danger') { ?>times-circle<?php  } ?><?php  if($label=='info') { ?>info-circle<?php  } ?><?php  if($label=='warning') { ?>exclamation-triangle<?php  } ?>"></i>
				</div>
				<div class="col-xs-12 col-sm-8 col-md-9 col-lg-10">
					<?php  if(is_array($msg)) { ?>
						<h2>MYSQL 错误：</h2>
						<p><?php  echo cutstr($msg['sql'], 300, 1);?></p>
						<p><b><?php  echo $msg['error']['0'];?> <?php  echo $msg['error']['1'];?>：</b><?php  echo $msg['error']['2'];?></p>
					<?php  } else { ?>
					<h2><?php  echo $caption;?></h2>
					<p><?php  echo $msg;?></p>
					<?php  } ?>
					<?php  if($redirect) { ?>
					<p><a href="<?php  echo $redirect;?>">如果你的浏览器没有自动跳转，请点击此链接</a></p>
					<script type="text/javascript">
						setTimeout(function () {
							location.href = "<?php  echo $redirect;?>";
						}, 3000);
					</script>
					<?php  } else { ?>
						<p>[<a href="javascript:history.go(-1);">点击这里返回上一页</a>] &nbsp; [<a href="./?refresh">首页</a>]</p>
					<?php  } ?>
				</div>
		<?php  } else { ?>
		<div class="row">
			<?php  $navs = SuermanMallMenu::webShopNavs($this->web['user_permission']);?>
			<?php  if(!empty($navs)) { ?>
				<div class="col-xs-12 col-sm-3 col-lg-2 big-menu">
					<?php  if(is_array($navs)) { foreach($navs as $k => $nav) { ?>
					<div class="panel panel-default" style="margin-bottom: 10px; border-top-width: 1px;">
						<div class="panel-heading">
							<h4 class="panel-title">
								<?php  if($nav['icon']) { ?><i class="<?php  echo $nav['icon'];?>"></i><?php  } ?> <?php  echo $nav['title'];?>
							</h4>
							<a class="panel-collapse <?php  if($nav['active']) { ?>collapsed<?php  } ?> nav_collapse_link" data-do="__nav_<?php  echo $nav['xdo'];?>" data-toggle="collapse" href="#frame-<?php  echo $k;?>">
								<i class="fa fa-chevron-circle-down"></i>
							</a>
						</div>
						<ul class="list-group collapse <?php  echo $nav['active'];?>" id="frame-<?php  echo $k;?>">
							<?php  if(is_array($nav['items'])) { foreach($nav['items'] as $link) { ?>
							<li class="list-group-item <?php  echo $link['active'];?>" onclick="window.location.href = '<?php  echo $link['url'];?>';" style="cursor:pointer; overflow:hidden;">
								<?php  echo $link['title'];?>
								<?php  if($link['extra']) { ?>
								<a class="pull-right" href="<?php  echo $link['extra']['url'];?>" title="<?php  echo $link['extra']['title'];?>">
									<i class="<?php  echo $link['extra']['icon'];?>"></i>
								</a>
								<?php  } ?>
							</li>
							<?php  } } ?>
						</ul>
					</div>
					<?php  } } ?>
					<script>
						require(['jquery'],function($) {
							$('.nav_collapse_link').click(function () {
								util.cookie.prefix = window.sysinfo.cookie.pre;
								var value = $(this).hasClass('collapsed')?0:1;
								util.cookie.set($(this).attr('data-do'), value, 365*86400);
							});
						});
					</script>
				</div>
				<div class="col-xs-12 col-sm-9 col-lg-10">
					<?php  if(CRUMBS_NAV == 1) { ?>
						<?php  global $module_types;global $module;global $ptr_title;?>
						<ol class="breadcrumb" style="padding:5px 0;">
							<li><a href="<?php  echo url('home/welcome/ext');?>"><i class="fa fa-cogs"></i> &nbsp; 扩展功能</a></li>
							<li><a href="<?php  echo url('home/welcome/ext', array('m' => $module['name']));?>"><?php  echo $module_types[$module['type']]['title'];?>模块 - <?php  echo $module['title'];?></a></li>
							<li class="active"><?php  echo $ptr_title;?></li>
						</ol>
					<?php  } else if(CRUMBS_NAV == 2) { ?>
						<?php  global $module_types;global $module;global $ptr_title; global $site_urls; $m = $_GPC['m'];?>
						<ul class="nav nav-tabs">
							<li><a href="<?php  echo url('platform/reply', array('m' => $m));?>">管理<?php  echo $module['title'];?></a></li>
							<li><a href="<?php  echo url('platform/reply/post', array('m' => $m));?>"><i class="fa fa-plus"></i> 添加<?php  echo $module['title'];?></a></li>
							<?php  if(!empty($site_urls)) { ?>
								<?php  if(is_array($site_urls)) { foreach($site_urls as $site_url) { ?>
									<li <?php  if($_GPC['do'] == $site_url['do']) { ?> class="active"<?php  } ?>><a href="<?php  echo $site_url['url'];?>"> <?php  echo $site_url['title'];?></a></li>
								<?php  } } ?>
							<?php  } ?>
						</ul>
					<?php  } ?>
			<?php  } else { ?>
				<div class="col-xs-12 col-sm-12 col-lg-12">
			<?php  } ?>
		<?php  } ?>
