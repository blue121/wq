<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<div class="page-group">
	<div class="page superpage_<?php  echo $do;?>" id="superpage_<?php  echo $do;?>_<?php  echo $act;?>">
		<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/nav', TEMPLATE_INCLUDEPATH)) : (include template('common/nav', TEMPLATE_INCLUDEPATH));?>
		<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/title', TEMPLATE_INCLUDEPATH)) : (include template('common/title', TEMPLATE_INCLUDEPATH));?>
		<div class="content">
			<div class="category_wrap">
				<div class="category_left pull-left">
					<?php  if($list) { ?>
					<ul>
						<?php  if(is_array($list)) { foreach($list as $key => $item) { ?>
						<li>
							<a href="#tab<?php  echo $key;?>" class="font7 tab-link <?php  if($key==0) { ?>active<?php  } ?> text-overflow"><?php  echo $item['title'];?></a>
						</li>
						<?php  } } ?>
					</ul>
					<?php  } ?>
				</div>
				<div class="category_right pull-right tabs" style="height: 300px;">
					<?php  if($list) { ?>
					<?php  if(is_array($list)) { foreach($list as $key => $item) { ?>
					<div id="tab<?php  echo $key;?>" class="tab <?php  if($key==0) { ?>active<?php  } ?> category_right_right">
						<?php  if(is_array($item['childs'])) { foreach($item['childs'] as $c) { ?>
						<div class="content-block-title">
							<a href="<?php  echo $this->createMobileUrl('list', array('cid' => $c['id']))?>" class="color-default <?php echo SUPERMAN_EXTERNAL;?>" data-no-cache="true"><?php  echo $c['title'];?></a>
						</div>
						<div class="row">
							<?php  if($c['childs']) { ?>
							<?php  if(is_array($c['childs'])) { foreach($c['childs'] as $cc) { ?>
							<div class="col-33">
								<a href="<?php  echo $this->createMobileUrl('list', array('ccid' => $cc['id']))?>" class="<?php echo SUPERMAN_EXTERNAL;?>" data-no-cache="true">
									<img class="lazyload img_square1" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC" data-original="<?php  echo tomedia($cc['cover'])?>" onerror="this.src='<?php  echo $this->superman_placeholder?>'"/>
									<span class="text-center font6 color-gray text-overflow"><?php  echo $cc['title'];?></span>
								</a>
							</div>
							<?php  } } ?>
							<?php  } ?>
						</div>
						<?php  } } ?>
					</div>
					<?php  } } ?>
					<?php  } ?>
				</div>
			</div>
		</div>
		<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/share', TEMPLATE_INCLUDEPATH)) : (include template('common/share', TEMPLATE_INCLUDEPATH));?>
	</div>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>