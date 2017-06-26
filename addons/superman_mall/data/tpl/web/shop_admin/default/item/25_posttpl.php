<?php defined('IN_IA') or exit('Access Denied');?><style>
    .star {
        color: red;
        margin-right: 5px;
        font-weight: bold;
    }
    ul, li {
        list-style: none;
        margin: 0;
        padding: 0;
    }
    .item_spec {
        padding-bottom: 10px;
    }
    .item_attr, .del_item_attr{
        margin-left: -15px;
        margin-right: -15px;
    }
    .item_value {
        float: left;
        margin: 10px 10px 0 0;
    }
    .item_value label {
        font-weight: normal;
        margin-bottom: 0;
    }
    #item_sku_wrap {
        padding:0;
    }
    #item_sku_wrap th, #item_sku_wrap td {
        text-align: center;
    }
    .del_item_attr {
        line-height: 30px;
        color: #f00;
        cursor: pointer;
        margin-left: 30px;
    }
    .qrcode_wrap {
        position: fixed;
        top: 10rem;
        right: 2rem;
        z-index: 1;
    }
    .qrcode_wrap img {
        width:11rem;
    }
    .qrcode_wrap > span {
        display: block;
        text-align: center;
    }
</style>
<?php  if($id > 0) { ?>
<div class="qrcode_wrap">
    <img src="<?php  echo $this->createWebUrl('qrcode', array('content' => urlencode($itemurl)))?>" onerror="this.src='<?php  echo $this->superman_placeholder?>'"/>
    <span>商品二维码入口</span>
    <i class="fa fa-close" style="position: absolute;top: 0;right: 0; cursor: pointer;" onclick="$(this).parent().hide()"></i>
</div>
<?php  } ?>
<script>
    require.config({
        paths: {
            'md5': '<?php echo MODULE_URL;?>template/web/default/js/md5'
        }
    });
