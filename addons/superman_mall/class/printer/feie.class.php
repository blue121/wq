<?php
/**
 * 【超人】超级商城模块，打印机接口
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
class SupermanPrinter_feie extends SupermanPrinter {
    private $api = array(
        'ip' => array(
            '5' => 'dzp.feieyun.com', //5对应打印机编号sn第三位
            '6' => 'api163.feieyun.com', //6对应打印机编号sn第三位
        ),
        'port' => 80,
        'uri' => array(
            'print' => '/FeieServer/printOrderAction',
            'check_print' => '/FeieServer/queryOrderStateAction',
            'check_status' => '/FeieServer/queryPrinterStatusAction',
        ),
    );
    public function __construct() {
        parent::__construct();
        if (defined('SUPERMAN_DEVELOPMENT')) {
            $this->debug = true;
        }
    }
    /*
     * 打印参数变量
     *  $params = array(
     *      'sn' => '',
     *      'key' => '',
     *      'times' => '', 打印次数
     *      'printContent' => '',
     *      'apitype' => 'php', 如果打印出来的订单中文乱码，请设置该参数
     *  );
     */
    public function order_print($order) {
        require_once MODULE_ROOT . '/class/printer/feie/HttpClient.class.php';
        $index = substr($this->printer['sn'], 2, 1);
        $ip = $this->api['ip'][$index];
        $client = new HttpClient($ip, $this->api['port']);
        $content = $this->format($order);
        $params = array(
            'sn' => $this->printer['sn'],
            'key' => $this->printer['key'],
            'times' => $this->printer['times'],
            'printContent' => $content,
            //'apitype' => 'php',
        );
        $ret = $client->post($this->api['uri']['print'], $params);
        if (!$ret) {
            WeUtility::logging('fatal', '[SupermanPrinter_feie:do_print] failed, api='.var_export($this->api, true).', params='.var_export($params, true));
            return false;
        } else {
            $result = $client->getContent();
            if (empty($result)) {
                WeUtility::logging('fatal', '[SupermanPrinter_feie:do_print] result is empty, api='.var_export($this->api, true).', params='.var_export($params, true));
                return false;
            } else {
                $result = json_decode($result, true);
                if ($result['responseCode'] == 0) {
                    if ($this->debug) {
                        WeUtility::logging('debug', '[SupermanPrinter_feie:do_print] succeed, result='.var_export($result, true));
                    }
                    return array(
                        'errno' => 0,
                        'errmsg' => 'ok',
                        'searchid' => $result['orderindex'],
                    );
                } else {
                    if ($this->debug) {
                        WeUtility::logging('debug', '[SupermanPrinter_feie:do_print] failed, result='.var_export($result, true));
                    }
                    return array(
                        'errno' => -1,
                        'errmsg' => $result['responseCode'].':'.$result['msg'],
                        'searchid' => '',
                    );
                }
            }
        }
    }
    public function check_print($searchid) {
        require_once MODULE_ROOT . '/class/printer/feie/HttpClient.class.php';
        $index = substr($this->printer['sn'], 2, 1);
        $ip = $this->api['ip'][$index];
        $client = new HttpClient($ip, $this->api['port']);
        $params = array(
            'sn' => $this->printer['sn'],
            'key' => $this->printer['key'],
            'index' => $searchid,
        );
        $ret = $client->post($this->api['uri']['check_print'], $params);
        if (!$ret) {
            WeUtility::logging('fatal', '[SupermanPrinter_feie:check_print] failed, api='.var_export($this->api, true).', params='.var_export($params, true));
            return false;
        } else {
            $result = $client->getContent();
            if (empty($result)) {
                WeUtility::logging('fatal', '[SupermanPrinter_feie:check_print] result is empty, api='.var_export($this->api, true).', params='.var_export($params, true));
                return false;
            } else {
                $result = json_decode($result, true);
                if ($result['responseCode'] == 0) {
                    if ($this->debug) {
                        WeUtility::logging('debug', '[SupermanPrinter_feie:check_print] succeed, result='.var_export($result, true));
                    }
                    if ($result['msg'] == '已打印') {
                        return array(
                            'errno' => 0,
                            'errmsg' => '已打印',
                        );
                    } else {
                        return array(
                            'errno' => 1,
                            'errmsg' => '未打印',
                        );
                    }
                } else {
                    if ($this->debug) {
                        WeUtility::logging('debug', '[SupermanPrinter_feie:check_print] failed, result='.var_export($result, true));
                    }
                    return array(
                        'errno' => -1,
                        'errmsg' => $result['responseCode'].':'.$result['msg'],
                    );
                }
            }
        }
    }
    public function check_status() {
        require_once MODULE_ROOT . '/class/printer/feie/HttpClient.class.php';
        $index = substr($this->printer['sn'], 2, 1);
        $ip = $this->api['ip'][$index];
        $client = new HttpClient($ip, $this->api['port']);
        $params = array(
            'sn' => $this->printer['sn'],
            'key' => $this->printer['key'],
        );
        $ret = $client->post($this->api['uri']['check_status'], $params);
        if (!$ret) {
            WeUtility::logging('fatal', '[SupermanPrinter_feie:check_status] failed, api='.var_export($this->api, true).', params='.var_export($params, true));
            return false;
        } else {
            $result = $client->getContent();
            if (empty($result)) {
                WeUtility::logging('fatal', '[SupermanPrinter_feie:check_status] result is empty, api='.var_export($this->api, true).', params='.var_export($params, true));
                return false;
            } else {
                $result = json_decode($result, true);
                if ($result['responseCode'] == 0) {
                    if ($this->debug) {
                        WeUtility::logging('debug', '[SupermanPrinter_feie:check_status] succeed, result='.var_export($result, true));
                    }
                    return array(
                        'errno' => 0,
                        'errmsg' => $result['msg'],
                    );
                } else {
                    if ($this->debug) {
                        WeUtility::logging('debug', '[SupermanPrinter_feie:check_status] failed, result='.var_export($result, true));
                    }
                    return array(
                        'errno' => -1,
                        'errmsg' => $result['responseCode'].':'.$result['msg'],
                    );
                }
            }
        }
    }
    private function format($order) {
        //标签说明："<BR>"为换行符,"<CB></CB>"为居中放大,"<B></B>"为放大,"<C></C>"为居中,"<L></L>"为字体变高
        //"<W></W>"为字体变宽,"<QR></QR>"为二维码,"<CODE>"为条形码,后面接12个数字
        $content = '';
        if ($this->printer['head'] != '') {
            $content .= '<C>'.$this->printer['head'].'</C><BR>';
        } else {
            $content .= '<C>'.$this->printer['title'].'订单打印小票</C><BR>';
        }
        $content .= '订单号：'.$order['ordersn'].'<BR>';
        if ($order['pay_type']) {
            $content .= "支付方式：".SupermanUtil::get_pay_type_title($order['pay_type'])."\n";
        } else {
            $content .= "支付方式：未支付\n";
        }
        if ($order['dispatch_type'] == 2) {
            $content .= '配送方式：自提<BR>';
        } else {
            $content .= '配送方式：快递<BR>';
        }
        $content .= '下单时间：'.date('Y-m-d H:i:s', $order['createtime']).'<BR>';
        $content .= '================================<BR>';
        $item_info = '';
        foreach ($order['items'] as $item) {
            if ($item_info != '') {
                $item_info .= '--------------------------------<BR>';
            }
            $item_info .= '商品名称：'.$item['title'].'<BR>';
            $item_info .= '价格：'.$item['price'].'<BR>';
            if ($item['number'] != '') {
                $item_info .= '货号：'.$item['number'].'<BR>';
            }
            $item_info .= '件数：'.$item['total'].'<BR>';
            if ($item['sku'] != '') {
                $item_info .= '规格：' . $item['sku'] . '<BR>';
            }
        }
        $content .= $item_info;
        $content .= '================================<BR>';
        $content .= '               合计：'.$order['price'].'<BR>';
        $content .= '<BR>';
        $content .= '收货人：'.$order['username'].'<BR>';
        $content .= '电话：'.$order['mobile'].'<BR>';
        $content .= '地址：'.$order['address'].'<BR>';
        if ($order['remark'] != '') {
            $content .= '留言：'.$order['remark'].'<BR>';
        }
        $content .= '<BR>';
        if ($this->printer['foot'] != '') {
            $content .= '<BR><C>'.$this->printer['foot'].'</C><BR>';
        }
        if ($this->printer['qrcode_url'] != '') {
            $content .= '<QR>'.SupermanUtil::short_url($this->printer['qrcode_url']).'</QR>';
        }
        return $content;
    }
}