<?php defined('IN_IA') or exit('Access Denied');?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php  echo $this->module['config']['seo']['title']?></title>
	<meta name="keywords" content="<?php  echo $this->module['config']['seo']['keywords']?>"/>
	<meta name="description" content="<?php  echo $this->module['config']['seo']['description']?>"/>
	<meta name="viewport" content="initial-scale=1, maximum-scale=1">
	<meta name="format-detection" content="telephone=no"/>
	<link rel="shortcut icon" href="/favicon.ico">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<?php  if(defined('LOCAL_DEVELOPMENT')) { ?>
	<link rel="stylesheet" href="//res.wx.qq.com/open/libs/weui/0.3.0/weui.css">
	<link rel="stylesheet" href="//g.alicdn.com/msui/sm/0.6.2/css/sm.css">
	<link rel="stylesheet" href="//g.alicdn.com/msui/sm/0.6.2/css/sm-extend.css">
	<?php  } else { ?>
	<link rel="stylesheet" href="//res.wx.qq.com/open/libs/weui/0.3.0/weui.min.css">
	<link rel="stylesheet" href="//g.alicdn.com/msui/sm/0.6.2/css/sm.css">
	<link rel="stylesheet" href="//g.alicdn.com/msui/sm/0.6.2/css/sm-extend.css">
	<?php  } ?>
	<?php  echo $this->superman_css?>
	<script>
		function _removeHTMLTag(str) {
			if(typeof str == 'string'){
				str = str.replace(/<script[^>]*?>[\s\S]*?<\/script>/g,'');
				str = str.replace(/<style[^>]*?>[\s\S]*?<\/style>/g,'');
				str = str.replace(/<\/?[^>]*>/g,'');
				str = str.replace(/\s+/g,'');
				str = str.replace(/&nbsp;/ig,'');
			}
			return str;
		}
		window.sysinfo = {
			'uniacid': "<?php  echo $_W['uniacid'];?>",
			'acid': "<?php  echo $_W['acid'];?>",
			'openid': "<?php  echo $_W['openid'];?>",
			'uid': "<?php  echo $_W['uid'];?>",
			'siteroot': "<?php  echo $_W['siteroot'];?>",
			'siteurl': "<?php  echo $_W['siteurl'];?>",
			'attachurl': "<?php  echo $_W['attachurl'];?>",
			'attachurl_local': "<?php  echo $_W['attachurl_local'];?>",
			'MODULE_URL': '<?php echo MODULE_URL;?>',
			'cookie' : {
				'pre': "<?php  echo $_W['config']['cookie']['pre'];?>"
			},
			'_debug': '<?php echo LOCAL_DEVELOPMENT;?>',
			'weixin_menu': "<?php  echo $this->module['config']['base']['weixin_menu']?>",
			'loginurl': '<?php  echo murl("auth/login", array("forward" => base64_encode($_SERVER["QUERY_STRING"])))?>',
			'check_loginurl': '<?php  echo $this->createMobileUrl("login", array("act" => "check"))?>',
			'container': "<?php  echo $_W['container'];?>",
			'global_hook_url': "<?php  echo $_W['siteroot'].'app/'.$this->createMobileUrl('hook')?>",
            'mobile_path': '<?php  echo $_W['siteroot'];?>addons/superman_mall/template/mobile/default',
            'placeholder': '<?php  echo $_W['siteroot'];?>addons/superman_mall/template/mobile/default/images/placeholder.gif',
			'member' : {
				'uid': "<?php  echo $_W['member']['uid'];?>"
			},
			'lbs': "<?php  if(!$lbs) { ?>0<?php  } else { ?>1<?php  } ?>"
		};
        var superman = {
            'external': '<?php echo SUPERMAN_EXTERNAL;?>'
        };
	</script>
	<script src="//res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
	<?php  if(defined('LOCAL_DEVELOPMENT')) { ?>
	<script type='text/javascript' src='//g.alicdn.com/sj/lib/zepto/zepto.js' charset="utf-8"></script>
	<?php  } else { ?>
	<script type='text/javascript' src='//g.alicdn.com/sj/lib/zepto/zepto.min.js' charset="utf-8"></script>
	<?php  } ?>
	<script>
		jssdkconfig = <?php  echo json_encode($_W['account']['jssdkconfig']);?> || {};
		jssdkconfig.debug = false;
		jssdkconfig.jsApiList = [
			'checkJsApi',
			'onMenuShareTimeline',
			'onMenuShareAppMessage',
			'onMenuShareQQ',
			'onMenuShareWeibo',
			'hideMenuItems',
			'showMenuItems',
			'hideAllNonBaseMenuItem',
			'showAllNonBaseMenuItem',
			'translateVoice',
			'startRecord',
			'stopRecord',
			'onRecordEnd',
			'playVoice',
			'pauseVoice',
			'stopVoice',
			'uploadVoice',
			'downloadVoice',
			'chooseImage',
			'previewImage',
			'uploadImage',
			'downloadImage',
			'getNetworkType',
			'openLocation',
			'getLocation',
			'hideOptionMenu',
			'showOptionMenu',
			'closeWindow',
			'scanQRCode',
			'chooseWXPay',
			'openProductSpecificView',
			'addCard',
			'chooseCard',
			'openCard'
		];
		wx.config(jssdkconfig);
	</script>
</head>
<body>