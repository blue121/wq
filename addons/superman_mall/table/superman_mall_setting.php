<?php
/**
 * 【超人】超级商城模块微站定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class table_superman_mall_setting extends SupermanMallTable {
	public function __construct() {
		$this->_table = 'superman_mall_setting';
	}
    public function fetch_value($skey, $params = null, $unserialize = true) {
        global $_W;
        static $data;
        if (isset($data[$skey]) && !empty($data[$skey])) {
            return $data[$skey];
        }
        $filter = array(
            'skey' => SupermanUtil::get_skey($skey, $params),
        );
        $row = $this->fetch($filter);
        if ($row) {
            $data[$skey] = $unserialize?iunserializer($row['svalue']):$row['svalue'];
        }
        return isset($data[$skey])&&!empty($data[$skey])?$data[$skey]:array();
    }
}
