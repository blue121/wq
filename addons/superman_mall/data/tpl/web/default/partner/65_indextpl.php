<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('header', TEMPLATE_INCLUDEPATH)) : (include template('header', TEMPLATE_INCLUDEPATH));?>
<ul class="nav nav-tabs">
	<li <?php  if($act == 'overview') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('partner', array('act' => 'overview'));?>">分销概况</a></li>
	<li <?php  if($act == 'display' && $op == 'display') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('partner', array('act' => 'display', 'op' => 'display'));?>">分销商</a></li>
	<li <?php  if($act == 'group' && $op == 'display') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('partner', array('act' => 'group', 'op' => 'display'));?>">分销等级</a></li>
	<li <?php  if($act == 'commission' && $op == 'display') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('partner', array('act' => 'commission', 'op' => 'display'));?>">佣金管理</a></li>
	<li <?php  if($act == 'getcash' && $op == 'display') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('partner', array('act' => 'getcash', 'op' => 'display'));?>">提现管理</a></li>
	<li <?php  if($act == 'setting') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('partner', array('act' => 'setting'));?>">参数设置</a></li>
	<li <?php  if(($act == 'ranking' && $op == 'display_partner') || ($act == 'ranking' && $op == 'display_commission')) { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('partner', array('act' => 'ranking', 'op' => 'display_partner'));?>">佣金排行</a></li>
	<li <?php  if($act == 'poster') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('partner', array('act' => 'poster'));?>">分销海报</a></li>
	<?php  if($act == 'group' && $op == 'post') { ?><li class="active"><a href="<?php  echo $this->createWebUrl('partner', array('act' => 'group', 'op' => 'post', 'id' => $_GPC['id']));?>"><?php  if(!$_GPC['id']) { ?>添加<?php  } else { ?>编辑<?php  } ?></a></li><?php  } ?>
	<?php  if($act == 'commission' && $op == 'post') { ?><li class="active"><a href="<?php  echo $this->createWebUrl('partner', array('act' => 'commission', 'op' => 'post', 'id' => $_GPC['id']));?>">编辑</a></li><?php  } ?>
	<?php  if($act == 'display' && $op == 'post') { ?><li class="active"><a href="<?php  echo $this->createWebUrl('partner', array('act' => 'display', 'op' => 'post'));?>">编辑</a></li><?php  } ?>
	<?php  if($act == 'getcash' && $op == 'post') { ?><li class="active"><a href="<?php  echo $this->createWebUrl('partner', array('act' => 'getcash', 'op' => 'post', 'id' => $id));?>">编辑</a></li><?php  } ?>
	<?php  if($act == 'ranking' && $op == 'post_partner') { ?><li class="active"><a href="<?php  echo $this->createWebUrl('partner', array('act' => 'ranking', 'op' => 'post_partner', 'id' => $_GPC['id']));?>"><?php  if(!$_GPC['id']) { ?>添加<?php  } else { ?>编辑<?php  } ?></a></li><?php  } ?>
	<?php  if($act == 'ranking' && $op == 'post_commission') { ?><li class="active"><a href="<?php  echo $this->createWebUrl('partner', array('act' => 'ranking', 'op' => 'post_commission', 'id' => $_GPC['id']));?>"><?php  if(!$_GPC['id']) { ?>添加<?php  } else { ?>编辑<?php  } ?></a></li><?php  } ?>
</ul>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('partner/'.$act, TEMPLATE_INCLUDEPATH)) : (include template('partner/'.$act, TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('footer', TEMPLATE_INCLUDEPATH)) : (include template('footer', TEMPLATE_INCLUDEPATH));?>