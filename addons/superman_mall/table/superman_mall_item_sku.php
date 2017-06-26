<?php
/**
 * 【超人】超级商城模块微站定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class table_superman_mall_item_sku extends SupermanMallTable {
	public function __construct() {
		$this->_table = 'superman_mall_item_sku';
	}
    /*public function fetch_by_skuid($id) {
        $data = $this->fetch($id);
        if ($data) {
            if ($data['valueids']) {
                $data['valueids'] = explode(',', $data['valueids']);
                $data['item_attr'] = M::t('superman_mall_item_attr')->fetchall_by_valueids($data['valueids'], '', 0, -1, 'id');
            }
        }
        return $data;

        //SELECT s.*,v.value,a.title FROM ve_superman_mall_item_sku AS s LEFT JOIN ve_superman_mall_item_value AS v ON s.vid1=v.id

        //SELECT a.*,b.title as attr_title FROM `ve_superman_mall_item_sku` as a LEFT JOIN ve_superman_mall_item_attr as b ON a.attrid=b.id WHERE a.id IN(1,2,3) ORDER BY b.displayorder DESC, a.id DESC
        $sql = "SELECT a.*,b.title AS attr_title FROM ".tablename($this->_table)." AS a LEFT JOIN ";
        $sql .= tablename('superman_mall_item_attr')." AS b ON a.attrid=b.id";
        $sql .= " WHERE a.id IN (".implode(',', $ids).")";
        $sql .= " ORDER BY b.displayorder DESC, a.id DESC";
        $data = pdo_fetchall($sql);
        return $data;
    }*/
    /*public function fetchall_by_itemid($itemids = array()) {
        $data = array();
        if (!$itemids) {
            return $data;
        }
        //SELECT a.*,b.title as attr_title FROM `ve_superman_mall_item_sku` as a LEFT JOIN ve_superman_mall_item_attr as b ON a.attrid=b.id WHERE a.itemid IN(1,2,3) ORDER BY b.displayorder DESC, a.id DESC
        $sql = "SELECT a.*,b.title AS attr_title FROM ".tablename($this->_table)." AS a LEFT JOIN ";
        $sql .= tablename('superman_mall_item_attr')." AS b ON a.attrid=b.id";
        $sql .= " WHERE a.itemid IN (".implode(',', $itemids).")";
        $sql .= " ORDER BY b.displayorder DESC, a.id DESC";
        $data = pdo_fetchall($sql);
        return $data;
    }*/
}
