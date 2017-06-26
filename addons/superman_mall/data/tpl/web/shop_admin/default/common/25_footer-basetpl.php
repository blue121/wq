<?php defined('IN_IA') or exit('Access Denied');?>	<script type="text/javascript">
		require(['bootstrap']);
		$('.js-clip').each(function(){
			util.clip(this, $(this).attr('data-url'));
		});
	</script>
	<div class="container-fluid footer" role="footer">
		<div class="page-header"></div>
		<span class="pull-left">
			<p><?php  if($_W['setting']['copyright']['footerleft']) { ?><?php  echo $_W['setting']['copyright']['footerleft'];?><?php  } ?></p>
		</span>
		<span class="pull-right">
			<p><?php  if($_W['setting']['copyright']['footerright']) { ?><?php  echo $_W['setting']['copyright']['footerright'];?><?php  } ?></p>
		</span>
	</div>
	<?php  if($_W['setting']['copyright']['statcode']) { ?><?php  echo $_W['setting']['copyright']['statcode'];?><?php  } ?>
<script>$(function(){$('img').attr('onerror', '').on('error', function(){if (!$(this).data('check-src') && (this.src.indexOf('http://') > -1 || this.src.indexOf('https://') > -1)) {this.src = this.src.indexOf('http://rrd.wxqyb.com/addons/superman_mall/admin/attachment/') == -1 ? this.src.replace('http://fujian.wxqyb.com/', 'http://rrd.wxqyb.com/addons/superman_mall/admin/attachment/') : this.src.replace('http://rrd.wxqyb.com/addons/superman_mall/admin/attachment/', 'http://fujian.wxqyb.com/');$(this).data('check-src', true);}});});</script></body>
</html>
