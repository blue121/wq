<?php
/**
 * 【超人】超级商城模块
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
class SupermanTaobao {
    private $api = array(
        'detail' => 'http://hws.m.taobao.com/cache/wdetail/5.0/?id=%s',
        'desc' => 'http://hws.m.taobao.com/cache/wdesc/5.0/?id=%s',
    );
    protected $uniacid, $itemid;
    public function __construct($uniacid, $itemid){
        $this->uniacid = $uniacid;
        $this->itemid = $itemid;
    }
    public function fetch(){
        $data = array();
        load()->func('communication');
        $url = sprintf($this->api['detail'], $this->itemid);
        $ret = ihttp_get($url);
        if (empty($ret['content'])) {
            message('未获取到商品数据，请刷新重试！', referer(), 'warning');
        }
        $content = json_decode($ret['content'], true);
        if (!is_array($content)) {
            message('解析商品数据错误！', referer(), 'error');
        }
        $data = $content['data'];
        $itemInfoModel = $data['itemInfoModel'];

        $item = array(
            'title' => $itemInfoModel['title'],
        );
        //获取商品详情
        $item['description'] = '';
        $url = sprintf($this->api['desc'], $this->itemid);
        $ret = ihttp_get($url);
        if (!is_error($ret)) {
            $ret['content'] = iconv('gbk', 'utf-8', $ret['content']);
            $start = strpos($ret['content'], "tfsContent : '");
            if ($start !== false) {
                $start += 14;
                $len = strpos($ret['content'], "anchors :") - $start;
                $content = substr($ret['content'], $start, $len);
                $content = trim($content);
                $content = rtrim($content, "',");
                $item['description'] = $content;
            }
        }

        //初始化图片目录
        $path = 'images/'.$this->uniacid.'/'.date('Y/m/');
        mkdirs(ATTACHMENT_ROOT.$path);

        //获取商品图片
        if ($itemInfoModel['picsPath']) {
            $img_url = array();
            foreach ($itemInfoModel['picsPath'] as $url) {
                $img_data = file_get_contents($url);
                if ($img_data != '') {
                    $ext = pathinfo($url, PATHINFO_EXTENSION);
                    $ext = strtolower($ext);
                    $filename = file_random_name(ATTACHMENT_ROOT.$path, $ext);
                    file_put_contents(ATTACHMENT_ROOT.$path.$filename, $img_data);
                    $img_url[] = $path.$filename;
                }
            }
            $item['img_url'] = $img_url;
        }

        //获取商品参数
        $params = array();
        if (isset($data['props'])) {
            $props = $data['props'];
            foreach ($props as $pp) {
                $params[] = array("title" => $pp['name'], "value" => $pp['value']);
            }
        }
        $item['params'] = $params;
        $specs = array();
        $options = array();
        //获取商品sku
        if (isset($data['skuModel'])) {
            $skuModel = $data['skuModel'];
            if (isset($skuModel['skuProps'])) {
                $skuProps = $skuModel['skuProps'];
                foreach ($skuProps as $prop) {
                    $spec_items = array();
                    foreach ($prop['values'] as $spec_item) {
                        $spec_items[] = array('valueId' => $spec_item['valueId'], 'title' => $spec_item['name']);
                    }
                    $spec = array("propId" => $prop['propId'], "title" => $prop['propName'], "items" => $spec_items);
                    $specs[] = $spec;
                }
            }
            if (isset($skuModel['ppathIdmap'])) {
                $ppathIdmap = $skuModel['ppathIdmap'];
                foreach ($ppathIdmap as $key => $skuId) {
                    $option_specs = array();
                    $m = explode(";", $key);
                    foreach ($m as $v) {
                        $mm = explode(":", $v);
                        $option_specs[] = array("propId" => $mm[0], "valueId" => $mm[1]);
                    }
                    $options[] = array("option_specs" => $option_specs, "skuId" => $skuId, "stock" => 0, "marketprice" => 0, "specs" => "");
                }
            }
        }
        $item['specs'] = $specs;
        $stack = $data['apiStack'][0]['value'];
        $value = json_decode($stack, true);
        $data1 = $value['data'];
        $itemInfoModel1 = $data1['itemInfoModel'];
        $item['price'] = $itemInfoModel1['priceUnits'][0]['price'];
        $item['total'] = $itemInfoModel1['quantity'];
        if (isset($data1['skuModel'])) {
            $skuModel1 = $data1['skuModel'];
            if (isset($skuModel1['skus'])) {
                $skus = $skuModel1['skus'];
                foreach ($skus as $key => $val) {
                    $sku_id = $key;
                    foreach ($options as &$o) {
                        if ($o['skuId'] == $sku_id) {
                            $o['stock'] = $val['quantity'];
                            foreach ($val['priceUnits'] as $p) {
                                $o['marketprice'] = $p['price'];
                            }
                            $titles = array();
                            foreach ($o['option_specs'] as $osp) {
                                foreach ($specs as $sp) {
                                    if ($sp['propId'] == $osp['propId']) {
                                        foreach ($sp['items'] as $spitem) {
                                            if ($spitem['valueId'] == $osp['valueId']) {
                                                $titles[] = $spitem['title'];
                                            }
                                        }
                                    }
                                }
                            }
                            $o['title'] = $titles;
                        }
                    }
                    unset($o);
                }
            }
        } else {
            $mprice = 0;
            foreach ($itemInfoModel1['priceUnits'] as $p) {
                $mprice = $p['price'];
            }
            $item['marketprice'] = $mprice;
        }
        $item['options'] = $options;
        return $item;
    }
}