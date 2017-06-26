<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
define('IN_SUPERMAN_MALL_PLATFORM', true);
class Superman_mall_doWebPlatform extends Superman {
	public function __construct() {
        parent::__construct();
        parent::init();
        $this->check_user_permission('superman_mall_menu_platform');
        $this->exec();
	}

    public function exec() {
        global $_W, $_GPC;
        $act = in_array($_GPC['act'], array(
            'base', 'seo', 'share', 'slide',
            'notice', 'service', 'nav', 'footer_nav', 'homead',
            'subscribe', 'plugin', 'sms',
            'paycert', 'shop', 'order',
            'discount', 'express', 'style', 'payments',
        ))?$_GPC['act']:'base';
        $nav['title'] = '平台设置';
        if ($act == 'base') {
            $nav['subtitle'] = '基本参数';
            $this->base_setting();
        } else if ($act == 'seo') {
            $nav['subtitle'] = 'SEO设置';
            $this->seo_setting();
        } else if ($act == 'share') {
            $nav['subtitle'] = '分享设置';
            $skey = SupermanUtil::get_skey(SUPERMAN_SKEY_PLATFORM_SHARE);
            $setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_SHARE);
            if (!$setting) {
                $setting = array(
                    'system' => array(
                        'title' => '超级商城',
                        'imgurl' => $_W['siteroot'].'addons/superman_mall/icon.jpg',
                        'desc' => '超级商城-专业的综合网上购物商城，销售家电、数码通讯、电脑、家居百货、服装服饰、母婴、图书、食品等数万个品牌优质商品，便捷、诚信的服务，为您提供愉悦的网上购物体验！',
                    ),
                    'mgroupon' => array(
                        'title' => '我参加了超级商城“{标题}”的拼团！',
                        'imgurl' => $_W['siteroot'].'addons/superman_mall/icon.jpg',
                        'desc' => '我的拼团【还差{人数}人】！快来一起帮我抢呀！',
                    ),
                );
                $data = array(
                    'uniacid' => $_W['uniacid'],
                    'skey' => $skey,
                    'svalue' => iserializer($setting),
                );
                M::t('superman_mall_kv')->insert($data);
            }
            if (checksubmit()) {
                $data = array(
                    'svalue' => iserializer($_GPC['setting']),
                );
                if ($setting) {
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
            //print_r($setting);
        } else if ($act == 'service') {
            $nav['subtitle'] = '联系客服';
            $this->service_setting();
        } else if ($act == 'slide') {
            $nav['subtitle'] = '幻灯片';
            if (isset($_GPC['add']) && $_GPC['add'] == 'yes') {
                include $this->template('platform/slide-new');
                exit();
            }
            $id = 0;
            $slides = array();
            $filter = array(
                'uniacid' => $_W['uniacid'],
                'skey' => SUPERMAN_SKEY_HOME_SLIDE,
            );
            $row = M::t('superman_mall_kv')->fetch($filter);
            if ($row) {
                $id = $row['id'];
                $slides = iunserializer($row['svalue']);
            }
            if (checksubmit()) {
                $id = intval($_GPC['id']);
                $slides = array();
                if (isset($_GPC['slide']) && $_GPC['slide']) {
                    foreach ($_GPC['slide']['title'] as $k => $v) {
                        $slides[] = array(
                            'title' => $_GPC['slide']['title'][$k],
                            'img' => $_GPC['slide']['img'][$k],
                            'url' => $_GPC['slide']['url'][$k],
                        );
                    }
                }
                $data = array(
                    'svalue' => iserializer($slides),
                );
                if ($id) {
                    M::t('superman_mall_kv')->update($data, array('id' => $id));
                } else {
                    $data['uniacid'] = $_W['uniacid'];
                    $data['skey'] = SUPERMAN_SKEY_HOME_SLIDE;
                    M::t('superman_mall_kv')->insert($data);
                }
                message('操作成功！', referer(), 'success');
            }
        } else if ($act == 'notice') {
            $nav['subtitle'] = '公告';
            $id = 0;
            $top = array();
            $filter = array(
                'uniacid' => $_W['uniacid'],
                'skey' => SUPERMAN_SKEY_HOME_TOP,
            );
            $row = M::t('superman_mall_kv')->fetch($filter);
            if ($row) {
                $id = $row['id'];
                $top = iunserializer($row['svalue']);
            }
            if (checksubmit()) {
                $id = intval($_GPC['id']);
                $data = array(
                    'svalue' => $_GPC['top']?iserializer($_GPC['top']):'',
                );
                if ($id) {
                    M::t('superman_mall_kv')->update($data, array('id' => $id));
                } else {
                    $data['uniacid'] = $_W['uniacid'];
                    $data['skey'] = SUPERMAN_SKEY_HOME_TOP;
                    M::t('superman_mall_kv')->insert($data);
                }
                message('操作成功！', referer(), 'success');
            }
        } else if ($act == 'footer_nav') {
            $nav['subtitle'] = '底部导航';
            $id = 0;
            $apps = array();
            $filter = array(
                'uniacid' => $_W['uniacid'],
                'skey' => SUPERMAN_SKEY_FOOTER_NAV,
            );
            $row = M::t('superman_mall_kv')->fetch($filter);
            if ($row) {
                $id = $row['id'];
                $apps = iunserializer($row['svalue']);
                usort($apps, array(SupermanUtil, "sort_displayorder_desc"));
            }
            if (checksubmit()) {
                $apps = array();
                if (isset($_GPC['title']) && $_GPC['title']) {
                    foreach ($_GPC['title'] as $k=>$v) {
                        $apps[] = array(
                            'title' => $_GPC['title'][$k],
                            'icon' => $_GPC['icon'][$k],
                            'url' => $_GPC['url'][$k],
                            'isshow' => $_GPC['isshow'][$k]?1:0,
                            'displayorder' => $_GPC['displayorder'][$k],
                        );
                    }
                }
                $id = intval($_GPC['id']);
                $data = array(
                    'svalue' => $apps?iserializer($apps):'',
                );
                if ($id) {
                    M::t('superman_mall_kv')->update($data, array('id' => $id));
                } else {
                    $data['uniacid'] = $_W['uniacid'];
                    $data['skey'] = SUPERMAN_SKEY_FOOTER_NAV;
                    M::t('superman_mall_kv')->insert($data);
                }
                message('操作成功！', referer(), 'success');
            }
        } else if ($act == 'nav') {
            $nav['subtitle'] = '首页导航';
            $id = 0;
            $apps = array();
            $filter = array(
                'uniacid' => $_W['uniacid'],
                'skey' => SUPERMAN_SKEY_HOME_APPS,
            );
            $row = M::t('superman_mall_kv')->fetch($filter);
            if ($row) {
                $id = $row['id'];
                $apps = iunserializer($row['svalue']);
                usort($apps, array(SupermanUtil, "sort_displayorder_desc"));
            }
            if (checksubmit()) {
                $apps = array();
                if (isset($_GPC['title']) && $_GPC['title']) {
                    foreach ($_GPC['title'] as $k=>$v) {
                        $apps[] = array(
                            'title' => $_GPC['title'][$k],
                            'icon' => $_GPC['icon'][$k],
                            'url' => $_GPC['url'][$k],
                            'isshow' => $_GPC['isshow'][$k]?1:0,
                            'displayorder' => $_GPC['displayorder'][$k],
                        );
                    }
                }
                $id = intval($_GPC['id']);
                $data = array(
                    'svalue' => $apps?iserializer($apps):'',
                );
                if ($id) {
                    M::t('superman_mall_kv')->update($data, array('id' => $id));
                } else {
                    $data['uniacid'] = $_W['uniacid'];
                    $data['skey'] = SUPERMAN_SKEY_HOME_APPS;
                    M::t('superman_mall_kv')->insert($data);
                }
                message('操作成功！', referer(), 'success');
            }
        } else if ($act == 'homead') {
            $nav['subtitle'] = '首页广告图';
            $settings = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_HOME_AD);
            if (empty($settings)) {
                $settings = array(
                    1 => array(
                        'banner' => array('img' => '', 'url' => ''),
                        'classified' => array(),
                    ),
                    2 => array(
                        'banner' => array('img' => '', 'url' => ''),
                        'classified' => array(),
                    ),
                    3 => array(
                        'banner' => array('img' => '', 'url' => ''),
                        'classified' => array(),
                    ),
                );
            }

            //加载模板
            if (isset($_GPC['add']) && $_GPC['add'] == 'yes') {
                $index = $_GPC['index'];
                include $this->template('platform/homead-new');
                exit();
            }

            if (checksubmit('submit')) {
                $setting = $_GPC['setting'];
                $data = array();
                foreach ($setting as $key => $value) {
                    $data[$key]['banner'] = $value['banner'];
                    if ($value['classified']['img']) {
                        foreach ($value['classified']['img'] as $k => $v) {
                            if ($v == '') {
                                continue;
                            }
                            $data[$key]['classified'][] = array(
                                'img' => $v,
                                'url' => $value['classified']['url'][$k],
                            );
                        }
                    }
                }
                $data = array(
                    'svalue' => iserializer($data),
                );
                $row = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_HOME_AD);
                if ($row) {
                    M::t('superman_mall_kv')->update($data, array(
                        'uniacid' => $_W['uniacid'],
                        'skey' => SUPERMAN_SKEY_HOME_AD,
                    ));
                } else {
                    $data['uniacid'] = $_W['uniacid'];
                    $data['skey'] = SUPERMAN_SKEY_HOME_AD;
                    M::t('superman_mall_kv')->insert($data);
                }
                message('操作成功！', referer(), 'success');
            }
        } else if ($act == 'subscribe') {
            $nav['subtitle'] = '引导关注';
            $setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_SUBSCRIBE);
            if (checksubmit('submit')) {
                $data = array(
                    'svalue' => iserializer($_GPC['setting']),
                );
                $skey = SupermanUtil::get_skey(SUPERMAN_SKEY_PLATFORM_SUBSCRIBE);
                if ($setting) {
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
        } else if ($act == 'plugin') {
            $nav['subtitle'] = '功能管理';
            $setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_PLUGIN);
            if (checksubmit('submit')) {
                $svalue = array(
                    'seckill' => $_GPC['setting']['seckill']?1:0,
                    'mgroupon' => $_GPC['setting']['mgroupon']?1:0,
                    'partner' => $_GPC['setting']['partner']?1:0,
                    'discount' => $_GPC['setting']['discount']?1:0,
                    'printer' => $_GPC['setting']['printer']?1:0,
                    'tbast' => $_GPC['setting']['tbast']?1:0,
                    'bm_payu' => $_GPC['setting']['bm_payu']?1:0,
                );
                $data = array(
                    'svalue' => iserializer($svalue),
                );
                $skey = SupermanUtil::get_skey(SUPERMAN_SKEY_PLATFORM_PLUGIN);
                if ($setting) {
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
        } else if ($act == 'sms') {
            $nav['subtitle'] = '短信设置';
            $op = $_GPC['op'];
            $provider = $_GPC['provider'];
            $sms = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_SMS);
            $setting = $sms['setting'];
            $account = $sms['account'];
            $template = $sms['template'];
            $balance = -1;
            if (isset($account[$provider]) && $provider != 'alidayu') {
                $balance = Sms::init($provider, $account[$provider])->balance();
            }
            if ($op == 'test') {
                if (checksubmit()) {
                    if (!array_key_exists($provider, SupermanSms::$providers)) {
                        message('短信服务商不合法！', '', 'error');
                    }
                    $template_name = $_GPC['template_name'];
                    if (!array_key_exists($template_name, SupermanSms::$templates)) {
                        message('短信模板不合法！', '', 'error');
                    }
                    $mobile = $_GPC['mobile'];
                    $message = '';
                    if ($template_name == 'verifycode') {
                        $message = $sms['template']['verifycode'];
                        $vars = array(
                            '{签名}' => $sms['account'][$provider]['signature'],
                            '{验证码}' => random(6, true),
                        );
                        foreach ($vars as $k=>$v) {
                            if (strpos($message, $k) !== false) {
                                $message = str_replace($k, $v, $message);
                            }
                        }
                        $ret = Sms::init($provider, $sms['account'][$provider])->send($mobile, $message);
                    } else if ($template_name == 'alidayu_verifycode') {
                        $template['id'] = $sms['template']['alidayu']['verifycode']['id'];
                        $template['variables'] = json_encode(array(
                            $sms['template']['alidayu']['verifycode']['variable'] => random(6, true),
                        ));
                        $ret = Sms::init($provider, $sms['account'][$provider])->send($mobile, $message, $template);
                    }
                    if ($ret !== true) {
                        message('发送失败！('.$ret.')', '', 'error');
                    } else {
                        message('发送成功！', '', 'success');
                    }
                }
            }
            //print_r($sms);
            /*
             * 数据结构：
             * svalue = array(
             *      setting => array(
             *          shop_reg => array(
             *              switch => 1,
             *              provider => '',
             *          ),
             *          shop_getcash => array(
             *              switch => 1,
             *              provider => '',
             *          ),
             *          shop_trigger => array(
             *              switch => 1,
             *              provider => '',
             *          ),
             *      ),
             *      account => array(
             *          chanzor => array(
             *              url => '',
             *              username => '',
             *              password => '',
             *              signature => '',
             *          ),
             *          smsbao => array(
             *              url => '',
             *              username => '',
             *              password => '',
             *              signature => '',
             *          ),
             *          alidayu => array(
             *              app_key => '',
             *              app_secret => '',
             *              signature => '',
             *          ),
             *          heysky => array(
             *              url => '',
             *              username => '',
             *              password => '',
             *              signature => '',
             *          ),
             *      ),
             *      template => array(
             *          verifycode => '',
             *          trigger => '',
             *          alidayu => array(
             *              verifycode => array(
             *                  id => '',
             *                  variable => '',
             *              ),
             *              trigger => array(
             *                  id => '',
             *                  variables => array(
             *                      name, action
             *                  ),
             *              ),
             *          ),
             *      ),
             * )
             */
            if (checksubmit('submit')) {
                //for test
                /*Trigger::init('shop')->send(array(
                    'action' => 'order_submit',
                    'shopid' => 1,
                ));
                echo 'ok';
                die;*/

                if ($sms) {
                    if ($op == 'account') {
                        if ($provider != 'alidayu' && !isset($_GPC['account'][$provider]['password'])) {
                            $_GPC['account'][$provider]['password'] = $sms['account'][$provider]['password'];
                        }
                        $sms[$op][$provider] = $_GPC[$op][$provider];
                    } else {
                        $sms[$op] = $_GPC[$op];
                    }
                    $data = array(
                        'svalue' => iserializer($sms),
                    );
                    M::t('superman_mall_kv')->update($data, array(
                        'uniacid' => $_W['uniacid'],
                        'skey' => SUPERMAN_SKEY_PLATFORM_SMS,
                    ));
                } else {
                    if ($op == 'account') {
                        $svalue = array(
                            $provider => array(
                                $op => $_GPC[$op],
                            ),
                        );
                    } else {
                        $svalue = array(
                            $op => $_GPC[$op],
                        );
                    }
                    $data = array(
                        'uniacid' => $_W['uniacid'],
                        'skey' => SUPERMAN_SKEY_PLATFORM_SMS,
                        'svalue' => iserializer($svalue),
                    );
                    M::t('superman_mall_kv')->insert($data);
                }
                message('操作成功！', referer(), 'success');
            }
        } else if ($act == 'payments') {
            $nav['subtitle'] = '支付设置';
            $setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_PAYMENTS);
            if (checksubmit('submit')) {
                $data = array(
                    'svalue' => iserializer($_GPC['setting']),
                );
                $skey = SupermanUtil::get_skey(SUPERMAN_SKEY_PLATFORM_PAYMENTS);
                if ($setting) {
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
        } else if ($act == 'paycert') {
            $nav['subtitle'] = '支付证书';
            $setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_PAYCERT);
            if (checksubmit('submit')) {
                $del = $_GPC['setting'];
                $_W['setting']['upload']['image']['limit'] = 1000; //KB
                $_W['setting']['upload']['image']['extentions'][] = 'pem';
                $arr = array(
                    'apiclient_cert',
                    'apiclient_key',
                    'rootca'
                );
                $data = array();
                foreach ($arr as $k) {
                    $data[$k] = isset($setting[$k])?$setting[$k]:'';
                    //删除所选证书
                    if (isset($setting[$k]) && $setting[$k]) {
                        $path[$k] = ATTACHMENT_ROOT.$setting[$k];
                        if ($del['del_'.$k]) {
                            if (file_exists($path[$k])) {
                                @unlink($path[$k]);
                            }
                            $data[$k] = '';
                        }
                    }
                    //保存新证书
                    if (!empty($_FILES['setting']['tmp_name'][$k])) {
                        $file = array(
                            'name' => $_FILES['setting']['name'][$k],
                            'tmp_name' => $_FILES['setting']['tmp_name'][$k],
                            'type' => $_FILES['setting']['type'][$k],
                            'error' => $_FILES['setting']['error'][$k],
                            'size' => $_FILES['setting']['size'][$k],
                        );
                        $upload = file_upload($file, 'image');
                        if (!$upload['success']) {
                            message($upload['errno'].':'.$upload['message']);
                        }
                        if (isset($path[$k]) && file_exists($path[$k])) {
                            @unlink($path[$k]);
                        }
                        $data[$k] = $upload['path'];
                    }
                }
                $data = array(
                    'svalue' => iserializer($data),
                );
                $skey = SupermanUtil::get_skey(SUPERMAN_SKEY_PLATFORM_PAYCERT);
                if ($setting) {
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
        } else if ($act == 'shop') {
            $nav['subtitle'] = '商户设置';
            $setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_SHOP);
            if (isset($_GPC['download'])) {
                $web_domain = $_GPC['web_domain']?$_GPC['web_domain']:$setting['web_domain'];
                if (empty($web_domain)) {
                    message('未填写域名，无法生成规则文件！', '', 'error');
                }
                $urls = parse_url($_W['siteroot']);
                $path = rtrim($urls['path'], '/');
                if ($_GPC['download'] == 'apache') {
                    $htaccess =<<<EOF
#superman_mall
RewriteEngine on
RewriteCond %{HTTP_HOST} ^\w+.{$web_domain}$
RewriteCond %{REQUEST_URI} !^{$path}/addons/superman_mall/admin
RewriteCond %{REQUEST_URI} !^{$path}/(web|app|attachment|addons|payment|api)/
RewriteRule ^(.*)$ {$path}/addons/superman_mall/admin/index.php [L]
EOF;
                    ob_end_clean();
                    @header("Content-Type: application/force-download");
                    @header("Content-Type: application/octet-stream");
                    @header("Content-Type: application/download");
                    @header('Content-Disposition:inline;filename="apache.htaccess"');
                    @header("Content-Transfer-Encoding: binary");
                    @header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
                    @header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
                    @header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                    @header("Pragma: no-cache");
                    echo $htaccess;
                    exit;
                } else if ($_GPC['download'] == 'nginx') {
                    $htaccess =<<<EOF
#superman_mall
set \$flag 0;
if (\$host ~ "^\w+.{$web_domain}$") {
    set \$flag "\${flag}1";
}
if (\$request_uri ~ "^{$path}/(web|app|attachment|addons|payment|api)/") {
    set \$flag "\${flag}2";
}
if (\$flag = "012") {
    break;
}
if (\$request_uri !~ "^{$path}/addons/superman_mall/admin") {
    set \$flag "\${flag}3";
}
if (\$flag = "013") {
	rewrite ^(.*)$ {$path}/addons/superman_mall/admin/index.php last;
}
EOF;
                    @header("Content-Type: application/force-download");
                    @header("Content-Type: application/octet-stream");
                    @header("Content-Type: application/download");
                    @header('Content-Disposition:inline;filename="nginx.htaccess"');
                    @header("Content-Transfer-Encoding: binary");
                    @header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
                    @header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
                    @header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                    @header("Pragma: no-cache");
                    echo $htaccess;
                    exit;
                }
            }
            if (checksubmit('submit')) {
                $data = array(
                    'svalue' => iserializer($_GPC['setting']),
                );
                $skey = SupermanUtil::get_skey(SUPERMAN_SKEY_PLATFORM_SHOP);
                if ($setting) {
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
        } else if ($act == 'order') {
            $nav['subtitle'] = '订单设置';
            $setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_ORDER);
            if (checksubmit('submit')) {
                $data = array(
                    'svalue' => iserializer($_GPC['setting']),
                );
                $skey = SupermanUtil::get_skey(SUPERMAN_SKEY_PLATFORM_ORDER);
                if ($setting) {
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
        } else if ($act == 'discount') {    //营销设置
            /*
            array(
                'credit' => array(
                    'credit_type' => 'credit1', 积分类型,string
                    'remark_open' => 1,         订单返积分开关,int   1/0
                    'remark_rate' => 0.1,       订单返积分比例,float
                    'remark_value' => 0.1,      订单返积分固定值,float
                    'min_order_amount' => ,     订单最低金额(返积分限制),float
                    'cash_open' => 1,           积分抵现开关,int    1/0
                    'cash_rate' => 0.1,         积分抵现比例XXX兑换1元,float
                ),
            );
            */
            $nav['subtitle'] = '营销设置';
            $setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_DISCOUNT);
            $credit_group = $this->get_credit_titles();
            if (checksubmit('submit')) {
                $data = array(
                    'svalue' => iserializer($_GPC['setting']),
                );
                $skey = SupermanUtil::get_skey(SUPERMAN_SKEY_PLATFORM_DISCOUNT);
                if ($setting) {
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
        } else if ($act == 'express') {
            $nav['subtitle'] = '快递公司';
            $filter = array(
                'uniacid' => $_W['uniacid'],
            );
            $total = M::t('superman_mall_express_company')->count($filter);
            $orderby = 'ORDER BY id ASC';
            if ($total) {
                $list = M::t('superman_mall_express_company')->fetchall($filter, $orderby, '', -1);
            }
        } else if ($act == 'style') {
            $nav['subtitle'] = '样式设置';
            $setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_STYLE);
            if (checksubmit('submit')) {
                $data = array(
                    'svalue' => iserializer($_GPC['setting']),
                );
                $skey = SupermanUtil::get_skey(SUPERMAN_SKEY_PLATFORM_STYLE);
                if ($setting) {
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
        }

        include $this->template('platform/index');
        //include $this->template('platform/index');
    }
    private function base_setting() {
        global $_W, $_GPC;
        //init
        if (!isset($this->module['config']['base'])) {
            $this->module['config']['base'] = array(
                'wechat' => 1,
                'debug' => 0,
                'stat_switch' => 0,
                'stat_code' => ''
            );
            $this->saveSettings($this->module['config']);
        }
        if (checksubmit()) {
            $this->module['config']['base'] = $_GPC['base'];
            if ($this->module['config']['base']['stat_code']) {
                $this->module['config']['base']['stat_code'] = trim($this->module['config']['base']['stat_code']);
            }
            if ($this->module['config']['base']['debug_uids']) {
                $this->module['config']['base']['debug_uids'] = explode(',', trim($this->module['config']['base']['debug_uids']));
            }
            $this->saveSettings($this->module['config']);
            message('操作成功！', referer(), 'success');
        }
    }
    private function seo_setting() {
        global $_W, $_GPC;
        //init
        if (!isset($this->module['config']['seo'])) {
            $this->module['config']['seo'] = array(
                'title' => '超级商城',
                'keywords' => '网上购物,网上商城,超级商城,微信商城',
                'description' => '超级商城-专业的综合网上购物商城，销售家电、数码通讯、电脑、家居百货、服装服饰、母婴、图书、食品等数万个品牌优质商品，便捷、诚信的服务，为您提供愉悦的网上购物体验！',
            );
            $this->saveSettings($this->module['config']);
        }
        if (checksubmit()) {
            $this->module['config']['seo'] = $_GPC['seo'];
            $this->saveSettings($this->module['config']);
            message('操作成功！', referer(), 'success');
        }
    }
    private function service_setting() {
        global $_W, $_GPC;
        //init
        if (!isset($this->module['config']['service'])) {
            $this->module['config']['service'] = array(
                'content' => '如有疑问请联系客服！',
            );
            $this->saveSettings($this->module['config']);
        }
        if (checksubmit()) {
            $this->module['config']['service'] = $_GPC['service'];
            $this->saveSettings($this->module['config']);
            message('操作成功！', referer(), 'success');
        }
    }
}
$obj = new Superman_mall_doWebPlatform;