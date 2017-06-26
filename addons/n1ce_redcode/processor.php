<?php
/**
 * 验证码生成模块模块处理程序
 *
 * @author n1ce   QQ：97391583
 * @url www.tule5.com
 */
defined('IN_IA') or exit('Access Denied');

class N1ce_redcodeModuleProcessor extends WeModuleProcessor {
	public function respond() {
		global $_W, $_GPC;
		load()->model('mc');
		$settings = $this->module['config'];
		//判断类型扫码还是文本
		//坑爹的扫码事件
		
		if($this->message['msgtype'] == 'event'){
			if($this->message['event'] == 'SCAN' || $this->message['event'] == 'subscribe'){
				$scene_id = $this->message['eventkey'];
				$ticket = $this->message['ticket'];
			}
			$scene_id = str_replace('qrscene_','',$scene_id);
			
			if(is_numeric($scene_id)){
				$contents = pdo_fetch('select * from ' . tablename('qrcode') . ' where uniacid = :uniacid and  qrcid = :qrcid and ticket = :ticket and redcode = 1', array(':uniacid' => $_W['uniacid'] ,':qrcid' => $scene_id ,':ticket' => $ticket));
				
				$content = $contents['keyword'];
				
				$pici = $contents['pici'];
				
			}else{
				$contents = pdo_fetch('select * from ' . tablename('qrcode') . ' where uniacid = :uniacid and  scene_str = :scene_str and redcode = 1 and ticket = :ticket', array(':uniacid' => $_W['uniacid'] ,':scene_str' => $scene_id ,':ticket' => $ticket));
				$content = $contents['keyword'];
				
				$pici = $contents['pici'];
			}
			
		}elseif($this->message['msgtype'] == 'voice'){
			$content = trim($this->message['recognition']);
			$content = str_replace('！','',$content);
			
			$picires = pdo_fetch('select * from ' . tablename('n1ce_red_code') . ' where uniacid = :uniacid and  code = :code', array(':code' => $content, ':uniacid' => $_W['uniacid']));
			//获取批次
			$pici = $picires['pici'];
		}else{
			$content = trim($this->message['content']);
			$msgid = $this->message['msgid'];
			$again = pdo_fetch('select * from ' . tablename('n1ce_red_msgid') . ' where uniacid = :uniacid and  msgid = :msgid', array(':msgid' => $msgid, ':uniacid' => $_W['uniacid']));
			if($again){
				return $this->respText('');
			}
			$picires = pdo_fetch('select * from ' . tablename('n1ce_red_code') . ' where uniacid = :uniacid and  code = :code', array(':code' => $content, ':uniacid' => $_W['uniacid']));
			//获取批次
			$pici = $picires['pici'];
			
		}
		
		$openid = $this->message['from'];
		$settings = $this->module['config'];
		if(empty($openid)){
			return $this->respText("非法用户！");
		}
		
		$timeinfo = pdo_fetch('select * from ' . tablename('n1ce_red_pici') .' where uniacid = :uniacid and pici = :pici',array(':uniacid'=>$_W['uniacid'],':pici'=>$pici));
		if($timeinfo['time_limit'] == '1'){
			if ($timeinfo['starttime'] > time()) {
				return $this->respText($timeinfo['miss_start']);
			}
			if ($timeinfo['endtime'] < time()) {
				return $this->respText($timeinfo['miss_end']);
			}
		}
		//获取昵称，坑爹的mc_fansinfo，用mc_fetch !不能实时获取到新关注的粉丝昵称
		$mc = mc_fetch($openid);
		
		if(empty($mc['nickname']) || empty($mc['avatar']) || empty($mc['resideprovince']) || empty($mc['residecity'])){
			load()->classs( 'account' );
			load()->func( 'communication' );
			$accToken = WeAccount::token();
			$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token={$accToken}&openid={$openid}&lang=zh_CN";
			$json = ihttp_get($url);
			$userinfo = @json_decode($json['content'],true);
			if($userinfo['nickname']) $mc['nickname'] = $userinfo['nickname'];
			if($userinfo['headimgurl']) $mc['avatar'] = $userinfo['headimgurl'];
			if($userinfo['resideprovince']) $mc['resideprovince'] = $userinfo['resideprovince'];
			if($userinfo['residecity']) $mc['residecity'] = $userinfo['residecity'];
			mc_update($openid,array('nickname' => $mc['nickname'] , 'avatar' => $mc['avatar'] , 'resideprovince' => $mc['resideprovince'], 'residecity' => $mc['residecity']));
		}
		if(empty($pici)){
			$nick = $mc['nickname'];
			$tempstr=str_replace("|#昵称#|",$nick,$settings['wrong']);
			$tempstr = htmlspecialchars_decode(str_replace('&quot;','&#039;',$tempstr),ENT_QUOTES);
			return $this->respText($tempstr);
		}
		//return $this->respText('1不开启限制');
		if($settings['xianzhi'] == '2'){
			$isget = pdo_fetch('select * from ' . tablename('n1ce_red_user') . ' where uniacid = :uniacid and openid = :openid', array(':uniacid' => $_W['uniacid'],':openid' => $openid));
			if($isget){
				$nick = $mc['nickname'];
				$tempstr=str_replace("|#昵称#|",$nick,$settings['isget']);
				$tempstr = htmlspecialchars_decode(str_replace('&quot;','&#039;',$tempstr),ENT_QUOTES);
				return $this->respText($tempstr);
			}
		}
		$res = pdo_fetch('select * from ' . tablename('n1ce_red_code') . ' where uniacid = :uniacid and  code = :code and status = 1', array(':code' => $content, ':uniacid' => $_W['uniacid']));
		
		//概率计算排除数量为0的奖品
		$prizes = pdo_fetchall('select * from' . tablename('n1ce_red_prize') . ' where prizesum > 0 and uniacid = :uniacid and pici = :pici order by id desc', array(':uniacid' => $_W['uniacid'], ':pici' => $pici));
		if(!$prizes){
			$nick = $mc['nickname'];
			$tempstr=str_replace("|#昵称#|",$nick,$settings['islater']);
			$tempstr = htmlspecialchars_decode(str_replace('&quot;','&#039;',$tempstr),ENT_QUOTES);
			return $this->respText($tempstr);
		}
		if($res){
			//防止微信5s回复
			if(!empty($msgid)){
				pdo_insert('n1ce_red_msgid',array('uniacid' => $_W['uniacid'],'msgid' => $msgid));
			}
			
			foreach ($prizes as $key => $val) {
				$arr[$val['id']] = $val['prizeodds'];
			}
			$pid = $this->get_rand($arr);
			$sends = pdo_fetch('select * from ' . tablename('n1ce_red_prize') . ' where id = :id', array(':id' => $pid));
			
			$insert = array(
					'uniacid' => $_W['uniacid'],
					'openid' => $openid,
					'nickname' => $mc['nickname'],
					'pici' => $pici,
					'time' => TIMESTAMP,
					'code' => $content,
					//'status' => '1',
				);
			$chprize = array(
				'prizesum' => $sends['prizesum'] - 1,
			);
			if($sends['type'] == '1'){
				
				$money = rand($sends['min_money'], $sends['max_money']);
				
				$brrow = $settings['brrow'];
				if($brrow == '2'){
					pdo_update('n1ce_red_code',array('status'=>'2'),array('id'=>$res['id']));
					$insert = array(
						'uniacid' => $_W['uniacid'],
						'openid' => $openid,
						'nickname' => $mc['nickname'],
						'pici' => $pici,
						'code' => $content,
						'name' => $sends['name'],
						'money' => $money,
						'salt' => md5($openid.time()),
						'time' => TIMESTAMP,
						'status' => '3',
					);
					$redurl = $this->createMobileUrl('redurl' , array('salt' => $insert['salt']));
					pdo_update('n1ce_red_prize',$chprize,array('id'=>$pid));
					pdo_insert('n1ce_red_user', $insert);
					$news = array(
						'Title' => '红包消息',
						'Description' => '点击领取',
						'PicUrl' => '',
						'Url' => $redurl,
					);
					
					return $this->respNews($news);
				}else{
					$exits = pdo_update('n1ce_red_code',array('status'=>'2'),array('id'=>$res['id']));
					$this->sendText($openid,"红包发送中，请稍后领取！");
					$action = $this->sendRedPacket($openid, $money);
					
					if($res['iscqr'] == '2'){
					 pdo_query('update ' . tablename('qrcode') . ' set redcode = 2 where uniacid = :uniacid and keyword = :keyword', array(':uniacid' => $_W['uniacid'],':keyword' => $content));
					}
					pdo_update('n1ce_red_prize',$chprize,array('id'=>$pid));
					$insert = array(
						'uniacid' => $_W['uniacid'],
						'openid' => $openid,
						'nickname' => $mc['nickname'],
						'pici' => $pici,
						'money' => $money,
						'name' => $sends['name'],
						'code' => $content,
						'time' => TIMESTAMP,
						//'status' => '1',
					);
					if($action === true){
						//替换粉丝标志换成昵称
						
						pdo_insert('n1ce_red_user', $insert);
						$nick = $mc['nickname'];
						$tempstr=str_replace("|#昵称#|",$nick,$settings['sendred']);
						$tempstr = htmlspecialchars_decode(str_replace('&quot;','&#039;',$tempstr),ENT_QUOTES);
						$this->sendText($openid,$tempstr);
						return $this->respText('');
					}else{
						
						$insert['status'] = '2';
						pdo_insert('n1ce_red_user', $insert);
						$actions = "亲爱的管理员，有粉丝红包领取失败！\n原因：".$action;
						$this->sendText($settings['mopenid'],$actions);
						$nick = $mc['nickname'];
						$tempstr=str_replace("|#昵称#|",$nick,$settings['sendbad']);
						$tempstr = htmlspecialchars_decode(str_replace('&quot;','&#039;',$tempstr),ENT_QUOTES);
						$this->sendText($openid,$tempstr);
						return $this->respText('');
					}
				}
			}
			if($sends['type'] == '2'){
				$cardres = $this->sendWxCard($openid,$sends['cardid']);
				pdo_update('n1ce_red_prize',$chprize,array('id'=>$pid));
				if($cardres){
					$exits = pdo_update('n1ce_red_code',array('status'=>'2'),array('id'=>$res['id']));
					if($res['iscqr'] == '2'){
					 pdo_query('update ' . tablename('qrcode') . ' set redcode = 2 where uniacid = :uniacid and keyword = :keyword', array(':uniacid' => $_W['uniacid'],':keyword' => $content));
					}
					$insert['name'] = $sends['name'];
					pdo_insert('n1ce_red_user', $insert);
					$nick = $mc['nickname'];
					$tempstr=str_replace("|#昵称#|",$nick,$settings['sendcard']);
					$tempstr = htmlspecialchars_decode(str_replace('&quot;','&#039;',$tempstr),ENT_QUOTES);
					return $this->respText($tempstr);
				}
			}
			if($sends['type'] == '3'){
				$exits = pdo_update('n1ce_red_code',array('status'=>'2'),array('id'=>$res['id']));
				if($res['iscqr'] == '2'){
				 pdo_query('update ' . tablename('qrcode') . ' set redcode = 2 where uniacid = :uniacid and keyword = :keyword', array(':uniacid' => $_W['uniacid'],':keyword' => $content));
				}
				pdo_update('n1ce_red_prize',$chprize,array('id'=>$pid));
				$insert['name'] = $sends['name'];
				pdo_insert('n1ce_red_user', $insert);
				return $this->respText("@".$mc['nickname']."恭喜你获得神秘礼品"."\n\n<a href='{$sends['url']}'>点击领取>>></a>");
				//return $this->respText($sends['url']);
			}
			if($sends['type'] == '4'){
				pdo_update('n1ce_red_prize',$chprize,array('id'=>$pid));
				$insert['name'] = $sends['name'];
				pdo_insert('n1ce_red_user', $insert);
				
				load()->model('mc');
				$uid = mc_openid2uid($openid);
				$creditres = mc_credit_update($uid, 'credit1', $sends['credit'], array(0, '系统积分'.$sends['credit'].'积分'));
				if($creditres){
					$exits = pdo_update('n1ce_red_code',array('status'=>'2'),array('id'=>$res['id']));
					if($res['iscqr'] == '2'){
					 pdo_query('update ' . tablename('qrcode') . ' set redcode = 2 where uniacid = :uniacid and keyword = :keyword', array(':uniacid' => $_W['uniacid'],':keyword' => $content));
					}
					$nick = $mc['nickname'];
					$tempstr=str_replace("|#昵称#|",$nick,$settings['sendcredit']);
					$credit = $sends['credit'];
					$tempstrs = str_replace("|#积分#|",$credit,$tempstr);
					$tempstrs = str_replace("|#用户#|",$openid,$tempstrs);
					$tempstrs = htmlspecialchars_decode(str_replace('&quot;','&#039;',$tempstrs),ENT_QUOTES);
					//$this->sendText($openid,$tempstr);
					return $this->respText($tempstrs);
				}
				//return $this->respText($sends['url']);
			}
			if($sends['type'] == '5'){
				$exits = pdo_update('n1ce_red_code',array('status'=>'2'),array('id'=>$res['id']));
				if($res['iscqr'] == '2'){
				 pdo_query('update ' . tablename('qrcode') . ' set redcode = 2 where uniacid = :uniacid and keyword = :keyword', array(':uniacid' => $_W['uniacid'],':keyword' => $content));
				}
				pdo_update('n1ce_red_prize',$chprize,array('id'=>$pid));
				$insert['name'] = $sends['name'];
				pdo_insert('n1ce_red_user', $insert);
				$tempstr = htmlspecialchars_decode(str_replace('&quot;','&#039;',$sends['txt']),ENT_QUOTES);
				$this->sendText($openid,$tempstr);
				return $this->respText('');
				//return $this->respText($sends['url']);
			}
			if($sends['type'] == '6'){
				$exits = pdo_update('n1ce_red_code',array('status'=>'2'),array('id'=>$res['id']));
				pdo_update('n1ce_red_prize',$chprize,array('id'=>$pid));
				$insert['name'] = $sends['name'];
				$money = rand($sends['min_money'], $sends['max_money']);
				$insert['money'] = $money;
					if($money){
						$action = $this->sendRedPacket($openid, $money);
						
						if($res['iscqr'] == '2'){
						 pdo_query('update ' . tablename('qrcode') . ' set redcode = 2 where uniacid = :uniacid and keyword = :keyword', array(':uniacid' => $_W['uniacid'],':keyword' => $content));
						}
						if($action === true){
							//替换粉丝标志换成昵称
							
							pdo_insert('n1ce_red_user', $insert);
							$nick = $mc['nickname'];
							$tempstr=str_replace("|#昵称#|",$nick,$settings['sendred']);
							$tempstr = htmlspecialchars_decode(str_replace('&quot;','&#039;',$tempstr),ENT_QUOTES);
							$this->sendText($openid,$tempstr);
							if($sends['cardid']){
								$this->sendWxCard($openid,$sends['cardid']);
							}
							return $this->respText($sends['txt']);
						}else{
							$insert['status'] = '2';
							pdo_insert('n1ce_red_user', $insert);
							$actions = "亲爱的管理员，有粉丝红包领取失败！\n原因：".$action;
							$this->sendText($settings['mopenid'],$actions);
							$nick = $mc['nickname'];
							$tempstr=str_replace("|#昵称#|",$nick,$settings['sendbad']);
							$tempstr = htmlspecialchars_decode(str_replace('&quot;','&#039;',$tempstr),ENT_QUOTES);
							return $this->respText($tempstr);
						}
					}
			}
			if($sends['type'] == '7'){
				
				$money = rand($sends['min_money'], $sends['max_money']);
				//$action = $this->sendCommonRedpack($openid, $settings, $money);
				$brrow = $settings['brrow'];
				if($brrow == '2'){
					$exits = pdo_update('n1ce_red_code',array('status'=>'2'),array('id'=>$res['id']));
					$insert = array(
						'uniacid' => $_W['uniacid'],
						'openid' => $openid,
						'nickname' => $mc['nickname'],
						'pici' => $pici,
						'code' => $content,
						'name' => $sends['name'],
						'money' => $money,
						'salt' => md5($openid.time()),
						'time' => TIMESTAMP,
						'status' => '3',
					);
					$redurl = $this->createMobileUrl('redurl' , array('salt'=>$insert['salt'],'total_num'=> $sends['total_num']));
					pdo_update('n1ce_red_prize',$chprize,array('id'=>$pid));
					pdo_insert('n1ce_red_user', $insert);
					$news = array(
						'Title' => '红包消息',
						'Description' => '点击领取',
						'PicUrl' => '',
						'Url' => $redurl,
					);
					
					return $this->respNews($news);
				}else{
					$exits = pdo_update('n1ce_red_code',array('status'=>'2'),array('id'=>$res['id']));
					$action = $this->sendRedgroupPacket($openid, $money,$sends['total_num']);
					pdo_update('n1ce_red_prize',$chprize,array('id'=>$pid));
					
					if($res['iscqr'] == '2'){
					 pdo_query('update ' . tablename('qrcode') . ' set redcode = 2 where uniacid = :uniacid and keyword = :keyword', array(':uniacid' => $_W['uniacid'],':keyword' => $content));
					}
					$insert = array(
						'uniacid' => $_W['uniacid'],
						'openid' => $openid,
						'nickname' => $mc['nickname'],
						'money' => $money,
						'name' => $sends['name'],
						'code' => $content,
						'time' => TIMESTAMP,
						//'status' => '1',
					);
					if($action === true){
						//替换粉丝标志换成昵称
						
						pdo_insert('n1ce_red_user', $insert);
						$nick = $mc['nickname'];
						$tempstr=str_replace("|#昵称#|",$nick,$settings['sendred']);
						$tempstr = htmlspecialchars_decode(str_replace('&quot;','&#039;',$tempstr),ENT_QUOTES);
						return $this->respText($tempstr);
					}else{
						$insert['status'] = '2';
						pdo_insert('n1ce_red_user', $insert);
						$actions = "亲爱的管理员，有粉丝红包领取失败！\n原因：".$action;
						$this->sendText($settings['mopenid'],$actions);
						$nick = $mc['nickname'];
						$tempstr=str_replace("|#昵称#|",$nick,$settings['sendbad']);
						$tempstr = htmlspecialchars_decode(str_replace('&quot;','&#039;',$tempstr),ENT_QUOTES);
						return $this->respText($tempstr);
					}
				}
			}
			
		}else{
			$nick = $mc['nickname'];
			$tempstr=str_replace("|#昵称#|",$nick,$settings['islater']);
			$tempstr = htmlspecialchars_decode(str_replace('&quot;','&#039;',$tempstr),ENT_QUOTES);
			return $this->respText($tempstr);
			//return $this->respText($content);
		}
	}
	private function sendCommonRedpack($openid, $settings, $money)
    {
        global $_W, $_GPC;
        $result = array();
        define('ROOT_PATH', dirname(preg_replace('@\\(.*\\(.*$@', '', __FILE__)));
        define('DS', DIRECTORY_SEPARATOR);
        define('SIGNTYPE', 'sha1');
        define('PARTNERKEY', $settings['pay_signkey']);
        define('APPID', $settings['appid']);
        define('apiclient_cert', $settings['apiclient_cert']);
        define('apiclient_key', $settings['apiclient_key']);
        define('rootca', $settings['rootca']);
        $mch_billno = $settings['pay_mchid'] . date('YmdHis') . rand(1000, 9999);
        include_once IA_ROOT . '/addons/n1ce_redcode/pay/WxHongBaoHelper.php';
        $commonUtil = new CommonUtil();
        $wxHongBaoHelper = new WxHongBaoHelper();
        $wxHongBaoHelper->setParameter('nonce_str', $commonUtil->create_noncestr());
        $wxHongBaoHelper->setParameter('mch_billno', $mch_billno);
        $wxHongBaoHelper->setParameter('mch_id', $settings['pay_mchid']);
        $wxHongBaoHelper->setParameter('wxappid', $settings['appid']);
        $wxHongBaoHelper->setParameter('nick_name', $settings['nick_name']);
        $wxHongBaoHelper->setParameter('send_name', $settings['send_name']);
        $wxHongBaoHelper->setParameter('re_openid', $openid);
        $wxHongBaoHelper->setParameter('total_amount', $money);
        $wxHongBaoHelper->setParameter('min_value', $money);
        $wxHongBaoHelper->setParameter('max_value', $money);
        $wxHongBaoHelper->setParameter('total_num', 1);
        $wxHongBaoHelper->setParameter('wishing', $settings['wishing']);
        $wxHongBaoHelper->setParameter('client_ip', '127.0.0.1');
        $wxHongBaoHelper->setParameter('act_name', $settings['act_name']);
        $wxHongBaoHelper->setParameter('remark', $settings['remark']);
        $wxHongBaoHelper->setParameter('logo_imgurl', 'https://www.baidu.com/img/bdlogo.png');
        $postXml = $wxHongBaoHelper->create_hongbao_xml();
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
        $responseXml = $wxHongBaoHelper->curl_post_ssl($url, $postXml);
        $responseObj = simplexml_load_string($responseXml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $return_code = $responseObj->return_code;
        $result_code = $responseObj->result_code;
        if ($return_code == 'SUCCESS') {
            if ($result_code == 'SUCCESS') {
                $result['type'] = 'ok';
                return $result;
            } else {
                if ($responseObj->err_code == 'NOTENOUGH') {
                    $result['content'] = '后台繁忙，请稍后再试！';
                    $result['type'] = 'error';
                    return $result;
                } else {
                    if ($responseObj->err_code == 'TIME_LIMITED') {
                        $result['content'] = '现在非红包发放时间，请在北京时间0:00-8:00之外的时间前来领取';
                        $result['type'] = 'error';
                        return $result;
                    } else {
                        if ($responseObj->err_code == 'SYSTEMERROR') {
                            $result['content'] = '系统繁忙，请稍后再试！';
                            $result['type'] = 'error';
                            return $result;
                        } else {
                            if ($responseObj->err_code == 'DAY_OVER_LIMITED') {
                                $result['content'] = '今日红包已达上限，请明日再试！';
                                $result['type'] = 'error';
                                return $result;
                            } else {
                                if ($responseObj->err_code == 'SECOND_OVER_LIMITED') {
                                    $result['content'] = '每分钟红包已达上限，请稍后再试！';
                                    $result['type'] = 'error';
                                    return $result;
                                }
                            }
                        }
                    }
                }
                $result['content'] = '红包发放失败！' . $responseObj->return_msg . '！请稍后再试！';
                $result['type'] = 'error';
                return $result;
            }
        }
        if ($return_code == 'FAIL') {
            $result['content'] = $responseObj->return_msg;
            $result['type'] = 'error';
            return $result;
        }
    }
	private function sendRedPacket($openid,$money){
		global $_W,$_GPC;
		$url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
		load()->func('communication');
		$pars = array();
		$cfg = $this->module['config'];
		$pars['nonce_str'] = random(32);
		$pars['mch_billno'] = $cfg['pay_mchid'] . date('YmdHis') . rand( 100, 999 );
		$pars['mch_id'] = $cfg['pay_mchid'];
		$pars['wxappid'] = $cfg['appid'];
		//$pars['nick_name'] = $cfg['nick_name'];
		$pars['send_name'] = $cfg['send_name'];
		$pars['re_openid'] = $openid;
		$pars['total_amount'] = $money;
		$pars['total_num'] = 1;
		$pars['wishing'] = $cfg['wishing'];
		$pars['client_ip'] = $_W['clientip'];
		$pars['act_name'] = $cfg['act_name'];
		$pars['remark'] = $cfg['remark'];
		ksort($pars, SORT_STRING);
		$string1 = '';
		foreach($pars as $k => $v) {
			$string1 .= "{$k}={$v}&";
		}
		$string1 .= "key={$cfg['pay_signkey']}";
		$pars['sign'] = strtoupper(md5($string1));
		$xml = array2xml($pars);
		$extras = array();
		$extras['CURLOPT_CAINFO'] = IA_ROOT .'/attachment/n1ce_redcode/cert_2/' . $_W['uniacid'] . '/' . $cfg['rootca'];
		$extras['CURLOPT_SSLCERT'] = IA_ROOT .'/attachment/n1ce_redcode/cert_2/' . $_W['uniacid'] . '/' . $cfg['apiclient_cert'];
		$extras['CURLOPT_SSLKEY'] = IA_ROOT .'/attachment/n1ce_redcode/cert_2/' . $_W['uniacid'] . '/' . $cfg['apiclient_key'];
		$procResult = false;
		$resp = ihttp_request($url, $xml, $extras);
		if(is_error($resp)) {
			$setting = $this->module['config'];
			$setting['api']['error'] = $resp['message'];
			$this->saveSettings($setting);
			$procResult = $resp['message'];
		} else {
			$xml = '<?xml version="1.0" encoding="utf-8"?>' . $resp['content'];
			$dom = new DOMDocument();
			if($dom->loadXML($xml)) {
				$xpath = new DOMXPath($dom);
				$code = $xpath->evaluate('string(//xml/return_code)');
				$ret = $xpath->evaluate('string(//xml/result_code)');
				if(strtolower($code) == 'success' && strtolower($ret) == 'success') {
					$procResult = true;
					$setting = $this->module['config'];
					$setting['api']['error'] = '';
					$this->saveSettings($setting);
				} else {
					$error = $xpath->evaluate('string(//xml/err_code_des)');
					$setting = $this->module['config'];
					$setting['api']['error'] = $error;
					$this->saveSettings($setting);
					$procResult = $error;
				}
			} else {
				$procResult = 'error response';
			}
		}
		return $procResult;
	}
	//裂变红包
	private function sendRedgroupPacket($openid,$money,$total_num){
		global $_W,$_GPC;
		$url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendgroupredpack';
		load()->func('communication');
		$pars = array();
		$cfg = $this->module['config'];
		$pars['nonce_str'] = random(32);
		$pars['mch_billno'] = $cfg['pay_mchid'] . date('YmdHis') . rand( 100, 999 );
		$pars['mch_id'] = $cfg['pay_mchid'];
		$pars['wxappid'] = $cfg['appid'];
		//$pars['nick_name'] = $cfg['nick_name'];
		$pars['send_name'] = $cfg['send_name'];
		$pars['re_openid'] = $openid;
		$pars['total_amount'] = $money;
		$pars['total_num'] = $total_num;
		$pars['amt_type'] = 'ALL_RAND';
		$pars['wishing'] = $cfg['wishing'];
		$pars['act_name'] = $cfg['act_name'];
		$pars['remark'] = $cfg['remark'];
		ksort($pars, SORT_STRING);
		$string1 = '';
		foreach($pars as $k => $v) {
			$string1 .= "{$k}={$v}&";
		}
		$string1 .= "key={$cfg['pay_signkey']}";
		$pars['sign'] = strtoupper(md5($string1));
		$xml = array2xml($pars);
		$extras = array();
		$extras['CURLOPT_CAINFO'] = IA_ROOT .'/attachment/n1ce_redcode/cert_2/' . $_W['uniacid'] . '/' . $cfg['rootca'];
		$extras['CURLOPT_SSLCERT'] = IA_ROOT .'/attachment/n1ce_redcode/cert_2/' . $_W['uniacid'] . '/' . $cfg['apiclient_cert'];
		$extras['CURLOPT_SSLKEY'] = IA_ROOT .'/attachment/n1ce_redcode/cert_2/' . $_W['uniacid'] . '/' . $cfg['apiclient_key'];
		$procResult = false;
		$resp = ihttp_request($url, $xml, $extras);
		if(is_error($resp)) {
			$setting = $this->module['config'];
			$setting['api']['error'] = $resp['message'];
			$this->saveSettings($setting);
			$procResult = $resp['message'];
		} else {
			$xml = '<?xml version="1.0" encoding="utf-8"?>' . $resp['content'];
			$dom = new DOMDocument();
			if($dom->loadXML($xml)) {
				$xpath = new DOMXPath($dom);
				$code = $xpath->evaluate('string(//xml/return_code)');
				$ret = $xpath->evaluate('string(//xml/result_code)');
				if(strtolower($code) == 'success' && strtolower($ret) == 'success') {
					$procResult = true;
					$setting = $this->module['config'];
					$setting['api']['error'] = '';
					$this->saveSettings($setting);
				} else {
					$error = $xpath->evaluate('string(//xml/err_code_des)');
					$setting = $this->module['config'];
					$setting['api']['error'] = $error;
					$this->saveSettings($setting);
					$procResult = $error;
				}
			} else {
				$procResult = 'error response';
			}
		}
		return $procResult;
	}
	private function sendWxCard($from_user, $cardid,$code = '') {
		load()->classs('weixin.account');
		load()->func('communication');
		$access_token = WeAccount::token();
		$url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$access_token}";
	
		$now = time();
		$nonce_str = $this->createNonceStr(8);
		$data = array(
				'api_ticket'=>$this->getApiTicket($access_token),
				'nonce_str'=>$nonce_str,
				'timestamp'=>$now,
				'code'=>$code,
				'card_id'=>$cardid,
				'openid'=>$from_user,
		);
		ksort($data);
		$buff = "";
		foreach ($data as $v) {
			$buff .= $v;
		}
		$sign = sha1($buff);
		$card_ext = array('code'=>$code,'openid'=>$from_user,'signature'=>$sign);
		$post = '{"touser":"' . $from_user . '","msgtype":"wxcard","wxcard":{"card_id":"' . $cardid . '","card_ext":"'.json_encode($card_ext).'"}}';
		load()->func('communication');
		$res = ihttp_post($url, $post);
		$res = json_decode($res['content'],true);
		if ($res['errcode'] == 0) return true;
		return $res['errmsg'];
	}
	
