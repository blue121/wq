<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doMobileComment extends Superman {
	public function __construct() {
		parent::__construct();
        parent::init();
		$this->exec();
	}
    public function exec() {
        global $_W, $_GPC, $do;
        $_share = $this->share;
        $title = '评价晒单';
        $uid = $_W['member']['uid'];
        $do = $do?$do:'comment';
        $act = in_array($_GPC['act'], array('display', 'post', 'list', 'album'))?$_GPC['act']:'display';
        if ($act == 'display') {    //展示评价
            $this->checkauth();
            $orderid = intval($_GPC['orderid']);
            $itemid = intval($_GPC['itemid']);
            if ($orderid <= 0 || $itemid <= 0) {
                $this->json(ERRNO::ORDER_NOT_EXIST);
            }
            $filter = array(
                'orderid' => $orderid,
                'itemid' => $itemid
            );
            $item = M::t('superman_mall_comment')->fetch($filter);
			if (!$item) {
                $this->json(ERRNO::COMMENT_NOT_EXIST);
			}
			$item['dateline'] = date('Y-m-d H:i:s', $item['dateline']);
            $item['img'] = $item['img'] != ''?unserialize($item['img']):'';
            $item['star'] = SupermanUtil::get_comment_star($item['score']);
        } else if ($act == 'post') {
            //提交评价
            $this->checkauth();
            $orderid = intval($_GPC['orderid']);
            $itemid = intval($_GPC['itemid']);
            if ($orderid <= 0 || $itemid <= 0) {
                $this->json(ERRNO::ORDER_NOT_EXIST);
            }
            //查询是否本人
            $filter = array(
                'uid' => $uid,
                'id' => $orderid,
            );
            $order = M::t('superman_mall_order')->fetch($filter);
            if (!$order) {
                $this->json(ERRNO::INVALID_REQUEST);
            }
            //该订单商品是否已评
            $filter = array(
                'orderid' => $orderid,
                'itemid' => $itemid,
                'iscomment' => 0
            );
            $order_item = M::t('superman_mall_order_item')->fetch($filter);
            if (!$order_item) {
                $this->json(ERRNO::INVALID_REQUEST);
            }

            if (checksubmit('submit')) {
                //分数合法检查
                $score = intval($_GPC['score']);
                if (!in_array($score, array(1,2,3,4,5))) {
                    $this->json(ERRNO::INVALID_REQUEST);
                }
                //评论字数检查
                $message = $_GPC['message'];
                if (mb_strlen($message, 'utf8') > 70) {
                    $this->json(ERRNO::MSG_INVALID);
                }
                $data = array(
                    'uniacid' => $_W['uniacid'],
                    'shopid' => $order['shopid'],
                    'uid' => $uid,
                    'itemid' => $itemid,
                    'orderid' => $orderid,
                    'ordersn' => $order['ordersn'],
                    'score' => $score,
                    'message' => $message?$message:'',
                    'anonymous' => $_GPC['anonymous']?1:0,
                    'status' => 1,      //默认为已审核，后台可以修改审核状态
                    'dateline' => TIMESTAMP,
                );
                $comment_id = M::t('superman_mall_comment')->insert($data);
                if ($comment_id <= 0) {
                    $this->json(ERRNO::SYSTEM_ERROR);
                }

                //评论图片
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
                        $md5 = md5($sid);
                        $filename = $md5.'.jpg';
                        $thumb = $md5.'_thumb.jpg';
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
                            file_image_thumb($allpath.'/'.$filename, $allpath.'/'.$thumb, 200);
                        }
                    }
                    $img = iserializer($img);
                    M::t('superman_mall_comment')->update(array('img' => $img), array('id' => $comment_id));
                }
                //商品 订单 订单详情 数据统计 表更新
                M::t('superman_mall_order_item')->update(array(
                    'iscomment' => 1
                ), array(
                    'itemid' => $itemid,
                    'orderid' => $orderid,
                ));
                if (in_array($score, array(4,5))) {
                    $field = 'comment_praise_count';
                } else if (in_array($score, array(2,3))) {
                    $field = 'comment_mid_count';
                } else {
                    $field = 'comment_bad_count';
                }
                $data = array(
                    'item_comment' => 1,
                );
                $stat_id = M::t('superman_mall_stat')->init(array(
                    'uniacid' => $_W['uniacid'],
                    'shopid' => $order['shopid'],
                    'daytime' => date('Ymd'),
                ));
                M::t('superman_mall_stat')->increment($data, array('id' => $stat_id));
                M::t('superman_mall_item')->increment(array(
                    'comment_count' => 1,
                    $field => 1,
                ), array('id' => $itemid));

                //全部评价后订单已完成
                $extend = $order['extend']?iunserializer($order['extend']):array();
                $item_count = M::t('superman_mall_order_item')->count(array(
                    'orderid' => $orderid,
                ));
                $commented_count = M::t('superman_mall_order_item')->count(array(
                    'orderid' => $orderid,
                    'iscomment' => 1
                ));
                if ($item_count == $commented_count) {
                    //返积分
                    if ($order['reward_credit'] > 0 && !isset($extend['discount_status']['reward_credit'])) {
                        $ret = mc_credit_update($uid, $order['credit_type'], $order['reward_credit'], array($uid, '订单'.$order['ordersn'].'完成返积分', 'superman_mall'));
                        if (is_error($ret)) {
                            WeUtility::logging('fatal', '[comment.inc]订单已完成，但由于未知原因返积分失败，orderid='.$orderid.',ret='.var_export($ret, true));
                        }
                        $extend['discount_status']['reward_credit'] = 1;
                    }
                    M::t('superman_mall_order')->update(array(
                        'extend' => $extend?iserializer($extend):'',
                        'status' => 4,
                        'updatetime' => TIMESTAMP,
                    ), array('id' => $orderid));
                }

                //商户触发器
                $param = array(
                    'action' => 'comment_post',
                    'shopid' => $order['shopid'],
                );
                Trigger::init('shop')->send($param);
                //平台触发器
                $param = array(
                    'action' => 'comment_post',
                    'uniacid' => $_W['uniacid'],
                );
                Trigger::init('platform')->send($param);
                $this->json(ERRNO::OK, '评价成功，跳转中...', array('url' => $this->createMobileUrl('order', array('status' => 'complete'))));
            }
        } else if ($act == 'list') {
            $itemid = intval($_GPC['itemid']);
            if ($itemid <= 0) {
                $this->json(ERRNO::INVALID_REQUEST);
            }
            $type = intval($_GPC['type']);
            if (!in_array($type, array(0,1,2,3,4))) {
                $this->json(ERRNO::INVALID_REQUEST);
            }
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = 10;
            $start = ($pindex - 1) * $pagesize;
            $filter = array(
                'itemid' => $itemid,
                'status' => 1,
            );
            switch ($type) {
                case 1: //好评
                    $filter['score'] = array(4, 5);
                    break;
                case 2: //中评
                    $filter['score'] = array(2, 3);
                    break;
                case 3: //差评
                    $filter['score'] = 1;
                    break;
            }
            $list = M::t('superman_mall_comment')->fetchall($filter, '', $start, $pagesize);
            if ($list) {
                $attachment_path = SupermanUtil::attachment_path();
                foreach ($list as &$li) {
                    $imgs = $li['img']?unserialize($li['img']):array();
                    foreach ($imgs as $img) {
                        $thumb = SupermanUtil::get_thumb_filename($img);
                        $li['imglist'][] = array(
                            'thumb' => file_exists($attachment_path.$thumb)?$thumb:$img,
                            'img' => $img,
                        );
                    }
                    $li['dateline'] = date('Y-m-d H:i:s', $li['dateline']);
                    if ($li['uid'] > 0) {
                        $li['member'] = mc_fetch($li['uid'], array('nickname', 'avatar'));
                        if ($li['member']) {
                            if ($li['anonymous']) {
                                $li['member']['nickname'] = SupermanUtil::hide_nickname($li['member']['nickname']);
                            }
                            $li['member']['avatar'] = tomedia($li['member']['avatar']);
                        }
                    }
                    if (!isset($li['member']) || !$li['member']) {
                        $li['member'] = array(
                            'nickname' => '**',
                            'avatar' => $this->mobile_path.'/images/avatar.png',
                        );
                    }
                    $li['score_html'] = SupermanUtil::get_comment_star($li['score']);
                }
                unset($li);
            }
            //print_r($list);
            //加载更多
            if ($_W['isajax'] && $_GPC['load'] == 'infinite') {
                die(json_encode($list));
            }
            $display_url = $this->createMobileUrl('comment', array(
                'act' => 'list',
                'itemid' => $itemid,
                'type' => $_GPC['type'],
                'load' => 'infinite',
            ));
        } else if ($act == 'album') {
            $imgs = array();
            $itemid = intval($_GPC['itemid']);
            if ($itemid <= 0) {
                $this->json(ERRNO::INVALID_REQUEST);
            }
            $pindex = max(1, intval($_GPC['page']));
            $pagesize = 15;
            $start = ($pindex - 1) * $pagesize;
            $filter = array(
                'itemid' => $itemid,
                'status' => 1,
                'img' => "#!=''",
            );
            $list = M::t('superman_mall_comment')->fetchall($filter, '', $start, $pagesize);
            if ($list) {
                $attachment_path = SupermanUtil::attachment_path();
                foreach ($list as $li) {
                    $li['img'] = $li['img'] ? unserialize($li['img']) : '';
                    if ($li['img']) {
                        //$imgs = array_merge($imgs, $li['img']);
                        $score_html = SupermanUtil::get_comment_star($li['score']);
                        $score_html = str_replace('"', '\'', $score_html);
                        $dateline = date('Y-m-d H:i:s', $li['dateline']);
                        $message =<<<EOF
<div class='clearfix'>
<span class='pull-left color-danger'>{$score_html}</span>
<span class='pull-right font6'>{$dateline}</span>
</div>
<div class='text-left font7'>{$li['message']}</div>
EOF;
                        foreach ($li['img'] as $img) {
                            $thumb = SupermanUtil::get_thumb_filename($img);
                            $imgs[] = array(
                                'id' => $li['id'],
                                'itemid' => $li['itemid'],
                                'img' => $img,
                                'thumb' => file_exists($attachment_path.$thumb)?$thumb:$img,
                                'message' => $message,
                            );
                        }
                    }
                }
            }
            //print_r($list);
            //print_r($imgs);
            //加载更多
            if ($_W['isajax'] && $_GPC['load'] == 'infinite') {
                die(json_encode($imgs));
            }
            $display_url = $this->createMobileUrl('comment', array(
                'act' => 'album',
                'itemid' => $itemid,
                'load' => 'infinite',
            ));
        }
        include $this->template('comment');
    }
}
$obj = new Superman_mall_doMobileComment;