<?php defined('IN_IA') or exit('Access Denied');?><div class="main">
	<form class="form-horizontal form" id="setting_form" action="" method="post">
		<div class="panel panel-default">
			<div class="panel-heading">
				商户设置
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">商户入驻</label>
					<div class="col-sm-6 col-md-8 col-xs-12">
						<div class="input-group">
							<label class="radio-inline">
								<input type="radio" name="setting[join_switch]" <?php  if($setting['join_switch'] == 1) { ?>checked<?php  } ?> value="1"> 开启
							</label>
							<label class="radio-inline">
								<input type="radio" name="setting[join_switch]" <?php  if(!$setting['join_switch']) { ?>checked<?php  } ?> value="0"> 关闭
							</label>
						</div>
						<span class="help-block">开启商户入驻后，手机端个人中心页面将开启入口，否则不显示</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">推荐商户</label>
					<div class="col-sm-6 col-md-8 col-xs-12">
						<div class="input-group">
							<label class="radio-inline">
								<input type="radio" name="setting[recommend_switch]" <?php  if($setting['recommend_switch'] == 1) { ?>checked<?php  } ?> value="1"> 开启
							</label>
							<label class="radio-inline">
								<input type="radio" name="setting[recommend_switch]" <?php  if(!$setting['recommend_switch']) { ?>checked<?php  } ?> value="0"> 关闭
							</label>
						</div>
						<span class="help-block">开启推荐商户后，首页将展示最新入驻的商户信息，否则不显示</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">入驻协议</label>
					<div class="col-sm-6 col-md-8 col-xs-12">
						<?php  echo tpl_ueditor('setting[content]', $setting['content'])?>
						<span class="help-block">不填写时，商户入驻申请页面将不显示：我已阅读并同意 《商户入驻协议》</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">最低提现金额</label>
					<div class="col-sm-6 col-md-8 col-xs-12">
						<div class="input-group ">
							<input type="text" name="setting[limit]" <?php  if(isset($setting['limit'])) { ?>value="<?php  echo $setting['limit'];?>"<?php  } ?> aria-describedby="getcash_price" class="form-control">
							<span class="input-group-btn">
								<button class="btn btn-default" type="button">元</button>
							</span>
						</div>
						<span class="help-block">最低提现金额不能小于1元，建议填写整数，不填写为不限制</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">提现费率</label>
					<div class="col-sm-6 col-md-8 col-xs-12">
						<div class="input-group ">
							<input type="text" name="setting[fee_rate]" class="form-control" <?php  if(isset($setting['fee_rate'])) { ?>value="<?php  echo $setting['fee_rate'];?>"<?php  } ?>>
							<span class="input-group-btn">
								<button class="btn btn-default" type="button">%</button>
							</span>
						</div>
						<span class="help-block">商户申请提现时，每笔申请提现扣除的费用，默认为空，即提现不扣费，支持填写小数<br>
						<span style="color: red">商户入驻时的默认提现费率</span>
						</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">提现费用</label>
					<div class="col-sm-6 col-md-8 col-xs-12">
						<div class="input-group">
							<span class="input-group-btn">
								<button class="btn btn-default" type="button">最低</button>
							</span>
							<input type="text" name="setting[fee_min]" class="form-control" <?php  if(isset($setting['fee_min'])) { ?>value="<?php  echo $setting['fee_min'];?>"<?php  } ?>>
							<span class="input-group-btn">
								<button class="btn btn-default" type="button">元</button>
							</span>
						</div>
						<div class="input-group" style="margin-top: .5rem">
							<span class="input-group-btn">
								<button class="btn btn-default" type="button">最高</button>
							</span>
							<input type="text" name="setting[fee_max]" class="form-control" <?php  if(isset($setting['fee_max'])) { ?>value="<?php  echo $setting['fee_max'];?>"<?php  } ?>>
							<span class="input-group-btn">
								<button class="btn btn-default" type="button">元</button>
							</span>
						</div>
						<span class="help-block">商户提现时，提现费用的上下限，最高为空时，表示不限制扣除的提现费用<br>
							例如：提现100元，费率5%，最低1元，最高2元，商户最终提现金额=100-2=98<br>
							例如：提现100元，费率5%，最低1元，最高10元，商户最终提现金额=100-100*5%=95<br>
						<span style="color: red">商户入驻时的默认提现费用</span>
						</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">绑定后台域名</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="setting[web_domain]" value="<?php  echo $setting['web_domain'];?>">
						<span class="help-block">
							设置商户后台独立访问域名，优化商户后台链接，推荐使用：shop.xx.com，<br>
							设置完成后访问http://<?php  echo $_W['uniacid'];?>.shop.xx.com/即可登录商户后台<br>
							下载重写规则文件：
							<span style="margin: 0 5px;">
								<a target="_blank" class="download_rule_link" href="<?php  echo $_W['siteurl']?>&download=apache">Apache</a>
							</span>
							<span style="margin: 0 5px;">
								<a target="_blank" class="download_rule_link" href="<?php  echo $_W['siteurl']?>&download=nginx">Nginx</a>
							</span>下载规则文件后，需上传到微擎根目录下<br>
							注意：绑定域名前，需要先解析域名，并绑定到当前微擎站点下<br>
							<span style="color: red">清除绑定域名需要对应删除.htaccess文件里面的重写规则</span>
						</span>
					</div>
					<script>
						$('.download_rule_link').click(function () {
							var web_domain = $('input[name="setting[web_domain]"]').val();
							if (web_domain == '') {
								util.message('未填写域名，无法生成规则文件！', '', 'error');
								return false;
							}
							var url = $(this).attr('href')+'&web_domain='+web_domain;
							$(this).attr('href', url);
						});
					</script>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">商户国际化</label>
					<div class="col-sm-6 col-md-8 col-xs-12">
						<div class="input-group">
							<label class="radio-inline">
								<input type="radio" name="setting[international]" <?php  if($setting['international'] == 1) { ?>checked<?php  } ?> value="1"> 开启
							</label>
							<label class="radio-inline">
								<input type="radio" name="setting[international]" <?php  if(!$setting['international']) { ?>checked<?php  } ?> value="0"> 关闭
							</label>
						</div>
						<span class="help-block">开启商户国际化后，商户入驻时，填写手机号码将需要选择所在国家，编辑商户和商户管理员账号时，可以修改国家区号等数据</span>
					</div>
				</div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">商户后台模板</label>
                    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-6">
                        <select name="setting[shop_admin_template]" class="form-control">
                            <option value="wechat" <?php  if(!isset($setting['shop_admin_template']) || $setting['shop_admin_template']=='wechat') { ?>selected<?php  } ?>>微信公众平台风格</option>
                            <option value="default" <?php  if($setting['shop_admin_template']=='default') { ?>selected<?php  } ?>>经典风格</option>
                        </select>
                    </div>
                </div>
			</div>
		</div>
		<div class="form-group col-sm-12">
			<input type="submit" class="btn btn-primary col-lg-1" name="submit" value="提交" />
			<input name="token" type="hidden" value="<?php  echo $_W['token'];?>" />
		</div>
	</form>
</div>
<script>
	require(['jquery', 'util'], function($){
		$('#setting_form').submit(function(){
			return true;
		});
	});
</script>