</script>
<form class="form-horizontal form" action="" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php  echo $item['id'];?>">
    <div class="panel panel-default">
        <div class="panel-heading">
            基本属性
        </div>
        <div class="panel-body">
            <?php  if($id > 0) { ?>
            <div class="form-group">
                <label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">访问地址</label>
                <div class="col-sm-8 col-md-8 col-xs-12">
                    <div class="form-control-static">
                        <a href="<?php  echo $itemurl;?>" target="_blank"><?php  echo $itemurl;?></a>
                    </div>
                    <span class="help-block">本网址为当前商品的唯一链接，可以拷贝到其他地方使用，二维码往右看</span>
                </div>
            </div>
            <?php  if(isset($item['extend']['tb_url']) && $item['extend']['tb_url']) { ?>
            <div class="form-group">
                <label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">淘宝地址</label>
                <div class="col-sm-8 col-md-8 col-xs-12">
                    <div class="form-control-static">
                        <a href="<?php  echo $item['extend']['tb_url'];?>" target="_blank"><?php  echo $item['extend']['tb_url'];?></a>
                    </div>
                    <span class="help-block">本商品为淘宝助手导入商品，可通过本网址查看淘宝商品信息
                        <br><a href="<?php  echo $this->createWebUrl('tbast', array('act' => 'display', 'keyword' => $item['extend']['tb_url']))?>">再次导入</a>
                    </span>
                </div>
            </div>
            <?php  } ?>
            <?php  } ?>
            <?php  if($item['id']) { ?>
            <div class="form-group">
                <label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">商品ID</label>
                <div class="col-sm-8 col-md-8 col-xs-12">
                    <div class="form-control-static"><?php  echo $item['id'];?></div>
                </div>
            </div>
            <?php  } ?>
            <div class="form-group">
                <label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label"><span class="star">*</span>商品标题</label>
                <div class="col-sm-8 col-md-8 col-xs-12">
                    <input type="text" class="form-control" placeholder="" name="title" value="<?php  echo $item['title'];?>">
                    <span class="help-block">最多30个字符</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label"><span class="star">*</span>商品分类</label>
                <div class="col-xs-12 col-sm-4 col-md-3 col-lg-3">
                    <select class="form-control" id="pcid" name="pcid" data-pcid="<?php  echo $item['pcid'];?>">
                        <option value="0">请选择一级分类</option>
                        <?php  if($pcids) { ?>
                        <?php  if(is_array($pcids)) { foreach($pcids as $pcid) { ?>
                        <option value="<?php  echo $pcid['id'];?>" <?php  if(isset($item['pcid']) && $item['pcid'] == $pcid['id']) { ?>selected="selected"<?php  } ?>>
                        <?php  echo $pcid['title'];?>
                        </option>
                        <?php  } } ?>
                        <?php  } ?>
                    </select>
                </div>
                <div class="col-xs-12 col-sm-4 col-md-3 col-lg-3">
                    <select class="form-control" id="cid" name="cid" data-cid="<?php  echo $item['cid'];?>">
                        <option value="0">请选择二级分类</option>
                    </select>
                </div>
                <div class="col-xs-12 col-sm-4 col-md-3 col-lg-3">
                    <select class="form-control" id="ccid" name="ccid" data-ccid="<?php  echo $item['ccid'];?>">
                        <option value="0">请选择三级分类</option>
                    </select>
                </div>
                <script>
                    require(['jquery', 'jquery.ui'], function($){
                        $('#pcid').change(function () {
                            var html = '<option value="0">请选择二级分类</option>';
                            var pcid = $(this).val();
                            if (pcid == 0) {
                                $('#cid').html(html);
                                $('#ccid').html('<option value="0">请选择三级分类</option>');
                                return;
                            }
                            $.ajax({
                                url:'<?php  echo $this->createWebUrl("item", array("act" => "getcate"))?>',
                                type: 'post',
                                data: 'cid='+pcid,
                                dataType: 'json',
                                success: function(resp){
                                    if (resp.length > 0) {
                                        var cid = $('#cid').attr('data-cid');
                                        var selected = '';
                                        for(var i = 0; i< resp.length; i++){
                                            if (cid == resp[i]['id']) {
                                                selected = 'selected';
                                            } else {
                                                selected = '';
                                            }
                                            html += '<option '+selected+' value="'+resp[i]['id']+'">'+resp[i]['title']+'</option>';
                                        }
                                    }
                                    $('#cid').html(html).trigger('change');
                                }
                            });
                        });
                        $('#pcid').trigger('change');
                        $('#cid').change(function () {
                            var html = '<option value="0">请选择三级分类</option>';
                            var cid = $(this).val();
                            if (cid == 0) {
                                $('#ccid').html(html);
                                return;
                            }
                            $.ajax({
                                url:'<?php  echo $this->createWebUrl("item", array("act" => "getcate"))?>',
                                type: 'post',
                                data: 'cid='+cid,
                                dataType: 'json',
                                success: function(resp){
                                    if (resp.length > 0) {
                                        var ccid = $('#ccid').attr('data-ccid');
                                        var selected = '';
                                        for(var i = 0; i< resp.length; i++){
                                            if (ccid == resp[i]['id']) {
                                                selected = 'selected';
                                            } else {
                                                selected = '';
                                            }
                                            html += '<option '+selected+' value="'+resp[i]['id']+'">'+resp[i]['title']+'</option>';
                                        }
                                    }
                                    $('#ccid').html(html)
                                }
                            });
                        });
                    })
                </script>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">商品类型</label>
                <div class="col-sm-8 col-xs-12">
                    <label class="radio-inline">
                        <input type="radio" name="type" value="1" <?php  if($item['type'] == 1) { ?>checked="checked"<?php  } ?>>实物商品
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="type" value="2" <?php  if($item['type'] == 2) { ?>checked="checked"<?php  } ?>>虚拟商品
                    </label>
                    <span class="help-block">实物商品需要物流，虚拟商品无需物流。</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label"><span class="star">*</span>封面</label>
                <div class="col-sm-8 col-xs-12">
                    <?php  echo tpl_form_field_image('cover', $item['cover'])?>
                    <span class="help-block">推荐尺寸：100*100</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label"><span class="star">*</span>相册</label>
                <div class="col-sm-8 col-xs-12">
                    <?php  echo tpl_form_field_multi_image('album', $item['album'])?>
                    <span class="help-block">推荐尺寸：400*300</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">配送方式</label>
                <div class="col-sm-6 col-md-8 col-xs-12">
                    <div class="input-group">
                        <div class="radio">
                            <label class="radio-inline">
                                <input type="radio" name="delivery_mode" value="0" <?php  if($item['delivery_mode'] == 0) { ?>checked<?php  } ?>> 全部(快递+自提)
                            </label>
                        </div>
                        <div class="radio">
                            <label class="radio-inline">
                                <input type="radio" name="delivery_mode" value="1" <?php  if($item['delivery_mode'] == 1) { ?>checked<?php  } ?>> 快递
                            </label>
                        </div>
                        <div class="radio">
                            <label class="radio-inline">
                                <input type="radio" name="delivery_mode" value="2" <?php  if($item['delivery_mode'] == 2) { ?>checked<?php  } ?>> 自提
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">邮费设置</label>
                <div class="col-sm-8 col-xs-12">
                    <div class="radio">
                        <label>
                            <input type="radio" name="postage_select" value="1" <?php  if($item['postage_select'] == 1) { ?>checked<?php  } ?>>
                            统一邮费
                        </label>
                        <label class="input-inline">
                            <input type="text" class="form-control" placeholder="" name="postage" value="<?php  echo $item['postage'];?>" />
                        </label>
                        元
                        <span class="help-block"></span>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="postage_select" value="2" <?php  if($item['postage_select'] == 2) { ?>checked<?php  } ?>>
                            邮费模板
                        </label>
                        <label class="select-inline">
                            <select name="postage_tmplid" class="form-control" <?php  if($item['postage_select'] != 2) { ?>disabled<?php  } ?>>
                            <?php  if($postage_tmpl) { ?>
                            <?php  if(is_array($postage_tmpl)) { foreach($postage_tmpl as $value) { ?>
                            <option value="<?php  echo $value['id'];?>" <?php  if($item['postage_tmplid'] == $value['id']) { ?>selected<?php  } ?>><?php  echo $value['title'];?></option>
                            <?php  } } ?>
                            <?php  } ?>
                            </select>
                        </label>
                        <a href="<?php  echo $this->createWebUrl('postage', array('act' => 'post'));?>" target="_blank">
                            <span class="fa fa-plus"></span>
                            邮费模板
                        </a>&nbsp;&nbsp;
                        <a href="javascript:;" class="refresh_postage_tmpl">
                            <span class="fa fa-refresh"></span>
                            刷新
                        </a>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">状态</label>
                <div class="col-sm-6 col-md-8 col-xs-12">
                    <div class="input-group">
                        <label class="radio-inline">
                            <input type="radio" name="status" value="1" <?php  if($item['status'] == 1) { ?>checked<?php  } else if($item['status'] == 2) { ?>disabled<?php  } ?>> 上架
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="status" value="0" <?php  if($item['status'] == 0) { ?>checked<?php  } else if($item['status'] == 2) { ?>disabled<?php  } ?>> 下架
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="status" value="3" <?php  if($item['status'] == 3) { ?>checked<?php  } else if($item['status'] == 2) { ?>disabled<?php  } ?>> 隐藏
                        </label>
                    </div>
                    <?php  if($item['status'] == 2) { ?><div class="help-block">禁售商品不允许修改状态，请联系管理员</div><?php  } ?>
                </div>
            </div>
            <?php  if(!$id || $item['sku']) { ?>
            <div class="form-group">
                <label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">商品规格</label>
                <div class="col-sm-6 col-md-8 col-xs-12">
                    <label style="font-weight: normal">
                        <input type="checkbox" name="item_spec_switch" value="<?php  if(!$id||$item['sku']) { ?>1<?php  } else { ?>0<?php  } ?>" <?php  if(isset($item['sku'])&&$item['sku']) { ?>checked<?php  } ?>> 开启
                        <span style="font-size: 12px; color: #f00">（商品规格开启后，商品的库存和销售价格以商品规格设置为准）</span>
                    </label>
                    <div class="item_spec_setting" style="<?php  if(!$item['sku']) { ?>display: none<?php  } ?>">
                        <div class="clearfix">
                            <ul class="item_spec_wrap">
                                <?php  if($item['sku'] && $item_attr && $item['attr_title']) { ?>
                                <?php  if(is_array($item['attr_title'])) { foreach($item['attr_title'] as $title=>$value) { ?>
                                <li class="item_spec clearfix">
                                    <div class="col-md-6 clearfix">
                                        <select class="item_attr form-control pull-left" name="item_attr[]" data-index="1">
                                            <option value="0">请选择</option>
                                            <?php  if(is_array($item_attr)) { foreach($item_attr as $attr) { ?>
                                            <option value="<?php  echo $attr['id'];?>" <?php  if($title==$attr['title']) { ?>selected<?php  } ?>><?php  echo $attr['title'];?></option>
                                            <?php  } } ?>
                                        </select>
                                        <span class="glyphicon glyphicon-remove del_item_attr pull-left" aria-hidden="true" title="删除"></span>
                                    </div>
                                    <div class="col-md-12">
                                        <ul class="item_value_wrap">
                                            <?php  if(is_array($value)) { foreach($value as $val) { ?>
                                            <li class="item_value">
                                                <label>
                                                    <input type="checkbox" value="<?php  echo $val['id'];?>" <?php  if($val['checked']) { ?>checked<?php  } ?> data-value="<?php  echo $val['value'];?>"> <?php  echo $val['value'];?>
                                                </label>
                                            </li>
                                            <?php  } } ?>
                                        </ul>
                                    </div>
                                </li>
                                <?php  } } ?>
                                <?php  } ?>
                            </ul>
                        </div>
                        <button type="button" class="btn btn-info add_item_attr" title="选择规格" data-index="0">
                            <img width="16" height="16" src="<?php  echo $_W['siteroot'];?>/attachment/images/global/loading.gif" style="display: none"/>
                            <span class="glyphicon glyphicon-list" aria-hidden="true"></span> 选择规格
                        </button>
                        &nbsp;
                        <button type="button" class="btn btn-success refresh_item_attr" title="刷新规格表">
                            <span class="glyphicon glyphicon-refresh" aria-hidden="true"></span> 刷新规格表
                        </button>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label"></label>
                <div class="col-sm-12 col-md-10 col-xs-12">
                    <div class="panel panel-default" style="display: none">
                        <div id="item_sku_wrap" class="panel-body table-responsive"></div>
                        <div class="panel-footer">
                            <!--批量操作-->&nbsp;
                            批量操作：
                            <a href="#" class="btn btn-default btn-sm" data-toggle="modal" data-target=".total_modal">库存</a>
                            <a href="#" class="btn btn-default btn-sm" data-toggle="modal" data-target=".price_modal">销售价</a>
                            <a href="#" class="btn btn-default btn-sm" data-toggle="modal" data-target=".market_price_modal">市场价</a>
                            <a href="#" class="btn btn-default btn-sm" data-toggle="modal" data-target=".cost_price_modal">成本价</a>
                            <a href="#" class="btn btn-default btn-sm" data-toggle="modal" data-target=".weight_modal">重量</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php  } ?>
            <div class="form-group">
                <label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">总库存</label>
                <div class="col-sm-8 col-md-8 col-xs-12">
                    <input type="text" class="form-control" <?php  if($item['sku']) { ?>readonly<?php  } ?> name="total" value="<?php  echo $item['total'];?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">销售价</label>
                <div class="col-sm-8 col-md-8 col-xs-12">
                    <input type="text" class="form-control" name="price" value="<?php  echo $item['price'];?>">
                    <span class="help-block">开启商品规格后，由于每个规格销售价不同，所以此销售价仅作页面展示，订单价格以具体商品规格为准</span>
                    <span class="help-block">推荐使用最低销售价展示，修改规格销售价时，程序自动选择最低价格</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">重量(kg)</label>
                <div class="col-sm-8 col-md-8 col-xs-12">
                    <input type="text" class="form-control" name="weight" value="<?php  echo $item['weight'];?>">
                    <span class="help-block">开启商品规格后，按重量计算的邮费模板将按各个规格中设置的重量计算</span>
                    <span class="help-block">未开启商品规格时，按重量计算的邮费模板将按此处设置的重量计算</span>
                </div>
            </div>
        </div>
    </div>
    <?php  if(isset($this->plugin_setting['partner']) && $this->plugin_setting['partner'] && !is_error($partner_permission)) { ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            分销属性
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">是否开启分销</label>
                <div class="col-sm-6 col-md-8 col-xs-12">
                    <div class="input-group">
                        <label class="radio-inline">
                            <input type="radio" name="partner_open" value="1" <?php  if($item['partner_open']) { ?>checked<?php  } ?>> 是
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="partner_open" value="0" <?php  if(!$item['partner_open']) { ?>checked<?php  } ?>> 否
                        </label>
                    </div>
                    <span class="help-block">开启商品分销后，分销商推广该商品后，可以从成交订单中获取佣金</span>
                </div>
            </div>
            <div class="item_partner_attr" <?php  if(!$item['partner_open']) { ?>style="display: none"<?php  } ?>>
            <div class="alert alert-info clearfix" style="width: 85%; margin: 0 auto 20px;">
                <i class="fa fa-exclamation-circle pull-left"></i>
                <p class="pull-left">
                    开启自定义佣金后，可设置佣金比例或固定佣金，两种佣金方式只能选择一种<br>
                    如开启了商品规格时，不支持设置每个商品规格的佣金
                </p>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">自定义佣金</label>
                <div class="col-sm-6 col-md-8 col-xs-12">
                    <div class="input-group">
                        <label class="checkbox-inline">
                            <input type="checkbox" value="<?php  echo $item['partner_attr']['commission_custom'];?>" name="commission_custom" <?php  if($item['partner_attr']['commission_custom']) { ?>checked<?php  } ?>> 开启
                        </label>
                    </div>
                    <span class="help-block">未开启自定义佣金时，分销商佣金根据分销等级计算，自定义佣金优先级最高，可单独设置商品自定义佣金</span>
                </div>
            </div>
            <div class="item_custom_setting" <?php  if(!$item['partner_attr']['commission_custom']) { ?>style="display: none"<?php  } ?>>
            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 control-label">一级分销佣金</label>
                <div class="col-sm-6 col-md-8 col-xs-12">
                    <div class="input-group" style="margin-bottom: .5rem">
                        <input type="number" step="0.01" class="form-control" data-commission1-rate-max="<?php  echo $partner_setting['base']['commission1_rate_max'];?>" value="<?php  echo $item['partner_attr']['commission1_rate'];?>" name="commission1_rate">
                        <span class="input-group-addon">%</span>
                    </div>
                    <div class="input-group">
                        <input type="number" step="0.01" class="form-control" data-commission1-value-max="<?php  echo $partner_setting['base']['commission1_value_max'];?>" value="<?php  echo $item['partner_attr']['commission1_value'];?>" name="commission1_value">
                        <span class="input-group-addon">元</span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 control-label">二级分销佣金</label>
                <div class="col-sm-6 col-md-8 col-xs-12">
                    <div class="input-group" style="margin-bottom: .5rem">
                        <input type="number" step="0.01" class="form-control" data-commission2-rate-max="<?php  echo $partner_setting['base']['commission2_rate_max'];?>" value="<?php  echo $item['partner_attr']['commission2_rate'];?>" name="commission2_rate">
                        <span class="input-group-addon">%</span>
                    </div>
                    <div class="input-group">
                        <input type="number" step="0.01" class="form-control" data-commission2-value-max="<?php  echo $partner_setting['base']['commission2_value_max'];?>" value="<?php  echo $item['partner_attr']['commission2_value'];?>" name="commission2_value">
                        <span class="input-group-addon">元</span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 control-label">三级分销佣金</label>
                <div class="col-sm-6 col-md-8 col-xs-12">
                    <div class="input-group" style="margin-bottom: .5rem">
                        <input type="number" step="0.01" class="form-control" data-commission3-rate-max="<?php  echo $partner_setting['base']['commission3_rate_max'];?>" value="<?php  echo $item['partner_attr']['commission3_rate'];?>" name="commission3_rate">
                        <span class="input-group-addon">%</span>
                    </div>
                    <div class="input-group">
                        <input type="number" step="0.01" class="form-control" data-commission3-value-max="<?php  echo $partner_setting['base']['commission3_value_max'];?>" value="<?php  echo $item['partner_attr']['commission3_value'];?>" name="commission3_value">
                        <span class="input-group-addon">元</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">是否显示佣金</label>
            <div class="col-sm-6 col-md-8 col-xs-12">
                <div class="input-group">
                    <label class="radio-inline">
                        <input type="radio" name="commission_show" value="1" <?php  if($item['partner_attr']['commission_show']) { ?>checked<?php  } ?>> 显示
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="commission_show" value="0" <?php  if(!$item['partner_attr']['commission_show']) { ?>checked<?php  } ?>> 不显示
                    </label>
                </div>
                <span class="help-block">当分销商打开商品页时，可选择是否显示分销佣金，选择显示时，分销商将可以查看该商品的各级分销佣金，此开关优先级最高</span>
            </div>
        </div>
        <div class="form-group">
            <label class="col-xs-12 col-sm-2 col-md-2 control-label">分销商优惠价</label>
            <div class="col-sm-6 col-md-8 col-xs-12">
                <div class="input-group" style="margin-bottom: .5rem">
                    <input type="number" step="0.01" class="form-control" data-discount-rate-min="<?php  echo $partner_setting['base']['discount_rate_min'];?>" value="<?php  echo $item['partner_attr']['discount_rate'];?>" name="discount_rate">
                    <span class="input-group-addon">%</span>
                </div>
                <div class="input-group">
                    <input type="number" step="0.01" class="form-control" data-discount-value-max="<?php  echo $partner_setting['base']['discount_value_max'];?>" value="<?php  echo $item['partner_attr']['discount_value'];?>" name="discount_value">
                    <span class="input-group-addon">元</span>
                </div>
                <span class="help-block">分销商购买商品时的优惠价，支持优惠比例和固定方式，两种优惠方式只能选择一种，设置优惠价后，手机端显示内部价xx元</span>
                <span class="help-block">如分销商享受8折优惠，填写 80%</span>
                <span class="help-block">如分销商享受固定减10元优惠，填写 10元</span>
            </div>
        </div>
    </div>
    </div>
    </div>
    <?php  } ?>
    <?php  if(isset($this->plugin_setting['discount']) && $this->plugin_setting['discount'] && !is_error($discount_permission)) { ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            营销属性
        </div>
        <div class="panel-body">
            <?php  if(isset($discount_setting['credit']['cash_open']) && $discount_setting['credit']['cash_open'] == 1) { ?>
            <div class="form-group">
                <label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">最多抵现</label>
                <div class="col-sm-8 col-xs-12">
                    <div class="input-group ">
                        <input type="number" step="0.01" name="cash_credit" value="<?php  echo $item['cash_credit'];?>" class="form-control cash_credit">
						<span class="input-group-btn">
							<button class="btn btn-default" type="button"><?php  echo $creditname;?>&nbsp;=（<span class="money_total">0.00</span> 元）</button>
						</span>
                    </div>
                    <span class="help-block">设置商品最多可以使用的抵现积分</span>
                </div>
            </div>
            <?php  } ?>
        </div>
    </div>
    <?php  } ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            其它属性
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">排序</label>
                <div class="col-sm-8 col-xs-12">
                    <input type="text" class="form-control" placeholder="" name="displayorder" value="<?php  echo $item['displayorder'];?>">
                    <span class="help-block">由大到小，排序值越大越靠前</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">副标题</label>
                <div class="col-sm-8 col-xs-12">
                    <input type="text" class="form-control" placeholder="" name="subtitle" value="<?php  echo $item['subtitle'];?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">货号</label>
                <div class="col-sm-8 col-xs-12">
                    <input type="text" class="form-control" placeholder="" name="number" value="<?php  echo $item['number'];?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">减库存方式</label>
                <div class="col-sm-9 col-xs-12">
                    <label class="radio-inline">
                        <input type="radio" name="minus_total" value="1" <?php  if($item['minus_total']==1) { ?>checked<?php  } ?>>付款减
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="minus_total" value="2" <?php  if($item['minus_total']==2) { ?>checked<?php  } ?>>拍下减
                    </label>
                    <span class="help-block">选择付款减库存时，提交订单页面将提示如下信息：订单创建后，请尽快支付，否则可能出现库存不足</span>
                    <span class="help-block">选择拍下减库存时，由于提交订单未支付问题，请注意补充库存</span>
                    <span class="help-block" style="color: red">当设置为付款减时，由于支付时间差问题，存在超出库存的销售订单，如对库存有严格要求，可使用拍下减</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">是否有发票</label>
                <div class="col-sm-6 col-md-8 col-xs-12">
                    <div class="input-group">
                        <label class="radio-inline">
                            <input type="radio" name="isreceipt" value="1" <?php  if($item['isreceipt']) { ?>checked<?php  } ?>> 是
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="isreceipt" value="0" <?php  if(!$item['isreceipt']) { ?>checked<?php  } ?>> 否
                        </label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">是否保修</label>
                <div class="col-sm-6 col-md-8 col-xs-12">
                    <div class="input-group">
                        <label class="radio-inline">
                            <input type="radio" name="isrepair" value="1" <?php  if($item['isrepair']) { ?>checked<?php  } ?>> 是
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="isrepair" value="0" <?php  if(!$item['isrepair']) { ?>checked<?php  } ?>> 否
                        </label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">是否可核销</label>
                <div class="col-sm-6 col-md-8 col-xs-12">
                    <div class="input-group">
                        <label class="radio-inline">
                            <input type="radio" name="ischeckout" value="1" <?php  if($item['ischeckout']) { ?>checked<?php  } ?>> 是
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="ischeckout" value="0" <?php  if(!$item['ischeckout']) { ?>checked<?php  } ?>> 否
                        </label>
                    </div>
                    <span class="help-block">设置可核销后，提交的订单默认具有可核销属性</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">是否需要清关</label>
                <div class="col-sm-6 col-md-8 col-xs-12">
                    <div class="input-group">
                        <label class="radio-inline">
                            <input type="radio" name="customs_clearance" value="1" <?php  if($item['customs_clearance']) { ?>checked<?php  } ?>> 是
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="customs_clearance" value="0" <?php  if(!$item['customs_clearance']) { ?>checked<?php  } ?>> 否
                        </label>
						<span class="help-block">通过海外购买商品时，海关清关需收货人身份证信息，如选择需要清关时，包含商品的订单提交时，将需要顾客填写身份证信息
