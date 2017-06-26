<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header-base', TEMPLATE_INCLUDEPATH)) : (include template('common/header-base', TEMPLATE_INCLUDEPATH));?>
<link href="/web/video/index.css" rel="stylesheet" type="text/css">
<div class="home">
	<div class="content clearfix">
		<div id="head">
			<div class="logo" <?php  if(!empty($copyright['flogo'])) { ?>style="background:url('<?php  echo tomedia($copyright['flogo']);?>') no-repeat;"<?php  } ?>></div>
			<div class="advertisement">
				<?php  if(!empty($copyright['notice'])) { ?>
					<?php  echo $copyright['notice'];?>
				<?php  } else { ?>
					
				<?php  } ?>
			</div>
			<div class="btns">
				<a href="<?php  echo url('user/login');?>" class="btn btn-primary btn-lg"><i class="fa fa-user"></i> 立即登录</a>
				<?php  if(!empty($settings['register']['open'])) { ?>
				<a href="<?php  echo url('user/register');?>" class="btn btn-primary btn-lg"><i class="fa fa-user-plus"></i> 我要加入</a>
				<?php  } ?>
			</div>
		</div>
		<div id="banner" class="carousel slide" data-ride="carousel">
			<div class="carousel-indicators"><i class="fa fa-angle-double-down"></i></div>
			<div class="carousel-inner" role="listbox">
				<?php  if(!empty($copyright['slides'])) { ?>
					<?php  $i = 1;?>
					<?php  if(is_array($copyright['slides'])) { foreach($copyright['slides'] as $slide) { ?>
						<div class="item <?php  if($i == 1) { ?>active<?php  } ?>" style="background-image:url(<?php  echo tomedia($slide);?>);"></div>
						<?php  $i++;?>
					<?php  } } ?>
				<?php  } ?>
			   <!--资源-->
<div class="intro">
    	<div class="introc">
		    <div class="intr_tit">微信营销视频详解</div>
			<div class="intr_ftit">WeChat marketing video explanation
</div>
<div class="intr_ftit">
<span class="en-voide-bg" style="
    position: absolute;
    left: 145px;
"><img src="/web/video/clientframe.png" alt="" style="
    height: 472px;
    width: 780px;
"></span>
<div id="a1" class="en-voide" style="
    width: 1079px;
    height: 400px;
    position: relative;
    z-index: 10;
    left: 10px;
    top: 35px;
	border-radius: 5px;
	background-color: rgba(0, 0, 0, 0);
">
</div>
</div>
<script type="text/javascript" src="video/ckplayer/ckplayer.js" charset="utf-8"></script>
<script type="text/javascript">
	var flashvars={
		p:1,
		e:1,
		hl:'video/video.mp4',
		ht:'20',
		hr:'http://www.tule5.com'
		};
	var video=['video/video.mp4->video/mp4',];
	var support=['all'];
	CKobject.embedHTML5('a1','ckplayer_a1',1050,400,video,flashvars,support);
</script>
        	<div class="intr_tit" style
			="margin-top: 75px;">微信营销推广平台 ABOUT US</div>
            <div class="intr_ftit">联合具备影响力的微信公众平台打造的微信第三方平台</div>
            <div class="intr_lt"><img src="/web/video/images/lt_tit.png"></div>
            <div class="intr1">
                <div class="fl">
                	<div class="ro_tit">如何在微信获取精准受众？</div>
                </div>
                <div class="fot">
                    <p class="f_28">“图乐”微信营销平台</p>
                </div>
                <div class="cl"></div>
                <div class="itr_pic"><img src="/web/video/images/intr_dt.png"></div>
               	<div class="itr_con1">
                	<b class="itr_b1"></b>
                    <b class="itr_b2"></b>
                	<p>不知道如何在微信上推广自己的品牌？</p>
                    <p>还在为如何在微信上找到自己的目标受众而苦恼？</p>
                </div>
                <div class="itr_con2">
                	<b class="itr_b1"></b>
                    <b class="itr_b2"></b>
                	<p>让海量微信第三方功能为品牌代言！</p>
                	<p>通过平台功能筛选，将品牌信息传播给目标受众。</p>
                </div>
                <div class="cl"></div>
            </div>
        </div>    
