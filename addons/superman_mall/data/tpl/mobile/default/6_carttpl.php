<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<div class="page-group">
	<div class="page superpage_<?php  echo $do;?>" id="superpage_<?php  echo $do;?>_<?php  echo $act;?>">
		<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/nav', TEMPLATE_INCLUDEPATH)) : (include template('common/nav', TEMPLATE_INCLUDEPATH));?>
		<?php  if($act == 'display') { ?>
		<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/title', TEMPLATE_INCLUDEPATH)) : (include template('common/title', TEMPLATE_INCLUDEPATH));?>
		<div class="content">
			<div class="cart_wrap list-block media-list">
				<?php  if($list) { ?>
				<?php  if(is_array($list)) { foreach($list as $item) { ?>
				<ul class="shopname_wrap">
					<li>
						<a href="<?php  echo $this->createMobileUrl('shop', array('shopid' => $item['shop']['id']))?>" class="item-content item-link">
							<div class="item-inner">
								<div class="item-title font8"><?php  echo $item['shop']['title'];?></div>
							</div>
						</a>
					</li>
				</ul>
				<ul class="item_list_wrap">
					<?php  if(is_array($item['list'])) { foreach($item['list'] as $li) { ?>
					<li class="clearfix">
						<label class="label-checkbox item-content pull-left">
							<input type="checkbox" <?php  if($li['checked']) { ?>checked<?php  } ?> name="cart_id" value="<?php  echo $li['id'];?>" data-shopid="<?php  echo $li['item']['shopid'];?>" data-cartid="<?php  echo $li['id'];?>" data-url="<?php  echo $this->createMobileUrl('cart', array('act' => 'post'))?>" data-price="<?php  if(isset($li['item']['sku']['price'])&&$li['item']['sku']['price']) { ?><?php  echo $li['item']['sku']['price'];?><?php  } else { ?><?php  echo $li['item']['price'];?><?php  } ?>">
							<div class="item-media item_checkbox" data-checked="<?php  if($li['checked']) { ?>1<?php  } else { ?>0<?php  } ?>" data-cartid="<?php  echo $li['id'];?>" data-url="<?php  echo $this->createMobileUrl('cart', array('act' => 'post'))?>" data-price="<?php  if(isset($li['item']['sku']['price'])&&$li['item']['sku']['price']) { ?><?php  echo $li['item']['sku']['price'];?><?php  } else { ?><?php  echo $li['item']['price'];?><?php  } ?>">
								<i class="icon icon-form-checkbox"></i>
							</div>
						</label>
						<label class="label-checkbox item-content">
							<a href="<?php  echo $this->createMobileUrl('detail', array('act' => 'display', 'itemid' => $li['itemid']))?>" class="<?php echo SUPERMAN_EXTERNAL;?>" data-no-cache="true">
								<div class="item-media item_img">
									<img src="<?php  echo tomedia($li['item']['cover'])?>" onerror="this.src='<?php  echo $this->superman_placeholder?>'">
								</div>
								<div class="item-inner">
									<div class="item-title-row">
										<div class="item-title color-default font7 text-overflow-line2">
											 <?php  echo $li['item']['title'];?>
										</div>
									</div>
									<div class="item-subtitle font6 color-gray">
										<?php  if($li['skuid']) { ?>
											<?php  if(isset($li['item']['sku']['attr'])&&$li['item']['sku']['attr']) { ?>
											<?php  if(is_array($li['item']['sku']['attr'])) { foreach($li['item']['sku']['attr'] as $a) { ?>
												<?php  echo $a['title'];?>:<?php  echo $a['value'];?>&nbsp;
											<?php  } } ?>
											<?php  } ?>
										<?php  } else { ?>
											<?php  echo $li['sku'];?>
										<?php  } ?>
									</div>
									<div class="item-title-row">
										<div class="item-title font8 price pull-left">
											&#165;<?php  if(isset($li['item']['sku']['price'])&&$li['item']['sku']['price']) { ?><?php  echo $li['item']['sku']['price'];?><?php  } else { ?><?php  echo $li['item']['price'];?><?php  } ?>
											<?php  if($li['item']['special']==2) { ?><span class="mgroupon_title">拼团价</span><?php  } ?>
											<?php  if($li['item']['special']==1) { ?><span class="mgroupon_title">秒杀价</span><?php  } ?>
										</div>
										<div class="item-after font6">
											<div class="pull-right">
												<?php  if($li['item']['status'] == 1) { ?>
													<?php  if($li['_no_stock']) { ?>
													<a class="color-gray" href="#">无库存</a>
													<?php  } else { ?>
													<div class="buttons-row">
														<a class="button btn_minus" data-cartid="<?php  echo $li['id'];?>" data-url="<?php  echo $this->createMobileUrl('cart', array('act' => 'post'))?>" data-title="购买数不能小于1" data-min-total="1" href="#">-</a>
														<input class="number" type="text" value="<?php  echo $li['total'];?>" data-cartid="<?php  echo $li['id'];?>" data-url="<?php  echo $this->createMobileUrl('cart', array('act' => 'post'))?>"/>
														<a class="button btn_plus" data-cartid="<?php  echo $li['id'];?>" data-url="<?php  echo $this->createMobileUrl('cart', array('act' => 'post'))?>" data-title="库存不足" href="#" data-max-total="<?php  if($li['skuid']) { ?><?php  echo $li['item']['sku']['total'];?><?php  } else { ?><?php  echo $li['item']['total'];?><?php  } ?>">+</a>
													</div>
													<?php  } ?>
												<?php  } else { ?>
													<a class="color-gray" href="#">已下架</a>
												<?php  } ?>
											</div>
										</div>
									</div>
								</div>
							</a>
						</label>
					</li>
					<?php  } } ?>
				</ul>
				<?php  } } ?>
				<?php  } else { ?>
				<div class="text-center color-gray font7 msg_tips">
					<?php  if($_W['member']['uid']) { ?>
						<span>购车是空的</span>
						<div class="content-padded">
							<div class="row">
								<div class="col-50">
									<a href="<?php  echo $this->createMobileUrl('home')?>" class="button button-dark">去首页逛逛</a>
								</div>
								<div class="col-50">
									<a href="<?php  echo $this->createMobileUrl('follow')?>" class="button button-dark">看看关注</a>
								</div>
							</div>
						</div>
					<?php  } else { ?>
						<span>登录后可查看购物车商品</span>
						<a href="<?php  echo url('auth/login', array('forward' => base64_encode($_SERVER['QUERY_STRING'])))?>" class="external">登录</a>
					<?php  } ?>
				</div>
				<?php  } ?>
				<div class="nodata font6 text-center color-gray">没有了</div>
			</div>
		</div>
		<nav class="bar bar-tab btn_buy">
			<div class="row no-gutter clearfix">
				<div class="col-25">
					<label class="text-center">
						<input type="checkbox" class="checkall" data-url="<?php  echo $this->createMobileUrl('cart', array('act' => 'post'))?>">全选
					</label>
				</div>
				<div class="col-40">
					合计: <span class="total_price"><?php  echo $cart['total_price'];?></span>
				</div>
				<div class="settlement pull-right">
					<a href="#" data-url="<?php  echo $this->createMobileUrl('confirm', array('act' => 'check'))?>" class="submit_cart">
						结算 <span class="font6">(<span class="total_item"><?php  echo $cart['total_item'];?></span>)</span>
					</a>
				</div>
			</div>
		</nav>
		<?php  } ?>
	</div>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>