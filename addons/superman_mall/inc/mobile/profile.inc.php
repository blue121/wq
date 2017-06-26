<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doMobileProfile extends Superman
{
    public function __construct() {
        parent::__construct();
        parent::init();
        $this->exec();
    }

    public function exec() {
        global $_W, $_GPC, $do;
        $_share = $this->share;
        $title = '个人信息';
        $do = $do ? $do : 'profile';
        $act = in_array($_GPC['act'], array('display')) ? $_GPC['act'] : 'display';
        $this->checkauth();
        if ($act == 'display') {
            $member = mc_fetch($_W['member']['uid'], array('nickname', 'realname', 'avatar', 'email', 'mobile'));
            if ($member) {
                $member['avatar'] = tomedia($member['avatar']);
                $member['big_avatar'] = rtrim($member['avatar'], '132');
            }
            $has_email = $member['email'] ? true : false;
            if (strexists($member['email'], '@we7.cc')) {
                $has_email = false;
            }

            if (checksubmit()) {
                //表单验证
                $serverId = $_GPC['serverId'];
                $nickname = $_GPC['nickname'];
                $realname = $_GPC['realname'];
                $mobile = $_GPC['mobile'];
                $email = $_GPC['email'];
                if ($mobile != '') {
                    if (!preg_match(REGULAR_MOBILE, $mobile)) {
                        $this->json(ERRNO::MOBILE_INVALID);
                    }
                    //检查手机是否存在
                    $sql = "SELECT uid FROM " . tablename('mc_members') . " WHERE mobile = :mobile AND uniacid = :uniacid AND uid != :uid";
                    $params = array(
                        ':mobile' => $mobile,
                        ':uniacid' => $_W['uniacid'],
                        ':uid' => $_W['member']['uid'],
                    );
                    $exists = pdo_fetchcolumn($sql, $params);
                    if ($exists) {
                        $this->json(ERRNO::MOBILE_EXISTS);
                    }
                }
                if ($email != '') {
                    if (!preg_match(REGULAR_EMAIL, $email)) {
                        $this->json(ERRNO::EMAIL_INVALID);
                    }
                    //检查邮箱是否存在
                    $sql = "SELECT uid FROM " . tablename('mc_members') . " WHERE email = :email AND uniacid = :uniacid AND uid != :uid";
                    $params = array(
                        ':email' => $email,
                        ':uniacid' => $_W['uniacid'],
                        ':uid' => $_W['member']['uid'],
                    );
                    $emailexists = pdo_fetchcolumn($sql, $params);
                    if ($emailexists) {
                        $this->json(ERRNO::EMAIL_EXISTS);
                    }
                }
                //下载微信图片
                if ($serverId != '') {
                    //初始化路径
                    $path = "images/{$_W['uniacid']}/" . date('Y/m');
                    $filename = md5($serverId) . '.jpg';
                    if (IMS_VERSION == 0.6) {
                        $avatar_file = ATTACHMENT_ROOT . '/' . $path . '/' . $filename;
                        $allpath = ATTACHMENT_ROOT . '/' . $path;
                    } else {
                        $avatar_file = ATTACHMENT_ROOT . $path . '/' . $filename;
                        $allpath = ATTACHMENT_ROOT . $path;
                    }
                    mkdirs($allpath);

                    //下载微信图片
                    load()->model('account');
                    $acc = WeAccount::create($_W['account']['acid']);
                    $token = $acc->getAccessToken();
                    if (is_error($token)) {
                        WeUtility::logging('fatal', 'token error, message=' . $token['message']);
                        $this->json(ERRNO::SYSTEM_ERROR, 'token error');
                    }
                    load()->func('communication');
                    $url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token={$token}&media_id={$serverId}";
                    $resp = ihttp_request($url);
                    if (is_error($resp)) {
                        WeUtility::logging('fatal', 'request error, message=' . $resp['message']);
                        $this->json(ERRNO::SYSTEM_ERROR, 'request error');
                    }

                    //保存头像图片
                    $fp = @fopen($avatar_file, 'wb');
                    @fwrite($fp, $resp['content']);
                    @fclose($fp);
                    $new_avatar = $path . '/' . $filename;
                    mc_update($_W['member']['uid'], array(
                        'avatar' => $new_avatar,
                    ));
                }
                //更新数据
                $data = array();
                if ($mobile != '') {
                    $data['mobile'] = trim($mobile);
                }
                if ($nickname != '') {
                    $data['nickname'] = trim($nickname);
                }
                if ($realname != '') {
                    $data['realname'] = trim($realname);
                }
                if ($email != '') {
                    $data['email'] = trim($email);
                }
                if (!empty($data)) {
                    mc_update($_W['member']['uid'], $data);
                    $this->json(ERRNO::OK, '', array('url' => $this->createMobileUrl('my')));
                }
            }
        }
        include $this->template('profile');
    }
}
$obj = new Superman_mall_doMobileProfile;