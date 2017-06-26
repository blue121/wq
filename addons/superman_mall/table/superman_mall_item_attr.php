<?php
/**
 * 【超人】超级商城模块微站定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class table_superman_mall_item_attr extends SupermanMallTable {
	public function __construct() {
		$this->_table = 'superman_mall_item_attr';
	}
    public function fetchall_by_valueids($valueids = array()) {
        $data = array();
        if (!$valueids) {
            return $data;
        }
        $sql = "SELECT a.title,v.value,v.attrid,v.id AS valueid FROM ".tablename($this->_table)." AS a RIGHT JOIN ";
        $sql .= tablename('superman_mall_item_value')." AS v ON a.id=v.attrid";
        $sql .= " WHERE v.id IN (".implode(',', $valueids).")";
        $sql .= " ORDER BY a.displayorder DESC, a.id DESC";
        $data = pdo_fetchall($sql);
        return $data;
    }
}
