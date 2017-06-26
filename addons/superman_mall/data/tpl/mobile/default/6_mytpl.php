<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<div class="page-group">
	<div class="page superpage_<?php  echo $do;?>" id="superpage_<?php  echo $do;?>_<?php  echo $act;?>">
		<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/nav', TEMPLATE_INCLUDEPATH)) : (include template('common/nav', TEMPLATE_INCLUDEPATH));?>
		<?php  if($act == 'display') { ?>
		<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/title', TEMPLATE_INCLUDEPATH)) : (include template('common/title', TEMPLATE_INCLUDEPATH));?>
		<div class="content">
			<div class="data_wrap" style="background: url('<?php  echo $this->mobile_path?>/images/data_wrap_bg.jpg') no-repeat center; background-size: cover">
				<div class="avatar_wrap">
					<div class="avatar pull-left">
						<img src="<?php  echo tomedia($_W['member']['avatar'])?>" onerror="this.src='resource/images/heading.jpg'"/>
					</div>
					<div class="pull-left member_wrap">
						<div class="font8"><?php  echo $_W['member']['nickname'];?></div>
						<div class="font5"><?php  echo $_W['groupname'];?></div>
					</div>
				</div>
				<div class="nav_wrap">
					<a class="nav_list" href="<?php  echo $this->createMobileUrl('follow')?>"><?php  echo $follow_total;?><span>关注的商品</span></a>
					<!--<a class="nav_list" href="#">50<span>关注的店铺</span></a>-->
					<a class="nav_list" href="<?php  echo $this->createMobileUrl('browse')?>"><?php  echo $browse_total;?><span>浏览记录</span></a>
				</div>
			</div>
			<div class="list-block order_wrap">
				<ul>
					<li class="item-content item-link">
						<a href="<?php  echo $this->createMobileUrl('order')?>">
							<div class="item-inner">
								<div class="item-title title_color font7">
									<span class="iconfont font9 icon_order">&#xe641;</span>
									我的订单
								</div>
								<div class="item-after font6 subtitle_color">全部订单</div>
							</div>
						</a>
					</li>
					<li>
						<div class="row font7 text-center order_item">
							<div class="col-25">
								<a href="<?php  echo $this->createMobileUrl('order', array('status' => 'no_pay'))?>">
									<div class="order_icon_wrap">
										<span class="iconfont color-gray">&#xe63d;</span>
										<?php  if($order_total['no_pay']) { ?><span class="<?php  if($order_total['no_pay'] == '99+') { ?>cornerbig<?php  } else { ?>corner<?php  } ?> font5"><?php  echo $order_total['no_pay'];?></span><?php  } ?>
									</div>
									<div class="color-gray">待支付</div>
								</a>
							</div>
							<div class="col-25">
								<a href="<?php  echo $this->createMobileUrl('order', array('status' => 'no_receive'))?>">
									<div class="order_icon_wrap">
										<span class="iconfont color-gray">&#xe612;</span>
										<?php  if($order_total['no_receive']) { ?><span class="<?php  if($order_total['no_receive'] == '99+') { ?>cornerbig<?php  } else { ?>corner<?php  } ?> font5"><?php  echo $order_total['no_receive'];?></span><?php  } ?>
									</div>
									<div class="color-gray">待收货</div>
								</a>
							</div>
							<div class="col-25">
								<a href="<?php  echo $this->createMobileUrl('order', array('status' => 'no_comment'))?>">
									<div class="order_icon_wrap">
										<span class="iconfont color-gray">&#xe614;</span>
										<?php  if($order_total['no_comment']) { ?><span class="<?php  if($order_total['no_comment'] == '99+') { ?>cornerbig<?php  } else { ?>corner<?php  } ?> font5"><?php  echo $order_total['no_comment'];?></span><?php  } ?>
									</div>
									<div class="color-gray">待评价</div>
								</a>
							</div>
							<div class="col-25">
								<a href="<?php  echo $this->createMobileUrl('refund')?>">
									<div class="order_icon_wrap">
										<span class="iconfont color-gray">&#xe634;</span>
										<!--<span class="corner font5"></span>-->
									</div>
									<div class="color-gray">退款/售后</div>
								</a>
							</div>
						</div>
					</li>
				</ul>
			</div>
			<div class="list-block credit_wrap">
				<ul>
					<li class="item-content item-link">
						<a href="#">
							<div class="item-inner">
								<div class="item-title title_color font7">
									<span class="iconfont font9 icon_credit">&#xe647;</span>
									我的钱包
								</div>
								<!--<div class="item-after font6 subtitle_color">
									余额充值
								</div>-->
							</div>
						</a>
					</li>
					<li>
						<div class="row font7 text-center credit_item">
							<div class="col-25">
								<a href="<?php  echo $this->createMobileUrl('creditlog', array('credittype' => 'credit1'))?>" class="color-gray">
									<div><?php  if($_W['member']['credit1']) { ?><?php  echo SupermanUtil::format_credit($_W['member']['credit1'])?><?php  } else { ?>0<?php  } ?></div>
									<div><?php  echo $credit_titles['credit1']['title'];?></div>
								</a>
							</div>
							<div class="col-25">
								<a href="<?php  echo $this->createMobileUrl('creditlog', array('credittype' => 'credit2'))?>" class="color-gray">
									<div><?php  if($_W['member']['credit2']) { ?><?php  echo SupermanUtil::format_credit($_W['member']['credit2'])?><?php  } else { ?>0<?php  } ?></div>
									<div><?php  echo $credit_titles['credit2']['title'];?></div>
								</a>
							</div>
							<!--<div class="col-25">
								<a href="#" class="color-gray">
									<div>10</div>
									<div>优惠券</div>
								</a>
							</div>-->
						</div>
					</li>
				</ul>
			</div>
			<div class="list-block">
				<ul>
					<?php  if(isset($this->plugin_setting['mgroupon']) && $this->plugin_setting['mgroupon']) { ?>
					<li>
						<a href="<?php  echo $this->createMobileUrl('mgroupon', array('act' => 'display'))?>" class="item-content item-link">
							<div class="item-inner">
								<div class="item-title title_color font7">
									<span class="iconfont font9 icon_distribution">&#xe644;</span>
									我的拼团
								</div>
							</div>
						</a>
					</li>
					<?php  } ?>
					<?php  if(isset($checkout_access) && $checkout_access > 0) { ?>
					<li>
						<a href="#" data-url="<?php  echo $this->createMobileUrl('checkout', array('act' => 'display'))?>" class="item-content item-link checkout_qrcode">
							<div class="item-inner">
								<div class="item-title title_color font7">
									<span class="iconfont font9 icon_sweep">&#xe601;</span>
									扫码核销
								</div>
								<div class="item-after font6 subtitle_color">
									扫一扫
								</div>
							</div>
						</a>
					</li>
					<?php  } ?>
					<?php  if(isset($this->plugin_setting['partner']) && $this->plugin_setting['partner']) { ?>
					<li>
						<a href="<?php  echo $this->createMobileUrl('partner', array('act' => 'home'))?>" class="item-content item-link">
							<div class="item-inner">
								<div class="item-title title_color font7">
									<span class="iconfont font9 icon_partner">&#xe63a;</span>
									<?php  echo $partner_setting['text']['partner_center'];?>
								</div>
								<div class="item-after font6 subtitle_color">
									<?php  echo $partner_setting['text']['get_commission'];?>
								</div>
							</div>
						</a>
					</li>
					<?php  } ?>
				</ul>
			</div>
			<div class="list-block">
				<ul>
					<li>
						<a href="<?php  echo $this->createMobileUrl('cart')?>" class="item-content item-link">
							<div class="item-inner">
								<div class="item-title title_color font7">
									<span class="iconfont font9 icon_cart">&#xe637;</span>
									购物车
								</div>
							</div>
						</a>
					</li>
					<li>
						<a href="<?php  echo $this->createMobileUrl('address')?>" class="item-content item-link">
							<div class="item-inner">
								<div class="item-title title_color font7">
									<span class="iconfont font9 icon_address">&#xe639;</span>
									收货地址
								</div>
								<div class="item-after font6 subtitle_color">
									添加新地址
								</div>
							</div>
						</a>
					</li>
					<li>
						<?php  if($this->module['config']['service']['link']) { ?>
						<a href="<?php  echo $this->module['config']['service']['link']?>" class="item-content item-link <?php  if(!strexists($this->module['config']['service']['link'], 'tel:')) { ?>external<?php  } ?>">
						<?php  } else { ?>
						<a href="<?php  echo $this->createMobileUrl('service')?>" class="item-content item-link <?php echo SUPERMAN_EXTERNAL;?>" data-no-cache="true">
						<?php  } ?>
							<div class="item-inner">
								<div class="item-title title_color font7">
									<span class="iconfont font9 icon_customer">&#xe646;</span>
									投诉建议
								</div>
							</div>
						</a>
					</li>
					<?php  if($_W['member']['uid']) { ?>
					<?php  if($setting['join_switch']) { ?>
					<li>
						<?php  if(defined('SUPERMAN_DEVELOPMENT')) { ?>
						<?php  if(!$shop_user) { ?>
						<a href="<?php  echo $shop_url;?>" class="<?php  if(!$_W['fans']['follow']) { ?>external<?php  } ?> item-content item-link shop_join_link" data-subscribe-url="<?php  echo $subscribe_setting['subscribeurl'];?>" data-subscribe-tips="关注公众号，轻松开店！" data-follow="<?php  echo $_W['fans']['follow'];?>" data-no-cache="true">
							<div class="item-inner">
								<div class="item-title title_color font7">
									<span class="iconfont font9 icon_join">&#xe600;</span>
									商户入驻
								</div>
								<div class="item-after font6 subtitle_color">
									轻松开店
								</div>
							</div>
						</a>
						<?php  } else { ?>
						<a href="<?php  echo $shop_url;?>" class="external item-content item-link">
							<div class="item-inner">
								<div class="item-title title_color font7">
									<span class="iconfont font9 icon_join">&#xe600;</span>
									商户后台
								</div>
							</div>
						</a>
						<?php  } ?>
						<?php  } else { ?>
						<a href="<?php  echo $this->createMobileUrl('shop', array('act' => 'reg'))?>" class="<?php  if(!$_W['fans']['follow']) { ?>external<?php  } ?> item-content item-link shop_join_link" data-subscribe-url="<?php  echo $subscribe_setting['subscribeurl'];?>" data-subscribe-tips="关注公众号，轻松开店！" data-follow="<?php  echo $_W['fans']['follow'];?>" data-no-cache="true">
							<div class="item-inner">
								<div class="item-title title_color font7">
									<span class="iconfont font9 icon_join">&#xe600;</span>
									商户入驻
								</div>
								<div class="item-after font6 subtitle_color">
									轻松开店
								</div>
							</div>
						</a>
						<?php  } ?>
					</li>
					<?php  } ?>
					<li>
						<a href="<?php  echo $this->createMobileUrl('logout')?>" class="external item-content item-link">
							<div class="item-inner">
								<div class="item-title title_color font7">
									<span class="iconfont font9 icon_logout">&#xe643;</span>
									退出
								</div>
							</div>
						</a>
					</li>
					<?php  } ?>
				</ul>
			</div>
		</div>
		<?php  } ?>
	</div>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>