<?php
/**
 * 【超人】超级商城模块微站定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class table_superman_mall_ad extends SupermanMallTable {
    public $pages = array(
        '1' => '首页',
        '2' => '分类',
        '3' => '购物车',
        '4' => '我的',
        '5' => '商品列表',
        '6' => '商品详情',
        '7' => '我的订单',
        '8' => '订单详情',
        '9' => '关注商品',
        '10' => '浏览记录',
    );
    public $types = array(
        '1' => '幻灯图',
        '2' => '图片',
        '3' => '链接',
        '4' => '文字',
        '5' => '代码',
    );
    public $positions = array();
	public function __construct() {
		$this->_table = 'superman_mall_ad';
        $this->positions = array(
            '1' => array(
                'id' => 1,
                'pageid' => $this->get_pageid('首页'),
                'typeid' => $this->get_type_id('链接'),
                'title' => '头条',
                'enable' => 1,
            ),
            '2' => array(
                'id' => 2,
                'pageid' => $this->get_pageid('首页'),
                'typeid' => $this->get_type_id('幻灯图'),
                'title' => '顶部幻灯图',
                'enable' => 1,
            )
        );
	}
    public function get_positions() {
        $data = array();
        foreach ($this->pages as $pageid=>$title) {
            $data[] = array(
                'id' => $pageid,
                'title' => $title,
                'childs' => $this->get_positions_by_pageid($pageid),
            );
        }
        return $data;
    }
    public function get_positions_by_pageid($pageid) {
        $data = array();
        foreach ($this->positions as $item) {
            if ($item['pageid'] == $pageid) {
                $data[] = $item;
            }
        }
        return $data;
    }
    public function get_positions_by_id($id) {
        $data = array();
        foreach ($this->positions as $item) {
            if ($item['id'] == $id) {
                $data[] = $item;
            }
        }
        return $data;
    }
    public function get_pageid($title) {
        foreach ($this->pages as $key=>$val) {
            if ($title == $val) {
                return $key;
            }
        }
        return 0;
    }
    public function get_type_id($title) {
        foreach ($this->types as $key=>$val) {
            if ($title == $val) {
                return $key;
            }
        }
        return 0;
    }
    public function get_type_title($id) {
        foreach ($this->types as $key=>$val) {
            if ($id == $key) {
                return $val;
            }
        }
        return 'unknown';
    }
}
