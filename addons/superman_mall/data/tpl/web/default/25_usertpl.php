<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('header', TEMPLATE_INCLUDEPATH)) : (include template('header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/shop-nav', TEMPLATE_INCLUDEPATH)) : (include template('common/shop-nav', TEMPLATE_INCLUDEPATH));?>
<ul class="nav nav-tabs">
	<li <?php  if($act == 'user' && $op == 'display') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('user', array('act' => 'user', 'op' => 'display'));?>">账号</a></li>
	<li <?php  if($act == 'group' && $op == 'display') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('user', array('act' => 'group', 'op' => 'display'));?>">身份</a></li>
	<?php  if($act == 'user' && $op == 'post' && !$_GPC['id']) { ?><li class="active"><a href="<?php  echo $this->createWebUrl('user', array('act' => 'user', 'op' => 'post'));?>">添加账号</a></li><?php  } ?>
	<?php  if($act == 'user' && $op == 'post' && $_GPC['id']) { ?><li class="active"><a href="<?php  echo $this->createWebUrl('user', array('act' => 'user', 'op' => 'post', 'id' => $_GPC['id']));?>">编辑账号</a></li><?php  } ?>
	<?php  if($act == 'group' && $op == 'post' && !$_GPC['id']) { ?><li class="active"><a href="<?php  echo $this->createWebUrl('user', array('act' => 'group', 'op' => 'post'));?>">添加身份</a></li><?php  } ?>
	<?php  if($act == 'group' && $op == 'post' && $_GPC['id']) { ?><li class="active"><a href="<?php  echo $this->createWebUrl('user', array('act' => 'group', 'op' => 'post', 'id' => $_GPC['id']));?>">编辑身份</a></li><?php  } ?>
</ul>
<?php  if($act == 'user') { ?>
	<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('user-user', TEMPLATE_INCLUDEPATH)) : (include template('user-user', TEMPLATE_INCLUDEPATH));?>
<?php  } else if($act == 'group') { ?>
	<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('user-group', TEMPLATE_INCLUDEPATH)) : (include template('user-group', TEMPLATE_INCLUDEPATH));?>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('footer', TEMPLATE_INCLUDEPATH)) : (include template('footer', TEMPLATE_INCLUDEPATH));?>