<div class="resource">
    	<div class="res">
        	<div class="intr_tit">产品服务中心 SERVICE</div>
            <div class="intr_ftit">我们提供最优质的互联网产品</div>
            
            <div class="serv clearfloat">
            	<div class="ser ser1">
                	<img class="ser_pic" src="/web/video/images/svs1.jpg" /> 
                    <div class="ser_con">
                    	<div class="ser_bg"></div>
                        <div class="ser_nt">
                        	<p class="p_tit">微信第三方平台</p>
                            <div class="ser_font">
                                <p class="ser_p1">微信第三方应用开发，企业专属模块定制</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ser ser2">
                	<img class="ser_pic" src="/web/video/images/svs2.jpg" />
                    <div class="ser_con">
                    	<div class="ser_bg"></div>
                        <div class="ser_nt">
                        	<p class="p_tit tit_red">H5营销工具</p>
                            <div class="ser_font">
                            	<p>移动建站平台。</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="serv serv1 clearfloat">
            	<div class="ser ser1">
                	<img class="ser_pic" src="/web/video/images/svs3.jpg" />
                    <div class="ser_con">
                    	<div class="ser_bg"></div>
                        <div class="ser_nt">
                        	<p class="p_tit tit_blu">什么是营销活动？</p>
                            <div class="ser_font1">
                            	<p>适合社群传播、粉丝互动、O2O导流、口碑营销等场景</p>
                                <p class="p_img"><img src="/web/video/images/ser_pic1.png" /></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ser ser2">
                	<img class="ser_pic" src="/web/video/images/svs4.jpg" />
                    <div class="ser_con">
                    	<div class="ser_bg"></div>
                        <div class="ser_nt">
                        	<p class="p_tit tit_yle">什么是O2O推广？</p>
                            <div class="ser_font ser_fontc">
                            	<p>微信增加真实精准的粉丝成本高效率低。但图乐提供从线下场景向线上导流的推广方式一举突破增粉瓶颈。目前上线的高铁微信O2O推广项目通过娱乐场景设计，让乘坐高铁一等座的高端消费人群，迅速成为你的微信公众号的真实粉丝。</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>    
		<div class="con container">
			<div class="panel panel-default">
				<h4>系统功能介绍</h4>
				<div class="panel-body">
					<div class="row system-info">
						<div class="col-xs-3">
							<div class="icon"><i class="fa fa-tablet"></i></div>
							<h6>微网站、微场景</h6>
						</div>
						<div class="col-xs-3">
							<div class="icon"><i class="fa fa-bullseye"></i></div>
							<h6>微信营销解决方案</h6>
						</div>
						<div class="col-xs-3">
							<div class="icon"><i class="fa fa-life-ring"></i></div>
							<h6>微信账号集中一站管理</h6>
						</div>
						<div class="col-xs-3">
							<div class="icon"><i class="fa fa-sitemap"></i></div>
							<h6>强大的商家运营管理平台</h6>
						</div>
					</div>
				</div>
			</div>
 
			<?php  if(!empty($news) || !empty($notices)) { ?>
				<div class="panel panel-default">
					<h4>系统公告</h4>
					<div class="panel-body">
						<div class="row system-announcement">
							<div class="col-xs-6">
								<ul class="list-unstyled">
									<?php  if(is_array($news)) { foreach($news as $new) { ?>
										<li><span class="style-1">新闻</span><a href="<?php  echo url('article/news-show/detail', array('id' => $new['id']));?>"><?php  echo $new['title'];?></a></li>
									<?php  } } ?>
								</ul>
							</div>
							<div class="col-xs-6">
								<ul class="list-unstyled">
									<?php  if(is_array($notices)) { foreach($notices as $notice) { ?>
										<li><span class="style-2">公告</span><a href="<?php  echo url('article/notice-show/detail', array('id' => $notice['id']));?>"><?php  echo $notice['title'];?></a></li>
									<?php  } } ?>
								</ul>
							</div>
						</div>
					</div>
				</div>
			<?php  } ?>
			
			<div class="panel panel-default">
				<h4>功能模块介绍</h4>
				<div class="panel-body">
					<div class="row module-info">
						<div class="col-xs-2">
							<div class="icon">
								<img src="resource/images/module/01.png">
							</div>
							<h6>微现场</h6>
						</div>
						<div class="col-xs-2">
							<div class="icon">
								<img src="resource/images/module/02.png">
							</div>
							<h6>微旅游</h6>
						</div>
						<div class="col-xs-2">
							<div class="icon">
								<img src="resource/images/module/03.png">
							</div>
							<h6>微外卖</h6>
						</div>
						<div class="col-xs-2">
							<div class="icon">
								<img src="resource/images/module/04.png">
							</div>
							<h6>微商城</h6>
						</div>
						<div class="col-xs-2">
							<div class="icon">
								<img src="resource/images/module/05.png">
							</div>
							<h6>微汽车</h6>
						</div>
						<div class="col-xs-2">
							<div class="icon">
								<img src="resource/images/module/06.png">
							</div>
							<h6>微房产</h6>
						</div>
						<div class="col-xs-2">
							<div class="icon">
								<img src="resource/images/module/08.png">
							</div>
							<h6>微点菜</h6>
						</div>
						<div class="col-xs-2">
							<div class="icon">
								<img src="resource/images/module/09.png">
							</div>
							<h6>微喜帖</h6>
						</div>
						<div class="col-xs-2">
							<div class="icon">
								<img src="resource/images/module/11.png">
							</div>
							<h6>微网站</h6>
						</div>
						<div class="col-xs-2">
							<div class="icon">
								<img src="resource/images/module/12.png">
							</div>
							<h6>微投票</h6>
						</div>
						<div class="col-xs-2">
							<div class="icon">
								<img src="resource/images/module/13.png">
							</div>
							<h6>微信自定义菜单</h6>
						</div>
						<div class="col-xs-2">
							<div class="icon">
								<img src="resource/images/module/14.png">
							</div>
							<h6>微信会员卡</h6>
						</div>
						<div class="col-xs-2">
							<div class="icon">
								<img src="resource/images/module/15.png">
							</div>
							<h6>微信营销活动</h6>
						</div>
						<div class="col-xs-2">
							<div class="icon">
								<img src="resource/images/module/17.png">
							</div>
							<h6>微信优惠券活动</h6>
						</div>
						<div class="col-xs-2">
							<div class="icon">
								<img src="resource/images/module/18.png">
							</div>
							<h6>微信LBS位置回复</h6>
						</div>
						<div class="col-xs-2">
							<div class="icon">
								<img src="resource/images/module/19.png">
							</div>
							<h6>微相册</h6>
						</div>
						<div class="col-xs-2">
							<div class="icon">
								<img src="resource/images/module/20.png">
							</div>
							<h6>微订单</h6>
						</div>
						<div class="col-xs-2">
							<div class="icon">
								<img src="resource/images/module/21.png">
							</div>
							<h6>微统计</h6>
						</div>
					</div>

				</div>
			</div>
			<div class="contact panel panel-default">
				<h4>联系我们</h4>
				<div class="panel-body">
					<div class="map col-xs-5" style="height:150px;">
						<div class="img" style="width:100%; height:150px; border:#ccc solid 1px;" id="baidumap"></div>
					</div>
					<div class="info col-xs-7">
						<p>联系人：<?php  echo $copyright['person'];?></p>
						<p>Q&nbsp;&nbsp;&nbsp;&nbsp; Q：<?php  echo $copyright['qq'];?></p>
						<p>手&nbsp;&nbsp;&nbsp;机：<?php  echo $copyright['phone'];?></p>
						<p>公&nbsp;&nbsp;&nbsp;司：<?php  echo $copyright['company'];?></p>
						<p>地&nbsp;&nbsp;&nbsp;址：<?php  echo $copyright['address'];?></p>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="center-block footer" role="footer">
		<div class="text-center">
			<?php  if(empty($copyright['footerright'])) { ?><?php  } else { ?><?php  echo $copyright['footerright'];?><?php  } ?> &nbsp; &nbsp; <?php  if(!empty($copyright['statcode'])) { ?><?php  echo $copyright['statcode'];?><?php  } ?>
		</div>
		<div class="text-center">
			<?php  if(empty($copyright['footerleft'])) { ?><?php  } else { ?><?php  echo $copyright['footerleft'];?><?php  } ?>
		</div>
	</div>
