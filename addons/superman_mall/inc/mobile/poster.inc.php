<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doMobilePoster extends Superman {
	public function __construct() {
		parent::__construct();
        parent::init();

		$this->exec();
	}
    public function exec() {
        global $_W, $_GPC, $do;
        $do = $do ? $do : 'poster';
        $act = in_array($_GPC['act'], array('display', 'partner'))?$_GPC['act']:'display';
        if ($act == 'display') {
            //do nothing
        } else if ($act == 'partner') {
            $keyword = isset($_GPC['keyword']) && $_GPC['keyword'] == 1?1:0;
            if ($keyword) {
                $member = mc_fetch($_GPC['openid']);
                $uid = mc_openid2uid($_GPC['openid']);
                if ($uid <= 0) {
                    WeUtility::logging('warning', '[poster:keyword], uid is null, $_GPC[openid]='.$_GPC['openid']);
                    return '';
                }
            } else {
                $this->checkauth();
                $member = $_W['member'];
                $uid = $_W['member']['uid'];
            }
            $id = intval($_GPC['id']);
            if (empty($id)) {
                $item = M::t('superman_mall_partner_poster')->fetch(array(
                    'uniacid' => $_W['uniacid'],
                ), 'ORDER BY isdefault DESC, id DESC');
                $id = $item['id'];
            } else {
                $item = M::t('superman_mall_partner_poster')->fetch($id);
            }
            if (empty($item)) {
                $this->json(ERRNO::INVALID_REQUEST);
            }

            $filter = array(
                'uniacid' => $_W['uniacid'],
                'uid' => $uid,
                'tid' => $id,
                'type' => 'partner_poster',
            );
            $member_poster = M::t('superman_mall_member_poster')->fetch($filter);
            if ($member_poster) {
                $poster_path = $member_poster['path'];
            } else {
                $poster_path = SupermanUtil::get_parter_poster_path($uid, $id);
                $member_poster = array(
                    'uniacid' => $_W['uniacid'],
                    'uid' => $uid,
                    'tid' => $id,
                    'type' => 'partner_poster',
                    'path' => $poster_path,
                    'media_id' => '',
                    'dateline' => TIMESTAMP,
                );
                M::t('superman_mall_member_poster')->insert($member_poster);
            }

            //检查是否已存在海报
            if (file_exists(MODULE_ROOT.'/'.$poster_path)) {
                if (!$keyword) {
                    $this->_output($poster_path);
                }
                if ($member_poster['media_id'] == '' || ($member_poster['media_id'] != '' && $member_poster['dateline'] + 259200 < TIMESTAMP)) {
                    $result = $this->_uploadMedia(MODULE_ROOT.'/'.$poster_path);

                    if(!is_error($result)) {
                        $_data = array(
                            'media_id' => $result['media_id'],
                            'dateline' => $result['created_at'],
                        );
                        M::t('superman_mall_member_poster')->update($_data, array('id' => $member_poster['id']));
                        $member_poster['media_id'] = $_data['media_id'];
                    } else {
                        WeUtility::logging('warning', '[post:upload] error ,result='.var_export($result, true));
                    }
                }
                //发客服消息
                $account = $this->_init_account();
                $message = array(
                    'touser'  => $_GPC['openid'],
                    'msgtype' => 'image',
                    'image'   => array('media_id' => $member_poster['media_id'])
                );
                $account->sendCustomNotice($message);
                exit;
            }

            $member_address = M::t('mc_member_address')->fetch(array(
                'uid' => $uid,
                'isdefault' => 1,
            ));
            $filter = array(
                'uniacid' => $_W['uniacid'],
                'uid' => $uid,
            );
            $partner = M::t('superman_mall_partner')->fetch($filter);

            //初始化背景图
            if (!empty($item['bgimg'])) {
                $imgpath = SupermanUtil::get_bgimg_localpath($item['bgimg']);
                if (!file_exists($imgpath)) {
                    WeUtility::logging('warning', '[poster] background image not exist, imgpath='.$imgpath);
                    $this->json(ERRNO::SYSTEM_ERROR);
                }
                $bgimg = SupermanUtil::create_image($imgpath);
            } else {
                $bgimg = imagecreatetruecolor(640, 1008);
                $white = imagecolorallocate($bgimg, 255, 255, 255);
                imagefill($bgimg, 0, 0, $white);
            }
            imagealphablending($bgimg, true); //混色模式
            //imagesavealpha($bgimg, false); //png透明

            //初始化组件
            $item['widgets'] = $item['widgets']?iunserializer($item['widgets']):array();
            //print_r($item);die;
            if ($item['widgets']) {
                foreach ($item['widgets'] as $v) {
                    $type = $v['type'];
                    if ($type == 'avatar') {
                        $w = intval($v['width'])?intval($v['width'])*2:'48';
                        $h = intval($v['height'])?intval($v['height'])*2:'48';
                    } else {
                        $w = intval($v['width'])?intval($v['width'])*2:'200';
                        $h = intval($v['height'])?intval($v['height'])*2:'200';
                    }
                    $x = $v['left'] * 2;
                    $y = $v['top'] * 2;
                    $rgb = SupermanUtil::hex2rgb($v['color']);
                    $fontsize = $v['fontsize'];
                    $imgpath = $v['imgpath'];
                    if ($type == 'image') {
                        if ($imgpath == '') {
                            WeUtility::logging('warning', '[poster:'.$type.'] not found image, widget='.var_export($v, true));
                            continue;
                        }
                        $imgpath = tomedia($imgpath);
                        if (!file_exists($imgpath)) {
                            WeUtility::logging('warning', '[poster:'.$type.'] image not exist, imgpath='.$imgpath);
                            continue;
                        }
                        $this->_create_image($bgimg, $imgpath, $x, $y, $w, $h);
                    } else if ($type == 'avatar') {
                        $imgpath = SupermanUtil::get_avatar_localpath($member['avatar']);
                        if (!file_exists($imgpath)) {
                            WeUtility::logging('warning', '[poster:'.$type.'] avatar image not exist, member='.var_export($_W['member'], true));
                            continue;
                        }
                        $this->_create_image($bgimg, $imgpath, $x, $y, $w, $h);
                    } else if ($type == 'qrcode') {
                        if (empty($partner)) {
                            WeUtility::logging('warning', '[poster:'.$type.'] not found partner, widget='.var_export($v, true));
                            continue;
                        }
                        $url = $_W['siteroot'].'app/'.$this->createMobileUrl('home', array(
                            'partnerid' => $partner['id'],
                            'posterid' => $item['id'],
                        ));
                        $ret = SupermanUtil::get_qrcode($url);
                        if (empty($ret) || empty($ret['abs_path'])) {
                            WeUtility::logging('warning', '[poster:'.$type.'] not found qrcode image, widget='.var_export($v, true).', ret='.var_export($ret, true));
                            continue;
                        }
                        if (!file_exists($ret['abs_path'])) {
                            WeUtility::logging('warning', '[poster:'.$type.'] qrcode image not exist, ret='.var_export($ret, true));
                            continue;
                        }
                        $this->_create_image($bgimg, $ret['abs_path'], $x, $y, $w, $h);
                    } else { //文字
                        if ($type == 'nickname') {
                            $text = $member['nickname'];
                        } else if ($type == 'mobile') {
                            $text = $member['mobile'];
                        } else if ($type == 'address') {
                            if (empty($member_address) || empty($member_address['address'])) {
                                WeUtility::logging('warning', '[poster:'.$type.'] not exist member address, widget='.var_export($v, true));
                                continue;
                            }
                            $text = $member_address['city'].$member_address['district'].$member_address['address'];
                        }
                        $this->_create_text($bgimg, $text, $x, $y, $rgb, SUPERMAN_FONT_MSYH, $fontsize*2);
                        //字号需要等比放大一倍
                    }
                }
            }
            //保存海报文件
            SupermanUtil::save_image($bgimg, MODULE_ROOT.'/'.$poster_path);
            if (!$keyword) {
                $this->_output($poster_path, $bgimg);
                imagedestroy($bgimg);
                exit;
            }
            $result = $this->_uploadMedia(MODULE_ROOT.'/'.$poster_path);
            if(!is_error($result)) {
                $_data = array(
                    'media_id' => $result['media_id'],
                    'dateline' => $result['created_at'],
                );
                M::t('superman_mall_member_poster')->update($_data, array('id' => $member_poster['id']));
                $member_poster['media_id'] = $_data['media_id'];
            } else {
                WeUtility::logging('warning', '[post:upload] error ,result='.var_export($result, true));
            }
            //发客服消息
            $account = $this->_init_account();
            $message = array(
                'touser'  => $_GPC['openid'],
                'msgtype' => 'image',
                'image'   => array('media_id' => $member_poster['media_id'])
            );
            $account->sendCustomNotice($message);
            exit;
//            return $this->respImage($member_poster['media_id']);
        }
        include $this->template('poster/index');
    }

    private function _init_account() {
        global $_W, $_GPC;
        static $account = null;
        if (!is_null($account)) {
            return $account;
        }
        if (empty($_W['account'])) {
            if (isset($_W['uniacid']) && $_W['uniacid']) {
                $_W['account'] = uni_fetch($_W['uniacid']);
            } else if (isset($_W['acid']) && $_W['acid']) {
                $_W['account'] = account_fetch($_W['acid']);
            } else {
                return error(-1, '初始化失败，缺少acid||uniacid参数');
            }
        }
        if ($_W['account']['level'] < 3) {
            return error(-1, '公众号没有经过认证');
        }
        $account = WeAccount::create();
        if (is_null($account)) {
            return error(-1, '创建公众号操作对象失败');
        }
        return $account;
    }

    private function _create_image(&$bgimg, $imgpath, $x, $y, $w, $h, $pct = 100) {
        if (empty($imgpath)) {
            return;
        }
        $arr = getimagesize($imgpath);
        $img_w = $arr[0];
        if ($img_w > $w) {
            $dest = SupermanUtil::get_thumb_filename($imgpath);
            file_image_thumb($imgpath, $dest, $w);
            $imgpath = $dest;
        }
        $img = SupermanUtil::create_image($imgpath);
        imagecopymerge($bgimg, $img, $x, $y, 0, 0, $w, $h, $pct);
        imagedestroy($img);
    }
    private function _create_text(&$bgimg, $text, $x, $y, $rgb, $font, $fonsize = 14) {
        $color = imagecolorallocate($bgimg, $rgb['r'], $rgb['g'], $rgb['b']);
        //$color = imagecolorallocatealpha ($bgimg, $rgb['r'], $rgb['g'], $rgb['b'], 100);
        //$color = imagecolorallocate ($bgimg, 0, 0, 0);
        //根据gd库判断size单位
        $gdv = SupermanUtil::gdVersion();
        $size = $gdv >=2?$fonsize*3/4:$fonsize;//磅值/像素
        //文字定位点在左下角
        imagettftext($bgimg, $size, 0, $x, $y+$fonsize, $color, $font, $text);
    }
    private function _output($path, $img = null) {
        global $_W;
        if ($_W['isajax']) {
            $this->json(ERRNO::OK, '', array('imgurl' => MODULE_URL.$path));
        } else {
            @header('Content-Type: image/png');
            if (!$img) {
                $img = SupermanUtil::create_image(MODULE_ROOT.'/'.$path);
            }
            imagepng($img);
            imagedestroy($img);
        }
        exit;
    }

    private function _uploadMedia($path, $type = 'image') {
        global $_W;
        if(empty($path)) {
            return error(-1, '参数错误');
        }
        $acc = WeAccount::create($_W['acid']);
        $token = $acc->getAccessToken();
        if(is_error($token)){
            return $token;
        }
        $url = "https://api.weixin.qq.com/cgi-bin/media/upload?access_token={$token}&type={$type}";
        if(class_exists('CURLFile')) {
            $data = array(
                'media' => new CURLFile($path)
            );
        } else {
            $data = array(
                'media' => '@'.$path,
            );
        }
        $response = ihttp_request($url, $data);
        if(is_error($response)) {
            return error(-1, "访问公众平台接口失败, 错误: {$response['message']}");
        }
        $result = @json_decode($response['content'], true);
        if(empty($result)) {
            return error(-1, "接口调用失败, 元数据: {$response['meta']}");
        } elseif(!empty($result['errcode'])) {
            return error(-1, "访问微信接口错误, 错误代码: {$result['errcode']}, 错误信息: {$result['errmsg']}, 错误详情：{$this->error_code($result['errcode'])}");
        }
        return $result;
    }
}
$obj = new Superman_mall_doMobilePoster;