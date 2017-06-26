<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');

class Superman_mallModule extends WeModule {
    public function fieldsFormDisplay($rid = 0) {
        //do nothing
        include $this->template('web/rule');
    }
    public function fieldsFormValidate($rid = 0) {
        //do nothing
    }
    public function fieldsFormSubmit($rid) {
        //do nothing
    }
    public function ruleDeleted($rid) {
        //do nothing
        return true;
    }
    public function settingsDisplay($settings) {
        //do nothing
        //include $this->template('web/setting');
    }
//    public function welcomeDisplay() {
//        @header('Location: '.$this->createWebUrl('dashboard', array('act' => 'display')));
//        exit;
//    }
}