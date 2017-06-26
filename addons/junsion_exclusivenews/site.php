<?php
//图乐源码：www.tule5.com
defined('IN_IA') or exit('Access Denied');
require IA_ROOT . '/addons/junsion_exclusivenews/jun/jun.php';
require IA_ROOT . '/addons/junsion_exclusivenews/qiniu.php';
define('RES', '../addons/junsion_exclusivenews/template/mobile/');
define('OD_ROOT', IA_ROOT . '/web/junsionnews');
class Junsion_exclusivenewsModuleSite extends WeModuleSite{
    public function doMobileCover(){
        global $_W, $_GPC;
        WXLimit();
        $cfg = $this -> module['config'];
        $prices = explode(';', $cfg['prices']);
        load() -> model('mc');
        $info = mc_oauth_userinfo();
        $bl = pdo_fetch('select openid from ' . tablename($this -> modulename . "_black") . " where openid='{$info['openid']}'");
        if ($bl){
            MSG('您已被拉黑，不能发布消息！', $this -> createMobileUrl('list'), 'e');
        }
        if ($_GPC['submit']){
            $price = $_GPC['price'];
            if (!in_array($price, $prices)){
                MSG('参数错误！');
            }
            $hideword = array('word' => $_GPC['hideword']);
            if ($_GPC['imgs']){
                $hideword['img'] = $_GPC['imgs'];
            }
            if ($_GPC['video']){
                $hideword['video'] = $_GPC['video'];
            }
            if ($_GPC['videourl']){
                $hideword['mp4'] = toimage($_GPC['videourl']);
            }elseif ($_FILES['videodata']['tmp_name']){
                set_time_limit(0);
                $hideword['mp4'] = $this -> uploadQiniu($_FILES['videodata']['tmp_name'], $cfg, '.mp4', toimage($cfg['mask']));
            }
            if ($_GPC['l_title']){
                foreach ($_GPC['l_title'] as $k => $value){
                    $hideword['links'][] = array('title' => $value, 'url' => $_GPC['l_url'][$k]);
                }
            }
            $data = array('weid' => $_W['uniacid'], 'words' => $_GPC['words'], 'hideword' => serialize($hideword), 'price' => $_GPC['price'], 'createtime' => time(), 'openid' => $info['openid'], 'nickname' => $info['nickname'], 'status' => !intval($_GPC['status']), 'avatar' => $info['headimgurl']?$info['headimgurl']:$info['avatar'],);
            pdo_insert($this -> modulename . '_news', $data);
            $nid = pdo_insertid();
            if ($_GPC['video']){
                MSG('提交成功！', $this -> createMobileUrl('show', array('nid' => $nid)));
            }
            header('location:' . $this -> createMobileUrl('show', array('nid' => $nid)));
            exit;
        }
        $keyword = pdo_fetchcolumn('select content from ' . tablename("rule") . " r left join " . tablename('rule_keyword') . " k on r.id=k.rid where (r.name='独家消息入口' or r.name='独家消息') and r.module='cover' and r.uniacid='{$_W['uniacid']}'");
        if ($cfg['ismulti'] && $cfg['multi']){
            $ck = $_COOKIE['jun_news' . $_W['uniacid']];
            if (empty($ck)){
                foreach ($cfg['multi'] as $value){
                    for ($i = 0; $i < $value['multi_rate']; $i++){
                        $rate[] = $value['multi_url'];
                    }
                }
                shuffle($rate);
                $multi = $rate[0];
                if ($_GPC['token']){
                    setcookie('jun_news' . $_W['uniacid'], '1', time() + 24 * 3600);
                    $multi = '';
                }
            }
        }
        include $this -> template('publish');
    }
    public function doMobileShow(){
        WXLimit();
        global $_W, $_GPC;
        $cfg = $this -> module['config'];
        $nid = $_GPC['nid'];
        if (empty($nid)) MSG('参数错误！');
        $news = pdo_fetch('select * from ' . tablename($this -> modulename . "_news") . " where id='{$nid}'");
        if (empty($news)) MSG('消息不存在！');
        load() -> model('mc');
        $info = mc_oauth_userinfo();
        $openid = $info['openid'];
        if ($cfg['checked'] && !$news['checked'] && $news['openid'] != $openid) MSG('该消息未审核！', $this -> createMobileUrl('list'), 'w');
        $bl = pdo_fetch('select openid from ' . tablename($this -> modulename . "_black") . " where openid='{$news['openid']}'");
        if ($bl){
            MSG('该用户已被拉黑！', $this -> createMobileUrl('list'), 'e');
        }
        $title = str_replace('#昵称#', $news['nickname'], $cfg['ntitle']);
        $title = str_replace('#价格#', $news['price'], $title);
        $title = str_replace('#消息#', $news['words'] . "██████", $title);
        $desc = str_replace('#昵称#', $news['nickname'], $cfg['ndesc']);
        $desc = str_replace('#价格#', $news['price'], $desc);
        $desc = str_replace('#消息#', $news['words'] . "██████", $desc);
        $share = array('title' => $title, 'desc' => $desc, 'link' => $_W['siteroot'] . "app" . substr($this -> createMobileUrl('show', array('nid' => $nid)), 1), 'imgUrl' => $cfg['nthumb']?toimage($cfg['nthumb']):$news['avatar']);
        $order = pdo_fetch('select * from ' . tablename($this -> modulename . '_order') . " where nid='{$nid}' and openid='{$openid}' and status=1");
        $orders = pdo_fetchall('select checked,price from ' . tablename($this -> modulename . '_order') . " where nid='{$nid}' and status=1");
        $good = 0;
        $fail = 0;
        $price = 0;
        foreach ($orders as $value){
            if ($value['checked'] == 1){
                $good++;
                $price += $value['price'];
            }elseif ($value['checked'] == -1) $fail++;
        }
        if ($news['openid'] == $openid){
            include $this -> template('myshow');
        }else{
            if ($news['opened'] && time() >= $news['createtime'] + $news['opendays'] * 24 * 3600){
                $order = 1;
            }
            include $this -> template('show');
        }
    }
    public function doMobileCheck(){
        global $_W, $_GPC;
        $nid = $_GPC['nid'];
        if (empty($nid)) MSG('参数错误！');
        $news = pdo_fetch('select * from ' . tablename($this -> modulename . "_news") . " where id='{$nid}'");
        if (empty($news)) MSG('消息不存在！');
        $order = pdo_fetch('select * from ' . tablename($this -> modulename . '_order') . " where nid='{$nid}' and openid='{$_W['openid']}' and status=1");
        if (empty($order)){
            MSG('未支付成功！');
        }
        if (empty($order['checked'])){
            $check = $_GPC['checked'];
            pdo_update($this -> modulename . "_order", array('checked' => $check), array('id' => $order['id']));
            $cfg = $this -> module['config'];
            if ($check == 1){
                if (!$cfg['iswith']){
                    $res = $this -> sendRedPacket($news, $order['id']);
                    if ($res != true){
                        pdo_update($this -> modulename . "_order", array('checked' => 0), array('id' => $order['id']));
                        MSG('提交错误，请重试！', $this -> createMobileUrl('show', array('nid' => $nid)), 'w');
                    }
                }
                $msgid = $cfg['MSG_READ'];
                if (!empty($msgid)){
                    $data = array('first' => array('value' => "你好，您分享的独家消息，有用户付费阅读了",), 'keyword1' => array('value' => $order['nickname']?$order['nickname']:'路人甲',), 'keyword2' => array('value' => date('Y-m-d H:i:s', $order['createtime']),), 'keyword3' => array('value' => $order['price'] . "元",), 'remark' => array('value' => "感谢您的分享",));
                    $res = $this -> sendTemplateMsg($news['openid'], $msgid, $data, $this -> createMobileUrl('show', array('nid' => $nid)));
                    file_put_contents(IA_ROOT . "/addons/junsion_exclusivenews/log.txt", $res);
                }
            }
        }
        header('location:' . $this -> createMobileUrl('show', array('nid' => $nid)));
        exit;
    }
    private function sendTemplateMsg($openid, $msgid, $data, $url, $topcolor = '#FF0000'){
        global $_W;
        if (stripos($url, 'http://') === false){
            $url = $_W['siteroot'] . str_replace('./', 'app/', $url);
        }
        load() -> classs('weixin.account');
        $acid = pdo_fetchcolumn('select acid from ' . tablename('account') . " where uniacid={$_W['uniacid']}");
        $accObj = WeixinAccount :: create($acid);
        return $accObj -> sendTplNotice($openid, $msgid, $data, $url, $topcolor);
    }
    private function getAccessToken(){
        global $_W;
        load() -> model('account');
        $acid = $_W['acid'];
        if ($_W['account']['level'] < 3) $acid = $_W['account']['oauth']['acid'];
        $account = WeAccount :: create($acid);
        return $account -> getAccessToken();
    }
    private function getMaskPic($source, $cfg){
        if ($cfg['isqiniu']){
            $dest = '../attachment/jun_news/mask' . time() . random(10) . '.jpg';
        }else $dest = '../attachment/images/mask' . time() . random(10) . '.jpg';
        $size = getimagesize($source);
        $this -> imageMosaics($source, $dest, 0, 0, $size[0], $size[1], 4);
        return $dest;
    }
    function imageMosaics($source, $dest, $x1, $y1, $x2, $y2, $deep){
        file_put_contents(IA_ROOT . "/addons/" . $this -> modulename . "/log.txt", 's:' . $source, FILE_APPEND);
        list($owidth, $oheight, $otype) = getimagesize($source);
        file_put_contents(IA_ROOT . "/addons/" . $this -> modulename . "/log.txt", 'size:' . $owidth . " " . $oheight . " " . $otype, FILE_APPEND);
        if($x1 > $owidth || $x1 < 0 || $x2 > $owidth || $x2 < 0 || $y1 > $oheight || $y1 < 0 || $y2 > $oheight || $y2 < 0){
            return false;
        }
        switch($otype){
        case 1: $source_img = imagecreatefromgif($source);
            break;
        case 2: $source_img = imagecreatefromjpeg($source);
            break;
        case 3: $source_img = imagecreatefrompng($source);
            break;
        default: return false;
        }
        for($x = $x1; $x < $x2; $x = $x + $deep){
        for($y = $y1; $y < $y2; $y = $y + $deep){
        $color = imagecolorat($source_img, $x + round($deep / 2), $y + round($deep / 2));
        imagefilledrectangle($source_img, $x, $y, $x + $deep, $y + $deep, $color);
    }
}
switch($otype){
case 1: imagegif($source_img, $dest);
    break;
case 2: imagejpeg($source_img, $dest);
    break;
case 3: imagepng($source_img, $dest);
    break;
}
file_put_contents(IA_ROOT . "/addons/" . $this -> modulename . "/log.txt", 'de:' . $dest, FILE_APPEND);
return is_file($dest)? true : false;
}
public function doMobileUpload(){
global $_W, $_GPC;
$cfg = $this -> module['config'];
$url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=" . $this -> getAccessToken() . "&media_id=" . $_GPC['imgid'];
if (empty($_GPC['type'])){
    $dst = imagecreatefromstring(file_get_contents($url));
    if ($cfg['isqiniu']){
        if (empty($cfg['mask'])) die($this -> uploadQiniu($url, $cfg, '.jpg'));
        $url = "../attachment/images/" . random(10) . "news.jpg";
    }else{
        $path = '../attachment/jun_news/';
        if (file_exists($path)){
            mkdir($path);
            chmod($path, 0777);
        }
        $url = $path . random(10) . time() . "news.jpg";
    }
    if ($cfg['mask']){
        $cfg['mask'] = toimage($cfg['mask']);
        $src = imagecreatefromstring(file_get_contents($cfg['mask']));
        list($src_w, $src_h) = getimagesize($cfg['mask']);
        imagecopy($dst, $src, 10, 10, 0, 0, $src_w, $src_h);
        imagedestroy($src);
    }
    imagejpeg($dst, $url);
    imagedestroy($dst);
    if ($cfg['isqiniu']){
        echo $this -> uploadQiniu($url, $cfg);
        @unlink($url);
    }else echo toimage($url);
}else{
    if (!$cfg['isqiniu']){
        $ch = curl_init ();
        curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt ($ch, CURLOPT_URL, $url);
        ob_start ();
        curl_exec ($ch);
        $return_content = ob_get_contents ();
        ob_end_clean ();
        $return_code = curl_getinfo ($ch, CURLINFO_HTTP_CODE);
        $path = '../attachment/jun_news/';
        if (file_exists($path)){
            mkdir($path);
            chmod($path, 0777);
        }
        $url = $path . random(10) . time() . "news.mp3";
        $fp = @fopen($url, "a");
        $r = fwrite($fp, $return_content);
        echo toimage($url);
    }else echo $this -> uploadQiniu($url, $cfg, '.amr');
}
}
private function uploadQiniu($url, $cfg, $type = '.png', $mask = ''){
$cfg['url'] = $cfg['qiniuUrl'];
$qiniu = new Qiniu();
$res = $qiniu -> save($url, $cfg, $type, $mask);
return $res;
}
public function doMobilePay(){
global $_W, $_GPC;
$cfg = $this -> module['config'];
$nid = $_GPC['nid'];
$news = pdo_fetch('select * from ' . tablename($this -> modulename . "_news") . " where id='{$nid}'");
load() -> model('mc');
$info = mc_oauth_userinfo();
$ordersn = 'A' . random(6, true) . time();
pdo_insert($this -> modulename . "_order", array('nickname' => $info['nickname'], 'avatar' => $info['headimgurl']?$info['headimgurl']:$info['avatar'], 'weid' => $_W['uniacid'], 'openid' => $_W['openid'], 'nid' => $nid, 'price' => $news['price'], 'createtime' => time(), 'ordersn' => $ordersn,));
$params = array();
$params['tid'] = pdo_insertid();
$params['user'] = $_W['openid'];
$params['fee'] = $news['price'];
$params['title'] = $_W['account']['name'] . '之独家消息';
$params['ordersn'] = $ordersn;
if ($cfg['isyunpay']){
    $this -> yunPay($cfg, $params);
}
$this -> pay($params);
}
public function yunPayResult($params){
global $_W, $_GPC;
include_once 'yun/func.php';
$order = pdo_fetch('select * from ' . tablename($this -> modulename . "_order") . " where ordersn='{$params['tid']}'");
if (empty($order)) MSG('订单不存在！');
$_W['uniacid'] = $_W['weid'] = $order['weid'];
$cfg = pdo_fetchcolumn('select settings from ' . tablename('uni_account_modules') . " where uniacid='{$_W['uniacid']}' and module='" . $this -> modulename . "'");
$cfg = unserialize($cfg);
$yunNotify = md5Verify($params['fee'], $params['tid'], $params['sign'], $cfg['yun_key'], $cfg['yun_pid']);
if (!$yunNotify){
    MSG('支付失败！');
}
if ($order['price'] != $params['fee']){
    MSG('订单有误！');
}
if ($params['from'] == 'notify'){
    if ($order['status'] == 0){
        pdo_update($this -> modulename . "_order", array('status' => 1, 'paytransid' => $params['trade_no']), array('id' => $order['id']));
    }
}elseif ($params['from'] == 'return'){
    header('location:../../../app' . substr($this -> createMobileUrl('show', array('nid' => $order['nid'])), 1));
    exit;
}
}
private function yunPay($cfg, $params){
global $_W, $_GPC;
$yun_config['partner'] = $cfg['yun_pid'];
$yun_config['key'] = $cfg['yun_key'];
$seller_email = $cfg['yun_user'];
include_once 'yun/func.php';
$out_trade_no = $params['ordersn'];
$subject = $params['title'];
$total_fee = $params['fee'];
$nourl = $_W['siteroot'] . "addons/" . $this -> modulename . "/yun/notify.php";
$reurl = $_W['siteroot'] . "addons/" . $this -> modulename . "/yun/return.php";
$parameter = array("partner" => trim($yun_config['partner']), "seller_email" => $seller_email, "out_trade_no" => $out_trade_no, "subject" => $subject, "total_fee" => $total_fee, "body" => $body, "nourl" => $nourl, "reurl" => $reurl, "orurl" => $orurl, "orimg" => $orimg);
$html_text = i2e($parameter, "支付进行中...");
die($html_text);
}
public function payResult($params){
global $_W;
if ($params['result'] == 'success'){
    $order = pdo_fetch('select nid,price from ' . tablename($this -> modulename . "_order") . " where id='{$params['tid']}'");
    if ($params['fee'] != $order['price'] || empty($order)){
        MSG('参数有误！');
    }
    $paylog = pdo_fetch('select * from ' . tablename('core_paylog') . " where module='" . $this -> modulename . "' and tid='{$params['tid']}' and status=1");
    if (empty($paylog)){
        MSG('参数有误！');
    }
    load() -> model('account');
    $setting = uni_setting($_W['uniacid'], array('payment'));
    if ($params['type'] == 'wechat'){
        if(!empty($setting['payment']['wechat']['switch'])){
            if (empty($params['tag']['transaction_id'])) MSG('参数有误！');
            elseif ($params['from'] == 'notify'){
                $res = $this -> checkWechatTran($setting, $params['tag']['transaction_id']);
                if ($res['code'] == 1 && $res['fee'] == $order['price']){
                    pdo_update($this -> modulename . "_order", array('status' => 1, 'paytransid' => $params['tag']['transaction_id']), array('id' => $params['tid']));
                }
            }
        }
    }elseif ($params['type'] == 'credit' && !empty($setting['payment']['credit']['switch'])){
        pdo_update($this -> modulename . "_order", array('status' => 1), array('id' => $params['tid']));
    }
    if ($params['from'] != 'notify'){
        header('location:' . $_W['siteroot'] . "app" . substr($this -> createMobileUrl('show', array('nid' => $order['nid'])), 1));
        exit;
    }
}
}
private function postXmlCurl($xml, $url, $useCert = false, $second = 30){
$ch = curl_init();
curl_setopt($ch, CURLOPT_TIMEOUT, $second);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_HEADER, FALSE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
if($useCert == true){
    curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
    curl_setopt($ch, CURLOPT_SSLCERT, WxPayConfig :: SSLCERT_PATH);
    curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
    curl_setopt($ch, CURLOPT_SSLKEY, WxPayConfig :: SSLKEY_PATH);
}
curl_setopt($ch, CURLOPT_POST, TRUE);
curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
$data = curl_exec($ch);
if($data){
    curl_close($ch);
    return $data;
}
}
private function ToUrlParams($post){
$buff = "";
foreach ($post as $k => $v){
    if($k != "sign" && $v != "" && !is_array($v)){
        $buff .= $k . "=" . $v . "&";
    }
}
$buff = trim($buff, "&");
return $buff;
}
private function ToXml($post){
$xml = "<xml>";
foreach ($post as $key => $val){
    if (is_numeric($val)){
        $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
    }else{
        $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
    }
}
$xml .= "</xml>";
return $xml;
}
private function checkWechatTran($setting, $transid){
$wechat = $setting['payment']['wechat'];
$sql = 'SELECT `key`,`secret` FROM ' . tablename('account_wechats') . ' WHERE `acid`=:acid';
$row = pdo_fetch($sql, array(':acid' => $wechat['account']));
$wechat['appid'] = $row['key'];
$url = "https://api.mch.weixin.qq.com/pay/orderquery";
$random = random(8);
$post = array('appid' => $wechat['appid'], 'transaction_id' => $transid, 'nonce_str' => $random, 'mch_id' => $wechat['mchid'],);
ksort($post);
$string = $this -> ToUrlParams($post);
$string .= "&key={$wechat['signkey']}";
$sign = md5($string);
$post['sign'] = strtoupper($sign);
$resp = $this -> postXmlCurl($this -> ToXml($post), $url);
libxml_disable_entity_loader(true);
$resp = json_decode(json_encode(simplexml_load_string($resp, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
if ($resp['result_code'] != 'SUCCESS' || $resp['return_code'] != 'SUCCESS'){
    exit('fail');
}
if ($resp['trade_state'] == 'SUCCESS') return array('code' => 1, 'fee' => $resp['total_fee'] / 100);
}
private function sendRedPacket($news, $oid = 0, $lid = 0){
global $_W, $_GPC;
$url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
load() -> func('communication');
$pars = array();
$cfg = $this -> module['config'];
$api = $cfg['api'];
$activity = $cfg['redpacket'];
$pars['nonce_str'] = random(32);
$pars['mch_billno'] = $api['mchid'] . date('Ymd') . random(10, true);
$pars['mch_id'] = $api['mchid'];
$pars['wxappid'] = $api['appid'];
$pars['nick_name'] = $activity['provider'];
$pars['send_name'] = $activity['provider'];
$pars['re_openid'] = $news['openid'];
$pars['total_amount'] = intval($news['price'] * 100);
$pars['total_num'] = 1;
$pars['wishing'] = $activity['wish'];
$pars['client_ip'] = $api['ip'];
$pars['act_name'] = $activity['title'];
$pars['remark'] = $activity['remark'];
ksort($pars, SORT_STRING);
$string1 = '';
foreach($pars as $k => $v){
    $string1 .= "{$k}={$v}&";
}
$string1 .= "key={$api['password']}";
$pars['sign'] = strtoupper(md5($string1));
$xml = array2xml($pars);
$extras = array();
$extras['CURLOPT_CAINFO'] = OD_ROOT . '/' . md5("root{$_W['uniacid']}ca") . ".pem";
$extras['CURLOPT_SSLCERT'] = OD_ROOT . '/' . md5("apiclient_{$_W['uniacid']}cert") . ".pem";
$extras['CURLOPT_SSLKEY'] = OD_ROOT . '/' . md5("apiclient_{$_W['uniacid']}key") . ".pem";
$procResult = false;
$resp = ihttp_request($url, $xml, $extras);
if(is_error($resp)){
    $setting = $this -> module['config'];
    $setting['api']['error'] = $resp['message'];
    $this -> saveSettings($setting);
    $procResult = $resp['message'];
}else{
    $xml = '<?xml version="1.0" encoding="utf-8"?>' . $resp['content'];
    $dom = new DOMDocument();
    if($dom -> loadXML($xml)){
        $xpath = new DOMXPath($dom);
        $code = $xpath -> evaluate('string(//xml/return_code)');
        $ret = $xpath -> evaluate('string(//xml/result_code)');
        $send_listid = $xpath -> evaluate('string(//xml/send_listid)');
        if(strtolower($code) == 'success' && strtolower($ret) == 'success'){
            $procResult = true;
            $setting = $this -> module['config'];
            $setting['api']['error'] = '';
            $this -> saveSettings($setting);
            if ($oid) pdo_update($this -> modulename . "_order", array('transid' => $send_listid), array('id' => $oid));
            if ($lid) pdo_update($this -> modulename . "_with", array('transid' => $send_listid, 'status' => 1), array('id' => $lid));
        }else{
            $error = $xpath -> evaluate('string(//xml/err_code_des)');
            $setting = $this -> module['config'];
            $setting['api']['error'] = $error;
            $this -> saveSettings($setting);
            $procResult = $error;
        }
    }else{
        $procResult = 'error response';
    }
}
return $procResult;
}
public function doMobileList(){
WXLimit();
global $_W, $_GPC;
$cfg = $this -> module['config'];
$status = $_GPC['status'];
if ($cfg['checked'] && $status != 2) $condition .= " and checked=1 ";
$bl = pdo_fetchall('select openid from ' . tablename($this -> modulename . "_black") . " where weid='{$_W['uniacid']}'", array(), 'openid');
if ($bl){
    $str = implode("','", array_keys($bl));
    $condition .= " and openid not in ('{$str}')";
}
if ($status == 1){
    $condition .= "  and status=0 order by createtime desc ";
}elseif ($status == 2){
    $condition .= " and openid='{$_W['openid']}'  order by createtime desc ";
}else{
    $select = " ,(select count(1) from " . tablename($this -> modulename . "_order") . " where nid=n.id and checked=1) good ";
    $condition .= "  and status=0 order by good desc ";
}
$news = pdo_fetchall("select *{$select} from " . tablename($this -> modulename . '_news') . " n where weid='{$_W['uniacid']}' {$condition} limit 5");
foreach ($news as & $value){
    $orders = pdo_fetchall('select checked,price from ' . tablename($this -> modulename . '_order') . " where nid='{$value['id']}' and status=1");
    $good = 0;
    $fail = 0;
    $price = 0;
    foreach ($orders as $val){
        if ($val['checked'] == 1){
            $good++;
            $price += $val['price'];
        }elseif ($val['checked'] == -1) $fail++;
    }
    $value['all'] = $price;
    $value['good'] = $good;
    $value['fail'] = $fail;
    if ($value['opened'] && time() >= $value['createtime'] + $value['opendays'] * 24 * 3600){
        $value['open'] = 1;
    }
}
include $this -> template('list');
}
public function doMobileWith(){
WXLimit();
global $_W, $_GPC;
$cfg = $this -> module['config'];
if (empty($cfg['iswith'])){
    header('location:' . $this -> createMobileUrl('list'));
    exit;
}
load() -> model('mc');
$fans = mc_oauth_userinfo();
$bl = pdo_fetch('select openid from ' . tablename($this -> modulename . "_black") . " where openid='{$fans['openid']}'");
if ($bl){
    MSG('您已被拉黑，不能提现！', $this -> createMobileUrl('list'), 'e');
}
$nids = pdo_fetchall('select * from ' . tablename($this -> modulename . "_news") . " where openid='{$fans['openid']}'", array(), 'id');
$nids = implode(',', array_keys($nids));
if ($nids){
    $credit = pdo_fetchcolumn('select sum(price) from ' . tablename($this -> modulename . "_order") . " where nid in ({$nids}) and status=1 and checked=1 and wstatus=0");
}
if ($cfg['with_pay'] > 0){
    $wpay = $credit * $cfg['with_pay'] / 100;
}
$credit = number_format($credit, 2);
include $this -> template('with');
}
public function doMobileRecord(){
WXLimit();
global $_W, $_GPC;
$cfg = $this -> module['config'];
if (empty($cfg['iswith'])) exit;
load() -> model('mc');
$fans = mc_oauth_userinfo();
$bl = pdo_fetch('select openid from ' . tablename($this -> modulename . "_black") . " where openid='{$fans['openid']}'");
if ($bl){
    die(json_encode(array('code' => -1, 'msg' => '您已被拉黑，不能提现！')));
}
$nids = pdo_fetchall('select * from ' . tablename($this -> modulename . "_news") . " where openid='{$fans['openid']}'", array(), 'id');
$nids = implode(',', array_keys($nids));
if ($nids){
    $credit = pdo_fetchcolumn('select sum(price) from ' . tablename($this -> modulename . "_order") . " where nid in ({$nids}) and status=1 and checked=1 and wstatus=0");
}
$credit = number_format($credit, 2);
$logs = pdo_fetchall('select * from ' . tablename($this -> modulename . "_with") . " where openid='{$fans['openid']}'");
include $this -> template('record');
}
public function doMobileWithDraw(){
WXLimit();
global $_W, $_GPC;
$cfg = $this -> module['config'];
if (empty($cfg['iswith'])) exit;
load() -> model('mc');
$fans = mc_oauth_userinfo();
$bl = pdo_fetch('select openid from ' . tablename($this -> modulename . "_black") . " where openid='{$fans['openid']}'");
if ($bl){
    die(json_encode(array('code' => -1, 'msg' => '您已被拉黑，不能发布消息！')));
}
$nids = pdo_fetchall('select * from ' . tablename($this -> modulename . "_news") . " where openid='{$fans['openid']}'", array(), 'id');
$nids = implode(',', array_keys($nids));
if ($nids){
    $credit = pdo_fetchcolumn('select sum(price) from ' . tablename($this -> modulename . "_order") . " where nid in ({$nids}) and status=1 and checked=1 and wstatus=0");
}
if ($cfg['with_pay'] > 0){
    $credit = $credit * (1 - $cfg['with_pay'] / 100);
}
if ($credit < $cfg['min_limit'] || $credit < 1){
    die(json_encode(array('code' => -1, 'msg' => '可提现金额不足！')));
}
if ($cfg['max_day'] > 0){
    $count = pdo_fetchcolumn('select count(1) from ' . tablename($this -> modulename . "_with") . " where openid='{$fans['openid']}' and weid='{$_W['uniacid']}' and to_days(from_unixtime(createtime)) = to_days(now())");
    if ($count >= $cfg['max_day']){
        die(json_encode(array('code' => -1, 'msg' => "每天只可提现{$cfg['max_day']}次哦，明天再来吧，亲！")));
    }
}
if (pdo_insert($this -> modulename . "_with", array('openid' => $fans['openid'], 'nickname' => $fans['nickname'], 'avatar' => $fans['avatar'], 'createtime' => time(), 'price' => $credit, 'status' => 0, 'weid' => $_W['uniacid'])) === false){
    die(json_encode(array('code' => -1, 'msg' => '系统错误，请联系管理员')));
}else{
    $lid = pdo_insertid();
    pdo_query('update ' . tablename($this -> modulename . "_order") . " set wstatus=1 where nid in ({$nids})");
    if ($cfg['min_check'] <= 0 || $cfg['min_check'] >= $credit){
        $res = $this -> sendRedPacket(array('openid' => $fans['openid'], 'price' => $credit), 0, $lid);
        if ($res === true){
            $msg = '提现成功，请于微信聊天界面查收！';
        }else $msg = '"提现申请已提交，请耐心等候...';
    }else{
        $msg = "提现申请已提交，请耐心等候...";
    }
    die(json_encode(array('code' => 1, 'msg' => $msg)));
}
}
public function doMobileMore(){
WXLimit();
global $_W, $_GPC;
$cfg = $this -> module['config'];
$status = $_GPC['status'];
$pageNo = $_GPC['pageNo'];
if ($cfg['checked'] && $status != 2) $condition .= " and checked=1 ";
$bl = pdo_fetchall('select openid from ' . tablename($this -> modulename . "_black") . " where weid='{$_W['uniacid']}'", array(), 'openid');
if ($bl){
    $str = implode("','", array_keys($bl));
    $condition .= " and openid not in ('{$str}')";
}
if ($status == 1){
    $condition .= " and status=0 order by createtime desc ";
}elseif ($status == 2){
    $condition .= " and openid='{$_W['openid']}' order by createtime desc ";
}else{
    $select = " ,(select count(1) from " . tablename($this -> modulename . "_order") . " where nid=n.id and checked=1) good ";
    $condition .= " and status=0 order by good desc ";
}
$news = pdo_fetchall("select *{$select} from " . tablename($this -> modulename . '_news') . " n where weid='{$_W['uniacid']}' {$condition} limit " . (($pageNo-1) * 5) . ",5");
foreach ($news as & $value){
    $orders = pdo_fetchall('select checked,price from ' . tablename($this -> modulename . '_order') . " where nid='{$value['id']}' and status=1");
    $good = 0;
    $fail = 0;
    $price = 0;
    foreach ($orders as $val){
        if ($val['checked'] == 1){
            $good++;
            $price += $val['price'];
        }elseif ($val['checked'] == -1) $fail++;
    }
    $value['all'] = $price;
    $value['good'] = $good;
    $value['fail'] = $fail;
    $hideword = unserialize($value['hideword']);
    if($hideword['img'][0] && strstr($hideword['img'][0], $cfg['qiniuUrl']) && !strstr($hideword['img'][0], '.gif')){
        $value['img'] = "{$hideword['img'][0]}?imageMogr2/auto-orient/blur/50x50";
    }
}
die(json_encode($news));
}
public function doWebIndex(){
global $_W, $_GPC;
$pindex = max(1, intval($_GPC['page']));
$psize = 15;
if ($_GPC['openid']){
    $con .= " and openid='{$_GPC['openid']}'";
}else{
    $bl = pdo_fetchall('select openid from ' . tablename($this -> modulename . "_black") . " where weid='{$_W['uniacid']}'", array(), 'openid');
    if ($bl){
        $str = implode("','", array_keys($bl));
        $con = " and openid not in ('{$str}')";
    }
}
$news = pdo_fetchall('select * from ' . tablename($this -> modulename . '_news') . " where weid='{$_W['uniacid']}' {$con} order by createtime desc LIMIT " . ($pindex - 1) * $psize . ",{$psize}");
foreach ($news as & $value){
    $orders = pdo_fetchall('select checked,price from ' . tablename($this -> modulename . '_order') . " where nid='{$value['id']}' and status=1");
    $good = 0;
    $fail = 0;
    $price = 0;
    foreach ($orders as $val){
        if ($val['checked'] == 1){
            $good++;
            $price += $val['price'];
        }elseif ($val['checked'] == -1) $fail++;
    }
    $value['all'] = $price;
    $value['good'] = $good;
    $value['fail'] = $fail;
}
$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename($this -> modulename . "_news") . " where weid='{$_W['uniacid']}' {$con}");
$pager = pagination($total, $pindex, $psize);
include $this -> template('index');
}
public function doWebWith(){
global $_W, $_GPC;
$pindex = max(1, intval($_GPC['page']));
$psize = 15;
$list = pdo_fetchall('select * from ' . tablename($this -> modulename . "_with") . " where weid='{$_W['uniacid']}' order by status,createtime desc LIMIT " . ($pindex - 1) * $psize . ",{$psize}");
$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename($this -> modulename . "_with") . " where weid='{$_W['uniacid']}'");
$pager = pagination($total, $pindex, $psize);
include $this -> template('with');
}
public function doWebWCheck(){
global $_W, $_GPC;
$wid = $_GPC['wid'];
$with = pdo_fetch('select * from ' . tablename($this -> modulename . "_with") . " where id='{$wid}'");
if (empty($with)) message('该提现申请不存在！');
if ($with['status'] != 0) message('该提现申请已发放！');
$res = $this -> sendRedPacket($with, 0, $wid);
if ($res === true){
    message('提现发放成功！', $this -> createWebUrl('with'));
}else{
    message('提现发放失败！原因：' . $res);
}
}
public function doWebWDel(){
global $_W, $_GPC;
$wid = $_GPC['wid'];
$with = pdo_fetch('select id from ' . tablename($this -> modulename . "_with") . " where id='{$wid}'");
if (empty($with)) message('该提现申请不存在！');
pdo_delete($this -> modulename . "_with", array('id' => $wid));
message('提现发放成功！', $this -> createWebUrl('with'));
}
public function doWebBList(){
global $_W, $_GPC;
$list = pdo_fetchall('select * from ' . tablename($this -> modulename . "_black") . " where weid='{$_W['uniacid']}'");
foreach ($list as & $value){
    $news = pdo_fetchall('select price,id from ' . tablename($this -> modulename . "_news") . " where openid='{$value['openid']}'", array(), 'id');
    $value['num'] = count($news);
    $str = implode(',', array_keys($news));
    if ($str){
        $value['all'] = pdo_fetchcolumn('select sum(price) from ' . tablename($this -> modulename . "_order") . " where nid in ({$str}) and status=1 and checked=1");
    }
    $value['all'] = floatval($value['all']);
}
include $this -> template('blist');
}
public function doWebBlack(){
global $_W, $_GPC;
$nid = $_GPC['nid'];
if ($nid){
    $new = pdo_fetch('select * from ' . tablename($this -> modulename . "_news") . " where id='{$nid}'");
    pdo_insert($this -> modulename . "_black", array('openid' => $new['openid'], 'nickname' => $new['nickname'], 'avatar' => $new['avatar'], 'weid' => $_W['uniacid']));
    die('1');
}else{
    $openid = $_GPC['openid'];
    pdo_delete($this -> modulename . "_black", array('openid' => $openid));
    message('释放成功！', $this -> createWebUrl('blist'));
}
}
public function doWebCheck(){
global $_W, $_GPC;
$nid = $_GPC['nid'];
if (pdo_query('update ' . tablename($this -> modulename . "_news") . " set checked = !checked where id='{$nid}'") === false){
    die('0');
}
die('1');
}
public function doWebOrder(){
global $_W, $_GPC;
$nid = $_GPC['nid'];
$checked = $_GPC['checked'];
$time = $_GPC['time'];
$pindex = max(1, intval($_GPC['page']));
$psize = 15;
$condition = '';
if ($nid) $condition .= ' and nid=' . $nid;
if ($checked) $condition .= " and checked={$checked}";
if (!$time){
    $time = array('start' => date('Y-m-d', time()), 'end' => date('Y-m-d', time() + 24 * 3600));
}
$condition .= " and createtime <= " . (strtotime($time['end']) + 24 * 3600) . " and createtime >=" . (strtotime($time['start'])-24 * 3600);
pdo_query('delete from ' . tablename($this -> modulename . "_order") . " where status=0 and createtime <= " . (time()-3600));
$orders = pdo_fetchall('select * from ' . tablename($this -> modulename . "_order") . " where weid='{$_W['uniacid']}' {$condition} order by createtime desc LIMIT " . ($pindex - 1) * $psize . ",{$psize}");
$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename($this -> modulename . "_order") . " where weid='{$_W['uniacid']}' {$condition}");
$pager = pagination($total, $pindex, $psize);
$all = 0;
$input = 0;
$os = pdo_fetchall('select checked,price from ' . tablename($this -> modulename . "_order") . " where weid='{$_W['uniacid']}' and status=1 {$condition} order by createtime desc");
foreach ($os as $value){
    $all += $value['price'];
    if ($value['checked'] == -1){
        $input += $value['price'];
    }
}
include $this -> template('order');
}
public function doWebDel(){
global $_W, $_GPC;
$nid = $_GPC['nid'];
pdo_delete($this -> modulename . "_news", array('id' => $nid));
pdo_delete($this -> modulename . "_order", array('nid' => $nid));
message('删除成功!', $this -> createWebUrl('index'));
}
}