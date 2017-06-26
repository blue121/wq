<?php
global $_W;
global $_GPC;
$user_agent = $_SERVER['HTTP_USER_AGENT'];
if (strpos($user_agent, 'MicroMessenger') === false) {
	message('本页面仅支持微信访问!非微信浏览器禁止浏览!', '', 'error');
}
load()->app('tpl');
$nickname = $_W['fans']['nickname'];
$avatar = $_W['fans']['avatar'];
if (checksubmit()) {
	$data = array('shopname' => $_GPC['shopname'], 'username' => $_GPC['username'], 'yyzz' => json_encode($_GPC['yyzz']), 'tel' => $_GPC['tel'], 'weid' => $_W['uniacid'], 'openid' => $_W['openid'], 'lng' => $_GPC['lng'], 'lat' => $_GPC['lat'], 'status' => '0');
	$data['add'] = '';
	foreach ($_GPC['add'] as $value) {
		$data['add'] .= $value;
	}
	$data['add'] .= $_GPC['street'];
	$result = pdo_getall('ld_card_users', array('openid' => $_W['openid'], 'status' => '0'));
	if ($result) {
		message('您有一个还未审核的店铺，审核后才能继续申请入驻', '', 'info');
	} else {
		$res = pdo_insert('ld_card_users', $data);
		if ($res) {
			message('提交成功！很快会有客服和您联系。', $_W['siteroot'] . $this->createmobileurl('index'), 'success');
		}
	}
}
include $this->template('ruzhu');