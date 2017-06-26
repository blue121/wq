<?php defined('IN_IA') or exit('Access Denied');?><nav class="bar bar-tab">
	<?php  if($this->footer_nav) { ?>
	<?php  if(is_array($this->footer_nav)) { foreach($this->footer_nav as $nav) { ?>
	<a class="<?php echo SUPERMAN_EXTERNAL;?> tab-item <?php  if($nav['active']) { ?>active<?php  } ?>" href="<?php  echo $nav['url'];?>" data-no-cache="true">
		<?php  if(strstr($nav['icon'], '#xe6')) { ?>
		<span class="icon iconfont">&<?php  echo $nav['icon'];?></span>
		<?php  } else { ?>
		<span class="icon"><img class="img_icon" src="<?php  echo tomedia($nav['icon'])?>" onerror="<?php  echo $this->superman_placeholder?>"></span>
		<?php  } ?>
		<span class="tab-label"><?php  echo $nav['title'];?></span>
	</a>
	<?php  } } ?>
	<?php  } ?>
</nav>