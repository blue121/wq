<?php defined('IN_IA') or exit('Access Denied');?><div id="gotop" style="display: none;"><span class="iconfont fonta2"></span></div>
<?php  echo $this->superman_global_js?>
<?php  if(defined('LOCAL_DEVELOPMENT')) { ?>
<script type="text/javascript" src="//g.alicdn.com/msui/sm/0.6.2/js/sm.js" charset="utf-8"></script>
<script type="text/javascript" src="//g.alicdn.com/msui/sm/0.6.2/js/sm-extend.js" charset="utf-8"></script>
<script type="text/javascript" src="//g.alicdn.com/msui/sm/0.6.2/js/sm-city-picker.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php  echo $this->mobile_path?>/js/zepto.picLazyLoad.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php  echo $this->mobile_path?>/js/zepto.cookie.js" charset="utf-8"></script>
<?php  } else { ?>
<script type="text/javascript" src="//g.alicdn.com/msui/sm/0.6.2/js/sm.min.js" charset="utf-8"></script>
<script type="text/javascript" src="//g.alicdn.com/msui/sm/0.6.2/js/sm-extend.min.js" charset="utf-8"></script>
<script type="text/javascript" src="//g.alicdn.com/msui/sm/0.6.2/js/sm-city-picker.min.js" charset="utf-8"></script>
<script src="<?php  echo $this->mobile_path?>/js/zepto.picLazyLoad.min.js" charset="utf-8"></script>
<script src="<?php  echo $this->mobile_path?>/js/zepto.cookie.min.js" charset="utf-8"></script>
<?php  } ?>
<?php  echo $this->superman_main_js?>
<?php
	$_share['title'] = !empty($_share['title']) ? $_share['title'] : $_W['account']['name'];
	$_share['imgUrl'] = !empty($_share['imgUrl']) ? $_share['imgUrl'] : '';
	if(isset($_share['content'])){
		$_share['desc'] = $_share['content'];
		unset($_share['content']);
	}
	$_share['desc'] = !empty($_share['desc']) ? $_share['desc'] : '';
	$_share['desc'] = preg_replace('/\s/i', '', str_replace('	', '', cutstr(str_replace('&nbsp;', '', ihtmlspecialchars(strip_tags($_share['desc']))), 60)));
	if(empty($_share['link'])) {
		$_share['link'] = '';
		$query_string = $_SERVER['QUERY_STRING'];
		if(!empty($query_string)) {
			parse_str($query_string, $query_arr);
			$query_arr['u'] = $_W['member']['uid'];
			$query_string = http_build_query($query_arr);
			$_share['link'] = $_W['siteroot'].'app/index.php?'. $query_string;
		}
	}
?>
<script>
	var $_share = <?php  echo json_encode($_share)?>;
	if(typeof sharedata == 'undefined'){
		sharedata = $_share;
	} else {
		sharedata['title'] = sharedata['title'] || $_share['title'];
		sharedata['desc'] = sharedata['desc'] || $_share['desc'];
		sharedata['link'] = sharedata['link'] || $_share['link'];
		sharedata['imgUrl'] = sharedata['imgUrl'] || $_share['imgUrl'];
	}
	function tomedia(src) {
		if(typeof src != 'string')
			return '';
		if(src.indexOf('http://') == 0 || src.indexOf('https://') == 0) {
			return src;
		} else if(src.indexOf('./addons') == 0) {
			src=src.substr(2);
			return window.sysinfo.siteroot + src;
		} else if(src.indexOf('../addons') == 0 || src.indexOf('../attachment') == 0) {
			src=src.substr(3);
			return window.sysinfo.siteroot + src;
		} else if(src.indexOf('./resource') == 0) {
			src=src.substr(2);
			return window.sysinfo.siteroot + 'app/' + src;
		} else if(src.indexOf('images/') == 0) {
			return window.sysinfo.attachurl+ src;
		}
	}

	if(sharedata.imgUrl == ''){
		var _share_img = $('div.page-current img').attr("src");
		if(_share_img == ""){
			sharedata['imgUrl'] = window.sysinfo.siteroot + 'addons/superman_mall/icon.jpg';
		} else {
			sharedata['imgUrl'] = tomedia(_share_img);
		}
	}

	if(sharedata.desc == ''){
		var _share_content = _removeHTMLTag($('div.page-current').html());
		if(typeof _share_content == 'string'){
			sharedata.desc = _share_content.replace($_share['title'], '')
		}
	}
	wx.ready(function () {
		wx.onMenuShareAppMessage(sharedata);
		wx.onMenuShareTimeline(sharedata);
		wx.onMenuShareQQ(sharedata);
		wx.onMenuShareWeibo(sharedata);
	});
</script>
<?php  if(!defined('LOCAL_DEVELOPMENT')) { ?>
	<?php  if(isset($this->module['config']['base']['stat_switch']) && $this->module['config']['base']['stat_switch'] == 1 && $this->module['config']['base']['stat_code']) { ?>
		<?php  echo htmlspecialchars_decode($this->module['config']['base']['stat_code'])?>
	<?php  } ?>
<?php  } ?>
</body>
</html>