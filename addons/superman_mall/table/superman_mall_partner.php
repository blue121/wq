<?php
/**
 * 【超人】超级商城模块微站定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class table_superman_mall_partner extends SupermanMallTable {
	public function __construct() {
		$this->_table = 'superman_mall_partner';
	}
    public function compute_distance($id1, $id2, $level = 3) {  //上级 id1 下级 id2
        $distance = 0;
        if (!$this->fetch($id1)) {
            return $distance;
        }
        $exit = true;
        $loop = 0;
        do {
            if ($loop >= $level) {
                $distance = -1;
                break;
            }
            $row2 = $this->fetch($id2);
            if (!$row2) {
                $exit = true;
            } else {
                if ($row2['recommendid'] == $id1) {
                    $exit = true;
                    $distance += 1;
                    $loop += 1;
                    continue;
                } else {
                    $id2 = $row2['recommendid'];
                    $exit = false;
                }
                $distance += 1;
            }
            $loop += 1;
        } while(!$exit);
        return $distance;
    }
}