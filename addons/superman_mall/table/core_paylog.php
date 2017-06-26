<?php
/**
 * 【超人】超级商城模块微站定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class table_core_paylog extends SupermanMallTable {
    public function __construct() {
        $this->_table = 'core_paylog';
        $this->_pk = 'plid';
    }
}