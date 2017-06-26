<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<div class="page-group">
	<div class="page superpage_<?php  echo $do;?>" id="superpage_<?php  echo $do;?>_<?php  echo $act;?>">
	<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/nav', TEMPLATE_INCLUDEPATH)) : (include template('common/nav', TEMPLATE_INCLUDEPATH));?>
		<div class="content infinite-scroll" data-flag="0" data-display-url="<?php  echo $this->createMobileUrl('home', array('load' => 'infinite'))?>" data-detail-url="<?php  echo $this->createMobileUrl('detail', array('act' => 'display'))?>" data-distance="50" data-page="<?php  echo $pindex;?>" data-cart-url="<?php  echo $this->createMobileUrl('cart', array('act' => 'post'))?>">
            <div class="searchbar row home_search_style1">
				<div class="search-input col-100">
					<form action="" method="get" class="clearfix search_form">
						<input type="hidden" name="i" value="<?php  echo $_W['uniacid'];?>">
						<input type="hidden" name="c" value="entry">
						<input type="hidden" name="do" value="list">
						<input type="hidden" name="m" value="superman_mall">
						<div class="searchbar">
							<a class="searchbar-cancel">取消</a>
							<div class="search-input">
								<label class="icon icon-search"></label>
								<input type="search" name="kw" placeholder='输入商品名称关键字...' value=""/>
							</div>
						</div>
					</form>
				</div>
			</div>
			<div class="searchbar row home_search_style2 searchbar_float" style="display: none">
				<div class="search-input <?php  if(!isset($style_setting['list_style_switch']) || $style_setting['list_style_switch'] == 1) { ?>col-85<?php  } else { ?>list_style_noswitch<?php  } ?>">
					<form action="" method="get" class="clearfix search_form">
						<input type="hidden" name="i" value="<?php  echo $_W['uniacid'];?>">
						<input type="hidden" name="c" value="entry">
						<input type="hidden" name="do" value="list">
						<input type="hidden" name="m" value="superman_mall">
						<div class="searchbar">
							<a class="searchbar-cancel">取消</a>
							<div class="search-input">
								<label class="icon icon-search"></label>
								<input type="search" name="kw" placeholder='输入商品名称关键字...' value=""/>
							</div>
						</div>
					</form>
				</div>
				<?php  if(!isset($style_setting['list_style_switch']) || $style_setting['list_style_switch'] == 1) { ?>
				<a class="col-15 style_switch_btn" href="<?php  echo $this->createMobileUrl('home')?>&__t=<?php  echo time()?>" data-no-cache="true">
					<span class="iconfont fonta1">&#xe610;</span>
				</a>
				<?php  } ?>
			</div>
			<?php  if($slides) { ?>
			<div class="swiper-container home_swiper_warp" data-space-between='10' data-autoplay="3000">
				<div class="swiper-wrapper">
					<?php  if(is_array($slides)) { foreach($slides as $slide) { ?>
					<div class="swiper-slide">
						<a href="<?php  if($slide['url']) { ?><?php  echo $slide['url'];?><?php  } else { ?>#<?php  } ?>" class="<?php  if($slide['url']) { ?>external<?php  } ?>">
							<img src="<?php  echo tomedia($slide['img'])?>">
						</a>
					</div>
					<?php  } } ?>
				</div>
			</div>
			<?php  } ?>
			<?php  if($apps) { ?>
			<div class="row apps no-gutter">
				<?php  if(is_array($apps)) { foreach($apps as $app) { ?>
				<div class="col-25">
					<a href="<?php  if($app['url']) { ?><?php  echo $app['url'];?><?php  } else { ?>#<?php  } ?>" class="external" data-no-cache="true">
						<div class="apps_icon <?php  if(substr($app['icon'], 0, 1) == '#') { ?>apps_icon_bg<?php  } ?>">
							<?php  if(substr($app['icon'], 0, 1) == '#') { ?>
							<i class="iconfont text-center">&<?php  echo $app['icon'];?></i>
							<?php  } else { ?>
							<img src="<?php  echo tomedia($app['icon'])?>"/>
							<?php  } ?>
						</div>
						<span class="text-center color-default text-overflow"><?php  echo $app['title'];?></span>
					</a>
				</div>
				<?php  } } ?>
			</div>
			<?php  } ?>
			<?php  if($top) { ?>
			<div class="list-block topline">
				<div class="item-link">
					<a href="<?php  if($top['url']) { ?><?php  echo $top['url'];?><?php  } else { ?>#<?php  } ?>" class="color-default <?php  if($top['url']) { ?>external<?php  } ?>">
						<div class="item-inner">
							<div class="text-center font7"><?php  echo $top['name'];?></div>
							<div class="font6 color-gray text-overflow"><?php  echo $top['title'];?></div>
						</div>
					</a>
				</div>
			</div>
			<?php  } ?>
            <!--秒杀-->
			<?php  if($items['seckills']) { ?>
			<div class="list-block seckill">
				<div class="item-link">
					<a href="<?php  echo $this->createMobileUrl('seckill', array('type' => $seckill_params['key']))?>" class="color-default <?php echo SUPERMAN_EXTERNAL;?>" data-no-cache="true">
						<div class="item-inner">
							<span class="seckill_name text-strong">秒杀</span>
							<span><?php  echo $seckill_params['key'];?>点场</span>
							<div class="seckill_countdown countdonw font7 color-gray" data-time="<?php  echo $seckill_params['end'];?>">
								<span>00</span>:<span>00</span>:<span>00</span>
							</div>
							<div class="font6 color-gray pull-right seckill_more">更多</div>
						</div>
					</a>
				</div>
				<div class="row seckill_list_wrap">
                    <div class="swiper-container" data-space-between="10" data-slides-per-view="3.5" data-autoplay="3000">
                        <div class="swiper-wrapper">
                            <?php  if(is_array($items['seckills'])) { foreach($items['seckills'] as $item) { ?>
                                <div class="swiper-slide">
                                    <a href="<?php  echo $this->createMobileUrl('detail', array('itemid' => $item['id']))?>" class="<?php echo SUPERMAN_EXTERNAL;?>" data-no-cache="true">
                                        <div class="seckill_img">
                                            <img class="img_square3" src="<?php  echo tomedia($item['cover'])?>" onerror="this.src='<?php  echo $this->superman_placeholder?>'"/>
                                            <?php  if($item['sale_percent']) { ?>
                                            <span class="font6"><?php  if($item['sale_percent'] == 100) { ?>已售完<?php  } else { ?>已售<?php  echo $item['sale_percent'];?>%<?php  } ?></span>
                                            <?php  } ?>
                                        </div>
                                        <span class="now_price font7 color-default">&#165;<?php  echo $item['price'];?></span>
                                        <span class="old_price font7 color-gray">&#165;<?php  echo $item['market_price'];?></span>
                                    </a>
                                </div>
                            <?php  } } ?>
                        </div>
                    </div>
				</div>
			</div>
			<?php  } ?>
            <!--拼团-->
			<?php  if($items['mgroupon']) { ?>
            <div class="list-block mgroupon">
                <div class="item-link">
                    <a href="<?php  echo $this->createMobileUrl('mgroupon')?>" class="color-default <?php echo SUPERMAN_EXTERNAL;?>" data-no-cache="true">
                        <div class="item-inner clearfix">
                            <span class="mgroupon_name text-strong">拼团</span>
                            <div class="font6 color-gray pull-right mgroupon_more">更多</div>
                        </div>
                    </a>
                </div>
                <div class="row mgroupon_list_wrap">
                    <div class="swiper-container" data-space-between="10" data-slides-per-view="3.5" data-autoplay="3000">
                        <div class="swiper-wrapper">
							<?php  if(is_array($items['mgroupon'])) { foreach($items['mgroupon'] as $item) { ?>
							<div class="swiper-slide">
								<a href="<?php  echo $this->createMobileUrl('detail', array('itemid' => $item['id']))?>" class="<?php echo SUPERMAN_EXTERNAL;?>" data-no-cache="true">
									<div class="mgroupon_img">
										<img class="img_square3" src="<?php  echo tomedia($item['cover'])?>" onerror="this.src='<?php  echo $this->superman_placeholder?>'"/>
										<?php  if($item['sales'] > 0) { ?><span class="font6">已售<?php  echo $item['sales'];?></span><?php  } ?>
									</div>
									<span class="now_price font7 color-default">&#165;<?php  echo $item['price'];?></span>
									<span class="old_price font7 color-gray">&#165;<?php  echo $item['market_price'];?></span>
								</a>
							</div>
							<?php  } } ?>
                        </div>
                    </div>
                </div>
            </div>
			<?php  } ?>
            <!--推荐店铺-->
			<?php  if($shops && $setting['recommend_switch']) { ?>
			<div class="list-block recommend_shop_wrap">
				<ul>
					<li>
						<a href="<?php  echo $this->createMobileUrl('shop', array('act' => 'list'))?>" class="<?php echo SUPERMAN_EXTERNAL;?> item-content item-link" data-no-cache="true">
							<div class="item-inner">
								<div class="text-strong">推荐店铺</div>
								<div class="font6 color-gray">更多</div>
							</div>
						</a>
					</li>
					<li></li>
				</ul>
				<div class="row shop_list_wrap">
					<div class="swiper-container" data-space-between="10" data-slides-per-view="3.5" data-autoplay="3000">
						<div class="swiper-wrapper">
							<?php  if(is_array($shops)) { foreach($shops as $shop) { ?>
							<div class="swiper-slide">
								<a href="<?php  echo $this->createMobileUrl('shop', array('shopid' => $shop['id']))?>" class="<?php echo SUPERMAN_EXTERNAL;?>" data-no-cache="true">
									<div>
										<img class="img_square1" src="<?php  echo tomedia($shop['logo'])?>" onerror="this.src='<?php  echo $this->superman_placeholder?>'"/>
									</div>
									<div class="text-overflow font6 text-center color-gray shop_name_wrap">
										<?php  echo $shop['title'];?>
									</div>
								</a>
							</div>
							<?php  } } ?>
						</div>
					</div>
				</div>
			</div>
			<?php  } ?>
			<?php  if($ad_settings) { ?>
			<?php  if(is_array($ad_settings)) { foreach($ad_settings as $ad) { ?>
			<?php  if($ad['banner']['img']) { ?>
			<div class="banner_ad">
				<a href="<?php  if($ad['banner']['url']) { ?><?php  echo $ad['banner']['url'];?><?php  } else { ?>#<?php  } ?>" class="<?php  if($ad['banner']['url']&&strpos($ad['banner']['url'], $_W['siteroot'])===false) { ?>external<?php  } ?>">
					<img src="<?php  echo tomedia($ad['banner']['img'])?>"/>
				</a>
			</div>
			<?php  } ?>
			<?php  if($ad['classified']) { ?>
			<div class="promotion_wrap">
				<div class="row no-gutter">
					<?php  if(is_array($ad['classified'])) { foreach($ad['classified'] as $classified) { ?>
					<div class="col-33">
						<a href="<?php  if($classified['url']) { ?><?php  echo $classified['url'];?><?php  } else { ?>#<?php  } ?>" class="<?php  if($classified['url']&&strpos($classified['url'], $_W['siteroot'])===false) { ?>external<?php  } ?>">
							<img src="<?php  echo tomedia($classified['img'])?>"/>
						</a>
					</div>
					<?php  } } ?>
				</div>
			</div>
			<?php  } ?>
			<?php  } } ?>
			<?php  } ?>
			<div class="list-block recommend_item">
				<div class="item-link recommend_bar">
					<a href="<?php  echo $this->createMobileUrl('list', array('special' => 0))?>" class="color-default <?php echo SUPERMAN_EXTERNAL;?>" data-no-cache="true">
						<div class="item-inner">
							<span class="text-strong">猜你喜欢</span>
							<span class="font6 color-gray">更多</span>
						</div>
					</a>
				</div>
				<div class="list-block media-list">
					<ul class="recommend_item_list row no-gutter <?php  if($list_style == '2') { ?>two_list_bg<?php  } ?>" data-list-style="<?php  echo $list_style;?>" data-list-style-cart-btn="<?php  echo $list_style_cart_btn;?>">
						<?php  if($items['likes']) { ?>
						<?php  if(is_array($items['likes'])) { foreach($items['likes'] as $item) { ?>
						<?php  if($list_style == 1) { ?>
						<li class="item_list_row item-content">

                            <div class="item-media">
                                <a href="<?php  echo $this->createMobileUrl('detail', array('itemid' => $item['id']))?>" class="list_style_one_img <?php echo SUPERMAN_EXTERNAL;?>" data-no-cache="true">
                                    <img class="lazyload img_square2" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC" data-original="<?php  echo tomedia($item['cover'])?>" onerror="this.src='<?php  echo $this->superman_placeholder?>'"/>
									<!--营销活动标签-->
									<!--
									<div class="item_discount clearfix">
										<span class="font6 pull-left discount_typ1">邮</span>
										<span class="font6 pull-left discount_typ2">折</span>
										<span class="font6 pull-left discount_typ3">满</span>
									</div>
									-->
								</a>
							</div>
                            <div class="item-inner">
                                <a href="<?php  echo $this->createMobileUrl('detail', array('itemid' => $item['id']))?>" class="<?php echo SUPERMAN_EXTERNAL;?>" data-no-cache="true">
                                    <div class="item-title-row">
                                        <div class="item-title color-default font7 text-overflow-line2"><?php  echo $item['title'];?></div>
                                    </div>
                                    <div class="item-title-row">
                                        <div class="item-title font8 price">&#165;<?php  echo $item['price'];?> <?php  if($item['market_price'] > 0) { ?><span class="market_price_wrap color-gray font6 text-delete-line">&#165;<?php  echo $item['market_price'];?></span><?php  } ?></div>
                                        <div class="item-after font6 sales"><?php  if($item['sales']) { ?>已售<?php  echo $item['sales'];?><?php  } ?></div>
                                    </div>
                                </a>
								<?php  if(!isset($style_setting['list_style_cart_btn']) || $style_setting['list_style_cart_btn'] == 1) { ?>
                                <a class="button add_cart" href="#" title="添加入购物车" data-itemid="<?php  echo $item['id'];?>" data-skuid="<?php  echo intval($item['skuid'])?>" data-cart-url="<?php  echo $this->createMobileUrl('cart', array('act' => 'post'))?>">
                                    <span class="icon iconfont">&#xe626;</span>
                                </a>
                                <?php  } ?>
                            </div>
						</li>
						<?php  } else if($list_style == 2) { ?>
						<div class="col-50 two_list_style item_list_row">
                            <div class="card two_list_wrap">
                                <a href="<?php  echo $this->createMobileUrl('detail', array('itemid' => $item['id']))?>" class="<?php echo SUPERMAN_EXTERNAL;?>" data-no-cache="true">
                                    <div valign="bottom" class="card-header color-white no-border no-padding">
                                        <img class='lazyload card-cover img_square2' src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC" data-original="<?php  echo tomedia($item['cover'])?>" onerror="this.src='<?php  echo $this->superman_placeholder?>'"/>
                                        <!--营销活动标签-->
										<!--
										<div class="item_discount clearfix">
											<span class="font6 pull-left discount_typ1">邮</span>
											<span class="font6 pull-left discount_typ2">折</span>
											<span class="font6 pull-left discount_typ3">满</span>
                                        </div>
                                        -->
										<?php  if($item['sales']) { ?>
                                        <div class="sales_wrap">
                                            <span class="font6">已售<?php  echo $item['sales'];?></span>
                                        </div>
                                        <?php  } ?>
                                    </div>
                                </a>
                                <div class="card-content">
                                    <div class="card-content-inner">
                                        <a href="<?php  echo $this->createMobileUrl('detail', array('itemid' => $item['id']))?>" class="<?php echo SUPERMAN_EXTERNAL;?>" data-no-cache="true">
                                            <div class="text-overflow color-default"><?php  echo $item['title'];?></div>
                                        </a>
                                        <div class="clearfix font7 two_list_item">
                                            <a href="<?php  echo $this->createMobileUrl('detail', array('itemid' => $item['id']))?>" class="<?php echo SUPERMAN_EXTERNAL;?>" data-no-cache="true">
                                                <span class="pull-left color-danger">&#165;<?php  echo $item['price'];?></span>
                                                <?php  if($item['market_price'] > 0) { ?>
                                                <span class="market_price_wrap color-gray font6 text-delete-line">&#165;<?php  echo $item['market_price'];?></span>
                                                <?php  } ?>
                                            </a>
                                            <?php  if(!isset($style_setting['list_style_cart_btn']) || $style_setting['list_style_cart_btn'] == 1) { ?>
											<a href="#" class="button pull-right add_cart" title="添加入购物车" data-itemid="<?php  echo $item['id'];?>"  data-skuid="<?php  echo intval($item['skuid'])?>" data-cart-url="<?php  echo $this->createMobileUrl('cart', array('act' => 'post'))?>"><span class="icon iconfont">&#xe626;</span></a>
                                            <?php  } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
						</div>
						<?php  } ?>
						<?php  } } ?>
						<?php  } ?>
					</ul>
				</div>
			</div>
			<div class="nodata font6 text-center color-gray" style="display: none">没有了</div>
			<?php  if(count($items['likes'])==$pagesize) { ?>
			<div class="infinite-scroll-preloader">
				<div class="preloader"></div>
			</div>
			<?php  } ?>
		</div>
	</div>
	<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/share', TEMPLATE_INCLUDEPATH)) : (include template('common/share', TEMPLATE_INCLUDEPATH));?>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>