</span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">简介</label>
                <div class="col-sm-8 col-xs-12">
                    <textarea class="form-control" rows="8" name="summary" placeholder=""><?php  echo $item['summary']?></textarea>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">详细描述</label>
                <div class="col-sm-8 col-xs-12">
                    <?php  if(defined('IN_SUPERMAN_MALL_ADMIN')) { ?>
                    <?php  echo superman_tpl_ueditor('description', $item['description'])?>
                    <?php  } else { ?>
                    <?php  echo tpl_ueditor('description', $item['description'])?>
                    <?php  } ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">市场价</label>
                <div class="col-sm-8 col-md-8 col-xs-12">
                    <input type="text" class="form-control" name="market_price" value="<?php  echo $item['market_price'];?>">
                    <span class="help-block"></span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">成本价</label>
                <div class="col-sm-8 col-md-8 col-xs-12">
                    <input type="text" class="form-control" name="cost_price" value="<?php  echo $item['cost_price'];?>">
                    <span class="help-block"></span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">产品参数</label>
                <div class="col-sm-8 col-xs-12">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th width="25"></th>
                            <th width="200">属性名</th>
                            <th width="600">属性值</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody id="list_wrap" class="ui-sortable">
                        <?php  if($all_params) { ?>
                        <?php  if(is_array($all_params)) { foreach($all_params as $param) { ?>
                        <tr>
                            <td>
                                <a href="javascript:;" class="fa fa-move" title="按住鼠标左键，拖动调整顺序">
                                    <i class="fa fa-arrows"></i>
                                </a>
                            </td>
                            <td>
                                <div class="form-group" style="margin-bottom: 0">
                                    <div class="col-xs-12">
                                        <input name="param_id[]" value="<?php  echo $param['id'];?>" type="hidden" class="form-control"/>
                                        <input name="param_name[]" value="<?php  echo $param['name'];?>" type="text" class="form-control" placeholder=""/>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="form-group" style="margin-bottom: 0">
                                    <div class="col-xs-12">
                                        <input name="param_value[]" value="<?php  echo $param['value'];?>" type="text" class="form-control" placeholder=""/>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <a href="javascript:;" class="del_param_link" onclick="delItem(this)" title="删除" data-id="<?php  echo $param['id'];?>">
                                    <i class='fa fa-remove'></i>
                                </a>
                            </td>
                        </tr>
                        <?php  } } ?>
                        <?php  } ?>
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-warning add_params" title="添加属性">
                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> 添加属性
                    </button>
                </div>
            </div>
        </div>
        <?php  if($_W['isfounder']) { ?>
        <div class="panel-footer">
            上次操作人: <?php  echo $item['user']['username'];?>
        </div>
        <?php  } ?>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <input name="submit" type="submit" value="提交" class="btn btn-primary col-lg-1">
            <input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
        </div>
    </div>
