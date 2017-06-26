<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<style>
    .star {
        color: red;
        margin-right: 5px;
        font-weight: bold;
    }
</style>
<ul class="nav nav-tabs">
    <li <?php  if($act == 'display') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('checkout', array('act' => 'display'));?>">核销记录</a></li>
    <li <?php  if($act == 'qrcode') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('checkout', array('act' => 'qrcode'));?>">扫码核销</a></li>
    <li <?php  if($act == 'oneself') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('checkout', array('act' => 'oneself'));?>">自助核销</a></li>
</ul>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template($this->do.'/'.$this->act, TEMPLATE_INCLUDEPATH)) : (include template($this->do.'/'.$this->act, TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>