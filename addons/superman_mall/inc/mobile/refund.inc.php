<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doMobileRefund extends Superman {
	public function __construct() {
		parent::__construct();
        parent::init();
		$this->exec();
	}
    public function exec() {
        global $_W, $_GPC, $do;
        $_share = $this->share;
        $do = $do?$do:'refund';
        $act = in_array($_GPC['act'], array('display', 'post', 'progress', 'progress_detail'))?$_GPC['act']:'display';
        if ($act == 'display') {
            $title = '售后服务';
            if ($_W['member']['uid']) {
                $order_setting = M::t('superman_mall_kv')->fetch_value(SUPERMAN_SKEY_PLATFORM_ORDER);
                $sql = 'SELECT a.ordersn,b.* FROM '.tablename('superman_mall_order').' AS a RIGHT JOIN '.tablename('superman_mall_order_item').' AS b ON a.id=b.orderid WHERE a.uid=:uid AND a.status IN (3, 4) AND (a.updatetime>:updatetime OR b.service_type!=0) ORDER BY a.createtime DESC, b.orderid DESC';
                $params = array(
                    ':uid' => $_W['member']['uid'],
                    ':updatetime' => isset($order_setting['after_sale_limit'])?strtotime("-".intval($order_setting['after_sale_limit'])." days"):strtotime("-7 days")
                );
                $list = pdo_fetchall($sql, $params);
                if ($list) {
                    $arr = array();
                    foreach($list as $k => $v) {
                        $arr[$v['orderid']]['item'][] = $v;
                        $arr[$v['orderid']]['ordersn'] = $v['ordersn'];
                    }
                }
            }
        } else if ($act == 'post') {
            $title = '申请售后';
            if ($_W['member']['uid']) {
                $oiid = intval($_GPC['oiid']);
                if ($oiid <= 0) {
                    $this->json(ERRNO::INVALID_REQUEST);
                }
                $order_item = M::t('superman_mall_order_item')->fetch($oiid);
                if (!$order_item) {
                    $this->json(ERRNO::INVALID_REQUEST);
                }
                //是否申请过售后
                if ($order_item['service_type'] != 0) {
                    $this->json(ERRNO::INVALID_REQUEST);
                }
                //是否本人
                $filter = array(
                    'uid' => $_W['member']['uid'],
                    'id' => $order_item['orderid']
                );
                $order = M::t('superman_mall_order')->fetch($filter);
                if (!$order) {
                    $this->json(ERRNO::ORDER_NOT_EXIST);
                }
                if (checksubmit('submit')) {
                    $total = intval($_GPC['total']);
                    if ($total > $order_item['total']) {
                        $this->json(ERRNO::INVALID_REQUEST);
                    }
                    $data = array(
                        'uniacid' => $_W['uniacid'],
                        'shopid' => $order['shopid'],
                        'oiid' => $oiid,
                        'type' => 1,    //1.0仅支持退货
                        'total' => $total,
                        'money' => $order_item['price'],
                        'remark' => $_GPC['remark'],
                        'status' => 0,
                        'createtime' => TIMESTAMP,
                        'updatetime' => TIMESTAMP
                    );
                    $serviceid = M::t('superman_mall_service')->insert($data);
                    if ($serviceid === false) {
                        $this->json(ERRNO::SYSTEM_ERROR);
                    }
                    //上传图片
                    $serverId = $_GPC['serverId'];
                    if (!empty($serverId)) {
                        $serverId = explode(',', $serverId);
                        //初始化路径
                        $path = "images/{$_W['uniacid']}/".date('Y/m');
                        $attachment_path = SupermanUtil::attachment_path();
                        $allpath = $attachment_path.$path;

                        mkdirs($allpath);

                        //下载微信图片
                        load()->model('account');
                        $acc = WeAccount::create($_W['account']['acid']);
                        $token = $acc->getAccessToken();
                        if (is_error($token)) {
                            WeUtility::logging('fatal', 'token error, message='.$token['message']);
                            $this->json(ERRNO::SYSTEM_ERROR, 'token error');
                        }
                        load()->func('communication');
                        $img = array();
                        foreach ($serverId as $sid) {
                            $filename = md5($sid).'.jpg';
                            $imgpath = $allpath.'/'.$filename;
                            $url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token={$token}&media_id={$sid}";
                            $resp = ihttp_request($url);
                            if (is_error($resp)) {
                                WeUtility::logging('fatal', 'request error, message='.$resp['message']);
                            } else {
                                //保存图片
                                $fp = @fopen($imgpath, 'wb');
                                @fwrite($fp, $resp['content']);
                                @fclose($fp);
                                $img[] = $path.'/'.$filename;
                            }
                        }
                        $img = iserializer($img);
                        M::t('superman_mall_service')->update(array('img' => $img), array('id' => $serviceid));
                    }
                    //更新表
                    M::t('superman_mall_order_item')->update(array(
                        'service_type' => 1
                    ), array('id' => $oiid));
                    $progress_title = '申请退货';
                    $srv_data = array(
                        'srvid' => $serviceid,
                        'userid' => $_W['member']['uid'],
                        'title' => $progress_title,
                        'remark' => $_GPC['remark'],
                        'dateline' => TIMESTAMP
                    );
                    M::t('superman_mall_service_progress')->insert($srv_data);
                    //商户触发器
                    $param = array(
                        'action' => 'order_refund_submit',
                        'shopid' => $order['shopid'],
                        'url' => $_W['siteroot'].'app/'.$this->createMobileUrl('admin', array('route' => 'order.post', 'id' => $order['id']))
                    );
                    Trigger::init('shop')->send($param);
                    //平台触发器
                    $param = array(
                        'action' => 'order_refund_submit',
                        'uniacid' => $_W['uniacid'],
                        'url' => $_W['siteroot'].'app/'.$this->createMobileUrl('admin', array('route' => 'order.post', 'id' => $order['id']))
                    );
                    Trigger::init('platform')->send($param);
                    $url = $this->createMobileUrl('refund', array('act'=>'progress', 'oiid' => $oiid));
                    $this->json(ERRNO::OK, '申请成功，跳转中...', array('url'=>$url));
                }
            }
        } else if ($act == 'progress') {
            $title = '查看售后进度';
            if ($_W['member']['uid']) {
                $oiid = intval($_GPC['oiid']);
                if ($oiid <= 0) {
                    $this->json(ERRNO::INVALID_REQUEST);
                }
                $order_item = M::t('superman_mall_order_item')->fetch($oiid);
                if (!$order_item) {
                    $this->json(ERRNO::INVALID_REQUEST);
                }
                //是否申请过售后
                if ($order_item['service_type'] == 0) {
                    $this->json(ERRNO::INVALID_REQUEST);
                }
                //是否本人
                $filter = array(
                    'uid' => $_W['member']['uid'],
                    'id' => $order_item['orderid']
                );
                $order = M::t('superman_mall_order')->fetch($filter);
                if (!$order) {
                    $this->json(ERRNO::ORDER_NOT_EXIST);
                }
                $filter = array('oiid' => $oiid);
                $service = M::t('superman_mall_service')->fetch($filter);
                if (!$service) {
                    $this->json(ERRNO::INVALID_REQUEST);
                }
                //确认/取消售后
                if (isset($_GPC['state']) && $_GPC['state']) {
                    $state = $_GPC['state'];
                    if (!in_array($state, array('confirm', 'cancel'))) {
                        $this->json(ERRNO::INVALID_REQUEST);
                    }
                    //更新service表、progress表
                    $srv_data = array(
                        'updatetime' => TIMESTAMP
                    );
                    $pro_data = array(
                        'dateline' => TIMESTAMP,
                        'srvid' => $service['id'],
                        'userid' => $_W['member']['uid'],
                    );
                    if ($state == 'confirm') {
                        $pro_data['title'] = '已完成';
                        $pro_data['remark'] = '用户已确认完成';
                        $srv_data['status'] = 1;
                    } else {
                        $pro_data['title'] = '已取消';
                        $pro_data['remark'] = '用户已取消售后';
                        $srv_data['status'] = -1;
                    }
                    M::t('superman_mall_service')->update($srv_data, array('id' => $service['id']));
                    M::t('superman_mall_service_progress')->insert($pro_data);
                    $url = $this->createMobileUrl('refund', array('act' => 'progress', 'oiid' => $oiid));
                    $this->json(ERRNO::OK, '操作成功，跳转中...', array('url' => $url));
                }
                $service['img'] = $service['img']?iunserializer($service['img']):'';
                $filter = array(
                    'srvid' => $service['id']
                );
                $orderby = 'ORDER BY dateline DESC';
                $progress_list = M::t('superman_mall_service_progress')->fetchall($filter, $orderby, 0, 4);
//                var_dump($progress_list);
            }
        } else if ($act == 'progress_detail') {
            $title = '售后进度详情';
            if ($_W['member']['uid']) {
                $srvid = intval($_GPC['srvid']);
                if ($srvid <= 0) {
                    $this->json(ERRNO::INVALID_REQUEST);
                }
                $service = M::t('superman_mall_service')->fetch($srvid);
                if (!$service) {
                    $this->json(ERRNO::INVALID_REQUEST);
                }
                $order_item = M::t('superman_mall_order_item')->fetch($service['oiid']);
                if (!$order_item) {
                    $this->json(ERRNO::INVALID_REQUEST);
                }
                $filter = array(
                    'id' => $order_item['orderid'],
                    'uid' => $_W['member']['uid']
                );
                $order = M::t('superman_mall_order')->fetch($filter);
                if (!$order) {
                    $this->json(ERRNO::INVALID_REQUEST);
                }
                $filter = array(
                    'srvid' => $srvid
                );
                $orderby = 'ORDER BY dateline DESC';
                $progress_list = M::t('superman_mall_service_progress')->fetchall($filter, $orderby, 0, -1);
            }
        }
		include $this->template('refund');
    }
}
$obj = new Superman_mall_doMobileRefund;