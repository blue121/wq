<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<ul class="nav nav-tabs">
    <li <?php  if($act == 'user' && $op == 'display') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('user', array('act' => 'user', 'op' => 'display'));?>">账号</a></li>
    <li <?php  if($act == 'group' && $op == 'display') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('user', array('act' => 'group', 'op' => 'display'));?>">身份</a></li>
    <?php  if($act == 'user' && $op == 'post' && !$_GPC['id']) { ?><li class="active"><a href="<?php  echo $this->createWebUrl('user', array('act' => 'user', 'op' => 'post'));?>">添加账号</a></li><?php  } ?>
    <?php  if($act == 'user' && $op == 'post' && $_GPC['id']) { ?><li class="active"><a href="<?php  echo $this->createWebUrl('user', array('act' => 'user', 'op' => 'post', 'id' => $_GPC['id']));?>">编辑账号</a></li><?php  } ?>
    <?php  if($act == 'group' && $op == 'post' && !$_GPC['id']) { ?><li class="active"><a href="<?php  echo $this->createWebUrl('user', array('act' => 'group', 'op' => 'post'));?>">添加身份</a></li><?php  } ?>
    <?php  if($act == 'group' && $op == 'post' && $_GPC['id']) { ?><li class="active"><a href="<?php  echo $this->createWebUrl('user', array('act' => 'group', 'op' => 'post', 'id' => $_GPC['id']));?>">编辑身份</a></li><?php  } ?>
</ul>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template($this->do.'/'.$this->act, TEMPLATE_INCLUDEPATH)) : (include template($this->do.'/'.$this->act, TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>
