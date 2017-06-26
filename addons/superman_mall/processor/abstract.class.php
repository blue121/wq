<?php
/**
 * 【超人】超级商城模块微站定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
Abstract class SupermanMallProcessor extends WeModuleProcessor {
    protected $rid;
    public function __construct() {
        parent::__construct();
        $this->rid = $this->rule;
    }
    public function respEmpty() {
        ob_clean();
        ob_start();
        echo '';
        ob_flush();
        ob_end_flush();
        exit(0);
    }
}