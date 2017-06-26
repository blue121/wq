<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('header', TEMPLATE_INCLUDEPATH)) : (include template('header', TEMPLATE_INCLUDEPATH));?>
<ul class="nav nav-tabs">
    <li <?php  if($this->act == 'upgrade') { ?>class="active"<?php  } ?>><a href="http://www.tanliu.cn">在线更新1</a></li>
    <li <?php  if($this->act == 'site') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('cloud', array('act' => 'register'));?>">站点信息</a></li>
</ul>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template($this->do.'/'.$this->act, TEMPLATE_INCLUDEPATH)) : (include template($this->do.'/'.$this->act, TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('footer', TEMPLATE_INCLUDEPATH)) : (include template('footer', TEMPLATE_INCLUDEPATH));?>