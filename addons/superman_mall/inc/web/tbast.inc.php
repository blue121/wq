<?php
/**
 * 【超人】超级商城模块定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class Superman_mall_doWebTbast extends Superman {
	public function __construct() {
		parent::__construct();
        parent::init();
        $this->check_user_permission('superman_mall_menu_plugin');
        if (defined('IN_SUPERMAN_MALL_ADMIN')) {
            $this->do_shop_admin();
        } else {
            $this->do_admin();
        }
	}
    public function do_admin() {
        global $_W, $_GPC;
        if (isset($this->shop['id']) && $this->shop['id']) {
            $plugin_permission = $this->check_plugin_permission('tbast', $this->shop['id']);
        }
        $act = in_array($_GPC['act'], array('display'))?$_GPC['act']:'display';
        $nav['title'] = '淘宝助手';
        if ($act == 'display') {
            $this->check_web_shop();
            //一级分类加载
            $filter = array(
                'uniacid' => $_W['uniacid'],
                'pid' => 0,
            );
            $pcids = M::t('superman_mall_item_category')->fetchall($filter, '', 0, -1);
            if (checksubmit()) {
                $this->do_fetch();
                message('导入成功！', $this->createWebUrl('item', array('act' => 'display', 'status' => 'offshelf')), 'success');
            }
        }
        include $this->template('tbast/index');
    }
    public function do_shop_admin() {
        global $_W, $_GPC;
        $plugin_permission = $this->check_plugin_permission('tbast', $this->shop['id']);
        $act = in_array($_GPC['act'], array('display'))?$_GPC['act']:'display';
        $nav['title'] = '淘宝助手';
        if ($act == 'display') {
            //一级分类加载
            $filter = array(
                'uniacid' => $_W['uniacid'],
                'pid' => 0,
            );
            $pcids = M::t('superman_mall_item_category')->fetchall($filter, '', 0, -1);
            if (checksubmit()) {
                $this->do_fetch();
                message('导入成功！', $this->createWebUrl('item', array('act' => 'display', 'status' => 'offshelf')), 'success');
            }
        }
        include $this->template('tbast/index');
    }
    private function do_fetch() {
        require MODULE_ROOT.'/class/taobao.class.php';
        global $_W, $_GPC;
        $keyword = $_GPC['keyword'];
        if (empty($keyword)) {
            message('未填写采集商品数据！', referer(), 'error');
        }
        $itemids = $this->get_itemids($keyword);
        if ($itemids) {
            foreach ($itemids as $itemid) {
                $tb_url = 'https://item.taobao.com/item.htm?id='.$itemid;
                if (empty($itemid)) {
                    WeUtility::logging('error', '[superman_mall:tbast.inc]do_fetch,未找到商品id，请检查商品链接是否填写错误！');
                    continue;
                }
                $taobao = new SupermanTaobao($_W['uniacid'], $itemid);
                $data = $taobao->fetch();
                //商品规格存在时，保存
                if (isset($data['specs'])) {
                    $sku = array();
                    foreach ($data['specs'] as $spec) {
                        //查询规格名是否重复
                        $filter = array(
                            'uniacid' => $_W['uniacid'],
                            'shopid' => $this->shop['id'],
                            'title' => $spec['title'],
                        );
                        $attr = M::t('superman_mall_item_attr')->fetch($filter);
                        if ($attr) {
                            $attrid = $attr['id'];
                        } else {
                            $attrid = M::t('superman_mall_item_attr')->insert($filter);
                        }
                        $sku[$spec['propId']] = array(
                            'attrid' => $attrid,
                            'values' => array(),
                        );
                        //保存规格名
                        if ($attrid) {
                            foreach ($spec['items'] as $item) {
                                //保存规格值
                                $filter = array(
                                    'attrid' => $attrid,
                                    'value' => $item['title']
                                );
                                $value = M::t('superman_mall_item_value')->fetch($filter);
                                if ($value) {
                                    $valueid = $value['id'];
                                } else {
                                    $valueid = M::t('superman_mall_item_value')->insert($filter);
                                }

                                $sku[$spec['propId']]['values'][$item['valueId']] = array(
                                    'valueid' => $valueid,
                                    'title' => $item['title']
                                );
                            }
                        }
                    }
                }
                $itemdata = array(
                    'uniacid' => $_W['uniacid'],
                    'shopid' => $this->shop['id'],
                    'userid' => $_W['uid'],
                    'type' => 1,
                    'title' => $data['title'],
                    'ccid' => $_GPC['ccid'],
                    'cid' => $_GPC['cid'],
                    'pcid' => $_GPC['pcid'],
                    'cover' => isset($data['img_url'])?$data['img_url'][0]:'',
                    'album' => isset($data['img_url'])?iserializer($data['img_url']):'',
                    'description' => $data['description'],  //有点问题
                    'postage' => 0,
                    'extend' => iserializer(array('tb_url' => $tb_url)),
                    'market_price' => $data['price'],
                    'price' => $data['price'],
                    'total' => $data['total'],
                    'minus_total' => 1,
                    'createtime' => TIMESTAMP,
                    'updatetime' => TIMESTAMP,
                );
                $new_id = M::t('superman_mall_item')->insert($itemdata);
                if ($new_id) {
                    //商品属性保存
                    if ($data['params']) {
                        foreach ($data['params'] as $k=>$param) {
                            M::t('superman_mall_item_params')->insert(array(
                                'itemid' => $new_id,
                                'name' => $param['title'],
                                'value' => $param['value'],
                                'displayorder' => count($data['params']) - $k,
                            ));
                        }
                    }
                    //商品sku保存
                    if ($data['options'] && isset($sku)) {
                        foreach ($data['options'] as $option) {
                            $valueids = array();
                            foreach ($option['option_specs'] as $op) {
                                $valueids[] = $sku[$op['propId']]['values'][$op['valueId']]['valueid'];
                            }
                            sort($valueids);
                            $skudata = array(
                                'itemid' => $new_id,
                                'valueids' => implode(',', $valueids),
                                'market_price' => $option['marketprice'],
                                'price' => $option['marketprice'],
                                'total' => $option['stock'],
                            );
                            M::t('superman_mall_item_sku')->insert($skudata);
                        }
                    }
                }
            }
        }
    }
    private function get_itemids($keyword) {
        $arr = explode("\n", $keyword);
        foreach ($arr as &$v) {
            if (is_numeric($v)) {
                continue;
            }
            preg_match('/id=(\d+)/i', $v, $matches);
            $v = !empty($matches[1])?$matches[1]:'';
            unset($v);
        }
        return $arr;
    }
}
$obj = new Superman_mall_doWebTbast;