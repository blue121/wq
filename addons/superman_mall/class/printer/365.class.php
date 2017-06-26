<?php
/**
 * 【超人】超级商城模块，打印机接口
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
class SupermanPrinter_365 extends SupermanPrinter {
    private $api = array(
        'ip' => 'http://open.printcenter.cn',
        'port' => 8080,
        'uri' => array(
            'print' => '/addOrder',
            'check_print' => '/queryOrder',
            'check_status' => '/queryPrinterStatus',
        ),
    );
    public function __construct() {
        parent::__construct();
        if (defined('SUPERMAN_DEVELOPMENT')) {
            $this->debug = true;
        }
    }

    public function order_print($order) {
        $content = $this->format($order);
        $selfMessage = array(
            'deviceNo' => $this->printer['sn'],
            'printContent' => $content,
            'key' => $this->printer['key'],
            'times' => 1    //time固定等于1，打印次数不在此控制
        );
        $result = $this->send_format_info($selfMessage, 'print');
        if (empty($result)) {
            WeUtility::logging('fatal', '[SupermanPrinter_365:order_print] failed, selfMessage='.var_export($selfMessage, true).', result='.var_export($result, true));
            return false;
        }
        $result = @json_decode($result, true);
        if (isset($result['orderindex']) && $result['orderindex']) {
            if ($this->debug) {
                WeUtility::logging('debug', '[SupermanPrinter_365:order_print] succeed, result='.var_export($result, true));
            }
            return array(
                'errno' => 0,
                'errmsg' => 'ok',
                'searchid' => $result['orderindex'],
            );
        } else {
            if ($this->debug) {
                WeUtility::logging('debug', '[SupermanPrinter_365:order_print] failed, result='.var_export($result, true));
            }
            return array(
                'errno' => -1,
                'errmsg' => $result['responseCode'].':'.$result['msg'],
                'searchid' => '',
            );
        }
    }

    public function check_print($searchid) {
        $selfMessage = array(
            'deviceNo' => $this->printer['sn'],
            'key' => $this->printer['key'],
            'orderindex' => $searchid,
        );
        $result = $this->send_format_info($selfMessage, 'check_print');
        if (empty($result)) {
            WeUtility::logging('fatal', '[SupermanPrinter_365:check_print] failed, result='.var_export($result, true));
            return false;
        }
        $result = @json_decode($result, true);
        if ($result['responseCode'] == 0) {
            if ($this->debug) {
                WeUtility::logging('debug', '[SupermanPrinter_feie:check_print] succeed, result='.var_export($result, true));
            }
            return array(
                'errno' => 0,
                'errmsg' => '已打印',
            );
        } else if ($result['responseCode'] == 1) {
            return array(
                'errno' => 1,
                'errmsg' => '打印中',
            );
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

    public function check_status() {
        $selfMessage = array(
            'deviceNo' => $this->printer['sn'],
            'key' => $this->printer['key'],
        );
        $result = $this->send_format_info($selfMessage, 'check_status');
        if (empty($result)) {
            WeUtility::logging('fatal', '[SupermanPrinter_365:check_status] failed, result='.var_export($result, true));
            return false;
        }
        $result = @json_decode($result, true);
        if ($result['responseCode'] == 1) {
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

    private function format($order) {
        //^N1 打印一次 ^F1 响铃
        $content = "^N{$this->printer['times']}^F1\n";
        if ($this->printer['head'] != '') {
            $content .= $this->printer['head']."\n";
        } else {
            $content .= $this->printer['title']."\n";
        }
        $content .= "\n订单号：{$order['ordersn']}\n";
        if ($order['pay_type']) {
            $content .= "支付方式：".SupermanUtil::get_pay_type_title($order['pay_type'])."\n";
        } else {
            $content .= "支付方式：未支付\n";
        }
        if ($order['dispatch_type'] == 2) {
            $content .= "配送方式：自提\n";
        } else {
            $content .= "配送方式：快递\n";
        }
        $content .= "下单时间：".date('Y-m-d H:i:s', $order['createtime'])."\n";
        $content .= "================================\n";
        $item_info = '';
        foreach ($order['items'] as $item) {
            if ($item_info != '') {
                $item_info .= "--------------------------------\n";
            }
            $item_info .= "商品名称：{$item['title']}\n";
            $item_info .= "价格：{$item['price']}\n";
            if ($item['number'] != '') {
                $item_info .= "货号：{$item['number']}\n";
            }
            $item_info .= "件数：{$item['total']}\n";
            if ($item['sku'] != '') {
                $item_info .= "规格：{$item['sku']}\n";
            }
        }
        $content .= $item_info;
        $content .= "================================\n";
        $content .= "                     合计：{$order['price']}\n";
        $content .= "\n";
        $content .= "收货人：{$order['username']}\n";
        $content .= "电话：{$order['mobile']}\n";
        $content .= "地址：{$order['address']}\n";
        if ($order['remark'] != '') {
            $content .= "留言：{$order['remark']}\n";
        }
        $content .= "\n";
        if ($this->printer['foot'] != '') {
            $content .= "\n{$this->printer['foot']}\n";
        }
        if ($this->printer['qrcode_url'] != '') {
            $qrlength = chr(strlen(SupermanUtil::short_url($this->printer['qrcode_url'])));
            $content .= "^Q".$qrlength.SupermanUtil::short_url($this->printer['qrcode_url'])."\n";
        }
        return $content;
    }

    private function send_format_info($content = array(), $uri_act = 'print', $extra = array()) {
        if (empty($content) || !in_array($uri_act, array('print', 'check_print', 'check_status'))) {
            WeUtility::logging('[SupermanPrinter_365:order_print:send_format_info] faild, content='.var_export($content, true).',uri_act='.$uri_act);
            return false;
        }
        $url = $this->api['ip'].':'.$this->api['port'].$this->api['uri'][$uri_act];
        $options = array(
            'http' => array(
                'header' => "Content-type: application/x-www-form-urlencoded ",
                'method'  => 'POST',
                'content' => http_build_query($content),
            ),
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        return $result;
    }
}

