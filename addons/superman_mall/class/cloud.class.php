<?php

//decode by  http://www.tule5.com/
defined('IN_IA') or die('Access Denied');
class SupermanMallCloud
{
	public $showMessage = true;
	private $module_info = array(), $site_info = array(), $result, $post_data;
	public function __construct($module_info, $site_info = array())
	{
		$this->module_info = $module_info;
		$this->site_info = $site_info;
	}
	public function register()
	{
		global $_W;
		$this->post_data = array('module_version' => SUPERMAN_MALL_VERSION, 'module_name' => SUPERMAN_MALL, 'branch' => SUPERMAN_MALL_BRANCH, 'siteroot' => $_W['siteroot'], 'sitename' => isset($_W['setting']['copyright']['sitename']) ? $_W['setting']['copyright']['sitename'] : '', 'qq' => isset($_W['setting']['copyright']['qq']) ? $_W['setting']['copyright']['qq'] : '', 'mobile' => isset($_W['setting']['copyright']['phone']) ? $_W['setting']['copyright']['phone'] : '', 'email' => isset($_W['setting']['copyright']['email']) ? $_W['setting']['copyright']['email'] : '', 'trade_status' => $this->module_info['trade_status'], 'key' => $this->get_key());
		$url = $this->url('register.index');
		$response = $this->request($url, $this->post_data);
		if (empty($response)) {
			WeUtility::logging('fatal', '[cloud.register] response is null, url=' . $url . ', post_data=' . var_export($this->post_data, true));
			if ($this->showMessage) {
				message('网络异常，请稍后重试！', '', 'error');
			} else {
				return false;
			}
		}
		$this->result = json_decode($response['content'], true);
		if (!is_array($this->result) || !isset($this->result['errno'])) {
			WeUtility::logging('fatal', "[cloud.register] response invalide, url={$url}, response=" . var_export($response, true) . ", post_data=" . var_export($this->post_data, true));
			return false;
		}
		if ($this->result['errno'] != 0 && $this->result['errno'] != 100) {
			$msg = "{$this->result['errmsg']}({$this->result['errnor']})";
			WeUtility::logging('fatal', '[cloud.register] register failed, response=' . var_export($this->result, true));
			if ($this->showMessage) {
				message($msg, '', 'error');
			} else {
				return false;
			}
		}
		return $this->result;
	}
	public function report()
	{
		global $_W;
		$this->post_data = array('module_version' => SUPERMAN_MALL_VERSION, 'module_name' => SUPERMAN_MALL, 'branch' => SUPERMAN_MALL_BRANCH, 'siteroot' => $_W['siteroot'], 'siteid' => $this->site_info['siteid'], 'uniacid' => $_W['uniacid'], 'title' => $_W['account']['name'], 'level' => $_W['account']['level'], 'key' => $this->get_key());
		$url = $this->url('report.index', array(), true);
		$response = $this->request($url, $this->post_data, true);
		if (empty($response)) {
			WeUtility::logging('fatal', '[cloud.report] response is null, url=' . $url . ', post_data=' . var_export($this->post_data, true));
			if ($this->showMessage) {
				message('网络异常，请稍后重试！', '', 'error');
			} else {
				return false;
			}
		}
		$this->result = json_decode($response['content'], true);
		if (!is_array($this->result) || !isset($this->result['errno'])) {
			WeUtility::logging('fatal', '[cloud.report] response invalide, response=' . $response);
			return false;
		}
		return $this->result;
	}
	public function upgrade_check($return_url = false)
	{
		global $_W;
		$this->post_data = array('module_version' => SUPERMAN_MALL_VERSION, 'module_name' => SUPERMAN_MALL, 'branch' => SUPERMAN_MALL_BRANCH, 'siteroot' => $_W['siteroot'], 'siteid' => $this->site_info['siteid'], 'key' => $this->get_key());
		$url = $this->url('upgrade_check', array(), true);
		if ($return_url) {
			$url .= '&' . http_build_query($this->post_data);
			return $url;
		}
		$response = $this->request($url, $this->post_data, true);
		if (empty($response)) {
			WeUtility::logging('fatal', '[cloud.upgrade_check] response is null, url=' . $url . ', post_data=' . var_export($this->post_data, true));
			if ($this->showMessage) {
				message('网络异常，请稍后重试！', '', 'error');
			} else {
				return false;
			}
		}
		$this->result = json_decode($response['content'], true);
		if (!is_array($this->result) || !isset($this->result['errno'])) {
			WeUtility::logging('fatal', '[cloud.upgrade_check] response invalide, response=' . $response);
			return false;
		}
		return $this->result;
	}
	public function upgrade_download($file, $filekey)
	{
		global $_W;
		$this->post_data = array('module_version' => SUPERMAN_MALL_VERSION, 'module_name' => SUPERMAN_MALL, 'branch' => SUPERMAN_MALL_BRANCH, 'siteroot' => $_W['siteroot'], 'siteid' => $this->site_info['siteid'], 'key' => $this->get_key(), 'file' => $file, 'filekey' => $filekey);
		$url = $this->url('upgrade_download', array(), true);
		$response = $this->request($url, $this->post_data, true);
		if (empty($response)) {
			WeUtility::logging('fatal', '[cloud.upgrade_download] response is null, url=' . $url . ', post_data=' . var_export($this->post_data, true));
			if ($this->showMessage) {
				message('网络异常，请稍后重试！', '', 'error');
			} else {
				return false;
			}
		}
		$this->result = json_decode($response['content'], true);
		if (!is_array($this->result) || !isset($this->result['errno'])) {
			WeUtility::logging('fatal', '[cloud.upgrade_download] response invalide, response=' . $response);
			return false;
		}
		if (!$this->check_signature('upgrade_download')) {
			return false;
		}
		return $this->result;
	}
	public function check()
	{
		global $_W;
		$this->post_data = array('module_version' => SUPERMAN_MALL_VERSION, 'module_name' => SUPERMAN_MALL, 'branch' => SUPERMAN_MALL_BRANCH, 'siteroot' => $_W['siteroot'], 'siteid' => $this->site_info['siteid'], 'key' => $this->get_key());
		$url = $this->url('check', array(), true);
		$response = $this->request($url, $this->post_data, true);
		if (empty($response)) {
			WeUtility::logging('fatal', '[cloud.check] response is null, url=' . $url . ', post_data=' . var_export($this->post_data, true));
			if ($this->showMessage) {
				message('网络异常，请稍后重试！', '', 'error');
			} else {
				return false;
			}
		}
		$result = json_decode($response['content'], true);
		if (!is_array($result) || !isset($result['errno'])) {
			WeUtility::logging('fatal', '[cloud.check] response invalide, response=' . $response);
			return false;
		}
		return $result;
	}
	public function url($action_method, $extra = array(), $signature = false)
	{
		$action = $action_method;
		$method = 'index';
		if (strpos($action_method, '.') !== false) {
			list($action, $method) = explode('.', $action_method);
		}
		$params = array('_common' => array('m' => 'index', 'c' => 'api'), 'register' => array('index' => array('a' => 'register', 'method' => 'index')), 'report' => array('index' => array('a' => 'report', 'method' => 'index', 'siteid' => $this->site_info['siteid'])), 'check' => array('index' => array('a' => 'check', 'method' => 'index', 'siteid' => $this->site_info['siteid'])), 'site' => array('index' => array('a' => 'site', 'method' => 'index', 'siteid' => $this->site_info['siteid']), 'post' => array('a' => 'site', 'method' => 'post', 'siteid' => $this->site_info['siteid'])), 'upgrade_check' => array('index' => array('a' => 'upgrade', 'method' => 'check', 'siteid' => $this->site_info['siteid'])), 'upgrade_download' => array('index' => array('a' => 'upgrade', 'method' => 'download', 'siteid' => $this->site_info['siteid'], 'file' => $this->post_data['file'], 'filekey' => $this->post_data['filekey'])));
		if (!isset($params[$action][$method])) {
			trigger_error('invalid params "' . $action_method . '"');
		}
		$host = defined('LOCAL_DEVELOPMENT') ? 'cloud.supermanapp.localhost.com' : 'cloud.supermanapp.cn';
		$querystring = array_merge($params['_common'], $params[$action][$method], $extra);
		if ($signature) {
			ksort($querystring);
			$str = http_build_query($querystring);
			$str .= '&secret=' . $this->site_info['secret'];
			$querystring['sn'] = sha1($str);
		}
		$url = 'http://' . $host . '/index.php?';
		$url .= http_build_query($querystring);
		return $url;
	}
	private function get_key()
	{
		$key = '';
		$files = array('site.php', 'superman.class.php', 'class/cloud.class.php', 'class/table.class.php', 'inc/web/cloud.inc.php', 'template/web/default/cloud/index.html', 'template/web/default/cloud/site.html', 'template/web/default/cloud/upgrade.html');
		foreach ($files as $f) {
			$key .= md5_file(MODULE_ROOT . '/' . $f);
		}
		return defined('SUPERMAN_DEVELOPMENT') ? 'superman' : md5($key);
	}
	private function request($url, $post, $extra = array(), $timeout = 30)
	{
		load()->func('communication');
		return ihttp_request($url, $post, $extra, $timeout);
	}
	private function check_signature($action_method)
	{
		if ($this->result['data']['sn'] == '') {
			WeUtility::logging('fatal', '[check_signature] check_signature failed, sn is null');
			return false;
		}
		$action = $action_method;
		$method = 'index';
		if (strpos($action_method, '.') !== false) {
			list($action, $method) = explode('.', $action_method);
		}
		$params = array('_common' => array('m' => 'index', 'c' => 'api'), 'upgrade_download' => array('index' => array('a' => 'upgrade', 'method' => 'download', 'siteid' => $this->site_info['siteid'], 'file' => $this->post_data['file'], 'filekey' => $this->post_data['filekey'])));
		if (!isset($params[$action][$method])) {
			WeUtility::logging('fatal', "[check_signature] check_signature failed, action={$action}, method={$method}");
			return false;
		}
		$querystring = array_merge($params['_common'], $params[$action][$method]);
		ksort($querystring);
		$str = http_build_query($querystring) . '&secret=' . $this->site_info['secret'];
		$new_sn = sha1($str);
		if ($new_sn != $this->result['data']['sn']) {
			WeUtility::logging('fatal', "[check_signature] check_signature failed, sn={$this->result['data']['sn']}, new_sn={$new_sn}");
			return false;
		}
		return true;
	}
}