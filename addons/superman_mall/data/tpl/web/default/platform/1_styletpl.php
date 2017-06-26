<?php defined('IN_IA') or exit('Access Denied');?><div class="main">
	<form class="form-horizontal form" id="setting_form" action="" method="post" enctype="multipart/form-data">
		<div class="panel panel-default">
			<div class="panel-heading">
				样式设置
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">默认商品列表</label>
					<div class="col-sm-6 col-md-8 col-xs-12">
						<div class="input-group">
							<label class="radio-inline">
								<input type="radio" name="setting[list_style_default]" value="1" <?php  if(isset($setting['list_style_default']) && $setting['list_style_default'] == 1) { ?>checked<?php  } ?>> 单列
							</label>
							<label class="radio-inline">
								<input type="radio" name="setting[list_style_default]" value="2" <?php  if(!isset($setting['list_style_default']) || $setting['list_style_default'] == 2) { ?>checked<?php  } ?>> 双列
							</label>
						</div>
						<span class="help-block">设置商品列表默认展示样式</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">商品列表切换</label>
					<div class="col-sm-6 col-md-8 col-xs-12">
						<div class="input-group">
							<label class="radio-inline">
								<input type="radio" name="setting[list_style_switch]" value="1" <?php  if(!isset($setting['list_style_switch']) || $setting['list_style_switch'] == 1) { ?>checked<?php  } ?>> 允许
							</label>
							<label class="radio-inline">
								<input type="radio" name="setting[list_style_switch]" value="0" <?php  if(isset($setting['list_style_switch']) && !$setting['list_style_switch']) { ?>checked<?php  } ?>> 不允许
							</label>
						</div>
						<span class="help-block">商品列表单、双列样式是否允许手机端自由切换，选择允许时，顶部搜索条右侧会出现切换按钮</span>
					</div>
				</div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">商品列表购物车按钮</label>
                    <div class="col-sm-6 col-md-8 col-xs-12">
                        <div class="input-group">
                            <label class="radio-inline">
                                <input type="radio" name="setting[list_style_cart_btn]" value="1" <?php  if(isset($setting['list_style_cart_btn']) && $setting['list_style_cart_btn'] == 1) { ?>checked<?php  } ?>> 显示
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="setting[list_style_cart_btn]" value="0" <?php  if(!isset($setting['list_style_cart_btn']) || !$setting['list_style_cart_btn']) { ?>checked<?php  } ?>> 不显示
                            </label>
                        </div>
                        <span class="help-block">商品列表是否显示购物车按钮，选择显示时，商品列表会显示购物车按钮</span>
                    </div>
                </div>

			</div>
		</div>
		<div class="form-group col-sm-12">
			<input name="token" type="hidden" value="<?php  echo $_W['token'];?>" />
			<input type="submit" class="btn btn-primary col-lg-1" name="submit" value="提交" />
		</div>
	</form>
</div>