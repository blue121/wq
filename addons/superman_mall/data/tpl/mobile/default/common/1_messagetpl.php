<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<div class="page-group">
	<div class="page superpage_<?php  echo $do;?>" id="superpage_<?php  echo $do;?>_<?php  echo $act;?>">
		<div class="content">
			<div class="weui_msg">
				<div class="weui_icon_area">
					<?php  if($type == 'success') { ?>
					<i class="weui_icon_msg weui_icon_success"></i>
					<?php  } else if($type == 'warn') { ?>
					<i class="weui_icon_msg weui_icon_warn"></i>
					<?php  } else if($type == 'info') { ?>
					<i class="weui_icon_msg weui_icon_info"></i>
					<?php  } else if($type == 'waiting') { ?>
					<i class="weui_icon_msg weui_icon_waiting"></i>
					<?php  } else if($type == 'safe_success') { ?>
					<i class="weui_icon_safe weui_icon_safe_success"></i>
					<?php  } else if($type == 'safe_warn') { ?>
					<i class="weui_icon_safe weui_icon_safe_warn"></i>
					<?php  } ?>
				</div>
				<div class="weui_text_area">
					<?php  if(is_array($msg)) { ?>
						<?php  if(isset($msg['sql'])) { ?>
							<h2 class="weui_msg_title">MYSQL 错误：</h2>
							<p class="weui_msg_desc"><?php  echo cutstr($msg['sql'], 300, 1);?></p>
							<p class="weui_msg_desc"><b><?php  echo $msg['error']['0'];?> <?php  echo $msg['error']['1'];?>：</b><?php  echo $msg['error']['2'];?></p>
						<?php  } else if(isset($msg['submsg'])) { ?>
							<h2 class="weui_msg_title"><?php  echo $msg['msg'];?></h2>
							<p class="weui_msg_desc en-word-break"><?php  echo $msg['submsg'];?></p>
						<?php  } ?>
					<?php  } else { ?>
						<h2 class="weui_msg_title"><?php  echo $msg;?></h2>
					<?php  } ?>
				</div>
				<div class="weui_opr_area">
					<p class="weui_btn_area">
						<?php  if($redirect) { ?>
						<a href="<?php  echo $redirect;?>" class="weui_btn weui_btn_primary external redirect">确定</a>
						<?php  } else { ?>
						<a href="#" class="weui_btn weui_btn_default back">返回</a>
						<?php  } ?>
					</p>
				</div>
				<div class="weui_extra_area">
					<a href="<?php  echo $this->createMobileUrl('home')?>" class="external">返回首页</a>
				</div>
			</div>
		</div>
	</div>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>