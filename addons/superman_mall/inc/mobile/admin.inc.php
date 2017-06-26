<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
define('SUPERMAN_MOBILE_ADMIN', true);
class Superman_mall_doMobileAdmin extends Superman {
    protected $shop = array();
    protected $shop_user = array();
    protected $user_permission = array();
    public function __construct() {
        parent::__construct();
        parent::init();
        $this->exec();
    }
    public function exec() {
        global $_W, $_GPC, $do;
        $route = $_GPC['route'];
        if (empty($route)) {
            $this->message('非法请求', '', 'warn');
        }
        @list($do, $act) = explode('.', $route);
        if (!in_array($do, array('dashboard', 'item', 'order', 'finance', 'user', 'account'))) {
            $this->message('非法参数', '', 'warn');
        }
        $act = !empty($act)?$act:'display';
        $filename = MODULE_ROOT.'/inc/mobile/admin/'.$do.'.inc.php';
        if (!file_exists($filename)) {
            $this->message('控制器不存在', '', 'warn');
        }
        $this->checkauth();
        if ($_W['fans']['follow'] == 0) {
            $this->message('未关注该公众号', '', 'warn');
        }
        $this->_init_shop_user();
        $this->_init_user_permission();
        include $filename;
    }

    private function _init_shop_user() {
        global $_W, $_GPC;
        if (!empty($_SESSION['mobile_admin_shopid']) && !empty($_SESSION['mobile_admin_userid'])) {  //选择帐号
            //初始化商户
            $this->shop = M::t('superman_mall_shop')->fetch($_SESSION['mobile_admin_shopid']);
            //初始化商户帐号信息
            $this->shop_user = M::t('superman_mall_shop_user')->fetch($_SESSION['mobile_admin_userid']);
            if ($this->shop_user['openid'] != $_W['openid'] || $this->shop_user['shopid'] != $this->shop['id']) {
                $this->message('非法请求', '', 'warn');
            }
            //初始化商户账号组
            if ($this->shop_user['groupid'] > 0) {
                $this->shop_user['group'] = M::t('superman_mall_shop_user_group')->fetch($this->shop_user['groupid']);
            }
        } else if ($_GPC['route'] != 'account.switch') {
            @header('Location: '.$this->createMobileUrl('admin', array('route' => 'account.switch', 'from' => $_GPC['route'])));
            exit;
        }
    }

    private function _init_user_permission() {
        global $_W;
        if ($this->shop_user) {
            $user = $this->shop_user;
            if ($user['groupid'] > 0) {
                if ($user['group']['permission'] == 'all') {
                    $this->user_permission = 'all';
                } else {
                    $this->user_permission = explode('|', $user['group']['permission']);
                }
            } else {     //groupid=0是商户创始人
                $this->user_permission = 'all';
            }
        }
    }

    public function check_user_permission($permission, $exit = true) {
        $has_permission = false;
        if ($this->shop_user) {
            do {
                if (empty($this->user_permission)) {
                    $has_permission = false;
                    break;
                }
                if (is_string($this->user_permission) && $this->user_permission == 'all') {
                    $has_permission = true;
                    break;
                }
                foreach ($this->user_permission as $p) {
                    if ($permission == $p || strexists($permission, $p) || strexists($p, $permission)) {
                        $has_permission = true;
                        break;
                    }
                }
            } while(0);
        }
        if (!$has_permission) {
            if ($exit) {
                $this->message('没有操作权限！', '', 'warn');
            } else {
                return false;
            }
        }
        return true;
    }
}
$obj = new Superman_mall_doMobileAdmin;