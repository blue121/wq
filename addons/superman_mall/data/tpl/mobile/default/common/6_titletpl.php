<?php defined('IN_IA') or exit('Access Denied');?><header class="bar bar-nav">
	<!--后退按钮-->
	<?php  if($back_url != '') { ?>
		<a class="icon icon-left pull-left external" href="<?php  echo $back_url;?>"></a>
	<?php  } else { ?>
		<a class="icon icon-left pull-left back"></a>
	<?php  } ?>

	<?php  if($do == 'list' || $do == 'category' || ($do == 'shop' && $act == 'list')) { ?>
		<!--商品列表搜索条-->
		<?php  if($do == 'list' || $do == 'category') { ?>
			<div class="bar bar-header-secondary <?php  if(!isset($style_setting['list_style_switch']) || $style_setting['list_style_switch'] == 1) { ?>list_search_style<?php  } else { ?>list_style_noswitch<?php  } ?>">
				<form action="" method="get" class="clearfix">
					<input type="hidden" name="i" value="<?php  echo $_W['uniacid'];?>"/>
					<input type="hidden" name="c" value="entry"/>
					<input type="hidden" name="do" value="list"/>
					<input type="hidden" name="m" value="superman_mall"/>
					<input type="hidden" name="sort" value="<?php  echo $sort;?>"/>
					<div class="searchbar">
						<a class="searchbar-cancel">取消</a>
						<div class="search-input">
							<label class="icon icon-search"></label>
							<input type="search" name="kw" placeholder='输入商品名称关键字...' value="<?php  echo $kw;?>"/>
						</div>
					</div>
				</form>
			</div>
			<?php  if($do == 'list' && (!isset($style_setting['list_style_switch']) || $style_setting['list_style_switch'] == 1)) { ?>
			<a class="icon pull-right style_switch_btn" href="<?php  echo $this->createMobileUrl('list', array('sort' => $_GPC['sort'], 'kw' => $_GPC['kw']))?>&__t=<?php  echo time()?>" data-no-cache="true">
				<span class="iconfont fonta1">&#xe610;</span>
			</a>
			<?php  } ?>
		<?php  } ?>
		<!--商户列表搜索条-->
		<?php  if($do == 'shop' && $act == 'list') { ?>
			<div class="bar bar-header-secondary list_search_style">
				<form action="" method="get" class="clearfix">
					<input type="hidden" name="i" value="<?php  echo $_W['uniacid'];?>"/>
					<input type="hidden" name="c" value="entry"/>
					<input type="hidden" name="do" value="shop"/>
					<input type="hidden" name="act" value="list"/>
					<input type="hidden" name="m" value="superman_mall"/>
					<div class="searchbar">
						<a class="searchbar-cancel">取消</a>
						<div class="search-input">
							<label class="icon icon-search"></label>
							<input type="search" name="kw" placeholder='输入商户名称关键字...<?php  if(!empty($_GPC["latitude"])&&!empty($_GPC["longitude"])) { ?>.<?php  } ?>' value="<?php  echo $kw;?>"/>
						</div>
					</div>
				</form>
			</div>
            <!--手动刷新地理位置按钮 下个版本有可能会被去除-->
            <a class="icon pull-right external" href="<?php  echo $this->createMobileUrl('shop', array('act' => 'list', 'location' => 'refresh'))?>" data-no-cache="true">
                <span class="iconfont fonta1">&#xe61c;</span>
            </a>
		<?php  } ?>
	<?php  } else { ?>
		<!--二级标题-->
		<h1 class="title"><?php  echo $title;?></h1>
	<?php  } ?>

	<!--购物车删除按钮-->
	<?php  if($do == 'cart') { ?>
		<a class="icon icon-remove pull-right open-panel delete_cart" data-url="<?php  echo $this->createMobileUrl('cart', array('act' => 'delete'))?>"></a>
	<?php  } ?>

	<!--个人信息按钮-->
	<?php  if($do == 'my') { ?>
		<a href="<?php  echo $this->createMobileUrl('profile', array('act' => 'display'))?>" class="pull-right check_login external">
			<i class="icon icon-settings"></i>
		</a>
	<?php  } ?>

	<!--切换海报样式-->
	<?php  if($do == 'partner' && $act == 'poster') { ?>
	<a href="javascript:;" class="icon icon-settings pull-right style_switch"></a>
	<?php  } ?>
</header>