</form>
<!--批量操作模态框-->
<div class="modal fade total_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title">批量设置</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="control-label text-center" style="display: inline-block; width:18%">库存:</label>
                    <input type="text" class="form-control batch_input" name="batch_total" style="width: 80%; display: inline-block">
                </div>
            </div>
            <div class="modal-footer" style="text-align: left">
                <button type="button" class="btn btn-primary btn_total_submit" data-dismiss="modal">确认</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade price_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title">批量设置</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="control-label text-center" style="display: inline-block; width:18%">销售价:</label>
                    <input type="text" class="form-control batch_input" name="batch_price" style="width: 80%; display: inline-block">
                </div>
            </div>
            <div class="modal-footer" style="text-align: left">
                <button type="button" class="btn btn-primary btn_price_submit" data-dismiss="modal">确认</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade market_price_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title">批量设置</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="control-label text-center" style="display: inline-block; width:18%">市场价:</label>
                    <input type="text" class="form-control batch_input" name="batch_market_price" style="width: 80%; display: inline-block">
                </div>
            </div>
            <div class="modal-footer" style="text-align: left">
                <button type="button" class="btn btn-primary btn_market_price_submit" data-dismiss="modal">确认</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade cost_price_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title">批量设置</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="control-label text-center" style="display: inline-block; width:18%">成本价:</label>
                    <input type="text" class="form-control batch_input" name="batch_cost_price" style="width: 80%; display: inline-block">
                </div>
            </div>
            <div class="modal-footer" style="text-align: left">
                <button type="button" class="btn btn-primary btn_cost_price_submit" data-dismiss="modal">确认</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade weight_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title">批量设置</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="control-label text-center" style="display: inline-block; width:18%">重量:</label>
                    <input type="text" class="form-control batch_input" name="batch_weight" style="width: 80%; display: inline-block">
                </div>
            </div>
            <div class="modal-footer" style="text-align: left">
                <button type="button" class="btn btn-primary btn_weight_submit" data-dismiss="modal">确认</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
            </div>
        </div>
    </div>
