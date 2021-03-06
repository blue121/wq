<?php
/**
 * 外送系统
 * @author TuLe wei系列
 * @QQ 97391583
 * @url http://Www.TuLe5.Com/
 */
defined('IN_IA') or exit('Access Denied');
mload()->model('cloud');
global $_W, $_GPC;
$do = 'file';
$op = trim($_GPC['op']);
$post = file_get_contents('php://input');

if($op == 'touch') {
	message(error(0, 'success'), '', 'ajax');
}

if($op == 'build') {
	$data = cloud_w_parse_build($post);
	message($data, '', 'ajax');
}

if($op == 'schema') {
	cloud_w_parse_schema($post);
}

if($op == 'download') {
	$data = cloud_w_parse_download($post);
	message($data, '', 'ajax');
}

