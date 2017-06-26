<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doMobileSeckill extends Superman {
	public function __construct() {
		parent::__construct();
        parent::init();
		$this->exec();
	}
    public function exec() {
        global $_W, $_GPC, $do;
        $_share = $this->share;
        $do = $do?$do:'refund';
        $act = in_array($_GPC['act'], array('display'))?$_GPC['act']:'display';
        if ($act == 'display') {
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = 10;
            $start = ($pindex - 1) * $pagesize;
            $seckill_params = SupermanUtil::get_seckill_params();
            $type = in_array($_GPC['type'], array(8, 12, 16, 20))?$_GPC['type']:$seckill_params['key'];
            $time_group = array(
                8 => strtotime('8:00'),
                12 => strtotime('12:00'),
                16 => strtotime('16:00'),
                20 => strtotime('20:00'),
            );
            $seckill_time = SupermanUtil::get_seckill_time();
            if (isset($this->plugin_setting['seckill']) && $this->plugin_setting['seckill']) {
                $filter = array(
                    'uniacid' => $_W['uniacid'],
                    'special' => 1,
                    'seckill_time' => $type,
                );
                //不允许展示历史秒杀数据
                if (!SUPERMAN_CONFIG_SECKILL_HISTORY) {
                    $filter['starttime'] = '#<='.TIMESTAMP;
                    $filter['endtime'] = '#>='.TIMESTAMP;
                }
                $orderby = 'ORDER BY displayorder DESC, endtime DESC';
                $list = M::t('superman_mall_item')->fetchall($filter, $orderby, $start, $pagesize);
                if ($list) {
                    foreach ($list as &$item) {
                        $item['cover'] = tomedia($item['cover']);
                        if ($item['total'] == 0) {
                            $item['sale_percent'] = 100;
                        } else {
                            $item['sale_percent'] = floor($item['sales']/($item['sales']+$item['total'])*100);
                        }
                        if ($item['sale_percent'] <= 0) {
                            $item['sale_percent'] = 1;
                        }
                    }
                }
                //print_r($list);
                if ($_W['isajax'] && $_GPC['load'] == 'infinite') {
                    die(json_encode($list));
                }
            }
        }
        include $this->template('seckill');
    }
}
$obj = new Superman_mall_doMobileSeckill;