</div>
<script type="text/javascript" src="http://api.map.baidu.com/api?v=1.4"></script>
<script>
	require(['bootstrap']);
	var bmap = {
		'option' : {
			'lock' : false,
			'container' : 'baidumap',
			'infoWindow' : {'width' : 250, 'height' : 100, 'title' : ''},
			'point' : {'lng' : '<?php  echo $copyright['baidumap']['lng'];?>', 'lat' : '<?php  echo $copyright['baidumap']['lat'];?>'}
		},
		'init' : function(option) {
			var $this = this;
			$this.option = $.extend({},$this.option,option);
			$this.option.defaultPoint = new BMap.Point($this.option.point.lng, $this.option.point.lat);
			$this.bgeo = new BMap.Geocoder();
			$this.bmap = new BMap.Map($this.option.container);
			$this.bmap.centerAndZoom($this.option.defaultPoint, 15);
			$this.bmap.enableScrollWheelZoom();
			$this.bmap.enableDragging();
			$this.bmap.enableContinuousZoom();
			$this.bmap.addControl(new BMap.NavigationControl());
			$this.bmap.addControl(new BMap.OverviewMapControl());
			//添加标注
			$this.marker = new BMap.Marker($this.option.defaultPoint);
			$this.marker.enableDragging();
			$this.bmap.addOverlay($this.marker);
			$this.marker.setAnimation(BMAP_ANIMATION_BOUNCE);
		},
		'setMarkerCenter' : function() {
			var $this = this;
			var center = $this.bmap.getCenter();
			$this.marker.setPosition(new BMap.Point(center.lng, center.lat));
			$this.showPointValue();
			$this.showAddress();
		}
	};
	$(function(){
		var option = {};
		option = {'point' : {'lng' : '<?php  echo $copyright['baidumap']['lng'];?>', 'lat' : '<?php  echo $copyright['baidumap']['lat'];?>'}}
		bmap.init(option);

		//banner
		$('#banner, #banner .item').height($(window).height());
	});
</script>
<link href="/web/video/images/kefuqq.css" rel="stylesheet" type="text/css">
<div class="lanrenzhijia_m">
<div class="kf-pop-text">
<h1>尊敬的用户：</h1>
<span>您好，请问有什么可以帮到您？</span>
<div class="kf-pop-btn">
<a href="tencent://message/?uin=97391583&Site=http://www.tule5.com/&Menu=yes" class="kf-pop-ok"><div class="phone-name">QQ交谈</div><div class="phone-nub"></div></a>
<a class="kf-pop-cancel" href="javascript:;">下次再说</a>
</div>
</div>
</div>
<script src="/web/video/images/jquery.min.js"></script>
<script>
$(function(){
	$('.lanrenzhijia_m').delay(3000).slideDown();
	$('.kf-pop-cancel').click(function(){
		$('.lanrenzhijia_m').slideUp();
	});
});
</script>
</body>
</html>