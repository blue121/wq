<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doMobileAddress extends Superman {
	public function __construct() {
		parent::__construct();
        parent::init();
		$this->exec();
	}
    public function exec() {
        global $_W, $_GPC, $do;
        $uid = $_W['member']['uid'];
        $_share = $this->share;
        $title = '收货地址';
        $do = $do?$do:'address';
        $act = in_array($_GPC['act'], array('display', 'post', 'delete', 'wechat_address'))?$_GPC['act']:'display';
        if ($act == 'display') {
            if ($uid) {
                if ($_GPC['isdefault'] == 1) {
                    $id = intval($_GPC['id']);
                    //去掉原默认地址
                    M::t('mc_member_address')->update(array('isdefault' => 0), array('uid' => $uid, 'isdefault' => 1));

                    //设置新默认地址
                    M::t('mc_member_address')->update(array('isdefault' => 1), array('id' => $id));

                    if (isset($_GPC['forward']) && $_GPC['forward']) {
                        $this->json(ERRNO::OK, '设置成功，跳转中...', array('url'=> base64_decode($_GPC['forward'])));
                    }
                    $this->json(ERRNO::OK, '设置成功！', array('url' => $this->createMobileUrl('address'), array('act'=>'display')));
                }
                $filter = array(
                    'uniacid' => $_W['uniacid'],
                    'uid' => $uid
                );
                $list = M::t('mc_member_address')->fetchall($filter);
                if ($list) {
                    foreach ($list as &$item) {
                        $item['mobile'] = SupermanUtil::hide_mobile($item['mobile']);
                        if ($item['province'] == $item['city']) {
                            $item['address'] = $item['province'].' '.$item['district'].' '.$item['address'];
                        } else {
                            $item['address'] = $item['province'].' '.$item['city'].' '.$item['district'].' '.$item['address'];
                        }
                    }
                    unset($item);
                }
                //微信版本是否支持共享地址
                $allowShareAddress = Agent::getAgent();
                preg_match('/MicroMessenger\/([\d\.]+)/i', $allowShareAddress, $wechatInfo);
                $wechat_addr_switch = true;
                //微擎支付设置
                $setting = uni_setting($_W['uniacid'], array('payment'));
                if (!isset($_GPC['forward']) || $_GPC['forward'] == ''                              //非来自确认订单页
                    || !$wechatInfo || (isset($wechatInfo[1]) &&$wechatInfo[1] < '5.0')             //非微信或版本不支持
                    || (isset($_GPC['wechat_addr_switch']) && $_GPC['wechat_addr_switch'] == 0)     //获取微信收货地址失败
                    || !isset($setting['payment']) || !$setting['payment']['wechat']['switch']      //未开启微信支付
                    || $_W['account']['level'] != 4) {                                              //非认证服务号
                    $wechat_addr_switch = false;
                }
            }
        } else if ($act == 'post') {
            if ($uid) {
                $id = intval($_GPC['id']);
                if ($id > 0) {          //编辑
                    //验证是否是本人地址
                    $address = M::t('mc_member_address')->fetch($id);
                    if (!$address || $address['uid'] != $uid) {
                        $this->json(ERRNO::INVALID_REQUEST);
                    }
                    //地区格式化
                    if ($address['province'] == $address['city']) {
                        $address['city'] = $address['city'].' '.$address['district'];
                    } else {
                        $address['city'] = $address['province'].' '.$address['city'].' '.$address['district'];
                    }
                }
                if (checksubmit('submit')) {
                    //表单参数检查
                    $username = trim($_GPC['username']);
                    if ($username == '') {
                        $this->json(ERRNO::USERNAME_NULL);
                    }
                    $mobile = $_GPC['mobile'];
                    if ($mobile == '') {
                        $this->json(ERRNO::MOBILE_NULL);
                    }
                    if (!preg_match('/^([0-9]{11})?$/', $mobile)) {
                        $this->json(ERRNO::MOBILE_INVALID);
                    }
                    $address = trim($_GPC['address']);

                    $data = array(
                        'uniacid' => $_W['uniacid'],
                        'uid' => $uid,
                        'username' => $username,
                        'mobile' => $mobile,
                        'address' => $address,
                        'isdefault' => $_GPC['isdefault']=='on'?1:0
                    );

                    if ($data['isdefault'] == 1) {
                        //旧默认地址初始化
                        M::t('mc_member_address')->update(array('isdefault' => 0), array(
                            'uid' => $uid,
                            'uniacid' => $_W['uniacid'],
                            'isdefault' => 1
                        ));
                    }

                    $city = trim($_GPC['city']);
                    if (!$city) {
                        $this->json(ERRNO::CITY_NULL);
                    }
                    $city = explode(' ',$city);
                    if (count($city) == 3) {
                        $data['province'] = $city[0];
                        $data['city'] = $city[1];
                        $data['district'] = $city[2];
                    } elseif (count($city)==2) {
                        $data['province'] = $city[0];
                        $data['city'] = $city[0];
                        $data['district'] = $city[1];
                    } else {
                        $this->json(ERRNO::CITY_INVALID);
                    }

                    if ($id > 0) {  //编辑
                        M::t('mc_member_address')->update($data, array('id' => $id));
                    } else {        //新增
                        M::t('mc_member_address')->insert($data);
                    }
                    if (isset($_GPC['forward']) && $_GPC['forward']) {
                        $this->json(ERRNO::OK, '更新成功，跳转中...', array('url' => base64_decode($_GPC['forward'])));
                    }
                    $this->json(ERRNO::OK, '更新成功，跳转中...', array('url' => $this->createMobileUrl('address', array('act' => 'display'))));
                }
            }
        } else if ($act == 'delete') {
            if ($uid) {
                $id = intval($_GPC['id']);
                if (!$id) {
                    $this->json(ERRNO::INVALID_REQUEST);
                }

                $address = M::t('mc_member_address')->fetch($id);
                //验证是否本人
                if (!$address || $address['uid'] != $uid) {
                    $this->json(ERRNO::ADDRESS_NOT_EXIST);
                }
                $ret = M::t('mc_member_address')->delete(array('id' => $id));
                if ($ret === false) {
                    $this->json(ERRNO::SYSTEM_ERROR);
                }
                $this->json(ERRNO::OK, '删除成功！');
            }
        } else if ($act == 'wechat_address') {
            //只有从确认订单页过来的能获取微信地址，所以$_GPC['forward']一定存在
            if (!isset($_GPC['forward']) || $_GPC['forward'] == '') {
                $this->json(ERRNO::INVALID_REQUEST);
            }
            $state = 'superman_mall';
            $code = $_GPC['code'];
            $oauth_account = WeAccount::create($_W['oauth_account']);
            if (empty($code)) {     //获取code
                $callback = urlencode($_W['siteurl']);      //$_W['siteurl']中应该有$_GPC[forward]
                $forward = $oauth_account->getOauthCodeUrl($callback, $state);
                @header('Location:'.$forward);
                exit();
            } else {                //获取$accessToken
                $OauthInfo = $oauth_account->getOauthInfo($code);
                if (!isset($OauthInfo['access_token'])) {   //未获取到
                    WeUtility::logging('fatal', 'code未获取到accesstoken，错误原因'.var_export($OauthInfo, true));
                    $url = $this->createMobileUrl('address', array(
                        'act' => 'display',
                        'forward' => $_GPC['forward'],
                        'wechat_addr_switch' => 0)
                    );
                    $this->json(ERRNO::SYSTEM_ERROR, '获取微信地址失败，请添加新地址', array('url' => $url));
                }
                $accessToken = $OauthInfo['access_token'];
                $timeStamp = $_W['timestamp'];
                $timeStamp = "$timeStamp";      //时间戳，必须是字符串类型
                $nonceStr = random(16);         //随机字符串
                $String1 = "accesstoken={$accessToken}&appid={$_W['account']['key']}&noncestr={$nonceStr}&timestamp={$timeStamp}&url={$_W['siteurl']}";
                $addrSign = SHA1($String1);
            }
        }
		include $this->template('address');
    }
}
$obj = new Superman_mall_doMobileAddress;