<?php
/**
 * 【超人】超级商城模块微站定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
/*
 * Usage:
 *  $express = new SupermanMallExpress('kuaidi100', '', '900138230728');
 *  $list = $express->query();
 */
class SupermanMallExpress {
    public $api = array(
        'kuaidi100' => array(
            /*
             * num => 快递单号
             */
            'autonumber' => 'http://m.kuaidi100.com/autonumber/auto?num=%s',
            /*
             * type => 快递公司别名
             * postid => 快递单号
             * temp => 随机数
             */
            'mobile_query' => 'http://m.kuaidi100.com/query?type=%s&postid=%s&id=&valicode=&temp=%s',
            /*
             * rand => 随机数
             * id => 快递公司别名
             * postid => 快递单号
             */
            'wap_query' => 'http://wap.kuaidi100.com/wap_result.jsp?rand=%s&id=%s&fromWeb=&postid=%s',
        ),
    );
    protected $api_key;  //接口类型
    protected $express_com;  //快递公司别名
    protected $express_no;  //快递单号
    public function __construct($api_key, $express_com, $express_no) {
        $this->api_key = $api_key;
        $this->express_com = $express_com;
        $this->express_no = $express_no;
    }
    public function query() {
        if (!in_array($this->api_key, array_keys($this->api))) {
            trigger_error('express api key('.$this->api_key.') error', E_USER_WARNING);
        }
        $method = "{$this->api_key}_query";
        return $this->$method();
    }
    private function kuaidi100_query() {
        load()->func('communication');
        if (!$this->express_no) {
            return false;
        }
        if (!$this->express_com) {  //快递单号
            $url = sprintf($this->api[$this->api_key]['autonumber'], $this->express_no);
            $response = ihttp_get($url);
            if (is_error($response)) {
                return false;
            }
            $content = $response['content']?json_decode($response['content'], true):array();
            if (empty($content)) {
                return false;
            }
            foreach ($content as $com) {
                $url = sprintf($this->api[$this->api_key]['mobile_query'], $com['comCode'], $this->express_no, random(4, 1));
                $res = ihttp_get($url);
                if (is_error($res)) {
                    continue;
                }
                $info = $res['content']?json_decode($res['content'], true):array();
                if (empty($info)) {
                    continue;
                }
                if (!empty($info['data'])) {
                    return $info['data'];
                }
            }
            return false;
        } else {    //快递公司别名 && 快递单号
            $url = sprintf($this->api[$this->api_key]['wap_query'], random(4, 1), $this->express_com, $this->express_no);
            $response = ihttp_get($url);
            preg_match_all('/\<p\>&middot;(.*)\<\/p\>/U', $response['content'], $arr);
            if (empty($arr)) {
                return array();
            }
            $list = array_reverse($arr[1]);
            if (empty($list)) {
                return array();
            }
            $data = array();
            foreach ($list as $k => $v) {
                $cut = explode("<br /> ", $v);
                $data[$k] = array(
                    'time' => $cut[0],
                    'context' => $cut[1]
                );
            }
            return $data;
        }
    }
}