	private function createNonceStr($length = 16) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$str = "";
		for ($i = 0; $i < $length; $i++) {
			$str.= substr($chars, mt_rand(0, strlen($chars) - 1) , 1);
		}
		return $str;
	}
	
	private function getApiTicket($access_token){
		global $_W, $_GPC;
		$w = $_W['uniacid'];
		$cookiename = "wx{$w}a{$w}pi{$w}ti{$w}ck{$w}et";
		$apiticket = $_COOKIE[$cookiename];
		if (empty($apiticket)){
			$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token={$access_token}&type=wx_card";
			load()->func('communication');
			$res = ihttp_get($url);
			$res = json_decode($res['content'],true);
			if (!empty($res['ticket'])){
				setcookie($cookiename,$res['ticket'],time()+$res['expires_in']);
				$apiticket = $res['ticket'];
			}else{
				message('获取api_ticket失败：'.$res['errmsg']);
			}
		}
		return $apiticket;
	}
	
	private function sendText($openid,$txt){
		global $_W;
		$acid=pdo_fetchcolumn("SELECT acid FROM ".tablename('account')." WHERE uniacid=:uniacid ",array(':uniacid'=>$_W['uniacid']));
		$acc = WeAccount::create($acid);
		$data = $acc->sendCustomNotice(array('touser'=>$openid,'msgtype'=>'text','text'=>array('content'=>urlencode($txt))));
		return $data;
	}
	
	function get_rand($proArr)
    {
        $result = '';
        $proSum = array_sum($proArr);
        foreach ($proArr as $key => $proCur) {
            $randNum = mt_rand(1, $proSum);
            if ($randNum <= $proCur) {
                $result = $key;
                break;
            } else {
                $proSum -= $proCur;
            }
        }
        unset($proArr);
        return $result;
    }
}