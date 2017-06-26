<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doWebStat extends Superman {
	public function __construct() {
		parent::__construct();
        parent::init();
        $this->check_user_permission('superman_mall_menu_stat');
		$this->exec();
	}
    public function exec() {
        global $_W, $_GPC;
        $act = in_array($_GPC['act'], array('display'))?$_GPC['act']:'display';
        $nav['title'] = '数据统计';
        if ($act == 'display') {
            $nav['subtitle'] = '商品数据';
            $type = in_array($_GPC['type'], array('item'))?$_GPC['type']:'item';
            $scroll = intval($_GPC['scroll']);
            $st = $_GPC['datelimit']['start'] ? strtotime($_GPC['datelimit']['start']) : strtotime('-30day');
            $et = $_GPC['datelimit']['end'] ? strtotime($_GPC['datelimit']['end']) : strtotime(date('Y-m-d 23:59:59'));
            $starttime = min($st, $et);
            $endtime = max($st, $et);
            if ($type == 'item') {
                $list = $this->stat_item($starttime, $endtime);
                if ($_W['isajax']) {
                    echo json_encode($list);
                    exit;
                }
            }
        }
        if (defined('IN_SUPERMAN_MALL_ADMIN')) {
            include $this->template('stat/index');
        } else {
            include $this->template('stat');
        }
    }
    private function stat_item($starttime, $endtime) {
        global $_W;
        $list = array();
        $params = array(
            ':uniacid' => $_W['uniacid'],
            ':starttime' => date('Ymd', $starttime),
            ':endtime' => date('Ymd', $endtime),
        );
        $sql = "SELECT daytime,SUM(item_view) AS item_view,SUM(item_share) AS item_share,SUM(item_comment) AS item_comment FROM ".tablename('superman_mall_stat');
        $sql .= " WHERE uniacid=:uniacid";
        if ($this->shop) {
            $sql .= " AND shopid=:shopid";
            $params[':shopid'] = $this->shop['id'];
        }
        $sql .= " AND daytime>=:starttime AND daytime<=:endtime GROUP BY daytime";
        $data = pdo_fetchall($sql, $params, 'daytime');
        for ($i = $starttime; $i <= $endtime; $i += (24*3600)) {
            if ($i == $starttime) {          //每日开始时间戳
                $t1 = $i;
            } else {
                $t1 = strtotime(date('Y-m-d 0:0:0', $i));
            }
            //$t2 = strtotime(date('Y-m-d 23:59:59', $i));
            $daytime = date('Ymd', $t1);

            //日期
            $list['label'][] = date('m-d', $t1);

            //浏览数
            $list['datasets']['flow1'][] = isset($data[$daytime]['item_view'])?$data[$daytime]['item_view']:0;

            //分享数
            $list['datasets']['flow2'][] = isset($data[$daytime]['item_share'])?$data[$daytime]['item_share']:0;

            //评论数
            $list['datasets']['flow3'][] = isset($data[$daytime]['item_comment'])?$data[$daytime]['item_comment']:0;
        }
//        var_dump($list);
        return $list;
    }
}
$obj = new Superman_mall_doWebStat;