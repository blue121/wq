<?php defined('IN_IA') or exit('Access Denied');?><?php  if($_W['isajax']) { ?>
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
		sharedata['title'] = $_share['title'] || sharedata['title'];
		sharedata['desc'] = $_share['desc'] || sharedata['desc'];
		sharedata['link'] = $_share['link'] || sharedata['link'];
		sharedata['imgUrl'] = $_share['imgUrl'] || sharedata['imgUrl'];
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
<?php  } ?>