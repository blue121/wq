<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/title', TEMPLATE_INCLUDEPATH)) : (include template('common/title', TEMPLATE_INCLUDEPATH));?>
<?php  if($location == '') { ?>
<div class="content infinite-scroll shop_list" data-flag="0" data-list-url="<?php  echo $this->createMobileUrl('shop', array('act' => 'list', 'load' => 'infinite'))?>" data-kw="<?php  echo $kw;?>" data-order-payed="<?php  echo $_GPC['order_payed'];?>" data-getlocal="<?php  echo $_GPC['getlocal'];?>" data-latitude="<?php  echo $latitude;?>" data-longitude="<?php  echo $longitude;?>" data-shop-url="<?php  echo $this->createMobileUrl('shop', array('act' => 'display'))?>" data-distance="50" data-page="<?php  echo $pindex;?>">
    <div class="card shop_list_wrap">
        <?php  if($list) { ?>
        <div class="list-block media-list">
            <ul class="shop_list_ul">
                <li class="shop_list_order">
                    <div class="item-content">
                        <div class="item-inner row text-center font7 no-gutter">
                            <div class="col-33">
                                <a href="<?php  echo $this->createMobileUrl('shop', array('act' => 'list'))?>" class="color-default  <?php  if($selected == 1) { ?>active<?php  } ?>">综合</a>
                            </div>
                            <div class="col-33">
                                <a href="<?php  echo $this->createMobileUrl('shop', array('act' => 'list', 'order_payed' => 1))?>" class="color-default <?php  if($selected == 2) { ?>active<?php  } ?>">销量</a>
                            </div>
                            <div class="col-33">
                                <a href="<?php  echo $this->createMobileUrl('shop', array('act' => 'list', 'location' => 'get'))?>" class="color-default external <?php  if($selected == 3) { ?>active<?php  } ?>">离我最近</a>
                            </div>
                        </div>
                    </div>
                </li>
                <?php  if(is_array($list)) { foreach($list as $li) { ?>
                <li class="shop_info">
                    <a href="<?php  echo $this->createMobileUrl('shop', array('shopid' => $li['id']))?>" class="item-content color-default external">
                        <div class="item-media">
                            <img class="shop_avatar img_square1" src="<?php  echo tomedia($li['logo'])?>" onerror="this.src='<?php  echo $this->superman_placeholder?>'"/>
                        </div>
                        <div class="item-inner">
                            <div class="item-title-row">
                                <div class="text-overflow">
                                    <span class="font8 text-strong shop_title">
                                        <?php  echo $li['title'];?>
                                    </span>
                                </div>
                            </div>
                            <div class="item-title-row font7">

                                <div class="text-overflow color-gray">
                                    <?php  if($li['phone']) { ?>
                                    <span class="iconfont color-gray font6">&#xe619;</span> <?php  echo $li['phone'];?>
                                    <?php  } ?>
                                </div>
                                <div class="item-after distance">
                                    <?php  if($li['distance']) { ?>
                                    <?php  echo $li['distance'];?>km
                                    <?php  } ?>
                                </div>
                            </div>
                            <?php  if($li['province']) { ?>
                            <div class="item-title-row font7">
                                <div class="text-overflow color-gray"><span class="iconfont color-gray font6">&#xe605;</span> <?php  echo $li['province'].$li['city'].$li['district'].$li['address']?></div>
                            </div>
                            <?php  } ?>
                        </div>
                    </a>
                </li>
				<?php  if($li['activity']) { ?>
                <div class="row no-gutter shop_discount_wrap">
                    <div class="col-80">
                        <div class="shop_discount_row text-overflow color-gray">
                            <a href="<?php  echo $this->createMobileUrl('shop', array('shopid' => $li['id'],'activityid' => $li['activity'][0]['id']))?>" class="color-gray">
                                <span class="button button-danger font6"><?php  echo $li['activity'][0]['title'];?></span>
                                <span class="font6">活动时间：<?php  echo $li['activity'][0]['start'];?> ~ <?php  echo $li['activity'][0]['end'];?></span>
                            </a>
                        </div>
						<?php  if(count($li['activity']) > 1) { ?>
                        <div class="show_more_activity">
							<?php  if(is_array($li['activity'])) { foreach($li['activity'] as $k => $activity) { ?>
							<?php  if($k != 0) { ?>
                            <div class="shop_discount_row text-overflow color-gray">
                                <a href="<?php  echo $this->createMobileUrl('shop', array('shopid' => $li['id'],'activityid' => $activity['id']))?>" class="color-gray">
                                    <span class="button button-danger font6"><?php  echo $activity['title'];?></span>
                                    <span class="font6">活动时间：<?php  echo $activity['start']?> ~ <?php  echo $activity['end']?></span>
                                </a>
                            </div>
							<?php  } ?>
							<?php  } } ?>
                        </div>
						<?php  } ?>
                    </div>
                    <div class="col-20 more_activity">
                        <span class="font6 color-gray"><?php  echo count($li['activity'])?>个活动</span>
                        <span class="icon icon-down more_activity_icon font6 color-gray"></span>
                    </div>
                </div>
				<?php  } ?>
                <?php  } } ?>
            </ul>
        </div>
        <?php  } else { ?>
        <div class="row text-center">
            <p class="color-gray font7">未找到数据</p>
        </div>
        <?php  } ?>
    </div>
    <div class="nodata font6 text-center color-gray" style="display: none;">没有了</div>
    <?php  if(count($list)==$pagesize) { ?>
    <div class="infinite-scroll-preloader">
        <div class="preloader"></div>
    </div>
    <?php  } ?>
</div>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/nav', TEMPLATE_INCLUDEPATH)) : (include template('common/nav', TEMPLATE_INCLUDEPATH));?>