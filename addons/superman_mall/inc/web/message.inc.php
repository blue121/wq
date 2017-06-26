<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
define('IN_SUPERMAN_MALL_PLATFORM', true);
class Superman_mall_doWebMessage extends Superman {
	public function __construct() {
		parent::__construct();
        parent::init();
        $this->check_user_permission('superman_mall_menu_platform');
		$this->exec();
	}
    public function exec() {
        global $_W, $_GPC;
        $act = in_array($_GPC['act'], array('template', 'service'))?$_GPC['act']:'template';
        $nav['title'] = '消息中心';
        if ($act == 'template') {
            $nav['subtitle'] = '模板消息';
            $message = array();
            $message['order'] = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_MESSAGE_ORDER);
            $message['shop'] = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_MESSAGE_SHOP);
            $message['partner'] = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_MESSAGE_PARTNER);
            $message['trigger'] = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_MESSAGE_TRIGGER);
            //print_r($message);
            if (checksubmit()) {
                //订单消息
                $data = array(
                    'svalue' => iserializer($_GPC['order']),
                );
                $skey = SupermanUtil::get_skey(SUPERMAN_SKEY_MESSAGE_ORDER);
                if ($message['order']) {
                    M::t('superman_mall_kv')->update($data, array(
                        'uniacid' => $_W['uniacid'],
                        'skey' => $skey,
                    ));
                } else {
                    $data['uniacid'] = $_W['uniacid'];
                    $data['skey'] = $skey;
                    M::t('superman_mall_kv')->insert($data);
                }
                //商户消息
                $data = array(
                    'svalue' => iserializer($_GPC['shop']),
                );
                $skey = SupermanUtil::get_skey(SUPERMAN_SKEY_MESSAGE_SHOP);
                if ($message['shop']) {
                    M::t('superman_mall_kv')->update($data, array(
                        'uniacid' => $_W['uniacid'],
                        'skey' => $skey,
                    ));
                } else {
                    $data['uniacid'] = $_W['uniacid'];
                    $data['skey'] = $skey;
                    M::t('superman_mall_kv')->insert($data);
                }
                //分销商消息
                $data = array(
                    'svalue' => iserializer($_GPC['partner']),
                );
                $skey = SupermanUtil::get_skey(SUPERMAN_SKEY_MESSAGE_PARTNER);
                if ($message['partner']) {
                    M::t('superman_mall_kv')->update($data, array(
                        'uniacid' => $_W['uniacid'],
                        'skey' => $skey,
                    ));
                } else {
                    $data['uniacid'] = $_W['uniacid'];
                    $data['skey'] = $skey;
                    M::t('superman_mall_kv')->insert($data);
                }
                //触发器消息
                $data = array(
                    'svalue' => iserializer($_GPC['trigger']),
                );
                $skey = SupermanUtil::get_skey(SUPERMAN_SKEY_MESSAGE_TRIGGER);
                if ($message['trigger']) {
                    M::t('superman_mall_kv')->update($data, array(
                        'uniacid' => $_W['uniacid'],
                        'skey' => $skey,
                    ));
                } else {
                    $data['uniacid'] = $_W['uniacid'];
                    $data['skey'] = $skey;
                    M::t('superman_mall_kv')->insert($data);
                }
                message('操作成功！', referer(), 'success');
            }
        } else if ($act == 'service') {
            $nav['subtitle'] = '客服消息';
            $message = array();
            $message['mgroupon'] = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_MESSAGE_MGROUPON);
            if (checksubmit()) {
                //拼团消息
                $data = array(
                    'svalue' => iserializer($_GPC['mgroupon']),
                );
                $skey = SupermanUtil::get_skey(SUPERMAN_SKEY_MESSAGE_MGROUPON);
                $filter = array(
                    'uniacid' => $_W['uniacid'],
                    'skey' => $skey,
                );
                $row = M::t('superman_mall_kv')->fetch($filter);
                if ($row) {
                    M::t('superman_mall_kv')->update($data, array(
                        'id' => $row['id']
                    ));
                } else {
                    $data['uniacid'] = $_W['uniacid'];
                    $data['skey'] = $skey;
                    M::t('superman_mall_kv')->insert($data);
                }
                //--end
                message('操作成功！', referer(), 'success');
            }
        }
        include $this->template('message');
    }
}
$obj = new Superman_mall_doWebMessage;