</div>
<script>
    ItemSkuData = [];
    <?php  if($item['sku']) { ?>
    <?php  if(is_array($item['sku'])) { foreach($item['sku'] as $k => $sku) { ?>
    ItemSkuData[<?php  echo $k;?>] = {
        id: "<?php  echo $sku['id'];?>",
        total: "<?php  echo $sku['total'];?>",
        price: "<?php  echo $sku['price'];?>",
        market_price: "<?php  echo $sku['market_price'];?>",
        cost_price: "<?php  echo $sku['cost_price'];?>",
        weight: "<?php  echo $sku['weight'];?>",
        sales: "<?php  echo $sku['sales'];?>"
    };
    <?php  } } ?>
    <?php  } ?>
    console.log(ItemSkuData);
</script>
<script>
    require(['jquery', 'jquery.ui', 'md5'], function($){
        $('.form').submit(function(){
            var title = $('input[name=title]');
            if (title.val() == '') {
                util.message('商品标题为空，请填写！', '', 'error');
                return false;
            }
            var pcid = $('select[name=pcid]');
            if (pcid.val() <= 0) {
                util.message('请选择一级分类！', '', 'error');
                return false;
            }
            var cid = $('select[name=cid]');
            if (cid.val() <= 0) {
                util.message('请选择二级分类！', '', 'error');
                return false;
            }
            /*var ccid = $('select[name=ccid]');
             if (ccid.val() <= 0) {
             util.message('请选择三级分类！', '', 'error');
             return false;
             }*/
            var cover = $('input[name=cover]');
            if (cover.val() == '') {
                util.message('封面为空，请上传！', '', 'error');
                return false;
            }
            var album = $('input[name="album[]"]');
            if (!album.val()) {
                util.message('相册为空，请上传！', '', 'error');
                return false;
            }
            var commission_custom = $('input[name=commission_custom]');
            if (commission_custom.val() == 1) {
                var commission1_rate_max = parseFloat($('input[name=commission1_rate]').attr('data-commission1-rate-max'));
                var commission1_rate = parseFloat($('input[name=commission1_rate]').val());
                if (commission1_rate_max > 0) {
                    if (commission1_rate_max < commission1_rate) {
                        util.message('一级分销佣金不能超过'+commission1_rate_max+'%！', '', 'error');
                        return false;
                    }
                }
                var commission1_value_max = parseFloat($('input[name=commission1_value]').attr('data-commission1-value-max'));
                var commission1_value = parseFloat($('input[name=commission1_value]').val());
                if (commission1_value_max > 0) {
                    if (commission1_value_max < commission1_value) {
                        util.message('一级分销佣金不能超过'+commission1_value_max+'元！', '', 'error');
                        return false;
                    }
                }
                var commission2_rate_max = parseFloat($('input[name=commission2_rate]').attr('data-commission2-rate-max'));
                var commission2_rate = parseFloat($('input[name=commission2_rate]').val());
                if (commission2_rate_max > 0) {
                    if (commission2_rate_max < commission2_rate) {
                        util.message('二级分销佣金不能超过'+commission2_rate_max+'%！', '', 'error');
                        return false;
                    }
                }
                var commission2_value_max = parseFloat($('input[name=commission2_value]').attr('data-commission2-value-max'));
                var commission2_value = parseFloat($('input[name=commission2_value]').val());
                if (commission2_value_max > 0) {
                    if (commission2_value_max < commission2_value) {
                        util.message('二级分销佣金不能超过'+commission2_value_max+'元！', '', 'error');
                        return false;
                    }
                }
                var commission3_rate_max = parseFloat($('input[name=commission3_rate]').attr('data-commission3-rate-max'));
                var commission3_rate = parseFloat($('input[name=commission3_rate]').val());
                if (commission3_rate_max > 0) {
                    if (commission3_rate_max < commission3_rate) {
                        util.message('三级分销佣金不能超过'+commission3_rate_max+'%！', '', 'error');
                        return false;
                    }
                }
                var commission3_value_max = parseFloat($('input[name=commission3_value]').attr('data-commission3-value-max'));
                var commission3_value = parseFloat($('input[name=commission3_value]').val());
                if (commission3_value_max > 0) {
                    if (commission3_value_max < commission3_value) {
                        util.message('三级分销佣金不能超过'+commission3_value_max+'元！', '', 'error');
                        return false;
                    }
                }
            }
            var discount_rate_min = parseFloat($('input[name=discount_rate]').attr('data-discount-rate-min'));
            var discount_rate = parseFloat($('input[name=discount_rate]').val());
            if (discount_rate_min > 0 && discount_rate > 0 ) {
                if (discount_rate_min > discount_rate) {
                    util.message('分销商优惠价最低'+discount_rate_min+'%！', '', 'error');
                    return false;
                }
            }
            var discount_value_max = parseFloat($('input[name=discount_value]').attr('data-discount-value-max'));
            var discount_value = parseFloat($('input[name=discount_value]').val());
            if (discount_value_max > 0) {
                if (discount_value_max < discount_value) {
                    util.message('分销商优惠价不能超过'+discount_value_max+'元！', '', 'error');
                    return false;
                }
            }
            var price = $('input[name=price]');
            if (price.val() <= 0) {
                return confirm('请确认是否设置销售价为0？');
            }
            return true;
        });
        //计算抵现金额
        $('.cash_credit').keyup(function(){
            var cash_rate = 1/'<?php  echo $discount_setting['credit']['cash_rate'];?>';
            var money_total = 0.00;
            if ($(this).val() != '') {
                money_total = parseFloat($(this).val() * cash_rate);
            }
            if (money_total > 0) {
                var arr = money_total.toString().split('.');
                $('.money_total').html(arr[0]+'.'+(arr[1]?arr[1].toString().substr(0, 2):'00'));
            } else {
                $('.money_total').html('0.00');
            }
        });
        //初始化产品参数排序功能
        $('#list_wrap').sortable({handle: '.fa-move'});
        //开启分销属性
        $('input:radio[name=partner_open]').click(function(){
            if ($(this).val() == '1') {
                $('.item_partner_attr').fadeIn();
            } else {
                $('.item_partner_attr').fadeOut();
            }
        });
        //开启商品规格
        $('input[name=item_spec_switch]').click(function(){
            if ($(this).prop('checked')) {
                $(this).val('1');
                $('.item_spec_setting').fadeIn();
                $('input[name=total]').prop('readonly', true);
                //$('input[name=price]').prop('readonly', true);
            } else {
                $(this).val('0');
                $('.item_spec_setting').fadeOut();
                $('input[name=total]').prop('readonly', false);
                //$('input[name=price]').prop('readonly', false);
                $('.item_spec_wrap').html('');
                $('#item_sku_wrap').html('').parent().fadeOut();
            }
        });
        var ItemAttr = {
            level: -1,
            selectedAttr: [],
            init: function(){
                ItemAttr.binding();
                ItemAttr.refresh();
            },
            refresh: function(){
                ItemAttr.mutex();
                ItemAttr.check();
            },
            binding: function(){
                var obj;
                obj = $('.add_item_attr');
                if (obj.length) {
                    obj.unbind('click').click(function(){
                        ItemAttr.add(this);
                    });
                }
                obj = $('.del_item_attr');
                if (obj.length) {
                    obj.unbind('click').click(function () {
                        ItemAttr.delete($(this).parent().parent());
                    });
                }
                obj = $('.item_attr');
                if (obj.length) {
                    obj.unbind('change').change(function () {
                        ItemAttr.change(this);
                    });
                }
                obj = $('.refresh_item_attr');
                if (obj.length && typeof(obj.attr('data-click')) == 'undefined') {
                    obj.attr('data-click', 1).bind('click', function(){
                        ItemAttr.refresh_table(this);
                    });
                }
                console.log('ItemAttr.binding');
            },
            change: function(obj){
                ItemAttr.mutex();
                var attrid = $('option:selected', obj).val();
                ItemValue.load(attrid, $(obj).parent().parent());
            },
            mutex: function(){	//商品规格选择值互斥
                var item_attr = $('.item_attr');
                ItemAttr.selectedAttr = [];
                item_attr.each(function() {
                    var value = $('option:selected', this).val();
                    if (value > 0) {
                        if ($.inArray(value, ItemAttr.selectedAttr) == -1) {
                            ItemAttr.selectedAttr.push(value);
                        }
                    }
                });
                if (ItemAttr.selectedAttr.length) {
                    item_attr.each(function(){
                        $('option', this).each(function(){
                            if ($.inArray($(this).val(), ItemAttr.selectedAttr) == -1) {
                                $(this).prop('disabled', false);
                            } else {
                                if (!$(this).prop('selected')) {
                                    $(this).prop('disabled', true);
                                }
                            }
                        });
                    });
                }
            },
            check: function(){
                if (ItemAttr.level > 0 && $('.item_spec_wrap .item_spec').length >= ItemAttr.level) {
                    $('.add_item_attr').hide();
                } else {
                    $('.add_item_attr').show();
                }
            },
            toggleAddBtn: function(obj){
                if (!$(obj).prop('disabled')) {
                    $('.glyphicon', obj).hide();
                    $('img', obj).show();
                    $(obj).prop('disabled', true)
                } else {
                    $('.glyphicon', obj).show();
                    $('img', obj).hide();
                    $(obj).prop('disabled', false)
                }
            },
            add: function(obj){
                ItemAttr.toggleAddBtn(obj);
                var index = parseInt($(obj).attr('data-index'))+1;
                $(obj).attr('data-index', index);
                $.ajax({
                    url:'<?php  echo $this->createWebUrl("spec", array("act" => "load"))?>',
                    dataType: 'json',
                    success: function(resp){
                        ItemAttr.toggleAddBtn(obj);
                        if (resp.errno == 0) {
                            var html = '', item;
                            html += '<li class="item_spec clearfix">';
                            html += '	<div class="col-md-6 clearfix">';
                            html += '		<select class="item_attr form-control pull-left" name="item_attr[]" data-index="'+index+'">';
                            html += '		<option value="0">请选择</option>';
                            for (var i=0; i<resp.data.length; i++) {
                                item = resp.data[i];
                                html += '<option value="'+item['id']+'">'+item['title']+'</option>';
                            }
                            html += '		</select>';
                            html += '		<span class="glyphicon glyphicon-remove del_item_attr pull-left" aria-hidden="true" title="删除"></span>';
                            html += '	</div>';
                            html += ' 	<div class="col-md-12"><ul class="item_value_wrap"></ul></div>';
                            html += '</li>';
                            $('.item_spec_wrap').append(html);
                            ItemAttr.binding();
                            ItemAttr.refresh();
                        } else {
                            util.message(resp.errmsg, '', 'error');
                        }
                    }
                });
            },
            delete: function(obj){
                var value = $('.item_attr option:selected', obj).val();
                var index = $.inArray(value, ItemAttr.selectedAttr);
                if (index != -1) {
                    ItemAttr.selectedAttr.splice(index, 1);
                }
                obj.remove();
                ItemAttr.refresh();
            },
            refresh_table: function(ele){
                var arr = new Array;
                $('.item_attr option:selected').each(function(){
                    arr.push($(this).text());
                });
                $('.item_spec_wrap :checkbox:checked').each(function(){
                    arr.push($(this).attr('data-value'));
                });
                if (arr.length) {
                    var md5 = $(ele).attr('data-md5');
                    var str = arr.join(',');
                    if (typeof(md5) == 'undefined') {
                        md5 = $.md5(str);
                        $(ele).attr('data-md5', md5);
                    }
                }

                $(ele).attr('disabled', true);
                $('span', ele).addClass('fa-spin');

                setTimeout(function(){
                    ItemSku.refresh();

                    if (md5 != '') {
                        var arr = new Array;
                        $('.item_attr option:selected').each(function(){
                            arr.push($(this).text());
                        });
                        $('.item_spec_wrap :checkbox:checked').each(function(){
                            arr.push($(this).attr('data-value'));
                        });
                        if (arr.length) {
                            var str = arr.join(',');
                            if (md5 != $.md5(str)) {
                                $('input[name=item_spec_switch]').val('2'); //规格发生变化，先删除，后添加
                            } else {
                                $('input[name=item_spec_switch]').val('1');
                            }
                        }
                    }

                    $(ele).attr('disabled', false);
                    $('span', ele).removeClass('fa-spin');
                }, 500);
            }
        };
        var ItemValue = {
            load: function(attrid, parent_obj) {
                if (attrid == 0) {
                    $('.item_value_wrap', parent_obj).html('');
                    return;
                }
                $.ajax({
                    url:'<?php  echo $this->createWebUrl("spec", array("act" => "load"))?>',
                    type: 'post',
                    data: 'attrid='+attrid,
                    dataType: 'json',
                    success: function(resp){
                        if (resp.errno == 0) {
                            var html = '', item;
                            for (var i=0; i<resp.data.length; i++) {
                                item = resp.data[i];
                                html += '<li class="item_value">';
                                html += '	<label>';
                                html += '		<input type="checkbox" value="'+item['id']+'" data-value="'+item['value']+'"> '+item['value'];
                                html += '	</label>';
                                html += '</li>';
                            }
                            $('.item_value_wrap', parent_obj).html(html);
                        } else {
                            util.message(resp.errmsg, '', 'error');
                        }
                    }
                });
            }
        };
        var ItemSku = {
            binding: function(){
                if ($('input[name="sku[total][]"]').length) {
                    $('input[name="sku[total][]"]').change(function(){
                        ItemSku.calcTotal();
                    });
                }
                if ($('input[name="sku[price][]"]').length) {
                    $('input[name="sku[price][]"]').change(function(){
                        var price = parseFloat($(this).val());
                        if (!price.isCurrency()) {
                            $(this).parent().addClass('has-error');
                        } else {
                            $(this).parent().removeClass('has-error');
                        }
                        ItemSku.calcPrice();
                    });
                }
                if ($('input[name="sku[market_price][]"]').length) {
                    $('input[name="sku[market_price][]"]').change(function(){
                        var market_price = parseFloat($(this).val());
                        if (!market_price.isCurrency()) {
                            $(this).parent().addClass('has-error');
                        } else {
                            $(this).parent().removeClass('has-error');
                        }
                        ItemSku.calcMarket_price();
                    });
                }
                if ($('input[name="sku[cost_price][]"]').length) {
                    $('input[name="sku[cost_price][]"]').change(function(){
                        var cost_price = parseFloat($(this).val());
                        if (!cost_price.isCurrency()) {
                            $(this).parent().addClass('has-error');
                        } else {
                            $(this).parent().removeClass('has-error');
                        }
                        ItemSku.calcCost_price();
                    });
                }
                console.log('ItemSku.binding');
            },
            calcTotal: function(){
                var total = 0;
                $('input[name="sku[total][]"]').each(function(){
                    total += parseInt($(this).val());
                });
                $('input[name=total]').val(total);
            },
            calcPrice: function(){
                var price = 0.00, sku_price = 0.00;
                $('input[name="sku[price][]"]').each(function(){
                    sku_price = parseFloat($(this).val());
                    if (sku_price > 0.00) {
                        if (price == 0.00) {
                            price = sku_price;
                        } else {
                            if (sku_price < price) {
                                price = sku_price;
                            }
                        }
                    }
                });
                $('input[name=price]').val(price);
            },
            calcMarket_price: function(){
                var price = 0.00, market_price = 0.00;
                $('input[name="sku[market_price][]"]').each(function(){
                    market_price = parseFloat($(this).val());
                    if (market_price > 0.00) {
                        if (price == 0.00) {
                            price = market_price;
                        } else {
                            if (market_price < price) {
                                price = market_price;
                            }
                        }
                    }
                });
                $('input[name=market_price]').val(price);
            },
            calcCost_price: function(){
                var price = 0.00, cost_price = 0.00;
                $('input[name="sku[cost_price][]"]').each(function(){
                    cost_price = parseFloat($(this).val());
                    if (cost_price > 0.00) {
                        if (price == 0.00) {
                            price = cost_price;
                        } else {
                            if (cost_price < price) {
                                price = cost_price;
                            }
                        }
                    }
                });
                $('input[name=cost_price]').val(price);
            },
            tdSuffix: function(index){
                var skuid = 0, total = 0, price = 0.00, market_price = 0.00, cost_price = 0.00, weight = 0, sales = 0;
                if (ItemSkuData.length && typeof(ItemSkuData[index]) != 'undefined') {
                    skuid = ItemSkuData[index].id;
                    total = ItemSkuData[index].total;
                    price = ItemSkuData[index].price;
                    market_price = ItemSkuData[index].market_price;
                    cost_price = ItemSkuData[index].cost_price;
                    weight = ItemSkuData[index].weight;
                    sales = ItemSkuData[index].sales;
                }
                return '<!--td></td-->'+
                        '<td><input type="hidden" name="sku[id][]" value="'+skuid+'"/>'+
                        '<input type="text" class="form-control" name="sku[total][]" value="'+total+'"/></td>'+
                        '<td><input type="text" class="form-control" name="sku[price][]" value="'+price+'"/></td>'+
                        '<td><input type="text" class="form-control" name="sku[market_price][]" value="'+market_price+'"/></td>'+
                        '<td><input type="text" class="form-control" name="sku[cost_price][]" value="'+cost_price+'"/></td>'+
                        '<td><input type="text" class="form-control" name="sku[weight][]" value="'+weight+'"/></td>'+
                        '<td><input type="text" class="form-control" name="sku[sales][]" value="'+sales+'"/></td>';
            },
            refresh: function(){
                var item_attr = $('.item_spec_wrap select');
                if (!item_attr.length) {
                    $('#item_sku_wrap').html('').parent().fadeOut();
                    return;
                }
                var list = new Array, value_ids = new Array;
                item_attr.each(function(){
                    if ($(this).val() == '0') {
                        return;
                    }
                    //var attr_title = $(this).find('option:selected').text();
                    var values = $(':checkbox:checked', $(this).parent().parent());
                    var arr = new Array, ids = new Array;
                    values.each(function(){
                        arr.push($(this).attr('data-value'));
                        ids.push($(this).val());
                    });
                    $(this).attr('data-value-length', values.length);
                    list.push(arr);
                    value_ids.push(ids);
                });
                if (!list.length) {
                    return;
                }
                list.sort(function(a, b){
                    return a.length - b.length;
                });
                value_ids.sort(function(a, b){
                    return a.length - b.length;
                });
                item_attr.sort(function(a, b){
                    return $(a).attr('data-value-length') - $(b).attr('data-value-length');
                });
                var rows = $(list).array2row(), ids = $(value_ids).array2row(), html = '';
                var thead = '<!--th width="160">产品图</th-->'+
                        '<th>库存</th>'+
                        '<th>销售价</th>'+
                        '<th>市场价</th>'+
                        '<th>成本价</th>'+
                        '<th>重量(kg)</th>'+
                        '<th width="70">销量</th>';

                html += '<table class="table table-bordered">';
                var tdPrefix = '';
                for (var i=0; i<item_attr.length; i++) {
                    tdPrefix += '<th>'+$(item_attr[i]).find('option:selected').text()+'</th>';
                }
                html += tdPrefix + thead;
                var arr;
                for (var i=0; i<rows.length; i++) {
                    html += '<tr>';
                    html += '<input type="hidden" name="sku[valueid][]" value="'+ids[i]+'"/>';
                    arr = rows[i].split(',');
                    for (var j=0; j<arr.length; j++) {
                        html += '<td>'+arr[j]+'</td>';
                    }
                    html += ItemSku.tdSuffix(i);
                    html += '</tr>';
                }
                html += '</table>';
                $('#item_sku_wrap').html(html).parent().fadeIn();
                for (var i=0; i<item_attr.length-1; i++) {
                    $('#item_sku_wrap table').rowspan(i);
                }
            },
        };
        ItemAttr.init();
        if (ItemSkuData.length) {
            $('.refresh_item_attr').trigger('click');
        }
        //邮费切换
        $('input[name=postage_select]').click(function () {
            var tmpl = $(this).val();
            if (tmpl == 2) {
                loadPostageTmpl();
                $('input[name=postage]').prop('disabled', true);
            } else {
                $('select[name=postage_tmplid]').prop('disabled', true);
                $('input[name=postage]').prop('disabled', false);
            }
        });
        $('.refresh_postage_tmpl').click(function(){
            if ($('select[name=postage_tmplid]').prop('disabled')) {
                return;
            }
            loadPostageTmpl();
        });
        var loadPostageTmpl = function(){
            $('select[name=postage_tmplid]').prop('disabled', true);
            $.ajax({
                type: 'post',
                url: '<?php  echo $this->createWebUrl("item", array("act" => "postage_tmpl"))?>',
                dataType: 'json',
                success: function(resp){
                    if (resp.length > 0) {
                        var html = '', item;
                        for (var i=0; i<resp.length; i++) {
                            item = resp[i];
                            html += '<option value="'+item.id+'">'+item.title+'</option>';
                        }
                        $('select[name=postage_tmplid]').html(html).prop('disabled', false);
                    }
                }
            });
        };
        //添加产品参数
        $('.add_params').click(function(){
            $.ajax({
                url: "<?php  echo $this->createWebUrl('item', array('act' => 'params', 'behavior' => 'add'))?>",
                success:function(response) {
                    $('#list_wrap').append(response);
                }
            });
        });
        //删除产品参数
        window.delItem = function(obj) {
            var id = $(obj).attr('data-id');
            if (!id) {	//new
                $(obj).parent().parent().remove();
                return;
            }
            $.ajax({
                'url': "<?php  echo $this->createWebUrl('item', array('act' => 'params', 'behavior' => 'delete'))?>"+'&paramid='+id,
                success:function(response) {
                    if (response == 'success') {
                        $(obj).parent().parent().remove();
                    } else {
                        util.message(response, '', 'error');
                    }
                }
            });
        };
        //批量设置
        $('.btn_total_submit').click(function(){
            var value = $('input[name=batch_total]', $(this).parent().prev()).val();
            $('input[name="sku[total][]"]').val(value);
            ItemSku.calcTotal();
        });
        $('.btn_price_submit').click(function(){
            var value = $('input[name=batch_price]', $(this).parent().prev()).val();
            $('input[name="sku[price][]"]').val(value);
            ItemSku.calcPrice();
        });
        $('.btn_market_price_submit').click(function(){
            var value = $('input[name=batch_market_price]', $(this).parent().prev()).val();
            $('input[name="sku[market_price][]"]').val(value);
            ItemSku.calcMarket_price();
        });
        $('.btn_cost_price_submit').click(function(){
            var value = $('input[name=batch_cost_price]', $(this).parent().prev()).val();
            $('input[name="sku[cost_price][]"]').val(value);
            ItemSku.calcCost_price();
        });
        $('.btn_weight_submit').click(function(){
            var value = $('input[name=batch_weight]', $(this).parent().prev()).val();
            $('input[name="sku[weight][]"]').val(value);
        });
        $('.batch_input').keypress(function(event){
            if (event.keyCode == "13") {
                var btn = $('.btn-primary', $(this).parent().parent().next());
                $(btn).trigger('click');
            }
        });
        //开启自定义佣金
        $('input[name=commission_custom]').click(function(){
            if ($(this).prop('checked')) {
                $(this).val('1');
                $('.item_custom_setting').fadeIn();
            } else {
                $(this).val('0');
                $('.item_custom_setting').fadeOut();
            }
        });
    });
</script>