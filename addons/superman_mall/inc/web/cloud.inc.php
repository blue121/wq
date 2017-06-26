<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doWebCloud extends Superman {
	public function __construct() {
        parent::__construct();
        parent::init();
        $this->exec();
	}
    public function exec() {
        global $_W, $_GPC;
        $this->act = in_array($_GPC['act'], array('site', 'register', 'upgrade')) ? $_GPC['act'] : 'site';
        $siteinfo = M::t('superman_mall_setting')->fetch_value(SUPERMAN_SETTING_SITE);
        if ($this->act == 'site') {
            if (empty($siteinfo)) {
                @header('Location: '.$this->createWebUrl('cloud', array('act' => 'register')));
                exit;
            }
            $cloud = new SupermanMallCloud($this->module, $siteinfo);
            if (TIMESTAMP - $siteinfo['dateline'] > 600) {
                $result = $cloud->report();
                if ($result['errno'] != 0) {
                    if ($result['errno'] == 101) {
                        M::t('superman_mall_setting')->delete(array('skey' => SUPERMAN_SETTING_SITE));
                        @header('Location: '.$this->createWebUrl('cloud', array('act' => 'register')));
                        exit;
                    }
                    WeUtility::logging('warning', '[cloud] report failed, result='.var_export($result, true));
                } else if (!empty($result['data']['siteid'])) {
                    $data = array(
                        'skey' => SUPERMAN_SETTING_SITE,
                        'svalue' => iserializer($result['data']),
                    );
                    M::t('superman_mall_setting')->update($data, array('skey' => SUPERMAN_SETTING_SITE));
                }
            }
            $cloud_url = $cloud->url('site', array(), true);
        } else if ($this->act == 'register') {
            /*if (!empty($siteinfo)) {
                @header('Location: '.$this->createWebUrl('cloud', array('act' => 'site')));
                exit;
            }*/
            $cloud = new SupermanMallCloud($this->module);
            $result = $cloud->register();
            if (!empty($result['data']['siteid'])) {
                $data = array(
                    'skey' => SUPERMAN_SETTING_SITE,
                    'svalue' => iserializer($result['data']),
                );
                if (!empty($siteinfo)) {
                    M::t('superman_mall_setting')->update($data, array('skey' => SUPERMAN_SETTING_SITE));
                } else {
                    M::t('superman_mall_setting')->insert($data);
                }
            }
            @header('Location: '.$this->createWebUrl('cloud', array('act' => 'site')));
            exit;
        } else if ($this->act == 'upgrade') {
            error_reporting(0);
            if (isset($_GPC['upgrade_result']) && $_GPC['upgrade_result'] == 'ok') {
                if (!empty($_SESSION['upgrade_errors'])) {
                    $msg = '以下文件更新失败：';
                    $msg .= '<ul>';
                    foreach ($_SESSION['upgrade_errors'] as $err) {
                        foreach ($err as $k => $v) {
                            $msg .= '<li>';
                            $msg .= $k.'<br>';
                            $msg .= '<p>'.$v.'</p>';
                            $msg .= '</li>';
                        }
                    }
                    $msg .= '</ul>';
                    unset($_SESSION['upgrade_errors']);
                    message($msg, '', 'warning');
                } else {
                    message('更新成功！', $this->createWebUrl('cloud', array('act' => 'upgrade')), 'success');
                }
            }
            $op = 'upgrade_check';
            $cloud = new SupermanMallCloud($this->module, $siteinfo);
            $upgrade_url = $cloud->upgrade_check(true);
            if (checksubmit('submit', true)) {
                if ($_GPC['check_upgrade'] == 'yes') {
                    $result = $cloud->upgrade_check();
                    if ($result['errno'] == 200) {
                        message('恭喜, 您的程序已经是最新版本！', referer(), 'success');
                    }
                } else {
                    $op = 'upgrade_download';
                    $file = isset($_GPC['file'])?intval($_GPC['file']):-1;
                    $filekey = $_GPC['filekey'];
                    $params = array(
                        'act' => 'upgrade',
                        'file' => $file!=-1?$file:0,
                        'filekey' => $filekey,
                        'submit' => 'yes',
                        'token' => $_W['token'],
                        'force_upgrade' => $_GPC['force_upgrade']==1?1:0,
                    );
                    $progress = 0;
                    $count = 0;
                    $current = 0;
                    if ($file != -1) {
                        $result = $cloud->upgrade_download($file, $filekey);
                        if (is_array($result) && $result['errno'] == 0) {
                            $count = $result['data']['file_count'];
                            $current = $file + 1;
                            if ($result['data']['next'] != -1) {
                                $progress = intval(($result['data']['next'] / $result['data']['file_count']) * 100);
                                $params['file'] = $result['data']['next'];
                            } else {
                                $progress = 100;
                                $params['upgrade_result'] = 'ok';
                                unset($params['file']);
                                unset($params['filekey']);
                                unset($params['submit']);
                                unset($params['token']);
                            }
                            if (isset($result['data']['data'])) {
                                if (defined('SUPERMAN_DEVELOPMENT')) {
                                    if (isset($_GPC['force_upgrade']) && $_GPC['force_upgrade'] == 1) {
                                        $this->_doUpgrade($result['data']);
                                    }
                                } else {
                                    $this->_doUpgrade($result['data']);
                                }
                            }
                        } else {
                            message('网络错误或服务器未响应，请稍后重试！', '', 'warning');
                        }
                    }
                    $redirect_url = $this->createWebUrl('cloud', $params);
                }
            }
        }
        include $this->template('cloud/index');
    }
    private function _doUpgrade($file) {
        $filename = MODULE_ROOT.'/'.$file['path'];
        $filedata = $file['data'];
        if ($file['type'] == 1) {
            $ret = file_put_contents($filename, base64_decode($filedata));
            if ($ret === false) {
                $errors = error_get_last();
                $_SESSION['upgrade_errors'][] = array(
                    'file' => $file['path'],
                    'message' => $errors['message'],
                );
            }
        } else if ($file['type'] == 2) {
            $func = create_function('', base64_decode($filedata));
            $ret = $func();
            if ($ret === false) {
                $errors = error_get_last();
                $_SESSION['upgrade_errors'][] = array(
                    'path' => $file['path'],
                    'message' => $errors['message'],
                );
            }
        }
    }
}
$obj = new Superman_mall_doWebCloud;