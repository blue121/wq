<?php
/**
 * 【超人】超级商城模块微站定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class SupermanMallProcessor_partner_poster extends SupermanMallProcessor {
    private $id;
    public function __construct($id) {
        parent::__construct();
        $this->id = intval($id);
    }
    public function respond() {
        global $_W;
        $plugin_setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_PLUGIN);
        if (!$plugin_setting['partner']) {
            WeUtility::logging('warning', '[SupermanMallProcessor_partner_poster]分销功能未开启');
            return $this->respText('分销功能未开启');
        }
        $uid = mc_openid2uid($this->message['from']);
        if (!$uid) {
            WeUtility::logging('warning', '[SupermanMallProcessor_partner_poster]请求的openid非法');
            return $this->respText('非法请求');
        }
        $partner = M::t('superman_mall_partner')->fetch(array('uid' => $uid));
        if (!$partner) {
            WeUtility::logging('warning', '[SupermanMallProcessor_partner_poster]该用户非分销商');
            return $this->respText('您不是分销商，无法生成分销海报');
        }
        if ($partner['status'] != 1) {
            WeUtility::logging('warning', '[SupermanMallProcessor_partner_poster]分销商状态不可用');
            return $this->respText('分销状态异常，无法生成分销海报');
        }
        $url = $_W['siteroot'].'app/'.murl('entry//poster', array('m' => 'superman_mall', 'act' => 'partner', 'id' => $this->id, 'openid' => $this->message['from'], 'keyword' => 1));
        load()->func('communication');
        ihttp_request($url, '', array(), 1);
        return $this->respText('海报正在拼命生成中...');
    }
}