<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<div class="page-group">
	<div class="page superpage_<?php  echo $do;?> page-current" id="superpage_<?php  echo $do;?>_<?php  echo $act;?>">
		<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template("$do/$act", TEMPLATE_INCLUDEPATH)) : (include template("$do/$act", TEMPLATE_INCLUDEPATH));?>
        <?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/share', TEMPLATE_INCLUDEPATH)) : (include template('common/share', TEMPLATE_INCLUDEPATH));?>
	</div>
    <!--商户入驻协议内联页-->
    <?php  if($act=='join' && !empty($shop_setting['content'])) { ?>
    <div class="page" id='agreement'>
        <header class="bar bar-nav">
            <a class="button button-link button-nav pull-left back" href="#">
                <span class="icon icon-left"></span>
            </a>
            <h1 class='title'>商户入驻协议</h1>
        </header>
        <div class="content">
            <div class="content-padded font7">
                <?php  echo htmlspecialchars_decode($shop_setting['content'])?>
            </div>
        </div>
    </div>
    <?php  } ?>
    <!--商户简介内链页-->
    <div class="page" id='superpage_shop_description'>
        <header class="bar bar-nav">
            <a class="button button-link button-nav pull-left back" href="#">
                <span class="icon icon-left"></span>
            </a>
            <h1 class='title'>商户简介</h1>
        </header>
        <div class="content">
            <div class="content-padded font7">
                <?php  echo $shop['description'];?>
            </div>
        </div>
    </div>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>