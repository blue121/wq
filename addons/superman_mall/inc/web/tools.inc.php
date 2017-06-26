<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doWebTools extends Superman {
	public function __construct() {
		parent::__construct();
        parent::init();
		$this->exec();
	}
    public function exec() {
        global $_W, $_GPC;
        $act = in_array($_GPC['act'], array('autotask'))?$_GPC['act']:'display';
        $nav['title'] = '工具';
        if ($act == 'display') {
            //TODO
        } else if ($act == 'autotask') {
            $nav['subtitle'] = '自动任务检测';
            $tasks = array(
                'order_close' => array(
                    'title' => '未付款订单自动关闭',
                    'interval' => 300,
                ),
                'order_confirm' => array(
                    'title' => '自动确认收货',
                    'interval' => 600,
                ),
                'order_complete' => array(
                    'title' => '自动评论',
                    'interval' => 300,
                ),
                'partner_check' => array(
                    'title' => '分销商状态检查',
                    'interval' => 600,
                ),
                'group_upgrade' => array(
                    'title' => '分销商等级变更',
                    'interval' => 3600,
                ),
                'commission_settlement' => array(
                    'title' => '分销商佣金结算',
                    'interval' => 300,
                ),
                'report' => array(
                    'title' => '模块状态',
                    'interval' => 86400,
                ),
            );
            $path = MODULE_ROOT.'/data/'.$_W['uniacid'];
            $list = array();
            foreach ($tasks as $key=>$val) {
                $filename = "{$path}/{$key}.txt";
                $list[$key] = $val;
                $list[$key]['mtime'] = 0;
                $list[$key]['ntime'] = 0;
                $list[$key]['status'] = 0;
                if (file_exists($filename)) {
                    $mtime = filemtime($filename);
                    $ntime = $mtime + $val['interval'];
                    $list[$key]['mtime'] = date('Y-m-d H:i:s', $mtime);
                    $list[$key]['ntime'] = date('Y-m-d H:i:s', $ntime);
                    $list[$key]['status'] = 1;
                }
            }
            //print_r($list);
        }
        include $this->template('tools');
        exit;
    }
}
$obj = new Superman_mall_doWebTools;