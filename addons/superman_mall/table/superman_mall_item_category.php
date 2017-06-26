<?php
/**
 * 【超人】超级商城模块微站定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class table_superman_mall_item_category extends SupermanMallTable {
	public function __construct() {
		$this->_table = 'superman_mall_item_category';
        $this->_cache_key = 'superman_mall_item_category';
	}
    public function clear_cache($uniacid) {
        $key = $this->key('uniacid:'.$uniacid);
        $this->cache_write($key, iserializer(array()));
        if (defined('SUPERMAN_DEVELOPMENT')) {
            WeUtility::logging('debug', '[superman_mall_item_category] clear cache, key='.$key);
        }
    }
    public function fetchall_recurse($filter, $orderby = '', $start = 0, $pagesize = 10,
                                     $keyfiled = '', $ignore_cache = false) {
        $data = array();
        if (!$ignore_cache) {
            $key = $this->key('uniacid:'.$filter['uniacid']);
            $data = $this->cache_read($key, true);
            if (!empty($data)) {
                if (defined('SUPERMAN_DEVELOPMENT')) {
                    WeUtility::logging('debug', '[superman_mall_item_category] data from memcache, key='.$key);
                }
                return $data;
            }
        }
        $data = $this->recurse($filter, 0, $orderby, $start, $pagesize, $keyfiled);
        if (!empty($data)) {
            if (!$ignore_cache) {
                $this->cache_write($key, iserializer($data));
            } else {
                $this->cache_write($key, ''); //clear cache
            }
            if (defined('SUPERMAN_DEVELOPMENT')) {
                WeUtility::logging('debug', '[superman_mall_item_category] data from mysql, key='.$key);
            }
        }
        //print_r($data);die;
        return $data;
    }
    private function recurse($filter, $pid = 0, $orderby = '', $start = 0, $pagesize = 10, $keyfiled = '') {
        $filter['pid'] = $pid;
        $list = parent::fetchall($filter, $orderby, $start, $pagesize, $keyfiled);
        if ($list) {
            usort($list, array(SupermanUtil, 'sort_displayorder_desc'));
            foreach ($list as $row) {
                $row['childs'] = $this->recurse($filter, $row['id'], $orderby, $start, $pagesize, $keyfiled);
                $data[] = $row;
            }
            return $data;
        }
    }
}