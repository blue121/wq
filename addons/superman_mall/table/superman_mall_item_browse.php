<?php
/**
 * 【超人】超级商城模块微站定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class table_superman_mall_item_browse extends SupermanMallTable {
	public function __construct() {
		$this->_table = 'superman_mall_item_browse';
	}
    public function clean($uid, $total = 100) { //先进先出
        $filter = array(
            'uid' => $uid,
        );
        $orderby = 'ORDER BY dateline ASC';
        $rows = $this->fetchall($filter, $orderby, 0, $total);
        if ($rows) {
            $row = array_pop($rows);
            $sql = "DELETE FROM ".tablename($this->_table)." WHERE id>{$row['id']}";
            return pdo_query($sql);
        }
        return false;
    }
}