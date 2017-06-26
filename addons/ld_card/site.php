<?php
defined('IN_IA') or die('Access Denied');
define('PI', 3.1415926535898);
define('EARTH_RADIUS', 6378.137);
class Ld_cardModuleSite extends WeModuleSite
{
    public function doMobileIndex()
    {
        global $_W, $_GPC;
        mc_oauth_userinfo();
        $nickname = $_W['fans']['nickname'];
        $avatar = $_W['fans']['avatar'];
        $cateid = $_GPC['cateid'];
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $carousel = pdo_getall('ld_card_carousel', array('weid' => $_W['uniacid']));
        $op = $_GPC['op'];
        if (strpos($user_agent, 'MicroMessenger') === false) {
            message('本页面仅支持微信访问!非微信浏览器禁止浏览!', '', 'error');
        }
        if ($op == 'location') {
            $lat = $_GPC['lat'];
            $lng = $_GPC['lng'];
            cache_write('userlocation', array('lat' => $lat, 'lng' => $lng));
            $url = 'http://api.map.baidu.com/geoconv/v1/?coords=' . $lng . ',' . $lat . '&from=1&to=5&ak=SAWFFdzmw4fHr32dA7a1dL48';
            $res = httpGet($url);
            $res1 = json_decode($res, true);
            $data = array('status' => $res1['status'], 'lng' => $res1['result'][0]['x'], 'lat' => $res1['result'][0]['y']);
            $url2 = 'http://api.map.baidu.com/geocoder/v2/?ak=SAWFFdzmw4fHr32dA7a1dL48&location=' . $data['lat'] . ',' . $data['lng'] . '&output=json&pois=1';
            $result = httpGet($url2);
            $data['addr'] = json_decode($result, true)['result']['addressComponent']['street'];
            $j = GetDistance($data['lat'], $data['lng'], 100, 39);
            return json_encode($data);
        }
        $title = !empty($this->module['config']['title']) ? $this->module['config']['title'] : '超级卡券汇';
        $desc = !empty($this->module['config']['desc']) ? $this->module['config']['desc'] : '大家来领券，超级划算!';
        $img = tomedia($this->module['config']['erweima']);
        if (!empty($cateid)) {
            $cards = pdo_fetchall('select * from ' . tablename('ld_card_cards') . ' where weid=:weid and category=:category order by id desc', array(':weid' => $_W['uniacid'], ':category' => $cateid));
        } else {
            $cards = pdo_fetchall('select * from ' . tablename('ld_card_cards') . ' where weid=:weid order by id desc', array(':weid' => $_W['uniacid']));
        }
        $settings = $this->module['config'];
        $jscfg = $_W['account']['jssdkconfig'];
        $lists = pdo_fetchall('select * from ' . tablename('ld_card_category') . ' where uniacid=:acid order by displayorder asc', array(':acid' => $_W['uniacid']));
        foreach ($cards as &$cardtemp) {
            $card_info = getcard($cardtemp['card_id']);
            $shop = pdo_fetch('select * from ' . tablename('ld_card_users') . ' where id=:id', array(':id' => $cardtemp['userid']));
            $cardtemp['card_type'] = $card_info['card_type'];
            $cardtemp['quantity'] = $card_info['quantity'];
            $cardtemp['total_quantity'] = $card_info['total_quantity'];
            $cardtemp['logo_url'] = tomedia($shop['logo']);
            $cardtemp['shopname'] = substr($shop['shopname'], -21);
            $minhb = $cardtemp['minhb'] / 100;
            $maxhb = $cardtemp['maxhb'] / 100;
            $cardtemp['hb'] = $minhb . '~~' . $maxhb . '/' . $cardtemp['hbnum'];
            $cardtemp['color'] = !empty($card_info['color']) ? $card_info['color'] : '#00cc66';
        }
        $sql = 'select * from ' . tablename('ld_card_users') . ' WHERE status=1 and weid=:weid;';
        $shops = pdo_fetchall($sql, array(':weid' => $_W['uniacid']));
        include $this->template('index');
    }
    public function doMobileShopsearch()
    {
        global $_W, $_GPC;
        mc_oauth_userinfo();
        $nickname = $_W['fans']['nickname'];
        $avatar = $_W['fans']['avatar'];
        $cateid = $_GPC['cateid'];
        $key = trim($_GPC['key']);
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $carousel = pdo_getall('ld_card_carousel', array('weid' => $_W['uniacid']));
        if (strpos($user_agent, 'MicroMessenger') === false) {
            message('本页面仅支持微信访问!非微信浏览器禁止浏览!', '', 'error');
        }
        $title = !empty($this->module['config']['title']) ? $this->module['config']['title'] : '超级卡券汇';
        $desc = !empty($this->module['config']['desc']) ? $this->module['config']['desc'] : '大家来领券，超级划算!';
        $img = tomedia($this->module['config']['erweima']);
        $sql = 'select * from ' . tablename('ld_card_users') . ' WHERE status=1 and weid =:weid and shopname LIKE :key;';
        $shops = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':key' => '%' . $key . '%'));
        include $this->template('shopsearch');
    }
    public function doMobileGetlocation()
    {
        global $_W, $_GPC;
        $lat = $_GPC['lat'];
        $lng = $_GPC['lng'];
        $url = 'http://api.map.baidu.com/geoconv/v1/?coords=' . $lng . ',' . $lat . '&from=1&to=5&ak=SAWFFdzmw4fHr32dA7a1dL48';
        $res = httpGet($url);
        $res1 = json_decode($res, true);
        $data = array('status' => $res1['status'], 'lng' => $res1['result'][0]['x'], 'lat' => $res1['result'][0]['y']);
        $url2 = 'http://api.map.baidu.com/geocoder/v2/?ak=SAWFFdzmw4fHr32dA7a1dL48&location=' . $data['lat'] . ',' . $data['lng'] . '&output=json&pois=1';
        $result = httpGet($url2);
        $data['addr'] = json_decode($result, true)['result']['addressComponent']['street'];
        $j = GetDistance($data['lat'], $data['lng'], 100, 39);
        return json_encode($data);
    }
    public function doMobileRanking()
    {
        global $_W, $_GPC;
        $settings = $this->module['config'];
        $avatar = $_W['fans']['avatar'];
        $title = !empty($this->module['config']['title']) ? $this->module['config']['title'] : '超级卡券汇';
        $desc = !empty($this->module['config']['desc']) ? $this->module['config']['desc'] : '大家来领券，超级划算!';
        $img = tomedia($this->module['config']['erweima']);
        $hbopenids = pdo_fetchall('select distinct hbopenid from ' . tablename('ld_card_log') . ' where sendhb>0');
        $hbcard = pdo_fetchall('select cardcode from ' . tablename('ld_card_log') . ' where sendhb>0');
        if (empty($hbopenids) || empty($hbcard)) {
            message('目前还没有排行榜', $this->createmobileurl('index'), 'info');
        }
        foreach ($hbopenids as &$temp) {
            $temp['name'] = mc_fansinfo($temp['hbopenid'])['nickname'];
            $temp['avatar'] = mc_fansinfo($temp['hbopenid'])['avatar'];
            $temp['hb'] = 0;
            $hbopenid = pdo_fetchall('select * from ' . tablename('ld_card_log') . ' where hbopenid=:hbopenid', array(':hbopenid' => $temp['hbopenid']));
            for ($i = 0; $i <= count($hbopenid); $i++) {
                $temp['hb'] += $hbopenid[$i]['sendhb'] / 100;
            }
        }
        $hbopenids = array_slice(my_sort($hbopenids, 'hb', SORT_DESC, SORT_NUMERIC), 0, $settings['ranknum']);
        $ranking = array();
        foreach ($hbopenids as $key => $value) {
            $ranking[$key] = $value['name'];
        }
        foreach ($ranking as $k => $v) {
            if ($v === $_W['fans']['nickname']) {
                $num = $k + 1;
            }
        }
        include $this->template('ranking');
    }
    public function doMobileMy()
    {
        global $_W, $_GPC;
        $nickname = $_W['fans']['nickname'];
        $avatar = $_W['fans']['avatar'];
        $openid = $_W['openid'];
        $userid = $_GPC['userid'];
        $shop = pdo_get('ld_card_users', array('id' => $userid));
        $cards = pdo_getall('ld_card_cards', array('userid' => $userid));
        $nums = count($cards);
        include $this->template('my');
    }
    public function doMobileMyShops()
    {
        global $_W, $_GPC;
        mc_oauth_userinfo();
        $nickname = $_W['fans']['nickname'];
        $avatar = $_W['fans']['avatar'];
        $openid = $_W['openid'];
        $shops = pdo_getall('ld_card_users', array('openid' => $openid));
        if (empty($shops)) {
            message('没有找到您的任何店铺', '', 'warning');
        }
        foreach ($shops as &$value) {
            $value['cardnum'] = count(pdo_getall('ld_card_cards', array('userid' => $value['id'])));
        }
        include $this->template('myshops');
    }
    public function doMobileDetails()
    {
        global $_W, $_GPC;
        $cardid = $_GPC['cardid'];
        $userid = $_GPC['userid'];
        $shop = pdo_get('ld_card_users', array('id' => $userid));
        $cardinfo = pdo_get('ld_card_cards', array('id' => $cardid));
        $p = isset($_GPC['page']) ? $_GPC['page'] : 1;
        $pindex = max(1, intval($_GPC['page']));
        $psize = 20;
        $temp = pdo_fetchall('select count(\'sendhb\') from ' . tablename('ld_card_log') . ' where card_id=:card_id', array(':card_id' => $cardinfo['card_id']));
        $total = $temp['0']['count(\'sendhb\')'];
        print_r($temp);
        $p_total = count(pdo_getall('ld_card_log', array('card_id' => $cardinfo['card_id'])));
        $pager = pagination($p_total, $pindex, $psize);
        $logs = pdo_fetchall('select * from ' . tablename('ld_card_log') . ' where card_id=:card_id order by id desc LIMIT ' . ($pindex - 1) * $psize . ",{$psize}", array(':card_id' => $cardinfo['card_id']));
        foreach ($logs as &$value) {
            $value['user'] = mc_fansinfo($value['card_user'])['nickname'];
            $value['friend'] = mc_fansinfo($value['friend'])['nickname'];
        }
        include $this->template('details');
    }
    public function doMobileDyindex()
    {
        global $_W, $_GPC;
        mc_oauth_userinfo();
        $title = !empty($this->module['config']['title']) ? $this->module['config']['title'] : '超级卡券汇';
        $desc = !empty($this->module['config']['desc']) ? $this->module['config']['desc'] : '大家来领券，超级划算!';
        $img = tomedia($this->module['config']['erweima']);
        $cards = pdo_fetchall('select * from ' . tablename('ld_card_cards') . ' where weid=:weid order by id desc', array(':weid' => $_W['uniacid']));
        $settings = $this->module['config'];
        $jscfg = $_W['account']['jssdkconfig'];
        foreach ($cards as &$temp) {
            $card_info = getcard($temp['card_id']);
            $shop = pdo_fetch('select * from ' . tablename('ld_card_users') . ' where id=:id', array(':id' => $temp['userid']));
            $temp['card_type'] = $card_info['card_type'];
            $temp['quantity'] = $card_info['quantity'];
            $temp['total_quantity'] = $card_info['total_quantity'];
            $temp['logo_url'] = tomedia($shop['logo']);
            $temp['shopname'] = substr($shop['shopname'], -21);
            $minhb = $temp['minhb'] / 100;
            $maxhb = $temp['maxhb'] / 100;
            $temp['hb'] = $minhb . '~~' . $maxhb . '/' . $temp['hbnum'];
        }
        include $this->template('dyindex');
    }
    public function doMobileShop()
    {
        global $_W, $_GPC;
        $settings = $this->module['config'];
        $userid = $_GPC['userid'];
        mc_oauth_userinfo();
        $title = pdo_fetchcolumn('select shopname from ' . tablename('ld_card_users') . ' where id=:userid', array(':userid' => $userid));
        $desc = !empty($this->module['config']['desc']) ? $this->module['config']['desc'] : '大家来领券，超级划算!';
        $img = tomedia($this->module['config']['erweima']);
        $cards = pdo_fetchall('select * from ' . tablename('ld_card_cards') . ' where userid=:userid order by id desc', array(':userid' => $userid));
        $settings = $this->module['config'];
        foreach ($cards as &$temp) {
            $card_info = getcard($temp['card_id']);
            $shop = pdo_fetch('select * from ' . tablename('ld_card_users') . ' where id=:id', array(':id' => $temp['userid']));
            $temp['card_type'] = $card_info['card_type'];
            $temp['quantity'] = $card_info['quantity'];
            $temp['total_quantity'] = $card_info['total_quantity'];
            $temp['logo_url'] = tomedia($shop['logo']);
            $temp['shopname'] = substr($shop['shopname'], -21);
            $minhb = $temp['minhb'] / 100;
            $maxhb = $temp['maxhb'] / 100;
            $temp['hb'] = $minhb . '~~' . $maxhb . '/' . $temp['hbnum'];
            $temp['color'] = $card_info['color'];
        }
        include $this->template('shop');
    }
    public function doMobileMycards()
    {
    }
    public function doWebindex()
    {
        global $_W, $_GPC;
        $carousel = pdo_getall('ld_card_carousel', array('weid' => $_W['uniacid']));
        include $this->template('index');
    }
    public function doWebAddcarousel()
    {
        global $_W, $_GPC;
        $data = array('title' => $_GPC['title'], 'img' => $_GPC['img'], 'href' => $_GPC['href'], 'weid' => $_W['uniacid']);
        if ($data) {
            $res = pdo_insert('ld_card_carousel', $data);
            if ($res) {
                return 'success';
            } else {
                return $res;
            }
        }
    }
    public function dowebeditcarousel()
    {
        global $_W, $_GPC;
        $id = $_GPC['id'];
        $carousel = pdo_get('ld_card_carousel', array('id' => $id));
        if (checksubmit()) {
            $data = array('title' => $_GPC['title'], 'img' => $_GPC['img'], 'href' => $_GPC['href']);
            $res = pdo_update('ld_card_carousel', $data, array('id' => $id));
            if ($res) {
                message('修改成功', $this->createweburl('index'), 'success');
            }
        }
        include $this->template('editcarousel');
    }
    public function dowebdelcarousel()
    {
        global $_W, $_GPC;
        $id = $_GPC['id'];
        $res = pdo_delete('ld_card_carousel', array('id' => $id));
        if ($res) {
            message('删除成功！', 'referer', 'success');
        }
    }
    public function doWebGategory()
    {
        global $_W, $_GPC;
        $op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';
        if ($op == 'list') {
            $condition = ' uniacid = :aid';
            $params[':aid'] = $_W['uniacid'];
            $pindex = max(1, intval($_GPC['page']));
            $psize = 20;
            $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('ld_card_category') . ' WHERE ' . $condition, $params);
            $lists = pdo_fetchall('SELECT * FROM ' . tablename('ld_card_category') . ' WHERE ' . $condition . ' ORDER BY displayorder DESC,id ASC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, $params, 'id');
            foreach ($lists as &$temp) {
                $temp['num'] = pdo_fetchcolumn('select count(*) from ' . tablename('ld_card_cards') . ' where category=:id', array(':id' => $temp['id']));
            }
            $pager = pagination($total, $pindex, $psize);
            if (checksubmit('submit')) {
                if (!empty($_GPC['ids'])) {
                    foreach ($_GPC['ids'] as $k => $v) {
                        $data = array('title' => trim($_GPC['title'][$k]), 'displayorder' => intval($_GPC['displayorder'][$k]));
                        pdo_update('ld_card_category', $data, array('uniacid' => $_W['uniacid'], 'id' => intval($v)));
                    }
                    message('编辑成功', $this->createWebUrl('gategory'), 'success');
                }
            }
        }
        if ($op == 'post') {
            if (checksubmit('submit')) {
                if (!empty($_GPC['title'])) {
                    foreach ($_GPC['title'] as $k => $v) {
                        $v = trim($v);
                        if (empty($v)) {
                            continue;
                        }
                        $data = array('uniacid' => $_W['uniacid'], 'title' => $v, 'thumb' => trim($_GPC['thumb'][$k]), 'link' => trim($_GPC['link'][$k]), 'displayorder' => intval($_GPC['displayorder'][$k]));
                        print_r($data);
                        pdo_insert('ld_card_category', $data);
                    }
                }
                message('添加卡券分类成功', $this->createWebUrl('gategory'), 'success');
            }
        }
        if ($op == 'del') {
            $id = intval($_GPC['id']);
            $num = pdo_fetchcolumn('select count(*) from ' . tablename('ld_card_cards') . ' where category=:id', array(':id' => $id));
            if ($num > 0) {
                message('此分类下还有' . $num . '张卡券，请删除卡券后，再删除分类！', $this->createWebUrl('gategory'), 'error');
            } else {
                pdo_delete('ld_card_category', array('uniacid' => $_W['uniacid'], 'id' => $id));
                message('删除门店分类成功', $this->createWebUrl('gategory'), 'success');
            }
        }
        if ($op == 'edit') {
            $id = intval($_GPC['id']);
            $category = pdo_get('ld_card_category', array('uniacid' => $_W['uniacid'], 'id' => $id));
            if (checksubmit('submit')) {
                $title = trim($_GPC['title']) ? trim($_GPC['title']) : message('分类名称不能为空');
                $update = array('title' => $title, 'thumb' => trim($_GPC['thumb']), 'link' => trim($_GPC['link']), 'displayorder' => intval($_GPC['displayorder']));
                pdo_update('ld_card_category', $update, array('uniacid' => $_W['uniacid'], 'id' => $id));
                message('编辑门店分类成功', $this->createWebUrl('gategory'), 'success');
            }
        }
        include $this->template('gategory');
    }
    public function doMobileSendcard()
    {
        global $_W, $_GPC;
        $openid = $_W['fans']['openid'];
        $cardid = $_GPC['card_id'];
        $resjson = respcard($openid, $cardid);
        $res = json_decode($resjson);
        $code = strval($res->errcode);
        $msg = $res->errmsg;
        if ($code == 0 && $msg == 'ok') {
            return '领取成功';
        } else {
            return '领取失败，请稍后再试！';
        }
    }
    public function doMobileAddcard()
    {
        global $_W, $_GPC;
        $settings = $this->module['config'];
        $card_id = $_GPC['cardid'];
        $openid = $_W['fans']['openid'];
        $cardext = getCardTicket($card_id, $openid);
        return json_encode($cardext);
    }
    public function doWebUsers()
    {
        global $_W, $_GPC;
        $users = pdo_fetchall('select * from ' . tablename('ld_card_users') . ' where weid=:weid order by id desc', array(':weid' => $_W['uniacid']));
        include MODULE_ROOT . '/phpqrcode.php';
        foreach ($users as &$list) {
            $shopurl = $_W['siteroot'] . substr($this->createmobileurl('shop', array('userid' => $list['id'])), 2);
            $list['url'] = $shopurl;
            QRcode::png($shopurl, $list['id'] . '.png', 'L', 6, 2);
            $qr = 'qr.png';
            imagepng($qr, $list['id'] . '.png');
            $list['qrcode'] = $list['id'] . '.png';
        }
        include $this->template('users');
    }
    public function doWebCards()
    {
        global $_W, $_GPC;
        $userid = $_GPC['userid'];
        $list = pdo_fetchall('select * from ' . tablename('ld_card_category') . ' where uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
        if (checksubmit()) {
            $card_id = $_GPC['card_id'];
            $cardinfo = getcard($card_id);
            if ($cardinfo) {
                $card_type = strtolower($cardinfo['card']['card_type']);
                $type = cardtype($card_type);
                $carddata = array('title' => $cardinfo['title'], 'card_id' => $cardinfo['id'], 'userid' => $_GPC['userid'], 'minhb' => $_GPC['minhb'] * 100, 'maxhb' => $_GPC['maxhb'] * 100, 'weid' => $_W['uniacid'], 'sign' => $_GPC['sign'], 'hbnum' => $_GPC['hbnum'], 'category' => $_GPC['category']);
                $jx = pdo_fetch('select * from ' . tablename('ld_card_cards') . ' where card_id=:card_id', array(':card_id' => $card_id));
                if ($jx) {
                    pdo_update('ld_card_cards', $carddata, array('id' => $jx['id']));
                    message('更新卡券成功', 'referer', 'success');
                } else {
                    $res = pdo_insert('ld_card_cards', $carddata);
                    if (!empty($res)) {
                        message('添加卡券成功', 'referer', 'success');
                    }
                }
            }
        }
        $cards = pdo_fetchall('select * from ' . tablename('ld_card_cards') . ' where userid=:userid order by id desc', array(':userid' => $_GPC['userid']));
        $shopname = pdo_fetchcolumn('select shopname from ' . tablename('ld_card_users') . ' where id=:id', array(':id' => $_GPC['userid']));
        foreach ($cards as &$temp) {
            $card_info = getcard($temp['card_id']);
            $count = pdo_fetchall('select sendhb from ' . tablename('ld_card_log') . ' where card_id = :card_id', array(':card_id' => $temp['card_id']));
            $category = pdo_fetchcolumn('select title from ' . tablename('ld_card_category') . ' where id=:id', array(':id' => $temp['category']));
            $temp['category'] = $category;
            $sum = 0;
            for ($i = 0; $i <= count($count); $i++) {
                $sum += $count[$i]['sendhb'];
            }
            $temp['sendsum'] = $sum / 100;
            $temp['card_type'] = $card_info['card_type'];
            $temp['quantity'] = $card_info['quantity'];
            $temp['total_quantity'] = $card_info['total_quantity'];
            $minhb = $temp['minhb'] / 100;
            $maxhb = $temp['maxhb'] / 100;
            $temp['hb'] = $minhb . '~' . $maxhb;
        }
        include $this->template('cards');
    }
    public function doWebDelcards()
    {
        global $_W, $_GPC;
        $id = $_GPC['id'];
        $cardid = pdo_fetchcolumn('select card_id from ' . tablename('ld_card_cards') . ' where id=:id', array(':id' => $id));
        $cardlogs = pdo_fetchall('select * from ' . tablename('ld_card_log') . ' where card_id=:card_id', array(':card_id' => $cardid));
        $res = pdo_delete('ld_card_cards', array('id' => $id));
        $coumn = pdo_delete('ld_card_log', array('card_id' => $cardid));
        message('删除成功，' . $res . '张卡券，' . $coumn . '条记录', 'referer', 'success');
    }
    public function doWebDetails()
    {
        global $_W, $_GPC;
        $card_id = $_GPC['cardid'];
        $userid = $_GPC['userid'];
        $p = isset($_GPC['page']) ? $_GPC['page'] : 1;
        $pindex = max(1, intval($p));
        $psize = 50;
        $_total = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('ld_card_log') . ' where card_id=:card_id', array(':card_id' => $card_id));
        $pager = pagination($_total, $pindex, $psize);
        $logs = pdo_fetchall('select * from ' . tablename('ld_card_log') . ' where card_id=:card_id order by id desc LIMIT ' . ($pindex - 1) * $psize . ",{$psize}", array(':card_id' => $card_id));
        foreach ($logs as &$temp) {
            $carduser = $temp['card_user'];
            $hbuser = $temp['hbopenid'];
            $frd = $temp['friend'];
            $name = pdo_fetchcolumn('select nickname from ' . tablename('mc_mapping_fans') . ' where openid=:openid', array(':openid' => $carduser));
            $hbname = pdo_fetchcolumn('select nickname from ' . tablename('mc_mapping_fans') . ' where openid=:openid', array(':openid' => $hbuser));
            $friend = pdo_fetchcolumn('select nickname from ' . tablename('mc_mapping_fans') . ' where openid=:openid', array(':openid' => $frd));
            $temp['card_user'] = $name;
            $temp['hbopenid'] = $hbname;
            $temp['friend'] = $friend;
        }
        include $this->template('details');
    }
    public function doWebAdduser()
    {
        global $_W, $_GPC;
        $op = $_GPC['op'];
        $id = $_GPC['id'];
        if ($op == 'edit') {
            $users = pdo_get('ld_card_users', array('id' => $id));
            if (checksubmit()) {
                $usersdata = array('shopname' => $_GPC['shopname'], 'username' => $_GPC['username'], 'tel' => $_GPC['tel'], 'add' => $_GPC['add'], 'weid' => $_W['uniacid'], 'openid' => $_GPC['openid'], 'logo' => $_GPC['logo'], 'yyzz' => json_encode($_GPC['yyzz']), 'lng' => $_GPC['lng'], 'lat' => $_GPC['lat']);
                $res = pdo_update('ld_card_users', $usersdata, array('id' => $id));
                if (!empty($res)) {
                    message('更新商户成功！', 'referer', 'success');
                }
            }
        }
        if (checksubmit()) {
            $usersdata = array('shopname' => $_GPC['shopname'], 'username' => $_GPC['username'], 'tel' => $_GPC['tel'], 'add' => $_GPC['add'], 'weid' => $_W['uniacid'], 'openid' => $_GPC['openid'], 'logo' => $_GPC['logo'], 'lng' => $_GPC['lng'], 'lat' => $_GPC['lat']);
            $jx = pdo_fetch('select * from ' . tablename('ld_card_users') . ' where openid=:openid', array(':openid' => $_GPC['openid']));
            if ($jx) {
                $res = pdo_update('ld_card_users', $usersdata, array('id' => $jx['id']));
                if (!empty($res)) {
                    message('更新商户成功！', 'referer', 'success');
                }
            } else {
                $res = pdo_insert('ld_card_users', $usersdata);
                if (!empty($res)) {
                    message('添加商户成功！', 'referer', 'success');
                }
            }
        }
        include $this->template('adduser');
    }
    public function doWebDeluser()
    {
        global $_W, $_GPC;
        $userid = $_GPC['id'];
        $logs = pdo_delete('ld_card_log', array('userid' => $userid));
        $cards = pdo_delete('ld_card_cards', array('userid' => $userid));
        $users = pdo_delete('ld_card_users', array('id' => $userid));
        message('成功删除了' . $users . '个商户，' . $cards . '张卡券，' . $logs . '条记录', 'referer', 'success');
    }
}
function cardtype($type)
{
    $types = array('GROUPON' => '团购券', 'DISCOUNT' => '折扣券', 'GIFT' => '礼品券', 'CASH' => '代金券', 'GENERAL_COUPON' => '通用券', 'MEMBER_CARD' => '会员卡', 'SCENIC_TICKET' => '景点门票', 'MOVIE_TICKET' => '电影票', 'BOARDING_PASS' => '飞机票', 'MEETING_TICKET' => '会议门票', 'BUS_TICKET' => '汽车票');
    return $types[$type];
}
function getcard($cardid)
{
    load()->classs('weixin.account');
    $accObj = WeixinAccount::create($_W['uniacid']);
    $access_token = $accObj->fetch_token();
    $url = 'https://api.weixin.qq.com/card/get?access_token=' . $access_token;
    $info = '{
		"card_id":"' . $cardid . '"
	}';
    $cardinfo = json_decode(https_post($url, $info), true);
    $card_type = strtolower($cardinfo['card']['card_type']);
    $temp['card_type'] = cardtype($cardinfo['card']['card_type']);
    $temp['quantity'] = $cardinfo['card'][$card_type]['base_info']['sku']['quantity'];
    $temp['total_quantity'] = $cardinfo['card'][$card_type]['base_info']['sku']['total_quantity'];
    $temp['title'] = $cardinfo['card'][$card_type]['base_info']['title'];
    $temp['id'] = $cardinfo['card'][$card_type]['base_info']['id'];
    $len = strlen($cardinfo['card'][$card_type]['base_info']['logo_url']) - 1;
    $temp['logo_url'] = $cardinfo['card'][$card_type]['base_info']['logo_url'];
    $temp['color'] = $cardinfo['card'][$card_type]['base_info']['color'];
    return $temp;
}
function https_post($url, $data)
{
    load()->func('communication');
    $result = ihttp_post($url, $data);
    return $result['content'];
}
function getsign($card)
{
    sort($card, SORT_STRING);
    $sign = sha1(implode($card));
    if (!$sign) {
        return false;
    }
    return $sign;
}
function getjsticket()
{
    global $_W, $_GPC;
    load()->classs('account');
    $account_api = WeAccount::create();
    $ACC_TOKEN = $account_api->getAccessToken();
    $url = file_get_contents('https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=' . $ACC_TOKEN . '&type=jsapi');
    $ticket_json = json_decode($url);
    $ticket = $ticket_json->ticket;
    return $ticket;
}
function respcard($touser, $cardid)
{
    global $_W;
    $account_api = WeAccount::create();
    $ACC_TOKEN = $account_api->getAccessToken();
    $ticket_url = file_get_contents('https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=' . $ACC_TOKEN . '&type=wx_card');
    $ticket_json = json_decode($ticket_url);
    $ticket = $ticket_json->ticket;
    $code = '';
    $nonce_str = getRandom(32);
    $sort = array($code, time(), $cardid, $ticket, $nonce_str);
    $signature = sha1(sort($sort));
    $card_arr = array('code' => '', 'openid' => $touser, 'timestamp' => time(), 'nonce_str' => getRandom(32), 'signature' => $signature);
    $card_ext = urldecode(json_encode($card_arr));
    $carddata = '{
		  "touser":"' . $touser . '", 
		  "msgtype":"wxcard",
		  "wxcard":{              
				   "card_id":"' . $cardid . '",
				   "card_ext": "' . $card_ext . '"            
					}
		}';
    $url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=' . $ACC_TOKEN;
    $result = https_post($url, $carddata);
    return $result;
}
function getRandom($param)
{
    $str = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $key = '';
    for ($i = 0; $i < $param; $i++) {
        $key .= $str[mt_rand(0, 32)];
    }
    return $key;
}
function getCardTicket($card_id, $openid)
{
    global $_W, $_GPC;
    $data = pdo_fetch(' SELECT * FROM ' . tablename('ld_card_cardticket') . ' WHERE weid=\'' . $_W['uniacid'] . '\' ');
    $appid = $_W['account']['key'];
    $appSecret = $_W['account']['secret'];
    load()->func('communication');
    if ($data['createtime'] < time()) {
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $appSecret . '';
        $res = json_decode(httpGet($url));
        $tokens = $res->access_token;
        if (empty($tokens)) {
            return;
        }
        $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=' . $tokens . '&type=wx_card';
        $res = json_decode(httpGet($url));
        $now = TIMESTAMP;
        $now = intval($now) + 7200;
        $ticket = $res->ticket;
        $insert = array('weid' => $_W['uniacid'], 'createtime' => $now, 'ticket' => $ticket);
        if (empty($data)) {
            pdo_insert('ld_card_cardticket', $insert);
        } else {
            pdo_update('ld_card_cardticket', $insert, array('id' => $data['id']));
        }
    } else {
        $ticket = $data['ticket'];
    }
    $protocol = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443 ? 'https://' : 'http://';
    $url = "{$protocol}{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
    $now = time();
    $timestamp = $now;
    $nonceStr = getRandom(16);
    $card_id = $card_id;
    $openid = $openid;
    $string = "card_id={$card_id}&jsapi_ticket={$ticket}&noncestr={$nonceStr}{$openid}={$openid}&timestamp={$timestamp}&url={$url}";
    $arr = array($card_id, $ticket, $nonceStr, $openid, $timestamp);
    asort($arr, SORT_STRING);
    $sortString = '';
    foreach ($arr as $temp) {
        $sortString = $sortString . $temp;
    }
    $signature = sha1($sortString);
    $cardArry = array('code' => '', 'openid' => $openid, 'timestamp' => $now, 'signature' => $signature, 'cardId' => $card_id, 'url' => $url, 'ticket' => $ticket, 'nonceStr' => $nonceStr);
    return $cardArry;
}
function httpGet($url)
{
    load()->func('communication');
    $result = ihttp_get($url);
    return $result['content'];
}
function my_sort($arrays, $sort_key, $sort_order = SORT_ASC, $sort_type = SORT_NUMERIC)
{
    if (is_array($arrays)) {
        foreach ($arrays as $array) {
            if (is_array($array)) {
                $key_arrays[] = $array[$sort_key];
            } else {
                return false;
            }
        }
    } else {
        return false;
    }
    array_multisort($key_arrays, $sort_order, $sort_type, $arrays);
    return $arrays;
}
function GetDistance($lat1, $lng1, $lat2, $lng2)
{
    $radLat1 = $lat1 * (PI / 180);
    $radLat2 = $lat2 * (PI / 180);
    $a = $radLat1 - $radLat2;
    $b = $lng1 * (PI / 180) - $lng2 * (PI / 180);
    $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2)));
    $s = $s * EARTH_RADIUS;
    $s = round($s * 10000) / 10000;
    return $s;
}