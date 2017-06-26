<?php defined('IN_IA') or exit('Access Denied');?><div class="main">
	<form class="form-horizontal form" id="setting_form" action="" method="post" enctype="multipart/form-data">
		<div class="panel panel-default">
			<div class="panel-heading">
				基本参数
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">微信模式</label>
					<div class="col-sm-6 col-md-8 col-xs-12">
						<div class="input-group">
							<label class="radio-inline">
								<input type="radio" name="base[wechat]" value="1" <?php  if($this->module['config']['base']['wechat']) { ?>checked<?php  } ?>> 开启
							</label>
							<label class="radio-inline">
								<input type="radio" name="base[wechat]" value="0" <?php  if(!$this->module['config']['base']['wechat']) { ?>checked<?php  } ?>> 关闭
							</label>
						</div>
						<span class="help-block">开启微信模式后，将只能在微信中打开，浏览器访问将提示使用微信打开</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">调试模式</label>
					<div class="col-sm-6 col-md-8 col-xs-12">
						<div class="input-group">
							<label class="radio-inline">
								<input type="radio" name="base[debug]" value="1" <?php  if($this->module['config']['base']['debug']) { ?>checked<?php  } ?>> 开启
							</label>
							<label class="radio-inline">
								<input type="radio" name="base[debug]" value="0" <?php  if(!$this->module['config']['base']['debug']) { ?>checked<?php  } ?>> 关闭
							</label>
						</div>
						<span class="help-block">开启调试模式后，前台手机端将无法访问，并展示提示信息。同时，可设置调试账号UID，调试账号访问将不受限制</span>
					</div>
				</div>
				<div class="form-group" style="<?php  if(!$this->module['config']['base']['debug']) { ?>display:none<?php  } ?>">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">调试信息</label>
					<div class="col-sm-9">
						<textarea class="form-control" name="base[debug_message]"><?php  echo $this->module['config']['base']['debug_message']?></textarea>
						<span class="help-block">开启调试模式后，没有访问权限的账号将展示调试信息</span>
					</div>
				</div>
				<div class="form-group" style="<?php  if(!$this->module['config']['base']['debug']) { ?>display:none<?php  } ?>">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">调试账号</label>
					<div class="col-sm-9">
						<textarea class="form-control" name="base[debug_uids]"><?php  if($this->module['config']['base']['debug_uids']) { ?><?php  echo implode(',', $this->module['config']['base']['debug_uids'])?><?php  } ?></textarea>
						<span class="help-block">请填写账号UID（会员编号），多个账号UID之间使用英文半角逗号分隔","&nbsp;&nbsp;<a href="<?php  echo url('mc/member')?>" target="_blank">查找账号UID</a></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">数据统计</label>
					<div class="col-sm-6 col-md-8 col-xs-12">
						<div class="input-group">
							<label class="radio-inline">
								<input type="radio" name="base[stat_switch]" value="1" <?php  if(isset($this->module['config']['base']['stat_switch']) && $this->module['config']['base']['stat_switch'] == 1) { ?>checked<?php  } ?>> 开启
							</label>
							<label class="radio-inline">
								<input type="radio" name="base[stat_switch]" value="0" <?php  if(!isset($this->module['config']['base']['stat_switch']) || $this->module['config']['base']['stat_switch'] == 0) { ?>checked<?php  } ?>> 关闭
							</label>
						</div>
						<span class="help-block">开启数据统计后，可设置统计代码，统计分析手机端全站访问数据，推荐使用<a target="_blank" href="http://mta.qq.com/">腾讯云分析</a></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">统计代码</label>
					<div class="col-sm-9">
						<textarea class="form-control" name="base[stat_code]"><?php  if(isset($this->module['config']['base']['stat_code']) && $this->module['config']['base']['stat_code']) { ?><?php  echo $this->module['config']['base']['stat_code']?><?php  } ?></textarea>
						<span class="help-block">数据统计开关关闭后，统计代码不删除</span>
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
<script>
	require(['jquery'], function($){
		$('input[name="base[debug]"]').bind('click', function(){
			var value = $(this).val();
			if (value == '1') {
				$('textarea[name="base[debug_message]"]').parent().parent().fadeIn();
				$('textarea[name="base[debug_uids]"]').parent().parent().fadeIn();
			} else {
				$('textarea[name="base[debug_message]"]').parent().parent().fadeOut();
				$('textarea[name="base[debug_uids]"]').parent().parent().fadeOut();
			}
		});
		$('#setting_form').submit(function(){
			return true;
		});
	});
</script>
